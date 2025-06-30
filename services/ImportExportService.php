<?php
namespace Services;

use Dao\Interfaces\CoffeeProductionDAO;
use Dao\Interfaces\CoffeePackagingDAO;
use Dao\Interfaces\CoffeeDistributionDAO;
use Dao\Interfaces\ReductionStrategyDAO;
use Dao\Interfaces\BranchDAO;
use Dao\Interfaces\AuditLoggingDAO;
use Models\CoffeeProduction;
use Models\CoffeePackaging;
use Models\CoffeeDistribution;
use Models\ReductionStrategy;
use Models\Branch;
use Models\CarbonFootprintMetrics;
use Models\AuditLogging;
use Exceptions\DataAccessException;
use Exceptions\ImportException;
use Exceptions\ExportException;
use DateTime;

class ImportExportService {
    private $productionDao;
    private $packagingDao;
    private $distributionDao;
    private $reductionStrategyDao;
    private $branchDao;
    private $auditLoggingDao;

    public function __construct(
        CoffeeProductionDAO $productionDao,
        CoffeePackagingDAO $packagingDao,
        CoffeeDistributionDAO $distributionDao,
        ReductionStrategyDAO $reductionStrategyDao,
        BranchDAO $branchDao,
        AuditLoggingDAO $auditLoggingDao
    ) {
        $this->productionDao = $productionDao;
        $this->packagingDao = $packagingDao;
        $this->distributionDao = $distributionDao;
        $this->reductionStrategyDao = $reductionStrategyDao;
        $this->branchDao = $branchDao;
        $this->auditLoggingDao = $auditLoggingDao;
    }

    // PRODUCTION DATA METHODS

