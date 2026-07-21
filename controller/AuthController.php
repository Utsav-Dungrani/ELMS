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
        $password = $_POST['password'] ?? '';

        $admin = $this->adminModel->getByName($username);
        if ($admin && $this->adminModel->verifyPassword($password, $admin['password_hash'])) {
            $_SESSION['user_logged_in'] = true;
            header("Location: index.php?route=dashboard");
            exit;
        }

        header("Location: index.php?route=login&error=1");
        exit;
    }

    public function logout(): void {
        session_destroy();
        header("Location: index.php?route=login");
        exit;
    }

    public function pagenotfound(): void {
        http_response_code(404);
        require_once __DIR__ . '/../views/layout/404-notfound.php';
        exit;
    }
}
?>
