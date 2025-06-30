<?php
namespace Dao\Impl;

use PDO;
use Models\PlanStatus;
use Dao\Interfaces\PlanStatusDAO;
use Exceptions\DataAccessException;

class PlanStatusDAOImpl implements PlanStatusDAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getPlanStatusesByBranchId(int $branchId): array {
        $sql = "SELECT ps.* FROM PlanStatus ps
                JOIN ReductionStrategy rs ON ps.StatusID = rs.StatusID
                WHERE rs.BranchID = ?";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            return array_map([$this, 'mapStatus'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Branch status query failed", 0, $e);
        }
    }

    private function mapStatus(array $row): PlanStatus {
        return new PlanStatus(
            (int)$row['StatusID'],
            $row['StatusName']
        );
    }

    // Implement base DAO methods
    public function getById(int $id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM PlanStatus WHERE StatusID = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                return null;
            }
            
            return $this->mapStatus($stmt->fetch(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get plan status by ID: $id", 0, $e);
        }
    }

    public function getAll(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM PlanStatus ORDER BY StatusID");
            return array_map([$this, 'mapStatus'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get all plan statuses", 0, $e);
        }
    }

    public function save($status): bool {
        return $status->getStatusId() ? $this->update($status) : $this->insert($status);
    }

    public function insert($status): bool {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO PlanStatus (StatusName) VALUES (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status->getStatusName()]);
            
            // Update the status object with the new ID
            $status->setStatusId($this->db->lastInsertId());
            
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Failed to insert plan status", 0, $e);
        }
    }

    public function update($status): bool {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE PlanStatus SET StatusName = ? WHERE StatusID = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $status->getStatusName(),
                $status->getStatusId()
            ]);
            
            $this->db->commit();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Failed to update plan status", 0, $e);
        }
    }

    public function delete($status): bool {
        try {
            // Check if the status is in use
            $checkStmt = $this->db->prepare("SELECT COUNT(*) FROM ReductionStrategy WHERE StatusID = ?");
            $checkStmt->execute([$status->getStatusId()]);
            if ($checkStmt->fetchColumn() > 0) {
                throw new DataAccessException("Cannot delete plan status that is in use");
            }
            
            $stmt = $this->db->prepare("DELETE FROM PlanStatus WHERE StatusID = ?");
            $stmt->execute([$status->getStatusId()]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to delete plan status", 0, $e);
        }
    }
}
?>