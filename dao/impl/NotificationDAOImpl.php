<?php
namespace Dao\Impl;

use PDO;
use Models\Notification;
use Dao\Interfaces\NotificationDAO;
use Exceptions\DataAccessException;

class NotificationDAOImpl implements NotificationDAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function markAsRead(int $id): bool {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE Notification SET IsRead = TRUE 
                    WHERE NotificationID = ?";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            
            $this->db->commit();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Mark read failed: $id", 0, $e);
        }
    }

    public function getUnreadNotificationsByUserId(int $userId): array {
        $sql = "SELECT * FROM Notification 
                WHERE UserID = ? AND IsRead = FALSE
                ORDER BY Timestamp DESC";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return array_map([$this, 'mapNotification'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Unread query failed", 0, $e);
        }
    }

    public function getNotificationsByUserId(int $userId): array {
        $sql = "SELECT * FROM Notification 
                WHERE UserID = ?
                ORDER BY Timestamp DESC";
                
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            return array_map([$this, 'mapNotification'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Get notifications query failed", 0, $e);
        }
    }

    public function deleteAllReadNotifications(int $userId): int {
        try {
            $this->db->beginTransaction();
            
            $sql = "DELETE FROM Notification 
                    WHERE UserID = ? AND IsRead = TRUE";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            
            $rowCount = $stmt->rowCount();
            $this->db->commit();
            return $rowCount;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Delete read notifications failed", 0, $e);
        }
    }

    public function markAllAsRead(int $userId): int {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE Notification SET IsRead = TRUE 
                    WHERE UserID = ? AND IsRead = FALSE";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            
            $rowCount = $stmt->rowCount();
            $this->db->commit();
            return $rowCount;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Mark all as read failed", 0, $e);
        }
    }

    private function mapNotification(array $row): Notification {
        return new Notification(
            (int)$row['NotificationID'],
            (int)$row['UserID'],
            $row['Message'],
            \DateTime::createFromFormat('Y-m-d H:i:s', $row['Timestamp']),
            (bool)$row['IsRead']
        );
    }

    // Implement base DAO methods
    public function getById(int $id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM Notification WHERE NotificationID = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                return null;
            }
            
            return $this->mapNotification($stmt->fetch(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get notification by ID: $id", 0, $e);
        }
    }

    public function getAll(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM Notification ORDER BY Timestamp DESC");
            return array_map([$this, 'mapNotification'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to get all notifications", 0, $e);
        }
    }

    public function save($notification): bool {
        return $notification->getNotificationId() ? $this->update($notification) : $this->insert($notification);
    }

    public function insert($notification): bool {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO Notification (UserID, Message, Timestamp, IsRead) 
                    VALUES (?, ?, ?, ?)";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $notification->getUserId(),
                $notification->getMessage(),
                $notification->getTimestamp()->format('Y-m-d H:i:s'),
                $notification->isRead() ? 1 : 0
            ]);
            
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Failed to insert notification", 0, $e);
        }
    }

    public function update($notification): bool {
        try {
            $this->db->beginTransaction();
            
            $sql = "UPDATE Notification 
                    SET UserID = ?, Message = ?, Timestamp = ?, IsRead = ? 
                    WHERE NotificationID = ?";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $notification->getUserId(),
                $notification->getMessage(),
                $notification->getTimestamp()->format('Y-m-d H:i:s'),
                $notification->isRead() ? 1 : 0,
                $notification->getNotificationId()
            ]);
            
            $this->db->commit();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new DataAccessException("Failed to update notification", 0, $e);
        }
    }

    public function delete($notification): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM Notification WHERE NotificationID = ?");
            $stmt->execute([$notification->getNotificationId()]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw new DataAccessException("Failed to delete notification", 0, $e);
        }
    }
}
?>