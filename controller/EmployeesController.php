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

    public function index(): void {
        $searchName = trim($_GET['name'] ?? '');
        $searchDepartment = trim($_GET['department'] ?? '');
        $employees = $this->employeeModel->getAllWithTotalLeaves($searchName, $searchDepartment);

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old['name'] = trim($_POST['name'] ?? '');
            $old['email'] = trim($_POST['email'] ?? '');
            $old['department_id'] = (int) ($_POST['department_id'] ?? 0);
            $old['joining_date'] = trim($_POST['joining_date'] ?? '');

            if (empty($old['name']) || empty($old['email']) || empty($old['department_id']) || empty($old['joining_date'])) {
                $error = 'All fields are required.';
            } elseif ($this->employeeModel->emailExists($old['email'])) {
                $error = 'This email is already used by another employee.';
            } else {
                $this->employeeModel->create($old['name'], $old['email'], $old['department_id'], $old['joining_date']);
                header("Location: index.php?route=employees");
                exit;
            }
        }

        $stmt = $departments; // keep view compatible if it expects $stmt
        require_once __DIR__ . '/../views/employees/create.php';
    }

    public function edit(int $id): void {
        if ($id <= 0) {
            header("Location: index.php?route=employees");
            exit;
        }

        $error = '';
        $employee = $this->employeeModel->getById($id);

        $departmentModel = new \DepartmentModel($this->db);
        $departments = $departmentModel->getAll();

        if (!$employee) {
            header("Location: index.php?route=employees");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $department = (int) ($_POST['department_id'] ?? 0);
            $joining_date = trim($_POST['joining_date'] ?? '');

            if (empty($name) || empty($email) || empty($department) || empty($joining_date)) {
                $error = 'All fields are required.';
            } elseif ($this->employeeModel->emailExists($email, $id)) {
                $error = 'This email is already used by another employee.';
            } else {
                $this->employeeModel->update($id, $name, $email, $department, $joining_date);
                header("Location: index.php?route=employees");
                exit;
            }

            $employee['employee_name'] = $name;
            $employee['email'] = $email;
            $employee['department_id'] = $department;
            $employee['joining_date'] = $joining_date;
        }

        $stmt = $departments; // compatibility for views that expect $stmt
        require_once __DIR__ . '/../views/employees/edit.php';
    }

    public function delete(int $id): void {
        if ($id <= 0) {
            header("Location: index.php?route=employees");
            exit;
        }

        if ($this->employeeModel->hasLeaveRecords($id)) {
            $_SESSION['error_message'] = 'Cannot delete employee with leave records.';
            header("Location: index.php?route=employees");
            exit;
        }

        $this->employeeModel->delete($id);
        header("Location: index.php?route=employees");
        exit;
    }
}