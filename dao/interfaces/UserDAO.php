<?php
/**
 * Interface for User data access operations
 */
namespace Dao\Interfaces;

use Exceptions\UserNotFoundException;
use Exceptions\DataAccessException;
use Models\User;

interface UserDAO extends DAO {
    /**
     * Get a user by email
     * 
     * @param string $email The email to search for
     * @return User The user object
     * @throws UserNotFoundException If the user is not found
     * @throws DataAccessException If a database error occurs
     */
    public function getByEmail(string $email);
    
    /**
     * Authenticate a user with email and password
     * 
     * @param string $email The user's email
     * @param string $password The user's password
     * @return bool True if authentication is successful, false otherwise
     * @throws DataAccessException If a database error occurs
     */
    public function authenticate(string $email, string $password): bool;
    
    /**
     * Update a user's password
     * 
     * @param int $userId The user ID
     * @param string $newPassword The new password
     * @return bool True if the update was successful
     * @throws DataAccessException If a database error occurs
     */
    public function updatePassword(int $userId, string $newPassword): bool;
}
?>