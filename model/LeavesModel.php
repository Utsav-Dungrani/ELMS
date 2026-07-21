<?php

class LeavesModel {
    private PDO $conn;
    private string $table = "leaves";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function getDashboardStats(): array {
        $query = "SELECT 
                    COUNT(*) AS total_requests,
                    SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) AS total_approved,
                    SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS total_pending,
                    SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) AS total_rejected
                FROM " . $this->table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();

        return [
            'total_requests' => (int) ($result['total_requests'] ?? 0),
            'total_approved' => (int) ($result['total_approved'] ?? 0),
            'total_pending'  => (int) ($result['total_pending'] ?? 0),
            'total_rejected' => (int) ($result['total_rejected'] ?? 0),
        ];
    }

    // Fetch leaves WITH employee names via Foreign Key Relationship
    public function getAllWithEmployeeDetails(string $employeeName = '', string $status = ''): array {
        $query = "SELECT l.*, e.employee_name AS employee_name "
               . "FROM " . $this->table . " l "
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

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function hasOverlappingLeave(int $employeeId, string $startDate, string $endDate): bool {
        $query = "SELECT 1 FROM " . $this->table . " WHERE employee_id = :employee_id "
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
               . "FROM " . $this->table . " "
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
        $query = "INSERT INTO " . $this->table . " (employee_id, leave_type, start_date, end_date, reason, status, created_at) 
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
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}