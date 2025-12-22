<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/PlantsRepository.php';
require_once __DIR__ . '/../repository/TasksRepository.php';
require_once __DIR__ . '/../repository/TaskHistoryRepository.php';

class DashboardController extends AppController
{
    // TODO Add comments
    private PlantsRepository $plantsRepository;
    private TasksRepository $tasksRepository;
    private TaskHistoryRepository $taskHistoryRepository;

    public function __construct()
    {
        $this->plantsRepository = new PlantsRepository();
        $this->tasksRepository = new TasksRepository();
        $this->taskHistoryRepository = new TaskHistoryRepository();
    }

    public function index()
    {
        $this->requireLogin();

        $user = $this->getUser();
        $userId = (int)$user['id'];

        $today = new DateTimeImmutable('today');
        $todayKey = $today->format('Y-m-d');

        $plants = $this->plantsRepository->getPlantsForUser($userId);

        // Birthdays: month-day matches today (date_added)
        $birthdays = $this->plantsRepository->getBirthdayPlantsForUser($userId, $today);

        $todayTasks = [];
        $overdueTasks = [];

        // Color per task type (same idea as Calendar)
        $taskColorIndexByTaskId = [];
        $nextColorIndex = 0;

        foreach ($plants as $plant) {
            $speciesId = (int)$plant['species_id'];
            $plantId = (int)$plant['plant_id'];

            $plantName = $plant['plant_name'] ?: $plant['species_name'];
            $speciesName = $plant['species_name'];

            $speciesTasks = $this->tasksRepository->getTasksForSpecies($speciesId);

            foreach ($speciesTasks as $taskRow) {
                $taskId = (int)$taskRow['task_id'];
                $taskName = $taskRow['task_name'];
                $freqDays = (int)$taskRow['frequency_days'];

                if ($freqDays <= 0) continue;

                if (!isset($taskColorIndexByTaskId[$taskId])) {
                    $taskColorIndexByTaskId[$taskId] = $nextColorIndex++;
                }
                $colorIndex = $taskColorIndexByTaskId[$taskId];

                $lastPerformedAt = $this->taskHistoryRepository->getLastPerformedAt($plantId, $taskId);

                // If never done, assume due starting from date_added (fallback to today)
                if ($lastPerformedAt) {
                    $base = new DateTimeImmutable($lastPerformedAt);
                } else {
                    $base = !empty($plant['date_added'])
                        ? new DateTimeImmutable($plant['date_added'])
                        : $today;
                }
                $nextDue = $base->modify('+' . $freqDays . ' days');
                $nextDueKey = $nextDue->format('Y-m-d');

                $taskPayload = [
                    'plant_id'     => $plantId,
                    'plant_name'   => $plantName,
                    'species_name' => $speciesName,
                    'task_id'      => $taskId,
                    'task_name'    => $taskName,
                    'color_index'  => $colorIndex,
                    'due_date'     => $nextDueKey,
                ];

                if ($nextDueKey === $todayKey) {
                    $todayTasks[] = $taskPayload;
                } elseif ($nextDue < $today) {
                    $overdueTasks[] = $taskPayload;
                }
            }
        }

        // Optional: sort overdue oldest first, and today's tasks by name
        usort($overdueTasks, fn($a, $b) => strcmp($a['due_date'], $b['due_date']));
        usort($todayTasks, fn($a, $b) => strcmp($a['plant_name'], $b['plant_name']));

        return $this->render('dashboard', [
            'user'         => $user,
            'todayKey'     => $todayKey,
            'todayTasks'   => $todayTasks,
            'overdueTasks' => $overdueTasks,
            'birthdays'    => $birthdays,
        ]);
    }
}