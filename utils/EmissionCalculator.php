<?php
namespace Utils;

class EmissionCalculator {
    const PACKAGING_EMISSION_FACTOR = 6.0;
    const PRODUCTION_EMISSION_FACTOR = 6.4;
    const MINIVAN_EMISSION_FACTOR = 10;
    const TRUCK_EMISSION_FACTOR = 15;

    public static function calculatePackagingEmissions($packagingWasteKg) {
        return $packagingWasteKg * self::PACKAGING_EMISSION_FACTOR;
    }

    public static function calculateProductionEmissions($productionQuantityKg) {
        return $productionQuantityKg * self::PRODUCTION_EMISSION_FACTOR;
    }

    public static function calculateDistributionEmissions($vehicleType, $distanceKm, $numberOfVehicles) {
        $factor = strtolower($vehicleType) === 'minivan' 
            ? self::MINIVAN_EMISSION_FACTOR 
            : self::TRUCK_EMISSION_FACTOR;

        return $distanceKm * 2 * $factor * $numberOfVehicles * 2.68;
    }
}
?>