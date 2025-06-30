<?php
namespace Services;

use Dao\Interfaces\CoffeeDistributionDAO;
use Dao\Interfaces\CoffeePackagingDAO;
use Dao\Interfaces\CoffeeProductionDAO;
use Dao\Interfaces\BranchDAO;
use Dao\Interfaces\CityDAO;
use Models\CarbonFootprintMetrics;
use Utils\EmissionCalculator;
use Exceptions\DataAccessException;

class CarbonFootprintService {
    private $distributionDao;
    private $packagingDao;
    private $productionDao;
    private $branchDao;
    private $cityDao;

    public function __construct(
        CoffeeDistributionDAO $distributionDao,
        CoffeePackagingDAO $packagingDao,
        CoffeeProductionDAO $productionDao,
        BranchDAO $branchDao,
        CityDAO $cityDao
    ) {
        $this->distributionDao = $distributionDao;
        $this->packagingDao = $packagingDao;
        $this->productionDao = $productionDao;
        $this->branchDao = $branchDao;
        $this->cityDao = $cityDao;
    }

    /**
     * Get overall carbon footprint metrics across all branches
     * @return array Array of metrics data
     * @throws DataAccessException
     */
    public function getOverallMetrics() {
        $result = [
            'total_emissions' => 0,
            'distribution_emissions' => 0,
            'packaging_emissions' => 0,
            'production_emissions' => 0,
            'branches' => [],
        ];
        
        try {
            // Get all branches
            $branches = $this->branchDao->getAll();
            
            // Calculate metrics for each branch
            foreach ($branches as $branch) {
                $branchMetrics = $this->getCarbonFootprintMetrics($branch->getBranchId());
                
                // Add to overall totals
                $result['distribution_emissions'] += $branchMetrics->getDistributionEmissions();
                $result['packaging_emissions'] += $branchMetrics->getPackagingEmissions();
                $result['production_emissions'] += $branchMetrics->getProductionEmissions();
                $result['total_emissions'] += $branchMetrics->getTotalEmissions();
                
                // Add branch-specific data
                $result['branches'][] = [
                    'branch_id' => $branch->getBranchId(),
                    'location' => $branch->getLocation(),
                    'city' => $branchMetrics->getCityName(),
                    'total_emissions' => $branchMetrics->getTotalEmissions(),
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            error_log("Error getting overall metrics: " . $e->getMessage());
            return $result; // Return empty result on error
        }
    }

    /**
     * @throws DataAccessException
     */
    public function getCarbonFootprintMetrics($branchId) {
        $metrics = new CarbonFootprintMetrics();
        $metrics->setBranchId($branchId);

        $branch = $this->branchDao->getById($branchId);
        if ($branch) {
            $city = $this->cityDao->getById($branch->getCityId());
            $metrics->setCityName($city ? $city->getCityName() : 'Unknown');
        }

        $metrics->setDistributionEmissions($this->distributionDao->getTotalCarbonEmissionsByBranchId($branchId));
        $metrics->setPackagingEmissions($this->packagingDao->getTotalCarbonEmissionsByBranchId($branchId));
        $metrics->setProductionEmissions($this->productionDao->getTotalCarbonEmissionsByBranchId($branchId));
        $metrics->setTotalEmissions(
            $metrics->getDistributionEmissions() +
            $metrics->getPackagingEmissions() +
            $metrics->getProductionEmissions()
        );

        return $metrics;
    }

    /**
     * Get all production records for a specific branch
     * 
     * @param int $branchId The branch ID
     * @return array Array of production records
     * @throws DataAccessException
     */
    public function getProductionByBranch($branchId) {
        $allProductions = $this->productionDao->getAll();
        return array_filter($allProductions, function($production) use ($branchId) {
            return $production->getBranchId() == $branchId;
        });
    }

    /**
     * Get all packaging records for a specific branch
     * 
     * @param int $branchId The branch ID
     * @return array Array of packaging records
     * @throws DataAccessException
     */
    public function getPackagingByBranch($branchId) {
        $allPackagings = $this->packagingDao->getAll();
        return array_filter($allPackagings, function($packaging) use ($branchId) {
            return $packaging->getBranchId() == $branchId;
        });
    }

    /**
     * Get all distribution records for a specific branch
     * 
     * @param int $branchId The branch ID
     * @return array Array of distribution records
     * @throws DataAccessException
     */
    public function getDistributionByBranch($branchId) {
        $allDistributions = $this->distributionDao->getAll();
        return array_filter($allDistributions, function($distribution) use ($branchId) {
            return $distribution->getBranchId() == $branchId;
        });
    }

    /**
     * Get production records for a specific branch and user
     * 
     * @param int $branchId The branch ID
     * @param int $userId The user ID
     * @return array Array of production records
     * @throws DataAccessException
     */
    public function getProductionByBranchAndUser($branchId, $userId) {
        $allProductions = $this->productionDao->getAll();
        return array_filter($allProductions, function($production) use ($branchId, $userId) {
            return $production->getBranchId() == $branchId && $production->getUserId() == $userId;
        });
    }

    /**
     * Get packaging records for a specific branch and user
     * 
     * @param int $branchId The branch ID
     * @param int $userId The user ID
     * @return array Array of packaging records
     * @throws DataAccessException
     */
    public function getPackagingByBranchAndUser($branchId, $userId) {
        $allPackagings = $this->packagingDao->getAll();
        return array_filter($allPackagings, function($packaging) use ($branchId, $userId) {
            return $packaging->getBranchId() == $branchId && $packaging->getUserId() == $userId;
        });
    }

    /**
     * Get distribution records for a specific branch and user
     * 
     * @param int $branchId The branch ID
     * @param int $userId The user ID
     * @return array Array of distribution records
     * @throws DataAccessException
     */
    public function getDistributionByBranchAndUser($branchId, $userId) {
        $allDistributions = $this->distributionDao->getAll();
        return array_filter($allDistributions, function($distribution) use ($branchId, $userId) {
            return $distribution->getBranchId() == $branchId && $distribution->getUserId() == $userId;
        });
    }

    /**
     * @throws DataAccessException
     */
    public function getHighEmissionDistributions($threshold) {
        return $this->distributionDao->getHighEmissionDistributions($threshold);
    }

    /**
     * @throws DataAccessException
     */
    public function getProductionByType($coffeeType) {
        return $this->productionDao->getProductionByCoffeeType($coffeeType);
    }

    /**
     * @throws DataAccessException
     */
    public function recalculateAllEmissions() {
        // Productions
        foreach ($this->productionDao->getAll() as $production) {
            $emissions = EmissionCalculator::calculateProductionEmissions(
                $production->getProductionQuantitiesOfCoffeeKG()
            );
            $production->setPrCarbonEmissionsKG($emissions);
            $this->productionDao->update($production);
        }

        // Packagings
        foreach ($this->packagingDao->getAll() as $packaging) {
            $emissions = EmissionCalculator::calculatePackagingEmissions(
                $packaging->getPackagingWasteKG()
            );
            $packaging->setPaCarbonEmissionsKG($emissions);
            $this->packagingDao->update($packaging);
        }

        // Distributions
        foreach ($this->distributionDao->getAll() as $distribution) {
            $emissions = EmissionCalculator::calculateDistributionEmissions(
                $distribution->getVehicleType(),
                $distribution->getDistancePerVehicleKM(),
                $distribution->getNumberOfVehicles()
            );
            $distribution->setVCarbonEmissionsKg($emissions);
            $this->distributionDao->update($distribution);
        }
    }

    /**
     * Save a production record to the database
     * 
     * @param \Models\CoffeeProduction $production The production record to save
     * @return bool Success status
     * @throws DataAccessException
     */
    public function saveProduction($production) {
        return $this->productionDao->save($production);
    }
    
    /**
     * Save a packaging record to the database
     * 
     * @param \Models\CoffeePackaging $packaging The packaging record to save
     * @return bool Success status
     * @throws DataAccessException
     */
    public function savePackaging($packaging) {
        return $this->packagingDao->save($packaging);
    }
    
    /**
     * Save a distribution record to the database
     * 
     * @param \Models\CoffeeDistribution $distribution The distribution record to save
     * @return bool Success status
     * @throws DataAccessException
     */
    public function saveDistribution($distribution) {
        return $this->distributionDao->save($distribution);
    }
    
    /**
     * Get all carbon footprint records
     * 
     * @return array Array containing all production, packaging, and distribution records
     * @throws DataAccessException
     */
    public function getAll() {
        return [
            'production' => $this->productionDao->getAll(),
            'packaging' => $this->packagingDao->getAll(),
            'distribution' => $this->distributionDao->getAll()
        ];
    }
    
    /**
     * Update an existing production record
     * 
     * @param \Models\CoffeeProduction $production The production record to update
     * @return bool Success status
     * @throws DataAccessException
     */
    public function updateProduction($production) {
        return $this->productionDao->update($production);
    }
    
    /**
     * Update an existing packaging record
     * 
     * @param \Models\CoffeePackaging $packaging The packaging record to update
     * @return bool Success status
     * @throws DataAccessException
     */
    public function updatePackaging($packaging) {
        return $this->packagingDao->update($packaging);
    }
    
    /**
     * Update an existing distribution record
     * 
     * @param \Models\CoffeeDistribution $distribution The distribution record to update
     * @return bool Success status
     * @throws DataAccessException
     */
    public function updateDistribution($distribution) {
        return $this->distributionDao->update($distribution);
    }

    /**
     * Get a production record by ID
     * 
     * @param int $productionId The production ID
     * @return \Models\CoffeeProduction|null The production record or null if not found
     * @throws DataAccessException
     */
    public function getProductionById($productionId) {
        return $this->productionDao->getById($productionId);
    }
    
    /**
     * Get a packaging record by ID
     * 
     * @param int $packagingId The packaging ID
     * @return \Models\CoffeePackaging|null The packaging record or null if not found
     * @throws DataAccessException
     */
    public function getPackagingById($packagingId) {
        return $this->packagingDao->getById($packagingId);
    }
    
    /**
     * Get a distribution record by ID
     * 
     * @param int $distributionId The distribution ID
     * @return \Models\CoffeeDistribution|null The distribution record or null if not found
     * @throws DataAccessException
     */
    public function getDistributionById($distributionId) {
        return $this->distributionDao->getById($distributionId);
    }
    
    /**
     * Add a new production record
     * 
     * @param int $branchId The branch ID
     * @param int $userId The user ID
     * @param string $supplier The supplier name
     * @param string $coffeeType The coffee type
     * @param string $productType The product type
     * @param float $quantity The production quantity in KG
     * @param string $date The production date
     * @return bool Success status
     * @throws DataAccessException
     */
    public function addProduction($branchId, $userId, $supplier, $coffeeType, $productType, $quantity, $date) {
        // Create a new production object
        $production = new \Models\CoffeeProduction(
            0, // ID will be set by the database
            $branchId,
            $userId,
            $supplier,
            $coffeeType,
            $productType,
            $quantity,
            0, // Emissions will be calculated
            new \DateTime($date)
        );
        
        // Calculate emissions
        $emissions = \Utils\EmissionCalculator::calculateProductionEmissions($quantity);
        $production->setPrCarbonEmissionsKG($emissions);
        
        // Save to database
        return $this->productionDao->save($production);
    }
    
    /**
     * Add a new packaging record
     * 
     * @param int $branchId The branch ID
     * @param int $userId The user ID
     * @param float $waste The packaging waste in KG
     * @param string $date The packaging date
     * @return bool Success status
     * @throws DataAccessException
     */
    public function addPackaging($branchId, $userId, $waste, $date) {
        // Create a new packaging object
        $packaging = new \Models\CoffeePackaging(
            0, // ID will be set by the database
            $branchId,
            $userId,
            $waste,
            0, // Emissions will be calculated
            new \DateTime($date)
        );
        
        // Calculate emissions
        $emissions = \Utils\EmissionCalculator::calculatePackagingEmissions($waste);
        $packaging->setPaCarbonEmissionsKG($emissions);
        
        // Save to database
        return $this->packagingDao->save($packaging);
    }
    
    /**
     * Add a new distribution record
     * 
     * @param int $branchId The branch ID
     * @param int $userId The user ID
     * @param string $vehicleType The vehicle type
     * @param int $numberOfVehicles The number of vehicles
     * @param float $distancePerVehicle The distance per vehicle in KM
     * @param string $date The distribution date
     * @return bool Success status
     * @throws DataAccessException
     */
    public function addDistribution($branchId, $userId, $vehicleType, $numberOfVehicles, $distancePerVehicle, $date) {
        // Calculate total distance
        $totalDistance = $numberOfVehicles * $distancePerVehicle;
        
        // Determine fuel efficiency based on vehicle type
        $fuelEfficiency = 10.0; // Default
        if ($vehicleType == 'Pickup Truck') {
            $fuelEfficiency = 15.0;
        }
        
        // Calculate emissions
        $emissions = \Utils\EmissionCalculator::calculateDistributionEmissions(
            $vehicleType,
            $distancePerVehicle,
            $numberOfVehicles
        );
        
        // Create a new distribution object
        $distribution = new \Models\CoffeeDistribution(
            0, // ID will be set by the database
            $branchId,
            $userId,
            $vehicleType,
            $numberOfVehicles,
            $distancePerVehicle,
            $totalDistance,
            $fuelEfficiency,
            $emissions,
            new \DateTime($date)
        );
        
        // Save to database
        return $this->distributionDao->save($distribution);
    }
}