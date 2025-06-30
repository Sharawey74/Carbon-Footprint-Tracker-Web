<?php
namespace Dao\Interfaces;

use Models\Branch;
use Models\BranchMetrics;
use Models\User;
use Exceptions\DataAccessException;

interface BranchDAO extends DAO {
    public function getBranchesByCityId(int $cityId): array;
    public function countBranchesByCityId(int $cityId): int;
    public function getBranchMetrics(int $branchId): BranchMetrics;
    public function getBranchUser(int $branchId): User;
    public function branchExists(int $branchId): bool;
    public function getBranchesWithMostEmployees(int $limit): array;
}
?>