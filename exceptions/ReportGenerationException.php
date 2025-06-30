<?php
namespace Exceptions;

class ReportGenerationException extends \Exception {
    public function __construct(string $message = "Report generation failed") {
        parent::__construct($message);
    }
}
?>