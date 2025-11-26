<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';

class DashboardController extends AppController {
    public function index() {
        return $this->render("dashboard");
    }
}