<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';

class SecurityController extends AppController {
    private UserRepository $userRepository;

    // Create new repository instance
    public function __construct() {
        $this->userRepository = UserRepository::getInstance();
    }

    public function login()
    {
        // If the request is GET show the login form
        if ($this->isGet()) {
            return $this->render("login");
        }

        // Initialize login attempts data
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [
                'count' => 0,
                'last_attempt' => null
            ];
        }

        // Check if too many attempts
        $maxAttempts = 5;
        $blockTime = 30; // seconds

        if (
            $_SESSION['login_attempts']['count'] >= $maxAttempts &&
            time() - $_SESSION['login_attempts']['last_attempt'] < $blockTime
        ) {
            sleep($blockTime);
            return $this->render("login", [
                "messages" => ["Too many login attempts. Please try again later."]
            ]);
        }

        // Get data sent from the login form
        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? '';

        // Check if fields are not empty
        if ($email === '' || $password === '') {
            return $this->render("login", [
                "messages" => ["Fields cannot be empty"]
            ]);
        }

        // Fetch the user by email
        $user = $this->userRepository->getUserByEmail($email);

        // Show error if the user does not exist or the password is incorrect
        if (!$user || !password_verify($password, $user["password"])) {
            $_SESSION['login_attempts']['count']++;
            $_SESSION['login_attempts']['last_attempt'] = time();

            return $this->render("login", [
                "messages" => ["Invalid credentials"]
            ]);
        }

        // Reset login attempts after successful login
        $_SESSION['login_attempts'] = [
            'count' => 0,
            'last_attempt' => null
        ];

        // New session identificator
        session_regenerate_id(true);

        // If credentials are correct, save user info to session
        $_SESSION['user'] = [
            'id'    => $user['user_id'] ?? null,
            'name'  => $user['user_name'] ?? null,
            'email' => $user['email'] ?? null,
            'role'  => $user['role'] ?? null,
        ];

        // Redirect to dashboard (no re-submission on refresh)
        $this->redirect('dashboard');
    }

    public function register()
    {
        // If the request is GET show the registration form
        if ($this->isGet()) {
            return $this->render("register");
        }

        // Get data sent from the registration form
        $name = $_POST["name"] ?? '';
        $email = $_POST["email"] ?? '';
        $password1 = $_POST["password1"] ?? '';
        $password2 = $_POST["password2"] ?? '';

        // Check if fields are not empty
        if ($email === '' || $password1 === '' || $password2 === '') {
            return $this->render("register", [
                "messages" => ["Fields cannot be empty"]
            ]);
        }

        // E-mail format validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render("register", [
                "messages" => ["Please enter a valid email address"]
            ]);
        }

        // Check if passwords match
        if ($password1 !== $password2) {
            return $this->render("register", [
                "messages" => ["Passwords do not match"]
            ]);
        }

        // Password constraints check
        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_])\S{8,}$/';
        if (!preg_match($pattern, $password1)) {
            return $this->render("register", [
                "messages" => ["Registration failed. Password must be 8+ chars, include upper/lowercase, number, special char, and no spaces"]
            ]);
        }

        // Check if user with this email already exists
        $existingUser = $this->userRepository->getUserByEmail($email);
        if ($existingUser) {
            return $this->render("register", [
                "messages" => ["Registration failed. Password must be 8+ chars, include upper/lowercase, number, special char, and no spaces"]
            ]);
        }

        // Hash the password
        $hashedPassword = password_hash($password1, PASSWORD_BCRYPT);

        // Insert the new user into the database
        $this->userRepository->createUser($name, $email, $hashedPassword);

        // Redirect user to login view with a message
        return $this->render("login", [
            "success" => ["User has been registered successfully"]
        ]);
    }

    public function logout()
    {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Unset all session variables
        session_unset();

        // Destroy the session
        session_destroy();

        // Delete session cookie (important for full logout)
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        $this->redirect('login');
    }
}