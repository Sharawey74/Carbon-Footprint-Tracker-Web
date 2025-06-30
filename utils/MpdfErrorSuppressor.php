<?php
namespace Utils;

/**
 * MpdfErrorSuppressor
 * 
 * A utility class to suppress errors from mPDF vendor library
 */
class MpdfErrorSuppressor {
    private static $originalErrorReporting;
    private static $originalErrorHandler;
    
    /**
     * Start error suppression for mPDF
     */
    public static function start() {
        self::$originalErrorReporting = error_reporting();
        // Suppress all notices and warnings
        error_reporting(E_ERROR | E_PARSE);
        
        // Save original error handler and set custom one
        self::$originalErrorHandler = set_error_handler([self::class, 'errorHandler']);
    }
    
    /**
     * Restore original error reporting
     */
    public static function end() {
        error_reporting(self::$originalErrorReporting);
        
        // Restore original error handler
        if (self::$originalErrorHandler) {
            set_error_handler(self::$originalErrorHandler);
        } else {
            restore_error_handler();
        }
    }
    
    /**
     * Custom error handler to suppress specific errors
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline) {
        // Check if error is from mPDF
        if (strpos($errfile, 'mpdf') !== false) {
            // Suppress all undefined variable errors
            if (strpos($errstr, 'Undefined variable') !== false) {
                return true;
            }
            
            // Suppress type errors
            if (strpos($errstr, 'expects') !== false && 
                (strpos($errstr, 'array') !== false || 
                 strpos($errstr, 'iterable') !== false ||
                 strpos($errstr, 'object') !== false ||
                 strpos($errstr, 'string') !== false)) {
                return true;
            }
            
            // Suppress class not found errors
            if (strpos($errstr, 'Class') !== false && strpos($errstr, 'not found') !== false) {
                return true;
            }
        }
        
        // Let PHP handle other errors
        return false;
    }
    
    /**
     * Use mPDF with error suppression
     * 
     * @param callable $callback Function that uses mPDF
     * @return mixed The return value from the callback
     */
    public static function run(callable $callback) {
        self::start();
        try {
            $result = $callback();
            self::end();
            return $result;
        } catch (\Exception $e) {
            self::end();
            throw $e;
        }
    }
} 