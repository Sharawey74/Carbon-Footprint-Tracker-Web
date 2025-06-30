<?php
namespace Dao\Impl;

use PDO;
use Models\CoffeePackaging;
use Exceptions\DataAccessException;

class CoffeePackagingDAOImpl implements \Dao\Interfaces\CoffeePackagingDAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getById(int $id): ?CoffeePackaging {
        $sql = "SELECT * FROM CoffeePackaging WHERE PackagingID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() ? $this->mapPackaging($stmt->fetch(PDO::FETCH_ASSOC)) : null;
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get packaging: $id", 0, $e);
        }
    }

    public function getAll(): array {
        $sql = "SELECT * FROM CoffeePackaging ORDER BY PackagingID DESC";
        try {
            $stmt = $this->db->query($sql);
            return array_map([$this, 'mapPackaging'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get all packaging data", 0, $e);
        }
    }

    public function getPackagingWasteByBranchId(int $branchId): array {
        $sql = "SELECT * FROM CoffeePackaging WHERE BranchID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            return array_map([$this, 'mapPackaging'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get packaging: $branchId", 0, $e);
        }
    }

    public function getTotalCarbonEmissionsByBranchId(int $branchId): float {
        $sql = "SELECT SUM(Pa_CarbonEmissions_KG) as total_emissions 
                FROM CoffeePackaging 
                WHERE BranchID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$branchId]);
            return (float)$stmt->fetchColumn();
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get total carbon emissions: " . $e->getMessage(), 0, $e);
        }
    }

    public function save($object): bool {
        if (!($object instanceof CoffeePackaging)) {
            throw new DataAccessException("Object must be an instance of CoffeePackaging");
        }
        
        return $object->getPackagingId() ? $this->update($object) : $this->insert($object);
    }

    public function insert($object): bool {
        if (!($object instanceof CoffeePackaging)) {
            throw new DataAccessException("Object must be an instance of CoffeePackaging");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO CoffeePackaging 
                    (BranchID, UserID, PackagingWaste_KG, Pa_CarbonEmissions_KG, ActivityDate) 
                    VALUES (?, ?, ?, ?, ?)";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $object->getBranchId(),
                $object->getUserId(),
                $object->getPackagingWasteKG(),
                $object->getPaCarbonEmissionsKG(),
                $object->getActivityDate()->format('Y-m-d')
            ]);
            
            $object->setPackagingId($this->db->lastInsertId());
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Insert failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function update($object): bool {
        if (!($object instanceof CoffeePackaging)) {
            throw new DataAccessException("Object must be an instance of CoffeePackaging");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE CoffeePackaging 
                    SET BranchID = ?, 
                        UserID = ?, 
                        PackagingWaste_KG = ?,
                        ActivityDate = ?
                    WHERE PackagingID = ?";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $object->getBranchId(),
                $object->getUserId(),
                $object->getPackagingWasteKG(),
                $object->getActivityDate()->format('Y-m-d'),
                $object->getPackagingId()
            ]);
            
            $affected = $stmt->rowCount();
            $this->db->commit();
            
            return $affected > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Update failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function delete($object): bool {
        if (!($object instanceof CoffeePackaging)) {
            throw new DataAccessException("Object must be an instance of CoffeePackaging");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "DELETE FROM CoffeePackaging WHERE PackagingID = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$object->getPackagingId()]);
            
            $affected = $stmt->rowCount();
            $this->db->commit();
            
            return $affected > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Delete failed: " . $e->getMessage(), 0, $e);
        }
    }

    private function mapPackaging(array $row): CoffeePackaging {
        return new CoffeePackaging(
            (int)$row['PackagingID'],
            (int)$row['BranchID'],
            (int)$row['UserID'],
            (float)$row['PackagingWaste_KG'],
            (float)$row['Pa_CarbonEmissions_KG'],
            new \DateTime($row['ActivityDate'])
        );
    }
}
?>