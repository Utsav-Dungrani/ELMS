<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/CrudInterface.php';

class EmployeesModel extends \BaseModel implements \CrudInterface {
    public function __construct(PDO $db) {
        parent::__construct($db, 'employees');
    }

    public function getTotalCount(): int {
        $query = "SELECT COUNT(*) AS total FROM " . $this->getTableName();
        $result = $this->fetchOne($query);
        return (int) ($result['total'] ?? 0);
    }

    // Get all employees
    public function getAll(): array {
        $query = "SELECT e.*, d.department_name AS department_name, COALESCE(d.is_probation, 0) AS department_is_probation FROM " . $this->table . " e "
               . "LEFT JOIN departments d ON e.department_id = d.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get all employees with total leave count and optional search filters
    public function getAllWithTotalLeaves(string $name = '', string $department = '', int $page = 1, int $limit = 10): array {
         $query = "SELECT e.*, COALESCE(l.total_leaves, 0) AS total_leaves, d.department_name AS department_name, COALESCE(d.is_probation, 0) AS department_is_probation "
             . "FROM " . $this->getTableName() . " e "
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

        $countQuery = 'SELECT COUNT(*) AS total FROM (' . $query . ') AS counted';
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute($params);
        $countResult = $countStmt->fetch();
        $total = (int) ($countResult['total'] ?? 0);

        $offset = ($page - 1) * $limit;
        $query .= ' LIMIT :limit OFFSET :offset';

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }

    public function hasLeaveRecords(int $employeeId): bool {
        $query = "SELECT 1 FROM leaves WHERE employee_id = :employee_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':employee_id' => $employeeId]);
        return (bool) $stmt->fetch();
    }

    public function getById(int $id): ?array {
        $query = "SELECT e.*, d.department_name AS department_name, COALESCE(d.is_probation, 0) AS department_is_probation FROM " . $this->getTableName() . " e "
               . "LEFT JOIN departments d ON e.department_id = d.id "
               . "WHERE e.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getByEmail(string $email): ?array {
        $query = "SELECT e.*, d.department_name AS department_name, COALESCE(d.is_probation, 0) AS department_is_probation FROM " . $this->getTableName() . " e "
               . "LEFT JOIN departments d ON e.department_id = d.id "
               . "WHERE e.email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Insert a new employee record (store department_id as FK)
    public function create(...$args): bool {
        if (count($args) < 4) {
            throw new InvalidArgumentException('Expected at least 4 arguments for create().');
        }

        [$name, $email, $department_id, $joining_date, $password] = array_pad($args, 5, '');

        $query = "INSERT INTO " . $this->getTableName() . " (employee_name, email, department_id, joining_date, password) VALUES (:name, :email, :department_id, :joining_date, :password)";
        return $this->execute($query, [
            ':name' => $name,
            ':email' => $email,
            ':department_id' => $department_id,
            ':joining_date' => $joining_date,
            ':password' => $password
        ]);
    }

    // Update existing employee record (update department_id)
    public function update(...$args): bool {
        if (count($args) < 5) {
            throw new InvalidArgumentException('Expected at least 5 arguments for update().');
        }

        [$id, $name, $email, $department_id, $joining_date, $password] = array_pad($args, 6, '');

        $query = "UPDATE " . $this->getTableName() . " SET employee_name = :name, email = :email, department_id = :department_id, joining_date = :joining_date, password = :password WHERE id = :id";
        return $this->execute($query, [
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
            ':department_id' => $department_id,
            ':joining_date' => $joining_date,
            ':password' => $password
        ]);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        $query = "SELECT id FROM " . $this->getTableName() . " WHERE email = :email";
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
        $query = "DELETE FROM " . $this->getTableName() . " WHERE id = :id";
        return $this->execute($query, [':id' => $id]);
    }

    public function getEmployeeSummary(int $id): ?array
    {
        $query = "SELECT
                    e.*,
                    d.department_name,

                    SUM(CASE WHEN l.status='Approved' THEN 1 ELSE 0 END) approved,
                    SUM(CASE WHEN l.status='Pending' THEN 1 ELSE 0 END) pending,
                    SUM(CASE WHEN l.status='Rejected' THEN 1 ELSE 0 END) rejected,
                    COUNT(l.id) total

                FROM employees e

                LEFT JOIN departments d
                    ON d.id=e.department_id

                LEFT JOIN leaves l
                    ON l.employee_id=e.id

                WHERE e.id=:id

                GROUP BY e.id";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id'=>$id]);

        $employee = $stmt->fetch();

        if(!$employee){
            return null;
        }

        $query = "SELECT leave_type,start_date,end_date,status,reason,rejection_reason
                FROM leaves
                WHERE employee_id=:id
                ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id'=>$id]);

        $employee['history']=$stmt->fetchAll();

        return $employee;
    }
}
?>