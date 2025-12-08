<?php

require_once 'Repository.php';

class PlantsRepository extends Repository {
    // Get all plants for a given user
    public function getPlantsForUser(int $userId): array
    {
        // Connect to the database
        $conn = $this->database->connect();

        // Prepare SQL query
        $stmt = $conn->prepare('
            SELECT 
                p.plant_id,
                p.plant_name,
                s.species_name
            FROM plants p
            LEFT JOIN species s ON p.species_id = s.species_id
            WHERE p.user_id = :user_id
            ORDER BY p.plant_id ASC
        ');

        // Bind parameters and execute query
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Return plants
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insert a new plant into the database
    public function createPlant(int $userId, int $speciesId, string $plantName): void
    {
        // Prepare SQL statement to insert a new user
        $stmt = $this->database->connect()->prepare('
            INSERT INTO plants (user_id, species_id, plant_name)
            VALUES (?, ?, ?);
        ');

        // Execute the prepared statement with provided data
        $stmt->execute([
            $userId,
            $speciesId,
            $plantName
        ]);
    }
}