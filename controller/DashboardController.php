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
        $totalEmployees = $this->employeeModel->getTotalCount();
        $leaveStats = $this->leaveModel->getDashboardStats();

        // Pass stats to view
        $stats = [
            'total_employees' => $totalEmployees,
            'total_leaves'    => $leaveStats['total_requests'],
            'approved_leaves' => $leaveStats['total_approved'],
            'pending_leaves'  => $leaveStats['total_pending'],
            'rejected_leaves'  => $leaveStats['total_rejected']
        ];

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}