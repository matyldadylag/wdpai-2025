<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';

class MyPlantsController extends AppController {
    public function index() {
        // Require user to be logged in
        $this->requireLogin();

        // Optionally pass user data to the view
        $user = $this->getUser();

        return $this->render("my-plants", [
            'user' => $user
        ]);
    }
}
