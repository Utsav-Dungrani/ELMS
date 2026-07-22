<?php

require_once __DIR__ . '/BaseModel.php';

use BaseModel;

class DepartmentModel extends BaseModel {
    public function __construct(PDO $db) {
        parent::__construct($db, 'departments');
    }

    public function getAll(): array {
        $query = "SELECT id, department_name, is_probation FROM " . $this->getTableName() . " ORDER BY department_name";
        return $this->fetchAll($query);
    }
}
?>
