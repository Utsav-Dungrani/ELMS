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

    private function validateLeaveData(array $data): array {
        $employeeId = (int) ($data['employee_id'] ?? 0);
        $leaveType = trim($data['leave_type'] ?? '');
        $startDate = trim($data['start_date'] ?? '');
        $endDate = trim($data['end_date'] ?? '');
        $reason = trim($data['reason'] ?? '');
        $errors = [];

        if ($employeeId <= 0) {
            $errors[] = 'Please select an employee.';
        }

        if ($leaveType === '') {
            $errors[] = 'Please select a leave type.';
        } elseif (!in_array($leaveType, ['Sick', 'Casual', 'Paid'], true)) {
            $errors[] = 'Please select a valid leave type.';
        }

        if ($startDate === '' || $endDate === '') {
            $errors[] = 'Start date and end date are required.';
        } else {
            $validDates = true;
            foreach ([$startDate, $endDate] as $dateValue) {
                $date = DateTime::createFromFormat('Y-m-d', $dateValue);
                if (!$date || $date->format('Y-m-d') !== $dateValue) {
                    $errors[] = 'Please enter valid dates in YYYY-MM-DD format.';
                    $validDates = false;
                    break;
                }
            }

            if ($validDates) {
                $today = new DateTime('today');
                $start = new DateTime($startDate);
                $end = new DateTime($endDate);

                if ($start < $today) {
                    $errors[] = 'Start date cannot be in the past.';
                }

                if ($end < $start) {
                    $errors[] = 'End date must be on or after start date.';
                }
            }
        }

        if (trim($reason) === '') {
            $errors[] = 'Please provide a reason for the leave.';
        } elseif (strlen($reason) > 500) {
            $errors[] = 'Reason must not exceed 500 characters.';
        }

        return [
            'errors' => $errors,
            'data' => [
                'employee_id' => $employeeId,
                'leave_type' => $leaveType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $reason,
            ]
        ];
    }

    // Displays list of all leaves
    public function index(): void {
        // Fall back across POST and GET so both initial rendering and AJAX filtering work
        $searchEmployeeName = trim($_POST['employee_name'] ?? $_GET['employee_name'] ?? '');
        $searchStatus = trim($_POST['status'] ?? $_GET['status'] ?? '');
        $page = max(1, (int) ($_POST['page'] ?? $_GET['page'] ?? 1));
        $limit = 10;

        $result = $this->leaveModel->getAllWithEmployeeDetails($searchEmployeeName, $searchStatus, $page, $limit);
        $leaves = $result['data'];
        $totalLeaves = $result['total'];
        $totalPages = max(1, (int) ceil($totalLeaves / $limit));

        // Detect AJAX call
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            // Render only the table & pagination partial
            require_once __DIR__ . '/../views/leaves/_leaves_table.php';
            exit;
        }

        require_once __DIR__ . '/../views/leaves/index.php';
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
        if ($id <= 0 || $status !== 'Approved') {
            header('Location: ' . buildUrl('leaves'));
            exit;
        }

        $leave = $this->leaveModel->getById($id);

        if (!$leave || !in_array($leave['status'] ?? '', ['Pending', 'Rejected'], true)) {
            header('Location: ' . buildUrl('leaves'));
            exit;
        }

        $startYear = (int) date('Y', strtotime($leave['start_date']));
        $endYear = (int) date('Y', strtotime($leave['end_date']));

        for ($year = $startYear; $year <= $endYear; $year++) {
            $requestedDays = $this->getRequestedDaysInYear(
                $leave['start_date'],
                $leave['end_date'],
                $year
            );

            $approvedDays = $this->leaveModel->getApprovedLeaveDaysInYear(
                (int)$leave['employee_id'],
                $year
            );

            if ($approvedDays + $requestedDays > 20) {
                $_SESSION['error_message'] = 'Cannot approve leave. The employee would exceed the 20-day annual leave limit.';
                header('Location: ' . buildUrl('leaves'));
                exit;
            }
        }

        $this->leaveModel->updateStatus($id, 'Approved');

        header('Location: ' . buildUrl('leaves'));
        exit;
    }

    public function reject(): void
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header('Location: ' . buildUrl('leaves'));
            exit;
        }

        $leave = $this->leaveModel->getById($id);

        if (
            !$leave ||
            !in_array($leave['status'], ['Pending', 'Approved'], true)
        ) {
            header('Location: ' . buildUrl('leaves'));
            exit;
        }

        $rejectionReason = trim($_POST['rejection_reason'] ?? '');

        if ($rejectionReason === '') {
            $error = 'Please provide a reason for rejection.';
            require_once __DIR__ . '/../views/leaves/reject.php';
            return;
        }

        if (strlen($rejectionReason) > 500) {
            $error = 'Rejection reason must not exceed 500 characters.';
            require_once __DIR__ . '/../views/leaves/reject.php';
            return;
        }

        if ($this->leaveModel->updateStatus($id, 'Rejected', $rejectionReason)) {
            header('Location: ' . buildUrl('leaves'));
            exit;
        }

        $error = 'Failed to reject leave request.';
        require_once __DIR__ . '/../views/leaves/reject.php';
    }

    public function showRejectForm(int $id): void
    {
        if ($id <= 0) {
            header('Location: ' . buildUrl('leaves'));
            exit;
        }

        $leave = $this->leaveModel->getById($id);

        if (
            !$leave ||
            !in_array($leave['status'], ['Pending', 'Approved'], true)
        ) {
            header('Location: ' . buildUrl('leaves'));
            exit;
        }

        $error = '';
        $rejectionReason = '';

        require_once __DIR__ . '/../views/leaves/reject.php';
    }
}