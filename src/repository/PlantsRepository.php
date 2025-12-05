<?php

require_once 'Repository.php';

class PlantsRepository extends Repository {
    // Search for specific plant by request with JSON
    public function getPlantsByName(string $searchString)
    {
        // Convert to lowercase and add wildcard characters
        $searchString = '%' . strtolower($searchString) . '%';

        // Execute SQL statement with binded parameters
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM plants
            WHERE LOWER(name) LIKE :search OR LOWER(description) LIKE :search
        ');
        $stmt->bindParam(':search', $searchString, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch all matching rows as an associative array and return them
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}