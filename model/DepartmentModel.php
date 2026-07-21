<?php

class DepartmentModel {
    private PDO $conn;
    private string $table = 'departments';

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function getAll(): array {
        $query = "SELECT id, department_name, is_probation FROM " . $this->table . " ORDER BY department_name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
