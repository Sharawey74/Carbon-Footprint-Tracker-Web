<?php
namespace Dao\Impl;

use PDO;
use Models\ReductionStrategy;
use Dao\Interfaces\ReductionStrategyDAO;
use Exceptions\DataAccessException;

class ReductionStrategyDAOImpl implements ReductionStrategyDAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getById(int $id): ?ReductionStrategy {
        $sql = "SELECT * FROM ReductionStrategy WHERE ReductionID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() ? $this->mapStrategy($stmt->fetch(PDO::FETCH_ASSOC)) : null;
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get reduction strategy: $id", 0, $e);
        }
    }

    public function getAll(): array {
        $sql = "SELECT * FROM ReductionStrategy ORDER BY ReductionID DESC";
        try {
            $stmt = $this->db->query($sql);
            return array_map([$this, 'mapStrategy'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get all reduction strategies", 0, $e);
        }
    }

    public function getByBranchId(int $branchId): array {
        $sql = "SELECT * FROM ReductionStrategy WHERE BranchID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            return array_map([$this, 'mapStrategy'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get reduction strategies by branch: $branchId", 0, $e);
        }
    }

    public function getTotalImplementationCostsByBranchId(int $branchId): float {
        $sql = "SELECT SUM(ImplementationCosts) as total_costs FROM ReductionStrategy WHERE BranchID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            return (float)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get total implementation costs: " . $e->getMessage(), 0, $e);
        }
    }

    public function getByStatusId(int $statusId): array {
        $sql = "SELECT * FROM ReductionStrategy WHERE StatusID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$statusId]);
            return array_map([$this, 'mapStrategy'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get reduction strategies by status: $statusId", 0, $e);
        }
    }

    public function save($strategy): bool {
        if (!($strategy instanceof ReductionStrategy)) {
            throw new DataAccessException("Object must be an instance of ReductionStrategy");
        }
        
        return $strategy->getReductionId() ? $this->update($strategy) : $this->insert($strategy);
    }

    public function insert($strategy): bool {
        if (!($strategy instanceof ReductionStrategy)) {
            throw new DataAccessException("Object must be an instance of ReductionStrategy");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO ReductionStrategy 
                    (BranchID, UserID, ReductionStrategy, StatusID, ImplementationCosts, ActivityDate) 
                    VALUES (?, ?, ?, ?, ?, ?)";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $strategy->getBranchId(),
                $strategy->getUserId(),
                $strategy->getStrategy(),
                $strategy->getStatusId(),
                $strategy->getImplementationCosts(),
                $strategy->getActivityDate()->format('Y-m-d')
            ]);
            
            $strategy->setReductionId($this->db->lastInsertId());
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Insert failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function update($strategy): bool {
        if (!($strategy instanceof ReductionStrategy)) {
            throw new DataAccessException("Object must be an instance of ReductionStrategy");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE ReductionStrategy 
                    SET BranchID = ?, 
                        UserID = ?, 
                        ReductionStrategy = ?, 
                        StatusID = ?, 
                        ImplementationCosts = ?, 
                        ActivityDate = ?
                    WHERE ReductionID = ?";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $strategy->getBranchId(),
                $strategy->getUserId(),
                $strategy->getStrategy(),
                $strategy->getStatusId(),
                $strategy->getImplementationCosts(),
                $strategy->getActivityDate()->format('Y-m-d'),
                $strategy->getReductionId()
            ]);
            
            $affected = $stmt->rowCount();
            $this->db->commit();
            
            return $affected > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Update failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function delete($strategy): bool {
        if (!($strategy instanceof ReductionStrategy)) {
            throw new DataAccessException("Object must be an instance of ReductionStrategy");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "DELETE FROM ReductionStrategy WHERE ReductionID = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$strategy->getReductionId()]);
            
            $affected = $stmt->rowCount();
            $this->db->commit();
            
            return $affected > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Delete failed: " . $e->getMessage(), 0, $e);
        }
    }

    private function mapStrategy(array $row): ReductionStrategy {
        return new ReductionStrategy(
            (int)$row['ReductionID'],
            (int)$row['BranchID'],
            (int)$row['UserID'],
            $row['ReductionStrategy'],
            (int)$row['StatusID'],
            (float)$row['ImplementationCosts'],
            (float)$row['ProjectedAnnualProfits'],
            new \DateTime($row['ActivityDate'])
        );
    }
}
?>