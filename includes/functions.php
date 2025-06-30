<?php
/**
 * Common utility functions for the Carbon Footprint Tracker application
 */

/**
 * Format a number with commas for thousands and specified decimal places
 * 
 * @param float $number The number to format
 * @param int $decimals Number of decimal places
 * @return string Formatted number
 */
function formatNumber($number, $decimals = 2) {
    return number_format($number, $decimals);
}

/**
 * Format a currency value
 * 
 * @param float $amount The amount to format
 * @param string $currency Currency symbol
 * @return string Formatted currency
 */
function formatCurrency($amount, $currency = '$') {
    return $currency . number_format($amount, 2);
}

/**
 * Safely get a value, returning a default if null
 * 
 * @param mixed $value The value to check
 * @param mixed $default Default value if null
 * @return mixed The value or default
 */
function safeValue($value, $default = 0) {
    return is_null($value) ? $default : $value;
}

/**
 * Generate a random color in rgba format
 * 
 * @param float $opacity The opacity value (0-1)
 * @return string RGBA color string
 */
function randomColor($opacity = 0.7) {
    return 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',' . $opacity . ')';
}

/**
 * Convert a date to a formatted string
 * 
 * @param string $date The date string
 * @param string $format The format string
 * @return string Formatted date
 */
function formatDate($date, $format = 'Y-m-d') {
    $dateObj = new DateTime($date);
    return $dateObj->format($format);
}

/**
 * Check if user has required role
 * 
 * @param string|array $requiredRoles Role or roles to check
 * @return bool True if user has required role
 */
function hasRole($requiredRoles) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    if (is_array($requiredRoles)) {
        return in_array($_SESSION['user_role'], $requiredRoles);
    } else {
        return $_SESSION['user_role'] === $requiredRoles;
    }
}

/**
 * Create a truncated version of text
 * 
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $append String to append if truncated
 * @return string Truncated text
 */
function truncateText($text, $length = 100, $append = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    
    return $text . $append;
}

/**
 * Get percentage change between two values
 * 
 * @param float $old Old value
 * @param float $new New value
 * @return float Percentage change
 */
function percentChange($old, $new) {
    if ($old == 0) {
        return $new > 0 ? 100 : 0;
    }
    
    return (($new - $old) / abs($old)) * 100;
} 