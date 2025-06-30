<?php
namespace Dao\Interfaces;

use Models\CarbonFootprintMetrics;
use Exceptions\DataAccessException;

interface CarbonFootprintDAO {
    public function calculateTotalEmissionsByCity(string $cityName): float;
    public function getEmissionsDetailsByCity(string $cityName): array;
    public function getEmissionsDetailsByBranch(int $branchId): CarbonFootprintMetrics;
    public function getReductionStrategies(int $branchId): array;
}
?>