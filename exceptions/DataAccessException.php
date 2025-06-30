<?php
/**
 * Exception thrown when there is an error accessing data
 */
namespace Exceptions;

class DataAccessException extends \Exception {
    public function __construct($message = "Data access error", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
?>