    /**
     * Import and save production data from a CSV file
     * 
     * @param string $filePath Path to CSV file
     * @param int $userId User ID for audit logging
     * @throws ImportException
     * @throws DataAccessException
     */
    public function importProductionData($filePath, $userId) {
        if (!file_exists($filePath)) {
            throw new ImportException("File not found: $filePath");
        }
        
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            $production = new CoffeeProduction(
                0, // productionId (will be set by the database)
                (int)$row[0], // branchId
                $userId,
                $row[1], // supplier
                $row[2], // coffeeType
                $row[3], // productType
                (float)$row[4], // productionQuantitiesOfCoffeeKG
                0, // prCarbonEmissionsKG (will be calculated)
                new DateTime() // productionDate
            );
            
            if ($this->productionDao->save($production)) {
                $this->logAction($userId, 'INSERT', 'CoffeeProduction', $production->getProductionId());
            }
        }
        fclose($handle);
    }

    /**
     * Export production data to CSV
     * 
     * @param int|null $branchId Optional branch ID to filter data by branch
     * @throws ExportException
     * @throws DataAccessException
     */
    public function exportProductionData($branchId = null) {
        try {
            $data = $this->productionDao->getAll();
            
            // Filter by branch if specified
            if ($branchId) {
                $data = array_filter($data, function($item) use ($branchId) {
                    return $item->getBranchId() == $branchId;
                });
            }

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="production.csv"');
        $output = fopen('php://output', 'w');

            fputcsv($output, ['ProductionID', 'BranchID', 'UserID', 'Supplier', 'CoffeeType', 'ProductType', 'Quantity', 'Emissions', 'Date']);
        foreach ($data as $item) {
            fputcsv($output, [
                    $item->getProductionId(),
                $item->getBranchId(),
                    $item->getUserId(),
                    $item->getSupplier(),
                    $item->getCoffeeType(),
                    $item->getProductType(),
                    $item->getProductionQuantitiesOfCoffeeKG(),
                    $item->getPrCarbonEmissionsKG(),
                    $item->getActivityDate()->format('Y-m-d H:i:s')
            ]);
        }
        fclose($output);
        } catch (\Exception $e) {
            throw new ExportException("Failed to export production data: " . $e->getMessage());
        }
    }

    /**
     * Parse production data from CSV input stream
     * 
     * @param resource $inputStream Input stream containing CSV data
     * @return array Array of CoffeeProduction objects
     * @throws ImportException
     */
    public function importProductionDataFromCSV($inputStream) {
        $productions = [];
        
        try {
            // Skip header row
            fgetcsv($inputStream);
            
            while (($row = fgetcsv($inputStream)) !== false) {
                $production = new CoffeeProduction(
                    0, // Will be set by DB
                    (int)$row[1], // BranchID
                    (int)$row[2], // UserID
                    $row[3], // Supplier
                    $row[4], // CoffeeType
                    $row[5], // ProductType
                    (float)$row[6], // ProductionQuantitiesKG
                    (float)$row[7], // CarbonEmissionsKG
                    new DateTime() // Current date
                );
                $productions[] = $production;
            }
        } catch (\Exception $e) {
            throw new ImportException("Failed to import production data: " . $e->getMessage());
        }
        
        return $productions;
    }

    // PACKAGING DATA METHODS

    /**
     * Import and save packaging data from a CSV file
     * 
     * @param string $filePath Path to CSV file
     * @param int $userId User ID for audit logging
     * @throws ImportException
     * @throws DataAccessException
     */
    public function importPackagingData($filePath, $userId) {
        if (!file_exists($filePath)) {
            throw new ImportException("File not found: $filePath");
        }
        
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            $packaging = new CoffeePackaging(
                0, // packagingId (will be set by the database)
                (int)$row[0], // branchId
                $userId,
                (float)$row[1], // wasteAmount
                0, // paCarbonEmissionsKG (will be calculated)
                new DateTime() // packagingDate
            );
            
            if ($this->packagingDao->save($packaging)) {
                $this->logAction($userId, 'INSERT', 'CoffeePackaging', $packaging->getPackagingId());
            }
        }
        fclose($handle);
    }

    /**
     * Export packaging data to CSV
     * 
     * @param int|null $branchId Optional branch ID to filter data by branch
     * @throws ExportException
     * @throws DataAccessException
     */
    public function exportPackagingData($branchId = null) {
        try {
            $data = $this->packagingDao->getAll();
            
            // Filter by branch if specified
            if ($branchId) {
                $data = array_filter($data, function($item) use ($branchId) {
                    return $item->getBranchId() == $branchId;
                });
            }

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="packaging.csv"');
            $output = fopen('php://output', 'w');

            fputcsv($output, ['PackagingID', 'BranchID', 'UserID', 'PackagingWasteKG', 'CarbonEmissionsKG', 'Date']);
            foreach ($data as $item) {
                fputcsv($output, [
                    $item->getPackagingId(),
                    $item->getBranchId(),
                    $item->getUserId(),
                    $item->getPackagingWasteKG(),
                    $item->getPaCarbonEmissionsKG(),
                    $item->getActivityDate()->format('Y-m-d H:i:s')]);
            }
            fclose($output);
        } catch (\Exception $e) {
            throw new ExportException("Failed to export packaging data: " . $e->getMessage());
        }
    }

    /**
     * Parse packaging data from CSV input stream
     * 
     * @param resource $inputStream Input stream containing CSV data
     * @return array Array of CoffeePackaging objects
     * @throws ImportException
     */
    public function importPackagingDataFromCSV($inputStream) {
        $packagingList = [];
        
        try {
            // Skip header row
            fgetcsv($inputStream);
            
            while (($row = fgetcsv($inputStream)) !== false) {
                $packaging = new CoffeePackaging(
                    0, // Will be set by DB
                    (int)$row[1], // BranchID
                    (int)$row[2], // UserID
                    (float)$row[3], // PackagingWasteKG
                    (float)$row[4], // CarbonEmissionsKG
                    new DateTime() // Current date
                );
                $packagingList[] = $packaging;
            }
        } catch (\Exception $e) {
            throw new ImportException("Failed to import packaging data: " . $e->getMessage());
        }
        
        return $packagingList;
    }

    // DISTRIBUTION DATA METHODS

    /**
     * Import and save distribution data from a CSV file
     * 
     * @param string $filePath Path to CSV file
     * @param int $userId User ID for audit logging
     * @throws ImportException
     * @throws DataAccessException
     */
    public function importDistributionData($filePath, $userId) {
        if (!file_exists($filePath)) {
            throw new ImportException("File not found: $filePath");
        }
        
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            $distribution = new CoffeeDistribution(
                0, // distributionId (will be set by the database)
                (int)$row[0], // branchId
                $userId,
                $row[1], // vehicleType
                (int)$row[2], // numberOfVehicles
                (float)$row[3], // distancePerVehicleKM
                (float)$row[4], // totalDistance
                (float)$row[5], // fuelEfficiency
                0, // vCarbonEmissionsKg (will be calculated)
                new DateTime() // distributionDate
            );
            
            if ($this->distributionDao->save($distribution)) {
                $this->logAction($userId, 'INSERT', 'CoffeeDistribution', $distribution->getDistributionId());
            }
        }
        fclose($handle);
    }

    /**
     * Export distribution data to CSV
     * 
     * @param int|null $branchId Optional branch ID to filter data by branch
     * @throws ExportException
     * @throws DataAccessException
     */
    public function exportDistributionData($branchId = null) {
        try {
            $data = $this->distributionDao->getAll();
            
            // Filter by branch if specified
            if ($branchId) {
                $data = array_filter($data, function($item) use ($branchId) {
                    return $item->getBranchId() == $branchId;
                });
            }

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="distribution.csv"');
            $output = fopen('php://output', 'w');

            fputcsv($output, ['DistributionID', 'BranchID', 'UserID', 'VehicleType', 'NumberOfVehicles', 
                'DistancePerVehicleKM', 'TotalDistanceKM', 'FuelEfficiency', 'CarbonEmissionsKG', 'Date']);
            foreach ($data as $item) {
                fputcsv($output, [
                    $item->getDistributionId(),
                    $item->getBranchId(),
                    $item->getUserId(),
                    $item->getVehicleType(),
                    $item->getNumberOfVehicles(),
                    $item->getDistancePerVehicleKM(),
                    $item->getTotalDistanceKM(),
                    $item->getFuelEfficiency(),
                    $item->getVCarbonEmissionsKg(),
                    $item->getActivityDate()->format('Y-m-d H:i:s')
                ]);
            }
            fclose($output);
        } catch (\Exception $e) {
            throw new ExportException("Failed to export distribution data: " . $e->getMessage());
        }
    }

    /**
     * Parse distribution data from CSV input stream
     * 
     * @param resource $inputStream Input stream containing CSV data
     * @return array Array of CoffeeDistribution objects
     * @throws ImportException
     */
    public function importDistributionDataFromCSV($inputStream) {
        $distributions = [];
        
        try {
            // Skip header row
            fgetcsv($inputStream);
            
            while (($row = fgetcsv($inputStream)) !== false) {
                $distribution = new CoffeeDistribution(
                    0, // Will be set by DB
                    (int)$row[1], // BranchID
                    (int)$row[2], // UserID
                    $row[3], // VehicleType
                    (int)$row[4], // NumberOfVehicles
                    (float)$row[5], // DistancePerVehicleKM
                    (float)($row[4] * $row[5]), // TotalDistanceKM
                    (float)$row[6], // FuelEfficiency
                    (float)$row[7], // CarbonEmissionsKG
                    new DateTime() // Current date
                );
                $distributions[] = $distribution;
            }
        } catch (\Exception $e) {
            throw new ImportException("Failed to import distribution data: " . $e->getMessage());
        }
        
        return $distributions;
    }

    // REDUCTION STRATEGY METHODS

    /**
     * Import and save reduction strategies from a CSV file
     * 
     * @param string $filePath Path to CSV file
     * @param int $userId User ID for audit logging
     * @throws ImportException
     * @throws DataAccessException
     */
    public function importReductionStrategies($filePath, $userId) {
        if (!file_exists($filePath)) {
            throw new ImportException("File not found: $filePath");
        }
        
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            $strategy = new ReductionStrategy(
                0, // reductionId (will be set by the database)
                (int)$row[0], // branchId
                $userId,
                $row[1], // reductionStrategy
                (int)$row[2], // statusId
                (float)$row[3], // implementationCosts
                (float)$row[4] // projectedAnnualProfits
            );
            
            if ($this->reductionStrategyDao->save($strategy)) {
                $this->logAction($userId, 'INSERT', 'ReductionStrategy', $strategy->getReductionId());
            }
        }
        fclose($handle);
    }

    /**
     * Export reduction strategies to CSV
     * 
     * @param int|null $branchId Optional branch ID to filter data by branch
     * @throws ExportException
     * @throws DataAccessException
     */
    public function exportReductionStrategies($branchId = null) {
        try {
            $data = $this->reductionStrategyDao->getAll();
            
            // Filter by branch if specified
            if ($branchId) {
                $data = array_filter($data, function($item) use ($branchId) {
                    return $item->getBranchId() == $branchId;
                });
            }

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="strategies.csv"');
            $output = fopen('php://output', 'w');

            fputcsv($output, ['ReductionID', 'BranchID', 'UserID', 'ReductionStrategy', 'StatusID', 
                'ImplementationCosts', 'ProjectedAnnualProfits']);
            foreach ($data as $item) {
                fputcsv($output, [
                    $item->getReductionId(),
                    $item->getBranchId(),
                    $item->getUserId(),
                    $item->getReductionStrategy(),
                    $item->getStatusId(),
                    $item->getImplementationCosts(),
                    $item->getProjectedAnnualProfits()
                ]);
            }
            fclose($output);
        } catch (\Exception $e) {
            throw new ExportException("Failed to export reduction strategies: " . $e->getMessage());
        }
    }

    /**
     * Parse reduction strategies from CSV input stream
     * 
     * @param resource $inputStream Input stream containing CSV data
     * @return array Array of ReductionStrategy objects
     * @throws ImportException
     */
    public function importReductionStrategiesFromCSV($inputStream) {
        $strategies = [];
        
        try {
            // Skip header row
            fgetcsv($inputStream);
            
            while (($row = fgetcsv($inputStream)) !== false) {
                $strategy = new ReductionStrategy(
                    0, // Will be set by DB
                    (int)$row[1], // BranchID
                    (int)$row[2], // UserID
                    $row[3], // ReductionStrategy
                    (int)$row[4], // StatusID
                    (float)$row[5], // ImplementationCosts
                    (float)$row[6] // ProjectedAnnualProfits
                );
                $strategies[] = $strategy;
            }
        } catch (\Exception $e) {
            throw new ImportException("Failed to import reduction strategies: " . $e->getMessage());
        }
        
        return $strategies;
    }

    // BRANCH DATA METHODS

    /**
     * Import and save branch data from a CSV file
     * 
     * @param string $filePath Path to CSV file
     * @param int $userId User ID for audit logging
     * @throws ImportException
     * @throws DataAccessException
     */
    public function importBranchData($filePath, $userId) {
        if (!file_exists($filePath)) {
            throw new ImportException("File not found: $filePath");
        }
        
        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            $branch = new Branch(
                0, // branchId (will be set by the database)
                (int)$row[0], // cityId
                $row[1], // location
                (int)$row[2] // numberOfEmployees
            );
            
            if ($this->branchDao->save($branch)) {
                $this->logAction($userId, 'INSERT', 'Branch', $branch->getBranchId());
            }
        }
        fclose($handle);
    }

    /**
     * Export branch data to CSV
     * 
     * @param int|null $cityId Optional city ID to filter data by city
     * @throws ExportException
     * @throws DataAccessException
     */
    public function exportBranchData($cityId = null) {
        try {
            $data = $this->branchDao->getAll();
            
            // Filter by city if specified
            if ($cityId) {
                $data = array_filter($data, function($item) use ($cityId) {
                    return $item->getCityId() == $cityId;
                });
            }

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="branches.csv"');
            $output = fopen('php://output', 'w');

            fputcsv($output, ['BranchID', 'CityID', 'Location', 'NumberOfEmployees']);
            foreach ($data as $item) {
                fputcsv($output, [
                    $item->getBranchId(),
                    $item->getCityId(),
                    $item->getLocation(),
                    $item->getNumberOfEmployees()
                ]);
            }
            fclose($output);
        } catch (\Exception $e) {
            throw new ExportException("Failed to export branch data: " . $e->getMessage());
        }
    }

    /**
     * Parse branch data from CSV input stream
     * 
     * @param resource $inputStream Input stream containing CSV data
     * @return array Array of Branch objects
     * @throws ImportException
     */
    public function importBranchDataFromCSV($inputStream) {
        $branches = [];
        
        try {
            // Skip header row
            fgetcsv($inputStream);
            
            while (($row = fgetcsv($inputStream)) !== false) {
                $branch = new Branch(
                    0, // Will be set by DB
                    (int)$row[1], // CityID
                    $row[2], // Location
                    (int)$row[3] // NumberOfEmployees
                );
                $branches[] = $branch;
            }
        } catch (\Exception $e) {
            throw new ImportException("Failed to import branch data: " . $e->getMessage());
        }
        
        return $branches;
    }

    // CARBON METRICS METHODS

    /**
     * Export carbon footprint metrics to CSV
     * 
     * @param array $metrics Array of CarbonFootprintMetrics objects
     * @throws ExportException
     */
    public function exportCarbonMetricsToCSV($metrics) {
        try {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="carbon_metrics.csv"');
            $output = fopen('php://output', 'w');

            fputcsv($output, ['BranchID', 'CityName', 'DistributionEmissions', 
                'PackagingEmissions', 'ProductionEmissions', 'TotalEmissions']);
            foreach ($metrics as $item) {
                fputcsv($output, [
                    $item->getBranchId(),
                    $item->getCityName(),
                    $item->getDistributionEmissions(),
                    $item->getPackagingEmissions(),
                    $item->getProductionEmissions(),
                    $item->getTotalEmissions()
                ]);
            }
            fclose($output);
        } catch (\Exception $e) {
            throw new ExportException("Failed to export carbon metrics: " . $e->getMessage());
        }
    }

    /**
     * @throws DataAccessException
     */
    private function logAction($userId, $action, $entity, $entityId) {
        $log = new AuditLogging(
            0, // logId
            $userId,
            $action,
            $entity,
            $entityId,
            new DateTime()
        );
        $this->auditLoggingDao->insert($log);
    }
}