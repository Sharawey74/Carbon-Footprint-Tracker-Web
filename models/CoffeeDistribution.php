<?php
// CoffeeDistribution.php
namespace Models;
class CoffeeDistribution {
    private $distributionId;
    private $branchId;
    private $userId;
    private $vehicleType;
    private $numberOfVehicles;
    private $distancePerVehicleKM;
    private $totalDistanceKM;
    private $fuelEfficiency;
    private $vCarbonEmissionsKg;
    private $activityDate;

    public function __construct($distributionId, $branchId, $userId, $vehicleType,
                                $numberOfVehicles, $distancePerVehicleKM,
                                $totalDistanceKM, $fuelEfficiency, $vCarbonEmissionsKg,
                                $activityDate) {
        $this->distributionId = $distributionId;
        $this->branchId = $branchId;
        $this->userId = $userId;
        $this->vehicleType = $vehicleType;
        $this->numberOfVehicles = $numberOfVehicles;
        $this->distancePerVehicleKM = $distancePerVehicleKM;
        $this->totalDistanceKM = $totalDistanceKM;
        $this->fuelEfficiency = $fuelEfficiency;
        $this->vCarbonEmissionsKg = $vCarbonEmissionsKg;
        $this->activityDate = $activityDate;
    }

    // Getters and Setters
    public function getDistributionId() { return $this->distributionId; }
    public function setDistributionId($distributionId) { $this->distributionId = $distributionId; }
    public function getBranchId() { return $this->branchId; }
    public function setBranchId($branchId) { $this->branchId = $branchId; }
    public function getUserId() { return $this->userId; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function getVehicleType() { return $this->vehicleType; }
    public function setVehicleType($vehicleType) { $this->vehicleType = $vehicleType; }
    public function getNumberOfVehicles() { return $this->numberOfVehicles; }
    public function setNumberOfVehicles($numberOfVehicles) { $this->numberOfVehicles = $numberOfVehicles; }
    public function getDistancePerVehicleKM() { return $this->distancePerVehicleKM; }
    public function setDistancePerVehicleKM($distancePerVehicleKM) { $this->distancePerVehicleKM = $distancePerVehicleKM; }
    public function getTotalDistanceKM() { return $this->totalDistanceKM; }
    public function setTotalDistanceKM($totalDistanceKM) { $this->totalDistanceKM = $totalDistanceKM; }
    public function getFuelEfficiency() { return $this->fuelEfficiency; }
    public function setFuelEfficiency($fuelEfficiency) { $this->fuelEfficiency = $fuelEfficiency; }
    public function getVCarbonEmissionsKg() { return $this->vCarbonEmissionsKg; }
    public function setVCarbonEmissionsKg($vCarbonEmissionsKg) { $this->vCarbonEmissionsKg = $vCarbonEmissionsKg; }

    public function getActivityDate() { return $this->activityDate; }
    public function setActivityDate($activityDate) { $this->activityDate = $activityDate; }
    // Add compatibility method for older code that might still use getDistributionDate
    public function getDistributionDate() { return $this->activityDate; }
    public function setDistributionDate($date) { $this->activityDate = $date; }
}