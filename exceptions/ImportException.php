<?php
namespace Exceptions;

class ImportException extends \Exception {
    public function __construct(string $message = "Import failed") {
        parent::__construct($message);
    }
}
?>