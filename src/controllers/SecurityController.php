<?php

require_once 'AppController.php';
require_once __DIR__.'/../repository/UserRepository.php';

class SecurityController extends AppController {
    private $userRepository;

    public function __construct() {
        // Przygotowanie repozytorium dla metod kontrolera
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

        // Create the repository and fetch the user by email
        $user = $this->userRepository->getUserByEmail($email);

        // Show error if the user does not exist or the password is incorrect
        if (!$user || !password_verify($password, $user["password"])) {
            return $this->render("login", [
                "messages" => ["Invalid credentials"]
            ]);
        }

        // If credentials are correct save user info to session
        // TODO User sessions

        // Render the dashboard
        return $this->render("dashboard");
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
}