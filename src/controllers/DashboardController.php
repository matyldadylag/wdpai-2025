<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/PlantsRepository.php';

class DashboardController extends AppController {
    private $plantsRepository;

    public function __construct() {
        $this->plantsRepository = new PlantsRepository();
    }

    public function index() {
        // Require user to be logged in
        $this->requireLogin();

        // Optionally pass user data to the view
        $user = $this->getUser();

        return $this->render("dashboard", [
            'user' => $user
        ]);
    }

    public function search()
    {
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === "application/json") {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content, true);

            header('Content-type: application/json');
            http_response_code(200);

            echo json_encode($this->cardsRepository->getCardsByTitle($decoded['search']));
        }
    }
}
