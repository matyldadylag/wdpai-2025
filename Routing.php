<?php
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/MyPlantsController.php';
require_once 'src/controllers/CalendarController.php';

// Zaprogramowac tak żeby te obiekty controller się nie tworzyły bez końca, architektura singletona
// pozbyć się switchcase'a
// path ma byc regex string

class Routing {
    public static $routes = [
        'login' => [
            'controller' => 'SecurityController',
            'action' => 'login'
        ],
        'register' => [
            'controller' => 'SecurityController',
            'action' => 'register'
        ],
        'logout' => [
            'controller' => 'SecurityController',
            'action' => 'logout'
        ],
        'dashboard' => [
            'controller' => 'DashboardController',
            'action' => 'index'
        ],
        'my-plants' => [
            'controller' => 'MyPlantsController',
            'action' => 'index'
        ],
        'calendar' => [
            'controller' => 'CalendarController',
            'action' => 'index'
        ],
    ];

    public static function run(string $path) {
    // path i regex coś przetworzyć
    // pozbyć się switchcase'a
        switch ($path) {
            case 'dashboard':
            case 'login':
            case 'register':
            case 'logout':
            case 'my-plants':
            case 'calendar':
                $controller = Routing::$routes[$path]['controller'];
                $action = Routing::$routes[$path]['action'];

                $controllerObj = new $controller;
                $controllerObj->$action();
                break;
            default:
                include 'public/views/404.html';
                break;
        } 
    }
}