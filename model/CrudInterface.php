<?php

interface CrudInterface {
    public function getAll(): array;
    public function getById(int $id): ?array;
    public function create(...$args): bool;
    public function update(...$args): bool;
    public function delete(int $id): bool;
}
?>
