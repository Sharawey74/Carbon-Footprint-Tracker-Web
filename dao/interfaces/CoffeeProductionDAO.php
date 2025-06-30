<?php
namespace Dao\Interfaces;

use Models\CoffeeProduction;
use Exceptions\DataAccessException;

interface CoffeeProductionDAO extends DAO {
    public function getProductionQuantitiesByBranchId(int $branchId): array;
    public function getTotalCarbonEmissionsByBranchId(int $branchId): float;
    public function getProductionByCoffeeType(string $coffeeType): array;
    public function getByBranchId(int $branchId): CoffeeProduction;
}
?>