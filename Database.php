<?php

class Database {
    // Singleton instance of Database class
    private static ?Database $instance = null;

    // Singleton instance of PDO connection
    private ?PDO $connection = null;

    // Database credentials
    private $username;
    private $password;
    private $host;
    private $database;
    private $port;

    // Private constructor prevents direct instantiation
    public function __construct()
    {
        // Read from .env, with defaults if missing
        $this->username = $_ENV['DB_USER'] ?? 'docker';
        $this->password = $_ENV['DB_PASS'] ?? 'docker';
        $this->host     = $_ENV['DB_HOST'] ?? 'localhost';
        $this->database = $_ENV['DB_NAME'] ?? 'db';
        $this->port     = $_ENV['DB_PORT'] ?? '5432';
    }

    // Returns single instance of Database class
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    public function connect(): PDO
    {
        // If already connected, reuse the same PDO object
        if ($this->connection !== null) {
            return $this->connection;
        }

        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->database}";

            $this->connection = new PDO(
                $dsn,
                $this->username,
                $this->password,
                ["sslmode"  => "prefer"]
            );

            // Set PDO error handling
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e) {
            // Log sensitive error message for developers
            error_log("DB connection error: " . $e->getMessage());

            // Show safe custom error page
            http_response_code(500);
            require __DIR__ . "/../../views/errors/500.html"; 
            exit;
        }

        return $this->connection;
    }

    public function disconnect(): void
    {
        // Sets PDO object to null → closes database connection
        $this->connection = null;
    }
}