<?php
namespace Dao\Interfaces;

use Models\Notification;
use Exceptions\DataAccessException;

interface NotificationDAO extends DAO {
    public function getNotificationsByUserId(int $userId): array;
    public function getUnreadNotificationsByUserId(int $userId): array;
    public function markAsRead(int $notificationId): bool;
    public function deleteAllReadNotifications(int $userId): int;
    public function markAllAsRead(int $userId): int;
}
?>