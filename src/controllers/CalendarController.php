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

    public function index()
    {
        // Require user to be logged in
        $this->requireLogin();

        // Retrieve data about the currently logged-in user
        $user = $this->getUser();
        $userId = (int)$user['id'];

        // Determine which month to show (current or selected by user)
        $today = new DateTimeImmutable('today');
        $paramMonth = $_GET['month'] ?? $today->format('Y-m');

        // Limit calendar display to +3 months from current date
        $currentMonth = $today->modify('first day of this month');
        $maxMonth     = $currentMonth->modify('+3 months');

        $requestedMonth = DateTimeImmutable::createFromFormat('Y-m-d', $paramMonth . '-01') ?: $currentMonth;

        if ($requestedMonth < $currentMonth) {
            $requestedMonth = $currentMonth;
        }
        if ($requestedMonth > $maxMonth) {
            $requestedMonth = $maxMonth;
        }

        $year  = (int)$requestedMonth->format('Y');
        $month = (int)$requestedMonth->format('m');

        $firstDayOfMonth = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
        $daysInMonth     = (int)$firstDayOfMonth->format('t');

        $fromDate = $firstDayOfMonth;
        $toDate   = $firstDayOfMonth->modify('last day of this month');

        // Retrieve info on plants
        $plants = $this->plantsRepository->getPlantsForUser($userId);

        // 4. Build schedule: [Y-m-d] => [task entries...]
        $tasksByDate = [];
        $taskColorIndexByTaskId = [];
        $nextColorIndex = 0;

        foreach ($plants as $plant) {
            $speciesId   = (int)$plant['species_id'];
            $plantId     = (int)$plant['plant_id'];
            $plantName   = $plant['plant_name'] ?: $plant['species_name'];
            $speciesName = $plant['species_name'];

            $speciesTasks = $this->tasksRepository->getTasksForSpecies($speciesId);

            foreach ($speciesTasks as $taskRow) {
                $taskId        = (int)$taskRow['task_id'];
                $taskName      = $taskRow['task_name'];
                $freqDays      = (int)$taskRow['frequency_days'];

                if ($freqDays <= 0) {
                    continue;
                }

                // Color index per task type
                if (!isset($taskColorIndexByTaskId[$taskId])) {
                    $taskColorIndexByTaskId[$taskId] = $nextColorIndex++;
                }
                $colorIndex = $taskColorIndexByTaskId[$taskId];

                // Last performed
                $lastPerformedAt = $this->taskHistoryRepository->getLastPerformedAt($plantId, $taskId);

                if ($lastPerformedAt) {
                    $base = new DateTimeImmutable($lastPerformedAt);
                } else {
                    // If never done, start from "today"
                    $base = $today;
                }

                // next due date = base + frequency
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

        // 5. Data for navigation
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
}