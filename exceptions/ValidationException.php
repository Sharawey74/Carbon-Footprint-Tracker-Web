<?php
namespace Exceptions;

class ValidationException extends \Exception {
    public function __construct(string $message = "Validation failed") {
        parent::__construct($message);
    }
}
?>