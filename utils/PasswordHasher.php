<?php
class PasswordHasher {
    /**
     * Hash a password
     * 
     * @param string $password The password to hash
     * @return string The hashed password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify a password against a hash
     * 
     * @param string $password The password to verify
     * @param string $storedHash The stored hash to compare against
     * @return bool True if the password is valid, false otherwise
     */
    public static function verifyPassword($password, $storedHash) {
        return password_verify($password, $storedHash);
    }
}
?>