<?php

require_once 'Repository.php';

class TasksRepository extends Repository
{
    // Get all info on tasks for a given species
    public function getTasksForSpecies(int $speciesId): array
    {
        // Connect to the database and prepare SQL query
        $stmt = $this->database->connect()->prepare("
            SELECT 
                st.species_task_id,
                st.species_id,
                st.task_id,
                st.frequency_days,
                t.task_name
            FROM species_task st
            JOIN tasks t ON t.task_id = st.task_id
            WHERE st.species_id = :species_id
        ");

        // Bind parameters and execute query
        $stmt->bindParam(':species_id', $speciesId, PDO::PARAM_INT);
        $stmt->execute();

        // Return records
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}