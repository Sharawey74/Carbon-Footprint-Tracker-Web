<?php
namespace Exceptions;

class AuthorizationException extends \Exception {
    public function __construct(string $message = "Unauthorized access") {
        parent::__construct($message);
    }
}
?>