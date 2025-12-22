<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/PlantsRepository.php';
require_once __DIR__.'/../repository/TasksRepository.php';
require_once __DIR__.'/../repository/TaskHistoryRepository.php';

class CalendarController extends AppController
{
    private PlantsRepository $plantsRepository;
    private TasksRepository $tasksRepository;
    private TaskHistoryRepository $taskHistoryRepository;

    // Create new repositories instances
    public function __construct()
    {
        $this->plantsRepository       = new PlantsRepository();
        $this->tasksRepository        = new TasksRepository();
        $this->taskHistoryRepository  = new TaskHistoryRepository();
    }

    // Builds a monthly schedule of plant care tasks for the logged-in user and renders the calendar view
    public function index()
    {
        // Require user to be logged in
        $this->requireLogin();

        // Retrieve data about the currently logged-in user
        $user = $this->getUser();
        $userId = (int)$user['id'];

        // If month wasn't requested via URL parameter (arrows) fallback to the current date month
        $today = new DateTimeImmutable('today');
        $paramMonth = $_GET['month'] ?? $today->format('Y-m');

        // Set boundaries - first day of the current month +3 months
        $currentMonth = $today->modify('first day of this month');
        $maxMonth     = $currentMonth->modify('+3 months');

        // Convert requested month string into a DateTime object
        $requestedMonth = DateTimeImmutable::createFromFormat('Y-m-d', $paramMonth . '-01') ?: $currentMonth;

        // Prevent navigating to months earlier than the current month
        if ($requestedMonth < $currentMonth) {
            $requestedMonth = $currentMonth;
        }

        // Prevent navigating beyond the maximum allowed future month
        if ($requestedMonth > $maxMonth) {
            $requestedMonth = $maxMonth;
        }

        // Extract year and month numbers for later use in rendering
        $year  = (int)$requestedMonth->format('Y');
        $month = (int)$requestedMonth->format('m');

        // Create a DateTime object representing the first day of the selected month
        $firstDayOfMonth = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));

        // Determine how many days the selected month contains
        $daysInMonth     = (int)$firstDayOfMonth->format('t');

        // Define the date range for which tasks should be generated
        $fromDate = $firstDayOfMonth;
        $toDate   = $firstDayOfMonth->modify('last day of this month');

        // Retrieve info on plants for given user
        $plants = $this->plantsRepository->getPlantsForUser($userId);

        // Build schedule
        $tasksByDate = [];
        $taskColorIndexByTaskId = [];
        $nextColorIndex = 0;
        
        // Iterate through all user's plants   
        foreach ($plants as $plant) {
            $speciesId   = (int)$plant['species_id'];
            $plantId     = (int)$plant['plant_id'];

            // Use custom plant name if available, otherwise fallback to species name
            $plantName   = $plant['plant_name'] ?: $plant['species_name'];
            $speciesName = $plant['species_name'];

            // Retrieve all tasks defined for the plant's species
            $speciesTasks = $this->tasksRepository->getTasksForSpecies($speciesId);

            // Iterate through each task assigned to this species
            foreach ($speciesTasks as $taskRow) {
                $taskId        = (int)$taskRow['task_id'];
                $taskName      = $taskRow['task_name'];
                $freqDays      = (int)$taskRow['frequency_days'];

                // Ignore tasks without a valid frequency
                if ($freqDays <= 0) {
                    continue;
                }

                // Color per task type
                if (!isset($taskColorIndexByTaskId[$taskId])) {
                    $taskColorIndexByTaskId[$taskId] = $nextColorIndex++;
                }
                $colorIndex = $taskColorIndexByTaskId[$taskId];

                // Retrieve the last time this task was performed for this plant
                $lastPerformedAt = $this->taskHistoryRepository->getLastPerformedAt($plantId, $taskId);

                // Determine the base date for scheduling the next occurrence
                if ($lastPerformedAt) {
                    $base = new DateTimeImmutable($lastPerformedAt);
                } else {
                    // If never done, start from "today"
                    $base = $today;
                }

                // Calculate the first due date based on frequency
                $nextDue = $base->modify('+' . $freqDays . ' days');

                // Generate all occurrences that fall into this month’s range
                while ($nextDue <= $toDate) {
                    if ($nextDue >= $fromDate) {
                        $key = $nextDue->format('Y-m-d');

                        $tasksByDate[$key][] = [
                            'plant_id'       => $plantId,
                            'plant_name'     => $plantName,
                            'species_name'   => $speciesName,
                            'task_id'        => $taskId,
                            'task_name'      => $taskName,
                            'color_index'    => $colorIndex,
                        ];
                    }

                    // Move to the next occurrence
                    $nextDue = $nextDue->modify('+' . $freqDays . ' days');
                }
            }
        }

        // Determine whether previous / next month navigation is allowed
        $prevMonth = $requestedMonth > $currentMonth
            ? $requestedMonth->modify('-1 month')->format('Y-m')
            : null;

        $nextMonth = $requestedMonth < $maxMonth
            ? $requestedMonth->modify('+1 month')->format('Y-m')
            : null;

        // Pre-select today if in month, otherwise first of month
        $selectedDate = $today->format('Y-m') === $requestedMonth->format('Y-m')
            ? $today->format('Y-m-d')
            : $firstDayOfMonth->format('Y-m-d');

        if (!isset($tasksByDate[$selectedDate]) && $selectedDate < $fromDate->format('Y-m-d')) {
            $selectedDate = $fromDate->format('Y-m-d');
        }

        // Render view
        $this->render('calendar', [
            'year'           => $year,
            'month'          => $month,
            'firstDayOfMonth'=> $firstDayOfMonth,
            'daysInMonth'    => $daysInMonth,
            'tasksByDate'    => $tasksByDate,
            'selectedDate'   => $selectedDate,
            'prevMonth'      => $prevMonth,
            'nextMonth'      => $nextMonth,
        ]);
    }

    // TODO check if FETCH API done correctly + add comments
    // Insert task into task history when user marks it as done
    public function markTaskDone()
    {
        // Required user to be logged in
        $this->requireLogin();

        // Retrieve user information
        $user = $this->getUser();
        $userId = (int)$user['id'];
        
        // If request is not POST return default view
        if ($this->isGet()) {
            return $this->render("index");
        }

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);

        $taskId  = isset($data['task_id']) ? (int)$data['task_id'] : 0;
        $plantId = isset($data['plant_id']) ? (int)$data['plant_id'] : 0;

        if ($taskId <= 0 || $plantId <= 0) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Invalid payload']);
            return;
        }

        // Zakładam, że możesz dodać taką metodę albo masz już coś podobnego:
        $plant = $this->plantsRepository->getSinglePlantForUser($plantId, $userId);
        if (!$plant) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Forbidden']);
            return;
        }

        // Insert task into history
        try {
            $historyId = $this->taskHistoryRepository->insertPerformedNow($plantId, $taskId);

            header('Content-Type: application/json');
            echo json_encode([
                'ok' => true,
                'task_history_id' => $historyId
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Server error']);
        }
    }
}