<?php
namespace Exceptions;

class ExportException extends \Exception {
    public function __construct(string $message = "Export failed") {
        parent::__construct($message);
    }
}
?>