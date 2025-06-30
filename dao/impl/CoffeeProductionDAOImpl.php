<?php
namespace Dao\Impl;

use PDO;
use Models\CoffeeProduction;
use Exceptions\DataAccessException;
use Exceptions\UserNotFoundException;

class CoffeeProductionDAOImpl implements \Dao\Interfaces\CoffeeProductionDAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getById(int $id): ?CoffeeProduction {
        $sql = "SELECT * FROM CoffeeProduction WHERE ProductionID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() ? $this->mapProduction($stmt->fetch(PDO::FETCH_ASSOC)) : null;
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get production: $id", 0, $e);
        }
    }

    public function getAll(): array {
        $sql = "SELECT * FROM CoffeeProduction";
        try {
            $stmt = $this->db->query($sql);
            return array_map([$this, 'mapProduction'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get productions", 0, $e);
        }
    }

    public function save($production): bool {
        if (!($production instanceof CoffeeProduction)) {
            throw new DataAccessException("Object must be an instance of CoffeeProduction");
        }
        return $production->getProductionId() ? $this->update($production) : $this->insert($production);
    }

    public function insert($production): bool {
        if (!($production instanceof CoffeeProduction)) {
            throw new DataAccessException("Object must be an instance of CoffeeProduction");
        }
        
        try {
            $this->db->beginTransaction();

            // Validate required fields
            if ($production->getBranchId() <= 0 || $production->getUserId() <= 0) {
                throw new DataAccessException("Invalid branch or user ID");
            }

            // Check user exists
            if (!$this->userExists($production->getUserId())) {
                throw new UserNotFoundException("User not found: " . $production->getUserId());
            }

            $sql = "INSERT INTO CoffeeProduction (
                BranchID, UserID, Supplier, CoffeeType, ProductType, 
                ProductionQuantitiesOfCoffee_KG, Pr_CarbonEmissions_KG, ActivityDate
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $production->getBranchId(),
                $production->getUserId(),
                $production->getSupplier(),
                $production->getCoffeeType(),
                $production->getProductType(),
                $production->getProductionQuantitiesOfCoffeeKG(),
                $production->getPrCarbonEmissionsKG(),
                $production->getActivityDate()->format('Y-m-d')
            ]);

            $production->setProductionId($this->db->lastInsertId());
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Insert failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function update($production): bool {
        if (!($production instanceof CoffeeProduction)) {
            throw new DataAccessException("Object must be an instance of CoffeeProduction");
        }
        
        try {
            $this->db->beginTransaction();
            
            // Avoid updating the calculated columns
            $sql = "UPDATE CoffeeProduction SET
                BranchID = ?, UserID = ?, Supplier = ?, CoffeeType = ?,
                ProductType = ?, ProductionQuantitiesOfCoffee_KG = ?,
                ActivityDate = ?
                WHERE ProductionID = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $production->getBranchId(),
                $production->getUserId(),
                $production->getSupplier(),
                $production->getCoffeeType(),
                $production->getProductType(),
                $production->getProductionQuantitiesOfCoffeeKG(),
                $production->getActivityDate()->format('Y-m-d'),
                $production->getProductionId()
            ]);

            $this->db->commit();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Update failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function delete($production): bool {
        if (!($production instanceof CoffeeProduction)) {
            throw new DataAccessException("Object must be an instance of CoffeeProduction");
        }
        return $this->deleteById($production->getProductionId());
    }

    private function deleteById(int $id): bool {
        $sql = "DELETE FROM CoffeeProduction WHERE ProductionID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            throw new DataAccessException("Delete failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function getProductionQuantitiesByBranchId(int $branchId): array {
        $sql = "SELECT * FROM CoffeeProduction WHERE BranchID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            return array_map([$this, 'mapProduction'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get productions by branch: $branchId", 0, $e);
        }
    }

    public function getTotalCarbonEmissionsByBranchId(int $branchId): float {
        $sql = "SELECT SUM(Pr_CarbonEmissions_KG) as total_emissions 
                FROM CoffeeProduction 
                WHERE BranchID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            return (float)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get total carbon emissions: " . $e->getMessage(), 0, $e);
        }
    }

    public function getProductionByCoffeeType(string $coffeeType): array {
        $sql = "SELECT * FROM CoffeeProduction WHERE CoffeeType = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$coffeeType]);
            return array_map([$this, 'mapProduction'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get productions by coffee type: " . $e->getMessage(), 0, $e);
        }
    }

    public function getByBranchId(int $branchId): CoffeeProduction {
        $sql = "SELECT * FROM CoffeeProduction WHERE BranchID = ? ORDER BY ActivityDate DESC LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            
            if ($stmt->rowCount() === 0) {
                throw new DataAccessException("No production found for branch: $branchId");
            }
            
            return $this->mapProduction($stmt->fetch(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get production by branch: " . $e->getMessage(), 0, $e);
        }
    }

    private function userExists(int $userId): bool {
        $sql = "SELECT 1 FROM User WHERE UserID = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return (bool)$stmt->fetchColumn();
    }

    private function mapProduction(array $row): CoffeeProduction {
        return new CoffeeProduction(
            (int)$row['ProductionID'],
            (int)$row['BranchID'],
            (int)$row['UserID'],
            $row['Supplier'],
            $row['CoffeeType'],
            $row['ProductType'],
            (float)$row['ProductionQuantitiesOfCoffee_KG'],
            (float)$row['Pr_CarbonEmissions_KG'],
            new \DateTime($row['ActivityDate'])
        );
    }

    // Implement other interface methods...
}
?>