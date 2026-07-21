<?php
class AdminModel {
    private PDO $conn;
    private string $table = "admins";

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function getByName(string $name): ?array {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :name LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':name' => $name]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function verifyPassword(string $plainPassword, string $storedHash): bool {
        if (password_needs_rehash($storedHash, \PASSWORD_DEFAULT)) {
            return password_verify($plainPassword, $storedHash);
        }
        return password_verify($plainPassword, $storedHash) || $plainPassword === $storedHash;
    }
}
?>