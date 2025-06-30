<?php
namespace Dao\Impl;

use PDO;
use Models\AuditLogging;
use Exceptions\DataAccessException;

class AuditLoggingDAOImpl implements \Dao\Interfaces\AuditLoggingDAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function insert($log): bool {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO AuditLogging 
                    (UserID, Action, TableName, RecordID, Timestamp)
                    VALUES (?, ?, ?, ?, ?)";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $log->getUserId(),
                $log->getAction(),
                $log->getTableName(),
                $log->getRecordId(),
                $log->getTimestamp()->format('Y-m-d H:i:s')
            ]);
            
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Insert failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function getById(int $id): ?AuditLogging {
        $sql = "SELECT * FROM AuditLogging WHERE LogID = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->rowCount() ? $this->mapLog($stmt->fetch(PDO::FETCH_ASSOC)) : null;
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get log: $id", 0, $e);
        }
    }

    public function getLogsByDateRange(\DateTime $start, \DateTime $end): array {
        $sql = "SELECT * FROM AuditLogging 
                WHERE Timestamp BETWEEN ? AND ?
                ORDER BY Timestamp DESC";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s')
            ]);
            return array_map([$this, 'mapLog'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Date range query failed", 0, $e);
        }
    }

    private function mapLog(array $row): AuditLogging {
        return new AuditLogging(
            (int)$row['LogID'],
            (int)$row['UserID'],
            $row['Action'],
            $row['TableName'],
            $row['RecordID'] !== null ? (int)$row['RecordID'] : null,
            \DateTime::createFromFormat('Y-m-d H:i:s', $row['Timestamp'])
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteAllLogs(): bool {
        try {
            $this->db->beginTransaction();
            
            $sql = "DELETE FROM AuditLogging";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Failed to delete all logs: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteLogsOlderThan(\DateTime $timestamp): bool {
        try {
            $this->db->beginTransaction();
            
            $sql = "DELETE FROM AuditLogging WHERE Timestamp < ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$timestamp->format('Y-m-d H:i:s')]);
            
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Failed to delete logs older than specified timestamp: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getLogsByAction(string $action): array {
        $sql = "SELECT * FROM AuditLogging 
                WHERE Action = ?
                ORDER BY Timestamp DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$action]);
            return array_map([$this, 'mapLog'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get logs by action: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getLogsByUserId(int $userId): array {
        $sql = "SELECT * FROM AuditLogging 
                WHERE UserID = ?
                ORDER BY Timestamp DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return array_map([$this, 'mapLog'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get logs by user ID: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete($object): bool {
        if (!($object instanceof AuditLogging)) {
            throw new DataAccessException("Object must be an instance of AuditLogging");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "DELETE FROM AuditLogging WHERE LogID = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$object->getLogId()]);
            
            $affected = $stmt->rowCount();
            $this->db->commit();
            
            return $affected > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Failed to delete log: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array {
        $sql = "SELECT * FROM AuditLogging ORDER BY Timestamp DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return array_map([$this, 'mapLog'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get all logs: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function save($object): bool {
        if (!($object instanceof AuditLogging)) {
            throw new DataAccessException("Object must be an instance of AuditLogging");
        }
        
        // If the object has an ID, update it; otherwise, insert it
        if ($object->getLogId() > 0) {
            return $this->update($object);
        } else {
            return $this->insert($object);
        }
    }

    /**
     * @inheritDoc
     */
    public function update($object): bool {
        if (!($object instanceof AuditLogging)) {
            throw new DataAccessException("Object must be an instance of AuditLogging");
        }
        
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE AuditLogging 
                    SET UserID = ?, 
                        Action = ?, 
                        TableName = ?, 
                        RecordID = ?, 
                        Timestamp = ?
                    WHERE LogID = ?";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $object->getUserId(),
                $object->getAction(),
                $object->getTableName(),
                $object->getRecordId(),
                $object->getTimestamp()->format('Y-m-d H:i:s'),
                $object->getLogId()
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