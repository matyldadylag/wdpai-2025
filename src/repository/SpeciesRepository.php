<?php

require_once 'Repository.php';

class SpeciesRepository extends Repository {
    // Returns all plant species
    public function findAll()
    {
        $stmt = $this->database->connect()->prepare("
            SELECT species_id, species_name 
            FROM species 
            ORDER BY species_name ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find species name by species id
    public function findNameById($speciesId): string
    {
        // Connect to database and prepare SQL query
        $stmt = $this->database->connect()->prepare('
            SELECT species_name
            FROM species
            WHERE species_id = :species_id
        ');

        // Bind parameters and execute query
        $stmt->execute([
            ':species_id' => $speciesId
        ]);

        // Fetch and return row
        return $stmt->fetchColumn() ?: null;
    }
}