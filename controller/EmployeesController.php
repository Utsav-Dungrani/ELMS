<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../model/EmployeesModel.php';
require_once __DIR__ . '/../model/DepartmentModel.php';

class EmployeesController {
    private \EmployeesModel $employeeModel;
    private PDO $db;

    public function __construct() {
        $database = new \Database();
        $db = $database->getConnection();
        $this->db = $db;
        $this->employeeModel = new \EmployeesModel($db);
    }

    private function validateEmployeeData(array $data, ?array $validDepartmentIds = null, ?int $excludeId = null): array {
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $departmentId = (int) ($data['department_id'] ?? 0);
        $joiningDate = trim($data['joining_date'] ?? '');
        $errors = [];

        if ($name === '') {
            $errors[] = 'Name is required.';
        } elseif (strlen($name) > 100) {
            $errors[] = 'Name must not exceed 100 characters.';
        }

        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, \FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        } elseif ($this->employeeModel->emailExists($email, $excludeId)) {
            $errors[] = 'This email is already used by another employee.';
        }

        if ($departmentId <= 0) {
            $errors[] = 'Please select a department.';
        } elseif (!empty($validDepartmentIds) && !in_array($departmentId, $validDepartmentIds, true)) {
            $errors[] = 'Please select a valid department.';
        }

        if ($joiningDate === '') {
            $errors[] = 'Joining date is required.';
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $joiningDate);
            if (!$date || $date->format('Y-m-d') !== $joiningDate) {
                $errors[] = 'Please enter a valid joining date in YYYY-MM-DD format.';
            } else {
                $today = new DateTime('today');
                if ($date > $today) {
                    $errors[] = 'Joining date cannot be a future date.';
                }
            }
        }

        return [
            'errors' => $errors,
            'data' => [
                'name' => $name,
                'email' => $email,
                'department_id' => $departmentId,
                'joining_date' => $joiningDate,
            ]
        ];
    }

    public function index(): void {
        $searchName = trim($_GET['name'] ?? '');
        $searchDepartment = trim($_GET['department'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 10;
        $departmentModel = new \DepartmentModel($this->db);
        $departments = $departmentModel->getAll();
        $result = $this->employeeModel->getAllWithTotalLeaves($searchName, $searchDepartment, $page, $limit);
        $employees = $result['data'];
        $totalEmployees = $result['total'];
        $totalPages = max(1, (int) ceil($totalEmployees / $limit));

        require_once __DIR__ . '/../views/employees/index.php';
    }

    public function create(): void {
        $error = '';
        $old = [
            'name' => '',
            'email' => '',
            'department_id' => '',
            'joining_date' => ''
        ];

        $departmentModel = new \DepartmentModel($this->db);
        $departments = $departmentModel->getAll();
        $validDepartmentIds = array_map('intval', array_column($departments, 'id'));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'department_id' => (int) ($_POST['department_id'] ?? 0),
                'joining_date' => trim($_POST['joining_date'] ?? '')
            ];

            $validation = $this->validateEmployeeData($old, $validDepartmentIds);
            $old = $validation['data'];

            if (!empty($validation['errors'])) {
                $error = implode(' ', $validation['errors']);
            } else {
                $this->employeeModel->create($old['name'], $old['email'], $old['department_id'], $old['joining_date']);
                header('Location: ' . buildUrl('employees'));
                exit;
            }
        }

        $stmt = $departments; // keep view compatible if it expects $stmt
        require_once __DIR__ . '/../views/employees/create.php';
    }

    public function edit(int $id): void {
        if ($id <= 0) {
            header('Location: ' . buildUrl('employees'));
            exit;
        }

        $error = '';
        $employee = $this->employeeModel->getById($id);

        $departmentModel = new \DepartmentModel($this->db);
        $departments = $departmentModel->getAll();
        $validDepartmentIds = array_map('intval', array_column($departments, 'id'));

        if (!$employee) {
            header('Location: ' . buildUrl('employees'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $department = (int) ($_POST['department_id'] ?? 0);
            $joining_date = trim($_POST['joining_date'] ?? '');

            $validation = $this->validateEmployeeData([
                'name' => $name,
                'email' => $email,
                'department_id' => $department,
                'joining_date' => $joining_date
            ], $validDepartmentIds, $id);

            if (!empty($validation['errors'])) {
                $error = implode(' ', $validation['errors']);
            } else {
                $this->employeeModel->update($id, $validation['data']['name'], $validation['data']['email'], $validation['data']['department_id'], $validation['data']['joining_date']);
                header('Location: ' . buildUrl('employees'));
                exit;
            }

            $employee['employee_name'] = $validation['data']['name'];
            $employee['email'] = $validation['data']['email'];
            $employee['department_id'] = $validation['data']['department_id'];
            $employee['joining_date'] = $validation['data']['joining_date'];
        }

        $stmt = $departments; // compatibility for views that expect $stmt
        require_once __DIR__ . '/../views/employees/edit.php';
    }

    public function delete(int $id): void {
        if ($id <= 0) {
            header('Location: ' . buildUrl('employees'));
            exit;
        }

        if ($this->employeeModel->hasLeaveRecords($id)) {
            $_SESSION['error_message'] = 'Cannot delete employee with leave records.';
            header('Location: ' . buildUrl('employees'));
            exit;
        }

        $this->employeeModel->delete($id);
        header('Location: ' . buildUrl('employees'));
        exit;
    }
}