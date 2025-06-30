<?php
// Start output buffering to prevent 'headers already sent' errors
ob_start();

/**
 * Main entry point for the Carbon Footprint Tracker application
 */

// Load initialization file
require_once __DIR__ . '/../config/init.php';

// Debug session information without affecting it
error_log('Session status: ' . session_status());
error_log('Session data: ' . print_r($_SESSION, true));

// If accessing root URL with no parameters, redirect to login page
if (empty($_GET)) {
    header('Location: ' . APP_URL . '/?controller=auth&action=login');
    exit;
}

// Default controller and action
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'auth';
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Route to appropriate controller
switch ($controller) {
    case 'auth':
        require_once CONTROLLERS_PATH . '/auth.php';
        break;
    case 'branch':
        require_once CONTROLLERS_PATH . '/branch_data_entry.php';
        break;
    case 'op_manager':
        require_once CONTROLLERS_PATH . '/op_manager_dashboard.php';
        break;
    case 'cio':
        require_once CONTROLLERS_PATH . '/cio_dashboard.php';
        break;
    case 'ceo':
        require_once CONTROLLERS_PATH . '/ceo_dashboard.php';
        break;
    case 'report':
        require_once CONTROLLERS_PATH . '/report_controller.php';
        break;
    default:
        require_once CONTROLLERS_PATH . '/auth.php';
        break;
}


// Call the appropriate action function if it exists
if (function_exists($action)) {
    call_user_func($action);
} else {

    // Default to login if action doesn't exist
    header('Location: ' . APP_URL . '/?controller=auth&action=login');
    exit;
}

// End output buffering
ob_end_flush();
?>