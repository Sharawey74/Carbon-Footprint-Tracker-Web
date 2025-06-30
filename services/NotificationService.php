<?php
namespace Services;

use Dao\Interfaces\NotificationDAO;
use Models\Notification;
use Exceptions\DataAccessException;

class NotificationService {
    private $notificationDao;
    private $languageService;

    public function __construct(NotificationDAO $notificationDao, LanguageService $languageService) {
        $this->notificationDao = $notificationDao;
        $this->languageService = $languageService;
    }

    /**
     * @throws DataAccessException
     */
    public function getUserNotifications($userId) {
        return $this->notificationDao->getNotificationsByUserId($userId);
    }

    /**
     * Get unread notifications for a user
     * 
     * @param int $userId User ID
     * @return array List of unread notifications
     * @throws DataAccessException
     */
    public function getUnreadNotifications($userId) {
        return $this->notificationDao->getUnreadNotificationsByUserId($userId);
    }

    /**
     * @throws DataAccessException
     */
    public function markAsRead($notificationId) {
        return $this->notificationDao->markAsRead($notificationId);
    }

    /**
     * Mark all notifications as read for a user
     * 
     * @param int $userId User ID
     * @return int Number of notifications marked as read
     * @throws DataAccessException
     */
    public function markAllAsRead($userId) {
        return $this->notificationDao->markAllAsRead($userId);
    }

    /**
     * Send a notification
     * 
     * @param Notification $notification Notification to send
     * @return bool Success status
     * @throws DataAccessException
     */
    public function sendNotification(Notification $notification) {
        return $this->notificationDao->save($notification);
    }

    /**
     * @throws DataAccessException
     */
    public function sendThresholdAlert($userId, $thresholdType, $value, $branchId) {
        $messageKey = "threshold.alert." . $thresholdType;
        $localizedMessage = $this->languageService->getMessage($messageKey);
        $message = sprintf($localizedMessage, $branchId, $value);
        
        $notification = new Notification(
            null,
            $userId,
            $message,
            new \DateTime(),
            false
        );
        
        return $this->notificationDao->save($notification);
    }
}