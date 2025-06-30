<?php
namespace Models;

class BranchMetrics {
    private int $branchId;
    private float $carbonEmissionsKg;
    private float $packagingWasteKg;
    private float $productionQuantitiesKg;
    private int $numberOfEmployees;
    private string $location;

    public function __construct(
        int $branchId,
        float $carbonEmissionsKg,
        float $packagingWasteKg,
        float $productionQuantitiesKg,
        int $numberOfEmployees,
        string $location
    ) {
        $this->branchId = $branchId;
        $this->carbonEmissionsKg = $carbonEmissionsKg;
        $this->packagingWasteKg = $packagingWasteKg;
        $this->productionQuantitiesKg = $productionQuantitiesKg;
        $this->numberOfEmployees = $numberOfEmployees;
        $this->location = $location;
    }

    // Getters
    public function getBranchId(): int { return $this->branchId; }
    public function getCarbonEmissionsKg(): float { return $this->carbonEmissionsKg; }
    public function getPackagingWasteKg(): float { return $this->packagingWasteKg; }
    public function getProductionQuantitiesKg(): float { return $this->productionQuantitiesKg; }
    public function getNumberOfEmployees(): int { return $this->numberOfEmployees; }
    public function getLocation(): string { return $this->location; }

    // Setters
    public function setBranchId(int $branchId): void { $this->branchId = $branchId; }
    public function setCarbonEmissionsKg(float $carbonEmissionsKg): void { $this->carbonEmissionsKg = $carbonEmissionsKg; }
    public function setPackagingWasteKg(float $packagingWasteKg): void { $this->packagingWasteKg = $packagingWasteKg; }
    public function setProductionQuantitiesKg(float $productionQuantitiesKg): void { $this->productionQuantitiesKg = $productionQuantitiesKg; }
    public function setNumberOfEmployees(int $numberOfEmployees): void { $this->numberOfEmployees = $numberOfEmployees; }
    public function setLocation(string $location): void { $this->location = $location; }
}
?>