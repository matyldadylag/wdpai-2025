<?php

require_once 'AppController.php';
require_once __DIR__ . '/../repository/UserRepository.php';

class AdminUsersController extends AppController
{
    private UserRepository $userRepository;

    // Create new repository instance
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    // Render admin users list
    public function index()
    {
        // Require admin role for this page
        $this->requireAdmin();

        // Fetch all users from database
        $users = $this->userRepository->getAllUsers();

        // Render view
        return $this->render('admin-users', [
            'users' => $users,
            'user'  => $this->getUser(),
        ]);
    }

    // Handle delete user request
    public function delete()
    {
        // Require admin role for this action
        $this->requireAdmin();

        // Allow only POST to delete
        if (!$this->isPost()) {
            $this->redirect('admin/users');
        }

        // Read user id from POST
        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            $this->redirect('admin/users');
        }

        // Prevent admin from deleting their own account (simple safety)
        $currentUser = $this->getUser();
        $currentUserId = (int)($currentUser['id'] ?? 0);

        if ($currentUserId === $userId) {
            $this->redirect('admin/users');
        }

        // Delete user and redirect back
        $this->userRepository->deleteUserById($userId);

        $this->redirect('admin/users');
    }
}