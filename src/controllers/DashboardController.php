<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/PlantsRepository.php';
require_once __DIR__ . '/../repository/TasksRepository.php';
require_once __DIR__ . '/../repository/TaskHistoryRepository.php';

class DashboardController extends AppController
{
    private PlantsRepository $plantsRepository;
    private TasksRepository $tasksRepository;
    private TaskHistoryRepository $taskHistoryRepository;

    // Create new repositories instances
    public function __construct()
    {
        $this->plantsRepository = new PlantsRepository();
        $this->tasksRepository = new TasksRepository();
        $this->taskHistoryRepository = new TaskHistoryRepository();
    }

    // Builds a user dashboard with most important information for the day
    public function index()
    {
        // Require user to be logged in
        $this->requireLogin();

        // Retrieve data about the currently logged-in user
        $user = $this->getUser();
        $userId = (int)$user['id'];

        // Retrieve current date
        $today = new DateTimeImmutable('today');
        $todayKey = $today->format('Y-m-d');

        // Get all plants for user
        $plants = $this->plantsRepository->getPlantsForUser($userId);

        // Get all plant birthdays for user
        $birthdays = $this->plantsRepository->getBirthdayPlantsForUser($userId, $today);

        // Build simplified schedule
        $todayTasks = [];
        $overdueTasks = [];

        // Color per task type
        $taskColorIndexByTaskId = [];
        $nextColorIndex = 0;

        // Iterate through all user's plants
        foreach ($plants as $plant) {
            // Extract plant and species identifiers
            $speciesId = (int)$plant['species_id'];
            $plantId = (int)$plant['plant_id'];

            // Use custom plant name if provided, otherwise fallback to species name
            $plantName = $plant['plant_name'] ?: $plant['species_name'];
            $speciesName = $plant['species_name'];

            // Retrieve all care tasks defined for the plant's species
            $speciesTasks = $this->tasksRepository->getTasksForSpecies($speciesId);

            // Iterate through all tasks assigned to this species
            foreach ($speciesTasks as $taskRow) {
                // Extract task information
                $taskId = (int)$taskRow['task_id'];
                $taskName = $taskRow['task_name'];
                $freqDays = (int)$taskRow['frequency_days'];

                // Assign a persistent color index for this task type
                if (!isset($taskColorIndexByTaskId[$taskId])) {
                    $taskColorIndexByTaskId[$taskId] = $nextColorIndex++;
                }
                $colorIndex = $taskColorIndexByTaskId[$taskId];

                // Retrieve the last time this task was performed for this plant
                $lastPerformedAt = $this->taskHistoryRepository->getLastPerformedAt($plantId, $taskId);

                // Determine base date for scheduling the next task occurrence
                // If never done, assume the task was first due relative to plant creation date
                if ($lastPerformedAt) {
                    $base = new DateTimeImmutable($lastPerformedAt);
                } else {
                    $base = !empty($plant['date_added'])
                        ? new DateTimeImmutable($plant['date_added'])
                        : $today;
                }

                // Calculate next due date based on task frequency
                $nextDue = $base->modify('+' . $freqDays . ' days');
                $nextDueKey = $nextDue->format('Y-m-d');

                // Prepare task payload for the dashboard view
                $taskPayload = [
                    'plant_id'     => $plantId,
                    'plant_name'   => $plantName,
                    'species_name' => $speciesName,
                    'task_id'      => $taskId,
                    'task_name'    => $taskName,
                    'color_index'  => $colorIndex,
                    'due_date'     => $nextDueKey,
                ];

                // Categorize task as due today or overdue
                if ($nextDueKey === $todayKey) {
                    $todayTasks[] = $taskPayload;
                } elseif ($nextDue < $today) {
                    $overdueTasks[] = $taskPayload;
                }
            }
        }

        // Render view
        return $this->render('dashboard', [
            'user'         => $user,
            'todayKey'     => $todayKey,
            'todayTasks'   => $todayTasks,
            'overdueTasks' => $overdueTasks,
            'birthdays'    => $birthdays,
        ]);
    }
}