<?php
namespace Dao\Impl;

use PDO;
use Models\Branch;
use Models\BranchMetrics;
use Models\User;
use Dao\Interfaces\BranchDAO;
use Exceptions\DataAccessException;

class BranchDAOImpl implements BranchDAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getById(int $id): ?Branch {
        $sql = "SELECT * FROM Branch WHERE BranchID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() ? $this->mapBranch($stmt->fetch(PDO::FETCH_ASSOC)) : null;
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get branch: $id", 0, $e);
        }
    }

    public function getAll(): array {
        $sql = "SELECT * FROM Branch";
        try {
            $stmt = $this->db->query($sql);
            return array_map([$this, 'mapBranch'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get branches", 0, $e);
        }
    }

    public function save($branch): bool {
        return $branch->getBranchId() ? $this->update($branch) : $this->insert($branch);
    }

    public function insert($branch): bool {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO Branch (CityID, Location, NumberOfEmployees) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $branch->getCityId(),
                $branch->getLocation(),
                $branch->getNumberOfEmployees()
            ]);
            
            $branch->setBranchId($this->db->lastInsertId());
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Insert failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function update($branch): bool {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE Branch SET CityID=?, Location=?, NumberOfEmployees=? WHERE BranchID=?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $branch->getCityId(),
                $branch->getLocation(),
                $branch->getNumberOfEmployees(),
                $branch->getBranchId()
            ]);
            
            $this->db->commit();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Update failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function delete($branch): bool {
        return $this->deleteById($branch->getBranchId());
    }

    private function deleteById(int $id): bool {
        $sql = "DELETE FROM Branch WHERE BranchID=?";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            throw new DataAccessException("Delete failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function getBranchesByCityId(int $cityId): array {
        $sql = "SELECT * FROM Branch WHERE CityID=?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$cityId]);
            return array_map([$this, 'mapBranch'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get branches by city: $cityId", 0, $e);
        }
    }

    private function mapBranch(array $row): Branch {
        return new Branch(
            (int)$row['BranchID'],
            (int)$row['CityID'],
            $row['Location'],
            (int)$row['NumberOfEmployees']
        );
    }

    /**
     * Check if a branch exists by ID
     */
    public function branchExists(int $branchId): bool {
        try {
            $stmt = $this->db->prepare("SELECT 1 FROM Branch WHERE BranchID = ?");
            $stmt->execute([$branchId]);
            return $stmt->fetchColumn() ? true : false;
        } catch (\PDOException $e) {
            throw new DataAccessException("Error checking branch existence: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Count branches by city ID
     */
    public function countBranchesByCityId(int $cityId): int {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM Branch WHERE CityID = ?");
            $stmt->execute([$cityId]);
            return (int)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new DataAccessException("Error counting branches by city: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get branches with most employees
     */
    public function getBranchesWithMostEmployees(int $limit): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Branch ORDER BY NumberOfEmployees DESC LIMIT ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return array_map([$this, 'mapBranch'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Error getting branches with most employees: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get branch metrics
     */
    public function getBranchMetrics(int $branchId): BranchMetrics {
        try {
            // Get branch information
            $branchStmt = $this->db->prepare("SELECT * FROM Branch WHERE BranchID = ?");
            $branchStmt->execute([$branchId]);
            $branch = $branchStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$branch) {
                throw new DataAccessException("Branch not found: $branchId");
            }
            
            // Get production data
            $prodStmt = $this->db->prepare("
                SELECT SUM(ProductionQuantitiesOfCoffee_KG) as totalProduction,
                       SUM(Pr_CarbonEmissions_KG) as productionEmissions
                FROM CoffeeProduction 
                WHERE BranchID = ?
            ");
            $prodStmt->execute([$branchId]);
            $prodData = $prodStmt->fetch(PDO::FETCH_ASSOC);
            
            // Get packaging data
            $packStmt = $this->db->prepare("
                SELECT SUM(PackagingWaste_KG) as totalWaste,
                       SUM(Pa_CarbonEmissions_KG) as packagingEmissions
                FROM CoffeePackaging 
                WHERE BranchID = ?
            ");
            $packStmt->execute([$branchId]);
            $packData = $packStmt->fetch(PDO::FETCH_ASSOC);
            
            // Get distribution data
            $distStmt = $this->db->prepare("
                SELECT SUM(V_CarbonEmissions_Kg) as distributionEmissions
                FROM CoffeeDistribution 
                WHERE BranchID = ?
            ");
            $distStmt->execute([$branchId]);
            $distData = $distStmt->fetch(PDO::FETCH_ASSOC);
            
            // Calculate total emissions
            $totalEmissions = 
                (float)($prodData['productionEmissions'] ?? 0) + 
                (float)($packData['packagingEmissions'] ?? 0) + 
                (float)($distData['distributionEmissions'] ?? 0);
            
            return new BranchMetrics(
                $branchId,
                $totalEmissions,
                (float)($packData['totalWaste'] ?? 0),
                (float)($prodData['totalProduction'] ?? 0),
                (int)$branch['NumberOfEmployees'],
                $branch['Location']
            );
        } catch (\PDOException $e) {
            throw new DataAccessException("Error getting branch metrics: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get branch user (manager)
     */
    public function getBranchUser(int $branchId): User {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM User 
                WHERE BranchID = ? AND UserRole = 'BranchUser'
                LIMIT 1
            ");
            $stmt->execute([$branchId]);
            $userData = $stmt->fetch(PDO::FETCH_OBJ);
            
            if (!$userData) {
                throw new DataAccessException("Branch user not found for branch: $branchId");
            }
            
            return new User($userData);
        } catch (\PDOException $e) {
            throw new DataAccessException("Error getting branch user: " . $e->getMessage(), 0, $e);
        }
    }
}
?>