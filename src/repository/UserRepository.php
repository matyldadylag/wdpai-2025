<?php

require_once 'Repository.php';

class UserRepository extends Repository
{
    // Insert a new user into the database
    public function createUser(string $name, string $email, string $hashedPassword): void
    {
        // Prepare SQL statement to insert a new user
        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (name, email, password)
            VALUES (?, ?, ?);
        ');

        // Execute the prepared statement with provided data
        $stmt->execute([
            $name,
            $email,
            $hashedPassword
        ]);
    }

    // Retrieve a user by their email address.
    public function getUserByEmail(string $email)
    {
        // Prepare SQL statement to select user by email
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE email = ?
        ');

        // Execute the prepared statement
        $stmt->execute([
            $email
        ]);

        // Fetch the user as an associative array
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user;
    }
}