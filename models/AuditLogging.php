<?php
namespace Models;

use DateTime;

class AuditLogging {
    private int $logId;
    private int $userId;
    private string $action;
    private string $tableName;
    private ?int $recordId;
    private DateTime $timestamp;

    public function __construct(int $logId, int $userId, string $action, 
                              string $tableName, ?int $recordId, DateTime $timestamp) {
        $this->logId = $logId;
        $this->userId = $userId;
        $this->action = $action;
        $this->tableName = $tableName;
        $this->recordId = $recordId;
        $this->timestamp = $timestamp;
    }

    // Getters and setters
    public function getLogId(): int { return $this->logId; }
    public function setLogId(int $logId): void { $this->logId = $logId; }
    public function getUserId(): int { return $this->userId; }
    public function setUserId(int $userId): void { $this->userId = $userId; }
    public function getAction(): string { return $this->action; }
    public function setAction(string $action): void { $this->action = $action; }
    public function getTableName(): string { return $this->tableName; }
    public function setTableName(string $tableName): void { $this->tableName = $tableName; }
    public function getRecordId(): ?int { return $this->recordId; }
    public function setRecordId(?int $recordId): void { $this->recordId = $recordId; }
    public function getTimestamp(): DateTime { return $this->timestamp; }
    public function setTimestamp(DateTime $timestamp): void { $this->timestamp = $timestamp; }
    
    // ... other getters/setters
}
?>