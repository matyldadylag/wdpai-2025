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

    public function search()
    {
        // Check the request's Content-Type header to ensure it's JSON.
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === "application/json") {
            // Read raw JSON and decode it into an associative array
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);

            // Set response type and HTTP status code
            header('Content-type: application/json');
            http_response_code(200);

            // Return search results
            echo json_encode($this->plantsRepository->getPlantsByName($decoded['search']));
        }
    }
}
