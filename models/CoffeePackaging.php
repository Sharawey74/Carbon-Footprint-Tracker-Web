<?php
// CoffeePackaging.php
namespace Models;
class CoffeePackaging {
    private $packagingId;
    private $branchId;
    private $userId;
    private $packagingWasteKG;
    private $paCarbonEmissionsKG;
    private $activityDate;

    public function __construct($packagingId, $branchId, $userId,
                                $packagingWasteKG, $paCarbonEmissionsKG,
                                $activityDate) {
        $this->packagingId = $packagingId;
        $this->branchId = $branchId;
        $this->userId = $userId;
        $this->packagingWasteKG = $packagingWasteKG;
        $this->paCarbonEmissionsKG = $paCarbonEmissionsKG;
        $this->activityDate = $activityDate;
    }

    public function __toString() {
        return "CoffeePackaging{" .
            "packagingId=" . $this->packagingId .
            ", branchId=" . $this->branchId .
            ", userId=" . $this->userId .
            ", packagingWasteKG=" . $this->packagingWasteKG .
            ", paCarbonEmissionsKG=" . $this->paCarbonEmissionsKG .
            ", activityDate=" . $this->activityDate->format('Y-m-d') .
            '}';
    }

    // Getters and Setters
    public function getPackagingId() { return $this->packagingId; }
    public function setPackagingId($packagingId) { $this->packagingId = $packagingId; }
    public function getBranchId() { return $this->branchId; }
    public function setBranchId($branchId) { $this->branchId = $branchId; }
    public function getUserId() { return $this->userId; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function getPackagingWasteKG() { return $this->packagingWasteKG; }
    public function setPackagingWasteKG($packagingWasteKG) { $this->packagingWasteKG = $packagingWasteKG; }
    public function getPaCarbonEmissionsKG() { return $this->paCarbonEmissionsKG; }
    public function setPaCarbonEmissionsKG($paCarbonEmissionsKG) { $this->paCarbonEmissionsKG = $paCarbonEmissionsKG; }
    public function getActivityDate() { return $this->activityDate; }
    public function setActivityDate($activityDate) { $this->activityDate = $activityDate; }
    // Add compatibility method for older code that might still use getPackagingDate
    public function getPackagingDate() { return $this->activityDate; }
    public function setPackagingDate($date) { $this->activityDate = $date; }
}