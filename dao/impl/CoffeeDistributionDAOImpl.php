<?php
namespace Dao\Impl;

use PDO;
use Models\CoffeeDistribution;
use Exceptions\DataAccessException;
use Exceptions\UserNotFoundException;

class CoffeeDistributionDAOImpl implements \Dao\Interfaces\CoffeeDistributionDAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getById(int $id): ?CoffeeDistribution {
        $sql = "SELECT * FROM CoffeeDistribution WHERE DistributionID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() ? $this->mapDistribution($stmt->fetch(PDO::FETCH_ASSOC)) : null;
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get distribution: $id", 0, $e);
        }
    }

    public function getAll(): array {
        $sql = "SELECT * FROM CoffeeDistribution ORDER BY DistributionID DESC";
        try {
            $stmt = $this->db->query($sql);
            return array_map([$this, 'mapDistribution'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get distributions", 0, $e);
        }
    }

    public function save($distribution): bool {
        if (!($distribution instanceof CoffeeDistribution)) {
            throw new DataAccessException("Object must be an instance of CoffeeDistribution");
        }
        return $distribution->getDistributionId() ? $this->update($distribution) : $this->insert($distribution);
    }

    public function insert($distribution): bool {
        if (!($distribution instanceof CoffeeDistribution)) {
            throw new DataAccessException("Object must be an instance of CoffeeDistribution");
        }
        
        try {
            $this->db->beginTransaction();

            if (!$this->userExists($distribution->getUserId())) {
                throw new UserNotFoundException("User not found: " . $distribution->getUserId());
            }

            $sql = "INSERT INTO CoffeeDistribution (
                BranchID, UserID, VehicleType, NumberOfVehicles, 
                DistancePerVehicle_KM, TotalDistance_KM, FuelEfficiency, 
                V_CarbonEmissions_Kg, ActivityDate
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $distribution->getBranchId(),
                $distribution->getUserId(),
                $distribution->getVehicleType(),
                $distribution->getNumberOfVehicles(),
                $distribution->getDistancePerVehicleKM(),
                $distribution->getTotalDistanceKM(),
                $distribution->getFuelEfficiency(),
                $distribution->getVCarbonEmissionsKg(),
                $distribution->getActivityDate()->format('Y-m-d')
            ]);

            $distribution->setDistributionId($this->db->lastInsertId());
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Insert failed: " . $e->getMessage(), 0, $e);
        }
    }

    private function userExists(int $userId): bool {
        $sql = "SELECT 1 FROM User WHERE UserID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return (bool)$stmt->fetchColumn();
    }

    private function mapDistribution(array $row): CoffeeDistribution {
        return new CoffeeDistribution(
            (int)$row['DistributionID'],
            (int)$row['BranchID'],
            (int)$row['UserID'],
            $row['VehicleType'],
            (int)$row['NumberOfVehicles'],
            (float)$row['DistancePerVehicle_KM'],
            (float)$row['TotalDistance_KM'],
            (float)$row['FuelEfficiency'],
            (float)$row['V_CarbonEmissions_Kg'],
            new \DateTime($row['ActivityDate'])
        );
    }

    /**
     * @inheritDoc
     */
    public function getAverageDistanceByBranchId(int $branchId): float {
        $sql = "SELECT AVG(TotalDistance_KM) as avg_distance 
                FROM CoffeeDistribution 
                WHERE BranchID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            return (float)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get average distance: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getDistributionsByBranchId(int $branchId): array {
        $sql = "SELECT * FROM CoffeeDistribution WHERE BranchID = ? ORDER BY ActivityDate DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            return array_map([$this, 'mapDistribution'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get distributions by branch: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getDistributionsByVehicleType(string $vehicleType): array {
        $sql = "SELECT * FROM CoffeeDistribution WHERE VehicleType = ? ORDER BY ActivityDate DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$vehicleType]);
            return array_map([$this, 'mapDistribution'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get distributions by vehicle type: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getHighEmissionDistributions(float $thresholdKg): array {
        $sql = "SELECT * FROM CoffeeDistribution WHERE V_CarbonEmissions_Kg > ? ORDER BY V_CarbonEmissions_Kg DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$thresholdKg]);
            return array_map([$this, 'mapDistribution'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get high emission distributions: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getMostCommonVehicleTypeByBranchId(int $branchId): string {
        $sql = "SELECT VehicleType, COUNT(*) as count 
                FROM CoffeeDistribution 
                WHERE BranchID = ? 
                GROUP BY VehicleType 
                ORDER BY count DESC 
                LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['VehicleType'] : '';
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get most common vehicle type: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getTotalCarbonEmissionsByBranchId(int $branchId): float {
        $sql = "SELECT SUM(V_CarbonEmissions_Kg) as total_emissions 
                FROM CoffeeDistribution 
                WHERE BranchID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            return (float)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get total carbon emissions: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete($object): bool {
        if (!($object instanceof CoffeeDistribution)) {
            throw new DataAccessException("Object must be an instance of CoffeeDistribution");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "DELETE FROM CoffeeDistribution WHERE DistributionID = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$object->getDistributionId()]);
            
            $affected = $stmt->rowCount();
            $this->db->commit();
            
            return $affected > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Delete failed: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function update($object): bool {
        if (!($object instanceof CoffeeDistribution)) {
            throw new DataAccessException("Object must be an instance of CoffeeDistribution");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE CoffeeDistribution 
                    SET BranchID = ?, 
                        UserID = ?, 
                        VehicleType = ?, 
                        NumberOfVehicles = ?, 
                        DistancePerVehicle_KM = ?,
                        ActivityDate = ?
                    WHERE DistributionID = ?";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $object->getBranchId(),
                $object->getUserId(),
                $object->getVehicleType(),
                $object->getNumberOfVehicles(),
                $object->getDistancePerVehicleKM(),
                $object->getActivityDate()->format('Y-m-d'),
                $object->getDistributionId()
            ]);
            
            $affected = $stmt->rowCount();
            $this->db->commit();
            
            return $affected > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Update failed: " . $e->getMessage(), 0, $e);
        }
    }
}
?>