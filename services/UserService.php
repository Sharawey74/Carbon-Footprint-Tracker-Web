<?php
namespace Services;

use Dao\Interfaces\UserDAO;
use Models\User;
use Exceptions\DataAccessException;
use Exceptions\UserNotFoundException;
use Exception;

class UserService {
    private $userDao;

    public function __construct(UserDAO $userDao) {
        $this->userDao = $userDao;
    }

    /**
     * Authenticate a user with email and password
     * 
     * @param string $email User email
     * @param string $password User password
     * @return User Authenticated user
     * @throws UserNotFoundException If authentication fails
     * @throws DataAccessException If data access fails
     */
    public function authenticate($email, $password) {
        if ($this->userDao->authenticate($email, $password)) {
            return $this->userDao->getByEmail($email);
        }
        throw new UserNotFoundException("Invalid credentials");
    }

    /**
     * Get a user by ID
     * 
     * @param int $id User ID
     * @return User User with the specified ID
     * @throws DataAccessException If data access fails
     */
    public function getUserById($id) {
        return $this->userDao->getById($id);
    }

    /**
     * Get all users
     * 
     * @return array List of all users
     * @throws DataAccessException If data access fails
     */
    public function getAllUsers() {
        return $this->userDao->getAll();
    }

    /**
     * Save a user
     * 
     * @param User $user User to save
     * @return bool Success status
     * @throws DataAccessException If data access fails
     */
    public function saveUser(User $user) {
        try {
            $result = $this->userDao->save($user);
            if ($result) {
                return ['success' => true, 'message' => 'User saved successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to save user'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete a user
     * 
     * @param User $user User to delete
     * @return bool Success status
     * @throws DataAccessException If data access fails
     */
    public function deleteUser(User $user) {
        try {
            $result = $this->userDao->delete($user);
            if ($result) {
                return ['success' => true, 'message' => 'User deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete user'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get a user by branch ID
     * 
     * @param int $branchId Branch ID
     * @return User|null User assigned to the branch or null if none found
     * @throws DataAccessException If data access fails
     */
    public function getUserByBranch($branchId) {
        foreach ($this->userDao->getAll() as $user) {
            if ($user->branchID == $branchId) {
                return $user;
            }
        }
        return null;
    }
}