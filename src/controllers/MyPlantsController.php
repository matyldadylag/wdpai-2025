<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__ . '/../repository/PlantsRepository.php';

class MyPlantsController extends AppController {
    private PlantsRepository $plantsRepository;

    // Create new repository instance
    public function __construct()
    {
        $this->plantsRepository = new PlantsRepository();
    }

    public function index() {
        // Require user to be logged in
        $this->requireLogin();

        // Retrieve data about the currently logged-in user
        $user = $this->getUser();

        // Get user ID
        $userId = (int)$user['id'];

        // Fetch plants for this user
        $plants = $this->plantsRepository->getPlantsForUser($userId);

        // Render view
        return $this->render("my-plants", [
            'user' => $user,
            'plants' => $plants
        ]);
    }
}
