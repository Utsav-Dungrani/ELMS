<?php
session_start();

// Helper loader for Models and Controllers
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/model/EmployeesModel.php';
require_once __DIR__ . '/model/LeavesModel.php';
require_once __DIR__ . '/controller/AuthController.php';
require_once __DIR__ . '/controller/EmployeesController.php';
require_once __DIR__ . '/controller/LeavesController.php';
require_once __DIR__ . '/controller/DashboardController.php';
require_once __DIR__ . '/routes/router.php';

$route = getCurrentRoute();

if ($route === 'dashboard') {
    $route = 'dashboard';
}

// Simple authentication check
$publicRoutes = ['login', 'do-login'];
if (!isset($_SESSION['user_logged_in']) && !in_array($route, $publicRoutes)) {
    header('Location: ' . buildUrl('login'));
    exit;
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
        (new \EmployeesController())->delete($_GET['id'] ?? 0);
        break;

    // --- LEAVES ---
    case 'leaves':
        (new \LeavesController())->index();
        break;
    case 'leaves-create':
        (new \LeavesController())->create();
        break;
    case 'leaves-approve':
        (new \LeavesController())->updateStatus($_GET['id'] ?? 0, 'Approved');
        break;
    case 'leaves-reject':
        (new \LeavesController())->updateStatus($_GET['id'] ?? 0, 'Rejected');
        break;

    default:
        http_response_code(404);
        $authController = new \AuthController((new \Database())->getConnection());
        $authController->pagenotfound();
        break;
}