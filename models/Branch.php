<?php
namespace Models;

class Branch {
    private int $branchId;
    private int $cityId;
    private string $location;
    private int $numberOfEmployees;

    public function __construct(int $branchId = 0, int $cityId = 0, 
                               string $location = '', int $numberOfEmployees = 0) {
        $this->branchId = $branchId;
        $this->cityId = $cityId;
        $this->location = $location;
        $this->numberOfEmployees = $numberOfEmployees;
    }

    // Getters and setters
    public function getBranchId(): int { return $this->branchId; }
    public function setBranchId(int $branchId): void { $this->branchId = $branchId; }
    public function getCityId(): int { return $this->cityId; }
    public function setCityId(int $cityId): void { $this->cityId = $cityId; }
    public function getLocation(): string { return $this->location; }
    public function setLocation(string $location): void { $this->location = $location; }
    public function getNumberOfEmployees(): int { return $this->numberOfEmployees; }
    public function setNumberOfEmployees(int $numberOfEmployees): void { 
        $this->numberOfEmployees = $numberOfEmployees; 
    }
    
    /**
     * Get the branch name (alias for getLocation)
     * 
     * @return string The branch location/name
     */
    public function getBranchName(): string {
        return $this->location;
    }

    // ... other getters/setters
}
?>