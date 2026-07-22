<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper loader for Models and Controllers
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/model/EmployeesModel.php';
require_once __DIR__ . '/model/LeavesModel.php';
require_once __DIR__ . '/controller/AuthController.php';
require_once __DIR__ . '/controller/EmployeesController.php';
require_once __DIR__ . '/controller/LeavesController.php';
require_once __DIR__ . '/controller/DashboardController.php';
require_once __DIR__ . '/controller/EmployeePortalController.php';
require_once __DIR__ . '/routes/router.php';

$route = getCurrentRoute();

if ($route === 'dashboard') {
    $route = 'dashboard';
}

// Simple authentication check
$publicRoutes = ['login', 'do-login', 'employee-login', 'employee-do-login'];
$adminRoutes = ['dashboard', 'employees', 'employees-create', 'employees-edit', 'employees-delete', 'leaves', 'leaves-create', 'leaves-approve', 'leaves-reject'];
$employeeRoutes = ['employee-dashboard', 'employee-leaves', 'employee-leaves-create'];
$hasAdminSession = !empty($_SESSION['user_logged_in']);
$hasEmployeeSession = !empty($_SESSION['employee_logged_in']);

if (!$hasAdminSession && !$hasEmployeeSession && !in_array($route, $publicRoutes, true)) {
    header('Location: ' . buildUrl('login'));
    exit;
}

if ($hasEmployeeSession && !$hasAdminSession && in_array($route, $adminRoutes, true)) {
    header('Location: ' . buildUrl('employee-dashboard'));
    exit;
}

if ($hasAdminSession && !$hasEmployeeSession && in_array($route, $employeeRoutes, true)) {
    header('Location: ' . buildUrl('dashboard'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
}

switch ($route) {
    // --- DASHBOARD ---
    case 'dashboard':
        (new \DashboardController())->index();
        break;

    // --- AUTHENTICATION ---
    case 'login':
        $authController = new \AuthController((new \Database())->getConnection());
        $authController->showLogin();
        break;
        
    case 'do-login':
        $authController = new \AuthController((new \Database())->getConnection());
        $authController->login();
        break;

    case 'employee-login':
        $authController = new \AuthController((new \Database())->getConnection());
        $authController->showEmployeeLogin();
        break;

    case 'employee-do-login':
        $authController = new \AuthController((new \Database())->getConnection());
        $authController->employeeLogin();
        break;

    case 'employee-dashboard':
        (new \EmployeePortalController())->dashboard();
        break;

    case 'employee-leaves':
        (new \EmployeePortalController())->leaves();
        break;

    case 'employee-leaves-create':
        (new \EmployeePortalController())->createLeave();
        break;

    case 'logout':
        $authController = new \AuthController((new \Database())->getConnection());
        $authController->logout();
        break;

    // --- EMPLOYEES ---
    case 'employees':
        (new \EmployeesController())->index();
        break;
    case 'employees-create':
        (new \EmployeesController())->create();
        break;
    case 'employees-edit':
        (new \EmployeesController())->edit($_GET['id'] ?? 0);
        break;
    case 'employees-delete':
        (new \EmployeesController())->delete((int)($_POST['id'] ?? 0));
        break;
    
    case 'employee-summary':
        (new \EmployeesController())->summary((int)($_GET['id'] ?? 0));
        break;

    // --- LEAVES ---
    case 'leaves':
        (new \LeavesController())->index();
        break;
    case 'leaves-create':
        (new \LeavesController())->create();
        break;
    case 'leaves-approve':
        (new \LeavesController())->updateStatus((int)($_POST['id'] ?? 0), 'Approved');
        break;
    case 'leaves-reject':
        $controller = new \LeavesController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->reject();
        } else {
            $controller->showRejectForm((int) ($_GET['id'] ?? 0));
        }
        break;

    default:
        http_response_code(404);
        $authController = new \AuthController((new \Database())->getConnection());
        $authController->pagenotfound();
        break;
}