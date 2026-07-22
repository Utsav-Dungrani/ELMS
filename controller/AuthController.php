<?php

require_once __DIR__ . '/../model/AdminModel.php';

class AuthController {
    private \AdminModel $adminModel;

    public function __construct(PDO $db) {
        $this->adminModel = new \AdminModel($db);
    }

    public function showLogin(): void {
        require_once __DIR__ . '/../views/login.php';
    }

    public function login(): void {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            header('Location: ' . buildUrl('login', ['error' => 1]));
            exit;
        }

        $admin = $this->adminModel->getByName($username);
        if ($admin && $this->adminModel->verifyPassword($password, $admin['password_hash'])) {
            $_SESSION['user_logged_in'] = true;
            header('Location: ' . buildUrl('dashboard'));
            exit;
        }

        header('Location: ' . buildUrl('login', ['error' => 1]));
        exit;
    }

    public function logout(): void {
        session_destroy();
        header('Location: ' . buildUrl('login'));
        exit;
    }

    public function pagenotfound(): void {
        http_response_code(404);
        require_once __DIR__ . '/../views/layout/404-notfound.php';
        exit;
    }
}
?>
