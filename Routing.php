<?php
require_once 'src/controllers/SecurityController.php';
require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/MyPlantsController.php';
require_once 'src/controllers/CalendarController.php';

class Routing {
    // Singleton instance of the Routing class
    private static ?Routing $instance = null;

    // Returns single instance of Routing class
    public static function getInstance(): Routing
    {
        if (self::$instance === null) {
            self::$instance = new Routing();
        }
        return self::$instance;
    }

    // Private constructor prevents direct instantiation
    private function __construct() {}

    // Private clone prevents creating a copy
    private function __clone() {}

    // Prevent unserializing the singleton
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    // Routing table
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
        'my-plants/create' => [
            'controller' => 'MyPlantsController',
            'action' => 'create'
        ],
        'my-plants/update' => [
            'controller' => 'MyPlantsController',
            'action'     => 'update'
        ],
        'my-plants/delete' => [
            'controller' => 'MyPlantsController',
            'action'     => 'delete'
        ],
        'calendar' => [
            'controller' => 'CalendarController',
            'action'     => 'index'
        ],
        'calendar/mark-task-done' => [
            'controller' => 'CalendarController',
            'action'     => 'markTaskDone'
        ],
    ];

    // Main routing method
    public function run(string $path) {
        // Checks if requested path matches a simple static route
        if (array_key_exists($path, self::$routes) && !isset(self::$routes[$path]['pattern'])) {
            $controllerName = self::$routes[$path]['controller'];
            $action         = self::$routes[$path]['action'];

            // Instantiate the controller and call the method
            $controller = new $controllerName();
            $controller->$action();

            return;
        }

        // Check dynamic routes using regex patterns
        foreach (self::$routes as $route) {
            // Skip routes without a pattern
            if (!isset($route['pattern'])) {
                continue;
            }

            // If URL matches the regex pattern
            if (preg_match($route['pattern'], $path, $matches)) {
                $controllerName = $route['controller'];
                $action         = $route['action'];

                // Instantiate the controller
                $controller = new $controllerName();

                // Extract parameters captured by regex
                $params = array_slice($matches, 1);

                // Call the action with dynamic parameters
                $controller->$action(...$params);
                return;
            }
        }

        // If no route matches, show 404 page
        include 'public/views/404.html';
    }
}