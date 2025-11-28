<?php

class AppController {

    // Check the HTTP request methods
    protected function isGet(): bool
    {
        return $_SERVER["REQUEST_METHOD"] === 'GET';
    }

    protected function isPost(): bool
    {
        return $_SERVER["REQUEST_METHOD"] === 'POST';
    }

    // Simple redirect helper
    protected function redirect(string $path): void
    {
        header("Location: /" . ltrim($path, '/'));
        exit();
    }

    // Is user logged in?
    protected function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }

    // Get current user data from session
    protected function getUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    // Require login for protected pages
    protected function requireLogin(): void
    {
        if (!$this->isAuthenticated()) {
            $this->redirect('login');
        }
    }

    // Render a given HTML template and pass variables to it
    protected function render(string $template = null, array $variables = [])
    {
        $templatePath = 'public/views/' . $template . '.html';
        $templatePath404 = 'public/views/404.html';

        $output = "";

        // If the requested template exists, render it
        if (file_exists($templatePath)) {
            // Convert array keys to variables available inside the template
            extract($variables);

            // Start output buffering to capture template output
            ob_start();

            // Load the template
            include $templatePath;

            // Get buffered content and clean the buffer
            $output = ob_get_clean();

        } else {
            // If template does not exist, show 404 page
            ob_start();
            include $templatePath404;
            $output = ob_get_clean();
        }

        echo $output;
    }
}