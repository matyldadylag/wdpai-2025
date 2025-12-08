<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/PlantsRepository.php';

class DashboardController extends AppController {
    private $plantsRepository;

    // Create new repository instance
    public function __construct() {
        $this->plantsRepository = new PlantsRepository();
    }

    public function index() {
        // Require user to be logged in
        $this->requireLogin();

        // Retrieve data about the currently logged-in user
        $user = $this->getUser();

        // Render view
        return $this->render("dashboard", [
            'user' => $user
        ]);
    }
}
