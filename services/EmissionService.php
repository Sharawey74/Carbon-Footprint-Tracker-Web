<?php
namespace Services;

use Models\CoffeeProduction;
use Models\CoffeePackaging;
use Models\CoffeeDistribution;
use Utils\EmissionCalculator;

class EmissionService {
    public function calculateProductionEmissions(CoffeeProduction $production) {
        $emissions = EmissionCalculator::calculateProductionEmissions(
            $production->getProductionQuantitiesOfCoffeeKG()
        );
        $production->setPrCarbonEmissionsKG($emissions);
    }

    public function calculatePackagingEmissions(CoffeePackaging $packaging) {
        $emissions = EmissionCalculator::calculatePackagingEmissions(
            $packaging->getPackagingWasteKG()
        );
        $packaging->setPaCarbonEmissionsKG($emissions);
    }

    public function calculateDistributionEmissions(CoffeeDistribution $distribution) {
        $emissions = EmissionCalculator::calculateDistributionEmissions(
            $distribution->getVehicleType(),
            $distribution->getDistancePerVehicleKM(),
            $distribution->getNumberOfVehicles()
        );
        $distribution->setVCarbonEmissionsKg($emissions);
    }
}