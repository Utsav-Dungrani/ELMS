<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/EmployeesModel.php';
require_once __DIR__ . '/../model/LeavesModel.php';

class DashboardController {
    private \EmployeesModel $employeeModel;
    private \LeavesModel $leaveModel;

    public function __construct() {
        $database = new \Database();
        $db = $database->getConnection();
        $this->employeeModel = new \EmployeesModel($db);
        $this->leaveModel = new \LeavesModel($db);
    }

    public function index(): void {
        // 1. Get current page (default: 1)
        $page = max(1, (int) ($_POST['page'] ?? $_GET['page'] ?? 1));
        $limit = 10; // Change to a smaller number (e.g., 2) to test pagination if you have few employees

        // 2. Fetch Employee Leave Summary Data
        $employeeLeaveSummary = [];
        $totalSummaryPages = 1;

        if (method_exists($this->leaveModel, 'getEmployeeLeaveSummary')) {
            $summaryResult = $this->leaveModel->getEmployeeLeaveSummary($page, $limit);
            
            $employeeLeaveSummary = $summaryResult['data'] ?? [];
            
            // Extract total records count from model result
            $totalRecords = $summaryResult['total'] ?? $summaryResult['totalRecords'] ?? count($employeeLeaveSummary);
            
            // Calculate total pages dynamically
            $totalSummaryPages = (int) ceil($totalRecords / $limit);
        }

        // 3. Overall Stats Calculation
        $stats = [
            'total_employees' => method_exists($this->employeeModel, 'getTotalCount') ? $this->employeeModel->getTotalCount() : 0,
            'total_leaves'    => 0,
            'approved_leaves' => 0,
            'pending_leaves'  => 0,
        ];

        // 4. Handle AJAX request
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
                || ($_SERVER['REQUEST_METHOD'] === 'POST');

        if ($isAjax) {
            require_once __DIR__ . '/../views/dashboard/_employee_summary_table.php';
            exit;
        }

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}