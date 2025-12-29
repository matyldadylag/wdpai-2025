<?php

require_once 'Repository.php';

class UserRepository extends Repository
{
    private static ?UserRepository $instance = null;

    // Prevent direct creation
    protected function __construct()
    {
        parent::__construct();
    }

    // Prevent cloning
    private function __clone() {}

    // Prevent unserialization
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    // Global access point
    public static function getInstance(): UserRepository
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // Insert a new user into the database
    public function createUser(string $name, string $email, string $hashedPassword): void
    {
        // Prepare SQL statement to insert a new user
        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (user_name, email, password)
            VALUES (?, ?, ?);
        ');

        // Execute the prepared statement with provided data
        $stmt->execute([
            $name,
            $email,
            $hashedPassword
        ]);
    }

    // Retrieve a user by their email address
    public function getUserByEmail(string $email): ?array
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

        return $user ?: null;
    }

    // Get all users for admin panel
    public function getAllUsers(): array
    {
        // Connect to the database and prepare SQL query
        $stmt = $this->database->connect()->prepare("
            SELECT
                u.user_id,
                u.user_name,
                u.email,
                u.role
            FROM users u
            ORDER BY u.user_id ASC
        ");

        // Execute query and return fetched records
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // Delete user by id
    public function deleteUserById(int $userId): bool
    {
        // Connect to the database and prepare SQL query
        $stmt = $this->database->connect()->prepare("
            DELETE FROM users
            WHERE user_id = :id
        ");

        // Bind parameters and execute
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}