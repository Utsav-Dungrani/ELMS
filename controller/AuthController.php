<?php

require_once __DIR__ . '/../model/AdminModel.php';
require_once __DIR__ . '/../model/EmployeesModel.php';

class AuthController {
    private \AdminModel $adminModel;
    private \EmployeesModel $employeeModel;

    public function __construct(PDO $db) {
        $this->adminModel = new \AdminModel($db);
        $this->employeeModel = new \EmployeesModel($db);
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
            unset($_SESSION['employee_logged_in'], $_SESSION['employee_id']);
            header('Location: ' . buildUrl('dashboard'));
            exit;
        }

        header('Location: ' . buildUrl('login', ['error' => 1]));
        exit;
    }

    public function showEmployeeLogin(): void {
        require_once __DIR__ . '/../views/employee/login.php';
    }

    public function employeeLogin(): void {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            header('Location: ' . buildUrl('employee-login', ['error' => 1]));
            exit;
        }

        $employee = $this->employeeModel->getByEmail($email);
        if ($employee && $this->verifyEmployeePassword($password, $employee)) {
            $_SESSION['employee_logged_in'] = true;
            $_SESSION['employee_id'] = (int) $employee['id'];
            unset($_SESSION['user_logged_in']);
            header('Location: ' . buildUrl('employee-dashboard'));
            exit;
        }

        header('Location: ' . buildUrl('employee-login', ['error' => 1]));
        exit;
    }

    public function logout(): void {
        session_destroy();
        header('Location: ' . buildUrl('login'));
        exit;
    }

    private function verifyEmployeePassword(string $plainPassword, array $employee): bool {
        $storedPassword = $employee['password'] ?? '';
        return $storedPassword !== '' && password_verify($plainPassword, $storedPassword);
    }

    public function pagenotfound(): void {
        http_response_code(404);
        require_once __DIR__ . '/../views/layout/404-notfound.php';
        exit;
    }
}
?>
