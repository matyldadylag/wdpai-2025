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
                p.date_added,
                s.species_id,
                s.species_name
            FROM plants p
            LEFT JOIN species s ON p.species_id = s.species_id
            WHERE p.user_id = :user_id
            ORDER BY p.plant_id ASC
        ');

        // Bind parameters and execute query
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Return records
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

    // Get a given plant for a given user
    public function getSinglePlantForUser(int $plantId, int $userId): ?array
    {
        // Connect to the database
        $conn = $this->database->connect();

        // Prepare SQL query
        $stmt = $conn->prepare('
            SELECT 
                p.plant_id,
                p.plant_name,
                p.date_added,
                p.species_id,
                s.species_name
            FROM plants p
            LEFT JOIN species s ON p.species_id = s.species_id
            WHERE p.plant_id = :plant_id
            AND p.user_id = :user_id
        ');

        // Bind parameters and execute query
        $stmt->bindParam(':plant_id', $plantId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Return plant
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePlant(int $plantId, int $userId, int $speciesId, string $plantName, ): void {
        // Connect to the database
        $conn = $this->database->connect();

        // Prepare SQL query
        $stmt = $conn->prepare('
            UPDATE plants
            SET species_id = :species_id,
                plant_name = :plant_name
            WHERE plant_id = :plant_id
            AND user_id = :user_id
        ');

        // Bind parameters
        $stmt->bindValue(':species_id', $speciesId, PDO::PARAM_INT);
        $stmt->bindValue(':plant_name', $plantName, PDO::PARAM_STR);
        $stmt->bindValue(':plant_id', $plantId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();
    }

    public function deletePlant(int $plantId, int $userId): void
    {
        // Connect to the database
        $conn = $this->database->connect();

        // Prepare SQL query
        $stmt = $conn->prepare('
            DELETE FROM plants
            WHERE plant_id = :plant_id
            AND user_id = :user_id
        ');

        // Bind parameters
        $stmt->bindValue(':plant_id', $plantId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        // Execute query
        $stmt->execute();
    }

    // Get all plants with date_added birthday today
    public function getBirthdayPlantsForUser(int $userId, DateTimeImmutable $today): array
    {
        // Connect to the database
        $conn = $this->database->connect();

        // Prepare SQL query
        $stmt = $conn->prepare('
            SELECT 
                p.plant_id,
                p.plant_name,
                p.date_added,
                s.species_name
            FROM plants p
            LEFT JOIN species s ON p.species_id = s.species_id
            WHERE p.user_id = :user_id
            AND to_char(p.date_added::date, \'MM-DD\') = :mmdd
            ORDER BY p.plant_id ASC
        ');

        // Bind parameters and execute query
        $mmdd = $today->format('m-d');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':mmdd', $mmdd, PDO::PARAM_STR);
        $stmt->execute();

        // Return all records
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}