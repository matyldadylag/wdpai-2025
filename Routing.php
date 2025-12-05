<?php
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/MyPlantsController.php';
require_once 'src/controllers/CalendarController.php';

class Routing {
    private static ?Routing $instance = null;

    public static function getInstance(): Routing
    {
        if (self::$instance === null) {
            self::$instance = new Routing();
        }
        return self::$instance;
    }

    private function __construct() {}

    private function __clone() {}

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    private static array $routes = [
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
        'search-plants' => [
            'controller' => 'DashboardController',
            'action' => 'search'
        ],
    ];

    public static function run(string $path) {
        if (array_key_exists($path, self::$routes)) {
            $controllerName = self::$routes[$path]['controller'];
            $action = self::$routes[$path]['action'];

            $controller = new $controllerName();
            $controller->$action();
        } else {
            include 'public/views/404.html';
        }
    }
}
    // parametr id i przekazywanie z pomocą regex