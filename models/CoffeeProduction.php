<?php
// CoffeeProduction.php
namespace Models;
class CoffeeProduction {
    private $productionId;
    private $branchId;
    private $userId;
    private $supplier;
    private $coffeeType;
    private $productType;
    private $productionQuantitiesOfCoffeeKG;
    private $prCarbonEmissionsKG;
    private $activityDate;

    public function __construct($productionId, $branchId, $userId, $supplier,
                                $coffeeType, $productType,
                                $productionQuantitiesOfCoffeeKG, $prCarbonEmissionsKG,
                                $activityDate) {
        $this->productionId = $productionId;
        $this->branchId = $branchId;
        $this->userId = $userId;
        $this->supplier = $supplier;
        $this->coffeeType = $coffeeType;
        $this->productType = $productType;
        $this->productionQuantitiesOfCoffeeKG = $productionQuantitiesOfCoffeeKG;
        $this->prCarbonEmissionsKG = $prCarbonEmissionsKG;
        $this->activityDate = $activityDate;
    }

    // Getters and Setters
    public function getProductionId() { return $this->productionId; }
    public function setProductionId($productionId) { $this->productionId = $productionId; }
    public function getBranchId() { return $this->branchId; }
    public function setBranchId($branchId) { $this->branchId = $branchId; }
    public function getUserId() { return $this->userId; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function getSupplier() { return $this->supplier; }
    public function setSupplier($supplier) { $this->supplier = $supplier; }
    public function getCoffeeType() { return $this->coffeeType; }
    public function setCoffeeType($coffeeType) { $this->coffeeType = $coffeeType; }
    public function getProductType() { return $this->productType; }
    public function setProductType($productType) { $this->productType = $productType; }
    public function getProductionQuantitiesOfCoffeeKG() { return $this->productionQuantitiesOfCoffeeKG; }
    public function setProductionQuantitiesOfCoffeeKG($productionQuantitiesOfCoffeeKG) { $this->productionQuantitiesOfCoffeeKG = $productionQuantitiesOfCoffeeKG; }
    public function getPrCarbonEmissionsKG() { return $this->prCarbonEmissionsKG; }
    public function setPrCarbonEmissionsKG($prCarbonEmissionsKG) { $this->prCarbonEmissionsKG = $prCarbonEmissionsKG; }
    public function getActivityDate() { return $this->activityDate; }
    public function setActivityDate($activityDate) { $this->activityDate = $activityDate; }
    
    // Add compatibility method for older code that might still use getProductionDate
    public function getProductionDate() { return $this->activityDate; }
    public function setProductionDate($date) { $this->activityDate = $date; }
    
    // ... (similar getters/setters for all other properties)
}