<?php

// TODO SINGLETON
class Database {
    private $username;
    private $password;
    private $host;
    private $database;
    private $port;

    public function __construct()
    {
        // Read from .env, with defaults if missing
        $this->username = $_ENV['DB_USER'] ?? 'docker';
        $this->password = $_ENV['DB_PASS'] ?? 'docker';
        $this->host     = $_ENV['DB_HOST'] ?? 'localhost';
        $this->database = $_ENV['DB_NAME'] ?? 'db';
        $this->port     = $_ENV['DB_PORT'] ?? '5432';
    }

    // Connect to the database
    public function connect()
    {
        try {
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->database}";

            $conn = new PDO(
                $dsn,
                $this->username,
                $this->password,
                ["sslmode"  => "prefer"]
            );

            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        }
        catch(PDOException $e) {
            // TODO custom 500 error page instead of die()
            die("Connection failed: " . $e->getMessage());
        }
    }

    // TODO disconnect method
}