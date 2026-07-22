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

                if ($end <= $start) {
                    $errors[] = 'End date must be greater than start date.';
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
        $searchEmployeeName = trim($_GET['employee_name'] ?? '');
        $searchStatus = trim($_GET['status'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $result = $this->leaveModel->getAllWithEmployeeDetails($searchEmployeeName, $searchStatus, $page, $limit);
        $leaves = $result['data'];
        $totalLeaves = $result['total'];
        $totalPages = max(1, (int) ceil($totalLeaves / $limit));

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
            $leaveType = trim($_POST['leave_type'] ?? '');
            $leaveTypeMap = [
                'S' => 'Sick',
                'C' => 'Casual',
                'P' => 'Paid',
                'Sick' => 'Sick',
                'Casual' => 'Casual',
                'Paid' => 'Paid'
            ];
            $old = [
                'employee_id' => (int) ($_POST['employee_id'] ?? 0),
                'leave_type' => $leaveTypeMap[$leaveType] ?? '',
                'start_date' => trim($_POST['start_date'] ?? ''),
                'end_date' => trim($_POST['end_date'] ?? ''),
                'reason' => trim($_POST['reason'] ?? '')
            ];

            $validation = $this->validateLeaveData($old);
            $old = $validation['data'];

            if (!empty($validation['errors'])) {
                $error = implode(' ', $validation['errors']);
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
                    header('Location: ' . buildUrl('leaves'));
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
            header('Location: ' . buildUrl('leaves'));
            exit;
        }

        $this->leaveModel->updateStatus($id, $status);
        header('Location: ' . buildUrl('leaves'));
        exit;
    }
}