<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';

class SecurityController extends AppController {
    private $userRepository;

    // Create new repository instance
    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function login()
    {
        // If the request is GET show the login form
        if ($this->isGet()) {
            return $this->render("login");
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
            return $this->render("login", [
                "messages" => ["Invalid credentials"]
            ]);
        }

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

        // Check if passwords match
        if ($password1 !== $password2) {
            return $this->render("register", [
                "messages" => ["Passwords do not match"]
            ]);
        }

        // Password length check
        if (strlen($password1) < 8) {
            return $this->render("register", [
                "messages" => ["Password must be at least 8 characters long"]
            ]);
        }

        // Check if user with this email already exists
        $existingUser = $this->userRepository->getUserByEmail($email);
        if ($existingUser) {
            return $this->render("register", [
                "messages" => ["User with this email already exists"]
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
        // Clear session data
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // Start a fresh session to avoid errors on pages expecting $_SESSION
        session_start();

        $this->redirect('login');
    }
}