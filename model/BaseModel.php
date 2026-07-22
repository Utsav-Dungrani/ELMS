<?php

abstract class BaseModel {
    protected PDO $conn;
    protected string $table;

    public function __construct(PDO $db, string $table) {
        $this->conn = $db;
        $this->table = $table;
    }

    protected function getTableName(): string {
        return $this->table;
    }

    protected function fetchAll(string $query, array $params = []): array {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    protected function fetchOne(string $query, array $params = []): ?array {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    protected function execute(string $query, array $params = []): bool {
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }
}
?>
