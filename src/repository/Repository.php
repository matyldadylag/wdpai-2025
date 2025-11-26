<?php

require_once __DIR__.'/../../Database.php';

class Repository {
    protected $database;

    // Create a new Database instance
    public function __construct()
    {
        $this->database = new Database();
    }
}