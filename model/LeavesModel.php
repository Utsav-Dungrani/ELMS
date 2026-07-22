<?php

require_once __DIR__ . '/BaseModel.php';

class LeavesModel extends \BaseModel {
    public function __construct(PDO $db) {
        parent::__construct($db, 'leaves');
    }

    public function getDashboardStats(): array {
        $query = "SELECT 
                    COUNT(*) AS total_requests,
                    SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS total_approved,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS total_pending,
                    SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS total_rejected
                FROM " . $this->getTableName();

        $result = $this->fetchOne($query);

        return [
            'total_requests' => (int) ($result['total_requests'] ?? 0),
            'total_approved' => (int) ($result['total_approved'] ?? 0),
            'total_pending'  => (int) ($result['total_pending'] ?? 0),
            'total_rejected' => (int) ($result['total_rejected'] ?? 0),
        ];
    }

    public function getEmployeeLeaveSummary(int $page = 1, int $limit = 10): array {
        $offset = ($page - 1) * $limit;

        $countQuery = "SELECT COUNT(*) AS total FROM employees";
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute();
        $countResult = $countStmt->fetch();
        $total = (int) ($countResult['total'] ?? 0);

        $query = "SELECT e.id, e.employee_name, d.department_name,
                    SUM(CASE WHEN l.status = 'Approved' THEN 1 ELSE 0 END) AS approved_leaves,
                    SUM(CASE WHEN l.status = 'Pending' THEN 1 ELSE 0 END) AS pending_leaves,
                    SUM(CASE WHEN l.status = 'Rejected' THEN 1 ELSE 0 END) AS rejected_leaves,
                    COUNT(l.id) AS total_leaves
                FROM employees e
                LEFT JOIN departments d ON d.id = e.department_id
                LEFT JOIN leaves l ON l.employee_id = e.id
                GROUP BY e.id, e.employee_name, d.department_name
                ORDER BY e.employee_name
                LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
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

    public function getLeavesForEmployee(int $employeeId, int $page = 1, int $limit = 10): array {
        $page = max(1, $page);
        $limit = max(1, $limit);
        $offset = ($page - 1) * $limit;

        $countQuery = "SELECT COUNT(*) AS total FROM " . $this->getTableName() . " WHERE employee_id = :employee_id";
        $countStmt = $this->conn->prepare($countQuery);
        $countStmt->execute([':employee_id' => $employeeId]);
        $countResult = $countStmt->fetch();
        $total = (int) ($countResult['total'] ?? 0);
        $totalPages = max(1, (int) ceil($total / $limit));

        $query = "SELECT * FROM " . $this->getTableName() . " WHERE employee_id = :employee_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':employee_id', $employeeId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => $totalPages,
        ];
    }

    // Fetch leaves WITH employee names via Foreign Key Relationship
    public function getAllWithEmployeeDetails(string $employeeName = '', string $status = '', int $page = 1, int $limit = 10): array {
        $query = "SELECT l.*, e.employee_name AS employee_name "
               . "FROM " . $this->getTableName() . " l "
               . "JOIN employees e ON l.employee_id = e.id";

        $conditions = [];
        $params = [];

        if ($employeeName !== '') {
            $conditions[] = "LOWER(e.employee_name) LIKE :employee_name";
            $params[':employee_name'] = '%' . strtolower($employeeName) . '%';
        }

        if ($status !== '') {
            $conditions[] = "l.status = :status";
            $params[':status'] = $status;
        }

        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $query .= ' ORDER BY l.created_at DESC';

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

    public function hasOverlappingLeave(int $employeeId, string $startDate, string $endDate): bool {
        $query = "SELECT 1 FROM " . $this->getTableName() . " WHERE employee_id = :employee_id "
               . "AND status IN ('Pending', 'Approved') "
               . "AND end_date >= :start_date AND start_date <= :end_date LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':employee_id' => $employeeId,
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ]);
        return (bool) $stmt->fetch();
    }

    public function getApprovedLeaveDaysInYear(int $employeeId, int $year): int {
        $yearStart = sprintf('%04d-01-01', $year);
        $yearEnd = sprintf('%04d-12-31', $year);

        $query = "SELECT COALESCE(SUM(DATEDIFF(LEAST(end_date, :year_end), GREATEST(start_date, :year_start)) + 1), 0) AS days "
               . "FROM " . $this->getTableName() . " "
               . "WHERE employee_id = :employee_id AND status = 'Approved' "
               . "AND end_date >= :year_start AND start_date <= :year_end";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':employee_id' => $employeeId,
            ':year_start' => $yearStart,
            ':year_end' => $yearEnd
        ]);

        $result = $stmt->fetch();
        return (int) ($result['days'] ?? 0);
    }

    // Apply for a new leave (creates record referencing an existing employee_id)
    public function create(int $employeeId, string $leaveType, string $startDate, string $endDate, string $reason): bool {
        $reason = trim($reason);
        if ($reason === '') {
            return false;
        }

        $query = "INSERT INTO " . $this->getTableName() . " (employee_id, leave_type, start_date, end_date, reason, status, created_at) 
                  VALUES (:employee_id, :leave_type, :start_date, :end_date, :reason, 'Pending', NOW())";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':employee_id' => $employeeId,
            ':leave_type'  => $leaveType,
            ':start_date'  => $startDate,
            ':end_date'    => $endDate,
            ':reason'      => $reason
        ]);
    }

    public function updateStatus(int $id, string $status): bool {
        $query = "UPDATE " . $this->getTableName() . " SET status = :status WHERE id = :id";
        return $this->execute($query, [':status' => $status, ':id' => $id]);
    }
}