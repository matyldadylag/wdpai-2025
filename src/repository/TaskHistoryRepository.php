<?php

require_once 'Repository.php';

class TaskHistoryRepository extends Repository
{
    // Get date of last time a stask was performed
    public function getLastPerformedAt(int $plantId, int $taskId): ?string
    {
        // Connect to database and prepare SQL query
        $stmt = $this->database->connect()->prepare("
            SELECT performed_at
            FROM task_history
            WHERE plant_id = :plant_id
              AND task_id = :task_id
            ORDER BY performed_at DESC
            LIMIT 1
        ");

        // Bind parameters and execute query
        $stmt->bindParam(':plant_id', $plantId, PDO::PARAM_INT);
        $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        // Returns last performed_at or null
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['performed_at'] : null;
    }
}
