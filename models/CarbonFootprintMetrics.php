<?php
// CarbonFootprintMetrics.php
namespace Models;

class CarbonFootprintMetrics {
    private $branchId;
    private $cityName;
    private $distributionEmissions;
    private $packagingEmissions;
    private $productionEmissions;
    private $totalEmissions;

    public function __construct($branchId = null, $cityName = null, $distributionEmissions = 0, 
                               $packagingEmissions = 0, $productionEmissions = 0, $totalEmissions = null) {
        $this->branchId = $branchId;
        $this->cityName = $cityName;
        $this->distributionEmissions = $distributionEmissions;
        $this->packagingEmissions = $packagingEmissions;
        $this->productionEmissions = $productionEmissions;
        $this->totalEmissions = $totalEmissions ?? ($distributionEmissions + $packagingEmissions + $productionEmissions);
    }

    // Getters and Setters
    public function getBranchId() { return $this->branchId; }
    public function setBranchId($branchId) { $this->branchId = $branchId; }
    
    public function getCityName() { return $this->cityName; }
    public function setCityName($cityName) { $this->cityName = $cityName; }
    
    public function getDistributionEmissions() { return $this->distributionEmissions; }
    public function setDistributionEmissions($distributionEmissions) { 
        $this->distributionEmissions = $distributionEmissions; 
    }
    public function getPackagingEmissions() { return $this->packagingEmissions; }
    public function setPackagingEmissions($packagingEmissions) { 
        $this->packagingEmissions = $packagingEmissions; 
    }
    public function getProductionEmissions() { return $this->productionEmissions; }
    public function setProductionEmissions($productionEmissions) { 
        $this->productionEmissions = $productionEmissions; 
    }
    public function getTotalEmissions() { return $this->totalEmissions; }
    public function setTotalEmissions($totalEmissions) { 
        $this->totalEmissions = $totalEmissions; 
    }
    
    
    // ... similar getters/setters for other properties
}