<?php
namespace Dao\Interfaces;

use Models\PlanStatus;
use Exceptions\DataAccessException;

interface PlanStatusDAO extends DAO {
    public function getPlanStatusesByBranchId(int $branchId): array;
}
?>