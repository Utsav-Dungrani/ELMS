<?php
require_once __DIR__ . '/BaseModel.php';

use BaseModel;

class AdminModel extends BaseModel {
    public function __construct(PDO $db) {
        parent::__construct($db, 'admins');
    }

    public function getByName(string $name): ?array {
        $query = "SELECT * FROM " . $this->getTableName() . " WHERE username = :name LIMIT 1";
        return $this->fetchOne($query, [':name' => $name]);
    }

    public function verifyPassword(string $plainPassword, string $storedHash): bool {
        if (password_needs_rehash($storedHash, \PASSWORD_DEFAULT)) {
            return password_verify($plainPassword, $storedHash);
        }
        return password_verify($plainPassword, $storedHash) || $plainPassword === $storedHash;
    }
}
?>