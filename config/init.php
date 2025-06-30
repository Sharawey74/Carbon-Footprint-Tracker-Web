<?php
/**
 * Initialization file for the CarbonTracker Web application
 */

// Load configuration
require_once __DIR__ . '/config.php';

// Define additional paths if not already defined
if (!defined('CONTROLLERS_PATH')) {
    define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
}

// Start or resume session
function init_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params(SESSION_LIFETIME);
        session_start();
    }
}

// Autoload classes
spl_autoload_register(function ($class_name) {
    // Convert namespace separators to directory separators
    $class_path = str_replace('\\', '/', $class_name);
    
    // Map namespace prefixes to directories
    $namespace_map = [
        'Models/' => ROOT_PATH . '/models/',
        'Dao/Interfaces/' => ROOT_PATH . '/dao/interfaces/',
        'Dao/Impl/' => ROOT_PATH . '/dao/impl/',
        'Exceptions/' => ROOT_PATH . '/exceptions/',
        'Services/' => ROOT_PATH . '/services/',
        'Utils/' => ROOT_PATH . '/utils/'
    ];
    
    // Check each namespace prefix
    foreach ($namespace_map as $prefix => $base_dir) {
        // If the class uses the namespace prefix
        if (strpos($class_path, $prefix) === 0) {
            // Get the relative class path
            $relative_class = substr($class_path, strlen($prefix));
            
            // Build the file path
            $file = $base_dir . $relative_class . '.php';
            
            // If the file exists, require it
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
    
    // For classes without namespace or not matching the map
    $possible_paths = [
        ROOT_PATH . '/models/' . $class_name . '.php',
        ROOT_PATH . '/dao/interfaces/' . $class_name . '.php',
        ROOT_PATH . '/dao/impl/' . $class_name . '.php',
        ROOT_PATH . '/services/' . $class_name . '.php',
        ROOT_PATH . '/utils/' . $class_name . '.php',
        ROOT_PATH . '/exceptions/' . $class_name . '.php'
    ];
    
    foreach ($possible_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Initialize session
init_session();

// Set up error handling
function exception_handler($exception) {
    echo "An error occurred: " . $exception->getMessage();
    // Log the error
    error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());
}

set_exception_handler('exception_handler');

// Load language files based on user preference or default
function load_language() {
    $language = isset($_SESSION['language']) ? $_SESSION['language'] : DEFAULT_LANGUAGE;
    require_once ROOT_PATH . '/i18n/' . $language . '.php';
}

// Initialize database connection
require_once ROOT_PATH . '/config/database.php';

// Initialize database connection global
$GLOBALS['db'] = database::getConnection();

// Load dependency container
require_once ROOT_PATH . '/utils/DependencyContainer.php';
require_once ROOT_PATH . '/config/container.php';

// Enable detailed error reporting in debug mode
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
?>