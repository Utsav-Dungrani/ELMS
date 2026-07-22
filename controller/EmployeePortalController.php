<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/EmployeesModel.php';
require_once __DIR__ . '/../model/LeavesModel.php';

class EmployeePortalController {
    private \EmployeesModel $employeeModel;
    private \LeavesModel $leaveModel;

    public function __construct() {
        $database = new \Database();
        $db = $database->getConnection();
        $this->employeeModel = new \EmployeesModel($db);
        $this->leaveModel = new \LeavesModel($db);
    }

    private function requireEmployeeSession(): void {
        if (empty($_SESSION['employee_logged_in']) || empty($_SESSION['employee_id'])) {
            header('Location: ' . buildUrl('employee-login'));
            exit;
        }
    }

    private function validateLeaveData(array $data): array {
        $leaveType = trim($data['leave_type'] ?? '');
        $startDate = trim($data['start_date'] ?? '');
        $endDate = trim($data['end_date'] ?? '');
        $reason = trim($data['reason'] ?? '');
        $errors = [];

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

        if ($reason === '') {
            $errors[] = 'Please provide a reason for the leave.';
        } elseif (strlen($reason) > 500) {
            $errors[] = 'Reason must not exceed 500 characters.';
        }

        return [
            'errors' => $errors,
            'data' => [
                'leave_type' => $leaveType,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'reason' => $reason,
            ],
        ];
    }

    private function getRequestedDaysInYear(string $startDate, string $endDate, int $year): int
    {
        $yearStart = sprintf('%04d-01-01', $year);
        $yearEnd = sprintf('%04d-12-31', $year);

        $start = max($startDate, $yearStart);
        $end = min($endDate, $yearEnd);

        if ($start > $end) {
            return 0;
        }

        return (int) ((strtotime($end) - strtotime($start)) / 86400) + 1;
    }

    public function dashboard(): void {
        $this->requireEmployeeSession();

        $employeeId = (int) $_SESSION['employee_id'];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $employee = $this->employeeModel->getById($employeeId);
        $leaveResult = $this->leaveModel->getLeavesForEmployee($employeeId, $page, 10);
        $employeeLeaves = $leaveResult['data'];
        $pagination = [
            'page' => $leaveResult['page'],
            'totalPages' => $leaveResult['totalPages'],
            'limit' => $leaveResult['limit'],
        ];

        $stats = [
            'approved' => 0,
            'pending' => 0,
            'rejected' => 0,
            'total' => count($employeeLeaves),
        ];

        foreach ($employeeLeaves as $leave) {
            $status = $leave['status'] ?? '';
            if ($status === 'Approved') {
                $stats['approved']++;
            } elseif ($status === 'Rejected') {
                $stats['rejected']++;
            } else {
                $stats['pending']++;
            }
        }

        require_once __DIR__ . '/../views/employee-dashboard/dashboard.php';
    }

    public function leaves(): void {
        $this->requireEmployeeSession();

        $employeeId = (int) $_SESSION['employee_id'];
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $employee = $this->employeeModel->getById($employeeId);
        $leaveResult = $this->leaveModel->getLeavesForEmployee($employeeId, $page, 10);
        $employeeLeaves = $leaveResult['data'];
        $pagination = [
            'page' => $leaveResult['page'],
            'totalPages' => $leaveResult['totalPages'],
            'limit' => $leaveResult['limit'],
        ];

        require_once __DIR__ . '/../views/employee-dashboard/leaves.php';
    }

    public function createLeave(): void {
        $this->requireEmployeeSession();

        $employeeId = (int) $_SESSION['employee_id'];
        $employee = $this->employeeModel->getById($employeeId);
        $error = '';
        $old = [
            'leave_type' => '',
            'start_date' => '',
            'end_date' => '',
            'reason' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $leaveType = trim($_POST['leave_type'] ?? '');
            $leaveTypeMap = [
                'S' => 'Sick',
                'C' => 'Casual',
                'P' => 'Paid',
                'Sick' => 'Sick',
                'Casual' => 'Casual',
                'Paid' => 'Paid',
            ];
            $old = [
                'leave_type' => $leaveTypeMap[$leaveType] ?? '',
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'reason' => trim($_POST['reason'] ?? ''),
            ];

            $validation = $this->validateLeaveData($old);
            $old = $validation['data'];

            if (!empty($validation['errors'])) {
                $error = implode(' ', $validation['errors']);
            } elseif ($this->leaveModel->hasOverlappingLeave($employeeId, $old['start_date'], $old['end_date'])) {
                $error = 'The selected dates overlap with an existing leave request.';
            } else {
                $isProbationDepartment = (int) ($employee['department_is_probation'] ?? 0) === 1;
                if ($isProbationDepartment && $old['leave_type'] === 'Paid') {
                    $error = 'Employees in probation departments cannot request Paid leave.';
                } else {

                    $startYear = (int) date('Y', strtotime($old['start_date']));
                    $endYear = (int) date('Y', strtotime($old['end_date']));

                    for ($year = $startYear; $year <= $endYear; $year++) {

                        $requestedDays = $this->getRequestedDaysInYear(
                            $old['start_date'],
                            $old['end_date'],
                            $year
                        );

                        $approvedDays = $this->leaveModel->getApprovedLeaveDaysInYear(
                            $employeeId,
                            $year
                        );

                        if ($approvedDays + $requestedDays > 20) {
                            $error = "This leave would exceed the 20 approved-day limit for calendar year {$year}.";
                            break;
                        }
                    }

                    if ($error === '') {
                        if ($this->leaveModel->create(
                            $employeeId,
                            $old['leave_type'],
                            $old['start_date'],
                            $old['end_date'],
                            $old['reason']
                        )) {
                            header('Location: ' . buildUrl('employee-leaves'));
                            exit;
                        }

                        $error = 'Failed to submit leave request.';
                    }
                }
            }
        }

        require_once __DIR__ . '/../views/employee-dashboard/create-leave.php';
    }
}
