<?php

require_once __DIR__.'/../../Database.php';

class Repository {
    protected $database;

    public function __construct()
    {
        // Get the shared Database instance (Singleton)
        $this->database = Database::getInstance();
    }
}