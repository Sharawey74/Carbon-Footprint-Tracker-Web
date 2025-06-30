<?php
// PlanStatus.php
namespace Models;

class PlanStatus {
    private $statusId;
    private $statusName;

    public function __construct($statusId, $statusName) {
        $this->statusId = $statusId;
        $this->statusName = $statusName;
    }

    // Getters and Setters
    public function getStatusId() { return $this->statusId; }
    public function setStatusId($statusId) { $this->statusId = $statusId; }
    public function getStatusName() { return $this->statusName; }
    public function setStatusName($statusName) { $this->statusName = $statusName; }
}