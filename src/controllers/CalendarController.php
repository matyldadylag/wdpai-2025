<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';

class CalendarController extends AppController {
    public function index() {
        // Require user to be logged in
        $this->requireLogin();

        // Retrieve data about the currently logged-in user
        $user = $this->getUser();

        // Render view
        return $this->render("calendar", [
            'user' => $user
        ]);
    }
}
