<?php
namespace Dao\Interfaces;

use Models\CoffeePackaging;
use Exceptions\DataAccessException;

interface CoffeePackagingDAO extends DAO {
    public function getPackagingWasteByBranchId(int $branchId): array;
    public function getTotalCarbonEmissionsByBranchId(int $branchId): float;
}
?>