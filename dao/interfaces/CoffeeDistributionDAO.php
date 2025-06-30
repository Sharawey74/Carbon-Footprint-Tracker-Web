<?php
namespace Dao\Interfaces;

use Models\CoffeeDistribution;
use Exceptions\DataAccessException;

interface CoffeeDistributionDAO extends DAO {
    public function getDistributionsByBranchId(int $branchId): array;
    public function getDistributionsByVehicleType(string $vehicleType): array;
    public function getTotalCarbonEmissionsByBranchId(int $branchId): float;
    public function getHighEmissionDistributions(float $thresholdKg): array;
    public function getAverageDistanceByBranchId(int $branchId): float;
    public function getMostCommonVehicleTypeByBranchId(int $branchId): string;
}
?>