<?php

class EmployeesModel {
    private PDO $conn;
    private string $table = "employees";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function getTotalCount(): int {
        $query = "SELECT COUNT(*) AS total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) ($result['total'] ?? 0);
    }

    // Get all employees
    public function getAll(): array {
        $query = "SELECT e.*, d.id AS department_id, d.department_name AS department_name, COALESCE(d.is_probation, 0) AS department_is_probation FROM " . $this->table . " e "
               . "LEFT JOIN departments d ON e.department_id = d.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get all employees with total leave count and optional search filters
    public function getAllWithTotalLeaves(string $name = '', string $department = ''): array {
         $query = "SELECT e.*, COALESCE(l.total_leaves, 0) AS total_leaves, d.id AS department_id, d.department_name AS department_name, COALESCE(d.is_probation, 0) AS department_is_probation "
             . "FROM " . $this->table . " e "
             . "LEFT JOIN ("
             . "SELECT employee_id, COUNT(*) AS total_leaves "
             . "FROM leaves "
             . "GROUP BY employee_id"
             . ") l ON e.id = l.employee_id "
             . "LEFT JOIN departments d ON e.department_id = d.id";

        $conditions = [];
        $params = [];

        if ($name !== '') {
            $conditions[] = "LOWER(e.employee_name) LIKE :name";
            $params[':name'] = '%' . strtolower($name) . '%';
        }

        if ($department !== '') {
            $conditions[] = "LOWER(d.department_name) LIKE :department";
            $params[':department'] = '%' . strtolower($department) . '%';
        }

        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $query .= ' ORDER BY e.id';

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function hasLeaveRecords(int $employeeId): bool {
        $query = "SELECT 1 FROM leaves WHERE employee_id = :employee_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':employee_id' => $employeeId]);
        return (bool) $stmt->fetch();
    }

    public function getById(int $id): ?array {
        $query = "SELECT e.*, d.id AS department_id, d.department_name AS department_name, COALESCE(d.is_probation, 0) AS department_is_probation FROM " . $this->table . " e "
               . "LEFT JOIN departments d ON e.department_id = d.id "
               . "WHERE e.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Insert a new employee record (store department_id as FK)
    public function create(string $name, string $email, int $department_id, string $joining_date): bool {
        $query = "INSERT INTO " . $this->table . " (employee_name, email, department_id, joining_date) VALUES (:name, :email, :department_id, :joining_date)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':department_id' => $department_id,
            ':joining_date' => $joining_date
        ]);
    }

    // Update existing employee record (update department_id)
    public function update(int $id, string $name, string $email, int $department_id, string $joining_date): bool {
        $query = "UPDATE " . $this->table . " SET employee_name = :name, email = :email, department_id = :department_id, joining_date = :joining_date WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
            ':department_id' => $department_id,
            ':joining_date' => $joining_date
        ]);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email";
        $params = [':email' => $email];

        if ($excludeId !== null) {
            $query .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return (bool) $stmt->fetch();
    }

    public function delete(int $id): bool {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}
?>