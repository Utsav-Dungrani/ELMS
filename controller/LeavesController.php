<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/LeavesModel.php';
require_once __DIR__ . '/../model/EmployeesModel.php';

class LeavesController {
    private \LeavesModel $leaveModel;
    private \EmployeesModel $employeeModel;

    public function __construct() {
        $database = new \Database();
        $db = $database->getConnection();
        $this->leaveModel = new \LeavesModel($db);
        $this->employeeModel = new \EmployeesModel($db);
    }

    // Displays list of all leaves
    public function index(): void {
        $searchEmployeeName = trim($_GET['employee_name'] ?? '');
        $searchStatus = trim($_GET['status'] ?? '');
        $leaves = $this->leaveModel->getAllWithEmployeeDetails($searchEmployeeName, $searchStatus);

        require_once __DIR__ . '/../views/leaves/index.php';
    }

    public function create(): void {
        $employees = $this->employeeModel->getAll();
        $error = '';
        $old = [
            'employee_id' => '',
            'leave_type' => '',
            'start_date' => '',
            'end_date' => '',
            'reason' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['employee_id'] = (int)($_POST['employee_id'] ?? 0);
            $leaveType = trim($_POST['leave_type'] ?? '');
            $leaveTypeMap = [
                'S' => 'Sick',
                'C' => 'Casual',
                'P' => 'Paid',
                'Sick' => 'Sick',
                'Casual' => 'Casual',
                'Paid' => 'Paid'
            ];
            $old['leave_type'] = $leaveTypeMap[$leaveType] ?? '';
            $old['start_date'] = trim($_POST['start_date'] ?? '');
            $old['end_date'] = trim($_POST['end_date'] ?? '');
            $old['reason'] = trim($_POST['reason'] ?? '');

            if ($old['employee_id'] <= 0) {
                $error = 'Please select an employee.';
            } elseif ($old['leave_type'] === '') {
                $error = 'Please select a leave type.';
            } elseif ($old['start_date'] === '' || $old['end_date'] === '') {
                $error = 'Start date and end date are required.';
            } elseif ($old['start_date'] > $old['end_date']) {
                $error = 'End date must be on or after start date.';
            } elseif ($this->leaveModel->hasOverlappingLeave($old['employee_id'], $old['start_date'], $old['end_date'])) {
                $error = 'The selected dates overlap with an existing leave for this employee.';
            } else {
                $employee = $this->employeeModel->getById($old['employee_id']);
                if (!$employee) {
                    $error = 'Selected employee does not exist.';
                } else {
                    $isProbationDepartment = (int) ($employee['department_is_probation'] ?? 0) === 1;

                    if ($isProbationDepartment && $old['leave_type'] === 'Paid') {
                        $error = 'Employees in Probation departments cannot request Paid leave.';
                    } else {
                        $startYear = (int) date('Y', strtotime($old['start_date']));
                        $endYear = (int) date('Y', strtotime($old['end_date']));
                        for ($year = $startYear; $year <= $endYear; $year++) {
                            $requestedDays = $this->getRequestedDaysInYear($old['start_date'], $old['end_date'], $year);
                            $approvedDays = $this->leaveModel->getApprovedLeaveDaysInYear($old['employee_id'], $year);
                            if ($approvedDays + $requestedDays > 20) {
                                $error = "This leave would exceed the 20 approved-day limit for calendar year {$year}.";
                                break;
                            }
                        }
                    }
                }
            }

            if ($error === '') {
                if ($this->leaveModel->create($old['employee_id'], $old['leave_type'], $old['start_date'], $old['end_date'], $old['reason'])) {
                    header("Location: index.php?route=leaves");
                    exit;
                }
                $error = 'Failed to submit leave request.';
            }
        }

        require_once __DIR__ . '/../views/leaves/create.php';
    }


    private function getRequestedDaysInYear(string $startDate, string $endDate, int $year): int {
        $yearStart = sprintf('%04d-01-01', $year);
        $yearEnd = sprintf('%04d-12-31', $year);

        $start = max($startDate, $yearStart);
        $end = min($endDate, $yearEnd);

        if ($start > $end) {
            return 0;
        }

        return (int) ((strtotime($end) - strtotime($start)) / 86400) + 1;
    }

    public function updateStatus(int $id, string $status): void {
        if ($id <= 0 || !in_array($status, ['Approved', 'Rejected'])) {
            header("Location: index.php?route=leaves");
            exit;
        }

        $this->leaveModel->updateStatus($id, $status);
        header("Location: index.php?route=leaves");
        exit;
    }
}