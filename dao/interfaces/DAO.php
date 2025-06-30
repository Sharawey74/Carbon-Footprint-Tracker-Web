<?php
namespace Dao\Interfaces;

use Exceptions\DataAccessException;

interface DAO {
    public function getById(int $id);
    public function getAll(): array;
    public function save($object): bool;
    public function insert($object): bool;
    public function update($object): bool;
    public function delete($object): bool;
}
?>