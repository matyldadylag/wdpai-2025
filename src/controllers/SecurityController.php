<?php

require_once 'AppController.php';

class SecurityController extends AppController {
    // TODO zdobywać dane z bazy danych
    // Tymczasowa lokalna baza użytkowników
    private static array $users = [
        [
            'email' => 'anna@example.com',
            'password' => '$2y$10$wz2g9JrHYcF8bLGBbDkEXuJQAnl4uO9RV6cWJKcf.6uAEkhFZpU0i', // test123
            'first_name' => 'Anna'
        ],
        [
            'email' => 'bartek@example.com',
            'password' => '$2y$10$fK9rLobZK2C6rJq6B/9I6u6Udaez9CaRu7eC/0zT3pGq5piVDsElW', // haslo456
            'first_name' => 'Bartek'
        ],
        [
            'email' => 'celina@example.com',
            'password' => '$2y$10$Cq1J6YMGzRKR6XzTb3fDF.6sC6CShm8kFgEv7jJdtyWkhC1GuazJa', // qwerty
            'first_name' => 'Celina'
        ],
    ];

    public function login() {
        // Jeśli żądanie to GET, wyświetla formularz logowania
        if($this->isGet()) {
            return $this->render("login");
        }

        // Jeśli żadanie to POST, pobiera dane z formularza
        $email = $_POST["email"] ?? '';
        $password = $_POST["password"] ?? '';

        // Sprawdza, czy oba pola są wypełnione
        if (empty($email) || empty($password)) {
            return $this->render('login', ['messages' => 'Fill all fields']);
        }

        // TODO wyszukiwanie w bazie danych
        // Wyszukiwanie użytkownika po emailu (w lokalnej tablicy)
        $userRow = null;
        foreach (self::$users as $u) {
            if (strcasecmp($u['email'], $email) === 0) {
                $userRow = $u;
                break;
            }
        }

        if (!$userRow) {
            return $this->render('login', ['messages' => 'User not found']);
        }

        // Weryfikacja hasła
        if (!password_verify($password, $userRow['password'])) {
            return $this->render('login', ['messages' => 'Wrong password']);
        }

        // TODO przechowywanie sesji użytkowika lub token
        // setcookie("username", $userRow['email'], time() + 3600, '/');

        // TODO nie działa
        // Przekierowanie po poprawnym logowaniu
        //$url = "http://$_SERVER[HTTP_HOST]";
        //header("Location: {$url}/dashboard");

        //header('Location: /dashboard', true, 303);
        //exit;
    }

    public function register() {
        // TODO pobranie z formularza email i hasła
        // TODO insert do bazy danych
        // TODO zwrocenie informajci o pomyslnym zarejstrowaniu
        if($this->isGet()) {
            return $this->render("register");
        }
        return $this->render("login", ["message" => "Zarejestrowano uytkownika"]);
    }
}