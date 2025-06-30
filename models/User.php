<?php
namespace Models;

class User {
    public $userID;
    public $branchID;
    public $userName;
    public $userRole;
    public $userEmail;
    public $password;
    public $forcePasswordChange;

    public function __construct($data = null) {
        if ($data) {
            // Handle stdClass objects and arrays
            if (is_object($data)) {
                $this->userID = $data->UserID ?? $data->userID ?? null;
                $this->branchID = $data->BranchID ?? $data->branchID ?? null;
                $this->userName = $data->UserName ?? $data->userName ?? '';
                $this->userRole = $data->UserRole ?? $data->userRole ?? '';
                $this->userEmail = $data->UserEmail ?? $data->userEmail ?? '';
                $this->password = $data->Password ?? $data->password ?? '';
                $this->forcePasswordChange = $data->ForcePasswordChange ?? $data->forcePasswordChange ?? false;
            } elseif (is_array($data)) {
                $this->userID = $data['UserID'] ?? $data['userID'] ?? null;
                $this->branchID = $data['BranchID'] ?? $data['branchID'] ?? null;
                $this->userName = $data['UserName'] ?? $data['userName'] ?? '';
                $this->userRole = $data['UserRole'] ?? $data['userRole'] ?? '';
                $this->userEmail = $data['UserEmail'] ?? $data['userEmail'] ?? '';
                $this->password = $data['Password'] ?? $data['password'] ?? '';
                $this->forcePasswordChange = $data['ForcePasswordChange'] ?? $data['forcePasswordChange'] ?? false;
            }
            
            // Convert string boolean values to actual booleans
            if (is_string($this->forcePasswordChange)) {
                $this->forcePasswordChange = ($this->forcePasswordChange === '1' || 
                                           strtolower($this->forcePasswordChange) === 'true');
            } else if (is_numeric($this->forcePasswordChange)) {
                $this->forcePasswordChange = (bool)$this->forcePasswordChange;
            }
        }
    }
}
?>