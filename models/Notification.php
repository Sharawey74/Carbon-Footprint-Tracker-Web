<?php
// Notification.php
namespace Models;

use DateTime;

class Notification {
    private $notificationId;
    private $userId;
    private $message;
    private $timestamp;
    private $isRead;

    public function __construct($notificationId = null, $userId = null, $message = null, $timestamp = null, $isRead = false) {
        $this->notificationId = $notificationId;
        $this->userId = $userId;
        $this->message = $message;
        $this->timestamp = $timestamp ?? new DateTime();
        $this->isRead = $isRead;
    }

    // Full constructor
    public static function createFull($notificationId, $userId, $message, DateTime $timestamp, $isRead) {
        $instance = new self();
        $instance->notificationId = $notificationId;
        $instance->userId = $userId;
        $instance->message = $message;
        $instance->timestamp = $timestamp;
        $instance->isRead = $isRead;
        return $instance;
    }

    // Partial constructor (replicating Java behavior)
    public static function createPartial($userId, $message, DateTime $timestamp, $isRead) {
        $instance = new self();
        $instance->userId = $userId;
        // Note: Original Java constructor doesn't set other parameters
        return $instance;
    }

    // Getters and Setters
    public function getNotificationId() { return $this->notificationId; }
    public function setNotificationId($notificationId) { $this->notificationId = $notificationId; }
    public function getUserId() { return $this->userId; }
    public function setUserId($userId) { $this->userId = $userId; }
    public function getMessage() { return $this->message; }
    public function setMessage($message) { $this->message = $message; }
    public function getTimestamp() { return $this->timestamp; }
    public function setTimestamp(DateTime $timestamp) { $this->timestamp = $timestamp; }
    public function isRead() { return $this->isRead; }
    public function setIsRead($isRead) { $this->isRead = $isRead; }
}