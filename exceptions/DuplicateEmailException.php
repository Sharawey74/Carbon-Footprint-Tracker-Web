<?php
namespace Exceptions;

class DuplicateEmailException extends \Exception {
    public function __construct(string $message = "Email already exists") {
        parent::__construct($message);
    }
}
?>