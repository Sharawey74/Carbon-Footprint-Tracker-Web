<?php
/**
 * Exception thrown when a user is not found
 */
namespace Exceptions;

class UserNotFoundException extends DataAccessException {
    public function __construct($message = "User not found") {
        parent::__construct($message);
    }
}
?>