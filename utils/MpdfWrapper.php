<?php
namespace Utils;

use Mpdf\Mpdf;

/**
 * MpdfWrapper
 * 
 * A wrapper for the mPDF library that suppresses errors and fixes common issues
 */
class MpdfWrapper {
    /**
     * Create a new mPDF instance with error suppression
     * 
     * @param array $config mPDF configuration
     * @return Mpdf
     */
    public static function create($config = []) {
        return MpdfErrorSuppressor::run(function() use ($config) {
            // Manually load QrCode classes if needed
            self::ensureQrCodeClasses();
            return new Mpdf($config);
        });
    }
    
    /**
     * Run any mPDF method with error suppression
     * 
     * @param Mpdf $mpdf The mPDF instance
     * @param string $method The method to call
     * @param array $args The arguments to pass
     * @return mixed
     */
    public static function call(Mpdf $mpdf, $method, $args = []) {
        return MpdfErrorSuppressor::run(function() use ($mpdf, $method, $args) {
            // Fix specific variables before calling methods
            self::fixVariables($mpdf);
            return call_user_func_array([$mpdf, $method], $args);
        });
    }
    
    /**
     * Ensure QrCode classes are loaded
     */
    private static function ensureQrCodeClasses() {
        // Only create these fake classes if they don't exist and are needed
        if (!class_exists('\\Mpdf\\QrCode\\QrCode')) {
            eval('namespace Mpdf\\QrCode { class QrCode { 
                public function __construct($content = "", $errorLevel = "") {}
                public function disableBorder() {}
            }}');
        }
        
        if (!class_exists('\\Mpdf\\QrCode\\Output\\Mpdf')) {
            eval('namespace Mpdf\\QrCode\\Output { class Mpdf { 
                public function output($qrCode, $mpdf) {}
            }}');
        }
    }
    
    /**
     * Fix variables in mPDF instance
     * 
     * @param Mpdf $mpdf The mPDF instance
     */
    private static function fixVariables($mpdf) {
        // Use reflection to fix common undefined variables
        $reflection = new \ReflectionClass($mpdf);
        
        // Try to access protected properties and initialize them if needed
        try {
            // Fix $bcor variable
            $bcorProperty = self::getPropertyIfExists($reflection, 'bcor');
            if ($bcorProperty && !isset($mpdf->bcor)) {
                $bcorProperty->setAccessible(true);
                $bcorProperty->setValue($mpdf, []);
            }
            
            // Fix $saved_block1 variable
            $savedBlock1Property = self::getPropertyIfExists($reflection, 'saved_block1');
            if ($savedBlock1Property && !isset($mpdf->saved_block1)) {
                $savedBlock1Property->setAccessible(true);
                $savedBlock1Property->setValue($mpdf, []);
            }
            
            // Fix $info variable
            $infoProperty = self::getPropertyIfExists($reflection, 'info');
            if ($infoProperty && !isset($mpdf->info)) {
                $infoProperty->setAccessible(true);
                $infoProperty->setValue($mpdf, ['i' => 0, 'w' => 0, 'h' => 0]);
            }
            
            // Fix textshadow array
            $textshadowProperty = self::getPropertyIfExists($reflection, 'textshadow');
            if ($textshadowProperty) {
                $textshadowProperty->setAccessible(true);
                $value = $textshadowProperty->getValue($mpdf);
                if (!is_array($value)) {
                    $textshadowProperty->setValue($mpdf, []);
                }
            }
        } catch (\Exception $e) {
            // Ignore reflection errors
        }
    }
    
    /**
     * Get a property from reflection if it exists
     * 
     * @param \ReflectionClass $reflection
     * @param string $propertyName
     * @return \ReflectionProperty|null
     */
    private static function getPropertyIfExists(\ReflectionClass $reflection, $propertyName) {
        try {
            return $reflection->getProperty($propertyName);
        } catch (\ReflectionException $e) {
            return null;
        }
    }
} 