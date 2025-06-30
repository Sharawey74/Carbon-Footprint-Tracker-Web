<?php
namespace Dao\Impl;

use PDO;
use Models\User;
use Dao\Interfaces\UserDAO;
use Exceptions\UserNotFoundException;
use Exceptions\DataAccessException;

class UserDAOImpl implements UserDAO {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getByEmail(string $email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM User WHERE UserEmail = ?");
            $stmt->execute([$email]);
            $userData = $stmt->fetch();
            
            if (!$userData) {
                throw new UserNotFoundException("User with email $email not found");
            }
            
            // Debug the user data
            error_log("User data from DB: " . print_r($userData, true));
            
            // Create a PHP object to properly pass to the User constructor
            $userObj = new \stdClass();
            foreach ($userData as $key => $value) {
                $userObj->$key = $value;
            }
            
            return new User($userObj);
        } catch (\PDOException $e) {
            error_log("Database error in getByEmail: " . $e->getMessage());
            throw new DataAccessException("Error retrieving user by email");
        }
    }

    public function authenticate(string $email, string $password): bool {
        try {
            $stmt = $this->db->prepare("SELECT * FROM User WHERE UserEmail = ?");
            $stmt->execute([$email]);
            $userData = $stmt->fetch();
            
            // Debug information
            error_log("Authentication attempt for email: $email");
            error_log("User found: " . ($userData ? "Yes" : "No"));
            
            if (!$userData) {
                return false;
            }
            
            // Check if we're using hashed passwords
            if (strlen($userData['Password']) > 40) { // Likely a hashed password
                return \PasswordHasher::verifyPassword($password, $userData['Password']);
            } else {
                // Direct password comparison for legacy passwords
                error_log("Using direct password comparison");
                return $password === $userData['Password'];
            }
            
        } catch (\PDOException $e) {
            error_log("Database error in authenticate: " . $e->getMessage());
            throw new DataAccessException("Error during authentication");
        }
    }

    public function updatePassword(int $userId, string $newPassword): bool {
        try {
            // Hash the password using the updated PasswordHasher
            $hashedPassword = \PasswordHasher::hashPassword($newPassword);
            
            // Update statement without salt
            $stmt = $this->db->prepare("UPDATE User SET Password = ?, ForcePasswordChange = 0 WHERE UserID = ?");
            $stmt->execute([$hashedPassword, $userId]);
            
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Database error in updatePassword: " . $e->getMessage());
            throw new DataAccessException("Error updating password");
        }
    }

    // Implement required DAO interface methods
    public function getById(int $id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM User WHERE UserID = ?");
            $stmt->execute([$id]);
            $userData = $stmt->fetch();
            
            if (!$userData) {
                throw new UserNotFoundException("User with ID $id not found");
            }
            
            // Create a PHP object to properly pass to the User constructor
            $userObj = new \stdClass();
            foreach ($userData as $key => $value) {
                $userObj->$key = $value;
            }
            
            return new User($userObj);
        } catch (\PDOException $e) {
            error_log("Database error in getById: " . $e->getMessage());
            throw new DataAccessException("Error retrieving user by ID");
        }
    }

    public function getAll(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM User");
            $usersData = $stmt->fetchAll();
            
            $result = [];
            foreach ($usersData as $userData) {
                // Create a PHP object to properly pass to the User constructor
                $userObj = new \stdClass();
                foreach ($userData as $key => $value) {
                    $userObj->$key = $value;
                }
                
                $result[] = new User($userObj);
            }
            
            return $result;
        } catch (\PDOException $e) {
            error_log("Database error in getAll: " . $e->getMessage());
            throw new DataAccessException("Error retrieving all users");
        }
    }

    public function save($user): bool {
        return $user->userID ? $this->update($user) : $this->insert($user);
    }

    public function insert($user): bool {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("INSERT INTO User (BranchID, UserName, UserRole, UserEmail, Password, ForcePasswordChange) 
                VALUES (?, ?, ?, ?, ?, ?)");
                
            $stmt->execute([
                $user->branchID,
                $user->userName,
                $user->userRole,
                $user->userEmail,
                $user->password,
                $user->forcePasswordChange ? 1 : 0
            ]);
            
            $user->userID = $this->db->lastInsertId();
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Database error in insert: " . $e->getMessage());
            throw new DataAccessException("Error inserting user");
        }
    }

    public function update($user): bool {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("UPDATE User SET 
                BranchID = ?, 
                UserName = ?, 
                UserRole = ?, 
                UserEmail = ?, 
                Password = ?, 
                ForcePasswordChange = ? 
                WHERE UserID = ?");
                
            $stmt->execute([
                $user->branchID,
                $user->userName,
                $user->userRole,
                $user->userEmail,
                $user->password,
                $user->forcePasswordChange ? 1 : 0,
                $user->userID
            ]);
            
            $this->db->commit();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Database error in update: " . $e->getMessage());
            throw new DataAccessException("Error updating user");
        }
    }

    public function delete($user): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM User WHERE UserID = ?");
            $stmt->execute([$user->userID]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Database error in delete: " . $e->getMessage());
            throw new DataAccessException("Error deleting user");
        }
    }
}
?>