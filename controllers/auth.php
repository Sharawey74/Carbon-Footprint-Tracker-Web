<?php
/**
 * Authentication controller for handling login, logout, and password management
 */

// Check if direct access
if (!defined('ROOT_PATH')) {
    die('Direct access not permitted');
}

// Include required files
use Services\UserService;
use Services\AuditLoggingService;
use Exceptions\UserNotFoundException;
use Exceptions\DataAccessException;
use Exceptions\AuthenticationException;
use Models\User;

/**
 * Display login form
 */
function login() {
    global $container;
    
    // If already logged in, redirect to appropriate dashboard
    if (isset($_SESSION['UserID'])) {
        redirectToDashboard($_SESSION['user_role']);
        exit;
    }

    // Check for database connection
    global $db;
    if (!$db) {
        // Display database error
        $pageTitle = 'Database Error';
        ob_start();
        echo '<div class="alert alert-danger">';
        echo '<h4>Database Connection Error</h4>';
        echo '<p>Could not connect to the database. Please check your database settings or contact system administrator.</p>';
        echo '</div>';
        $content = ob_get_clean();
        include VIEWS_PATH . '/templates/main_layout.php';
        exit;
    }

    // Process login form submission
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $userEmail = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        try {
            // Get services from container
            $userService = $container->get(UserService::class);
            
            // Try to authenticate the user
            $user = $userService->authenticate($userEmail, $password);
                
                // Check if password change required
                if ($user->forcePasswordChange) {
                    $_SESSION['temp_user_id'] = $user->userID;
                    $_SESSION['flash_message'] = 'You must change your password before continuing';
                    $_SESSION['flash_type'] = 'warning';
                header('Location: ' . APP_URL . '/?controller=auth&action=resetPassword');
                    exit;
                }
                
                // Set user session
                $_SESSION['user_id'] = $user->userID;
                $_SESSION['user_name'] = $user->userName;
                $_SESSION['user_role'] = $user->userRole;
                $_SESSION['branch_id'] = $user->branchID;
                
                // Log successful login
                logAuditAction($user->userID, 'LOGIN', 'User', $user->userID);
                
                // Redirect to dashboard
                redirectToDashboard($user->userRole);
                exit;
            
        } catch (AuthenticationException $e) {
                // Invalid login
            error_log("Authentication exception: " . $e->getMessage());
            showAlert('Invalid email or password', 'danger');
        } catch (Exception $e) {
            // Log more details about the error
            error_log("Login error: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());
            
            // Show a more specific error message for debugging if in debug mode
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                showAlert('Authentication error: ' . $e->getMessage(), 'danger');
            } else {
                showAlert('An error occurred during login. Please try again.', 'danger');
            }
        }
    }
    
    // Set language preference
    $isEnglish = $_SESSION['language'] ?? 'en';
    
    // Display login form directly without main layout
    $pageTitle = 'Login';
    header('Content-Type: text/html; charset=utf-8');
    ?><!DOCTYPE html>
<html lang="<?= $isEnglish ? 'en' : 'ar' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= SITE_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/styles.css">
</head>
<body>
    <?php include VIEWS_PATH . '/auth/login.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= APP_URL ?>/js/main.js"></script>
</body>
</html>
<?php
    exit;
}

/**
 * Log out the current user
 */
function logout() {
    if (isset($_SESSION['user_id'])) {
        // Log the logout action
        logAuditAction($_SESSION['user_id'], 'LOGOUT', 'User', $_SESSION['user_id']);
    }
    
    // Clear session data
    $_SESSION = array();
    
    // If a session cookie is used, destroy it
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Redirect to login page
    header('Location: ' . APP_URL . '/?controller=auth&action=login');
    exit;
}

/**
 * Display and process password reset form
 */
function resetPassword() {
    global $container;
    
    // Check if user is authenticated or has temp access
    $userID = $_SESSION['user_id'] ?? $_SESSION['temp_user_id'] ?? null;
    
    if (!$userID) {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    // Process form submission
    if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate passwords
        if (empty($newPassword) || $newPassword !== $confirmPassword) {
            showAlert('Passwords do not match or are empty', 'danger');
        } else {
            try {
                // Update password using service
                $userService = $container->get(UserService::class);
                
                if ($userService->updatePassword($userID, $newPassword)) {
                    // Log password change
                    logAuditAction($userID, 'UPDATE', 'User', $userID);
                    
                    showAlert('Password updated successfully', 'success');
                    
                    // If was temp access, redirect to login
                    if (isset($_SESSION['temp_user_id'])) {
                        unset($_SESSION['temp_user_id']);
                        header('Location: ' . APP_URL . '/?controller=auth&action=login');
                        exit;
                    }
                    
                    // Otherwise redirect to dashboard
                    redirectToDashboard($_SESSION['user_role']);
                    exit;
                } else {
                    showAlert('Failed to update password', 'danger');
                }
            } catch (Exception $e) {
                error_log("Password reset error: " . $e->getMessage());
                showAlert('An error occurred while updating password', 'danger');
            }
        }
    }
    
    // Display password reset form
    $pageTitle = 'Reset Password';
    ob_start();
    include VIEWS_PATH . '/auth/reset_password.php';
    $content = ob_get_clean();
    include VIEWS_PATH . '/templates/main_layout.php';
}

/**
 * Toggle language between English and Arabic
 */
function toggleLanguage() {
    // If language is not set, default to English
    if (!isset($_SESSION['language'])) {
        $_SESSION['language'] = 'en';
    }
    
    // Toggle language
    $_SESSION['language'] = ($_SESSION['language'] === 'en') ? 'ar' : 'en';
    
    // Redirect back to previous page
    $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/';
    header('Location: ' . $referer);
    exit;
}

/**
 * Generate a random password
 * 
 * @param int $length Length of password (default: 10)
 * @return string Random password
 */
function generateRandomPassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
    $password = '';
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    
    return $password;
}

/**
 * Force password reset for a user
 */
function forcePasswordReset() {
    global $container;
    
    // Check if user is logged in and has admin privileges
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['OPManager', 'CIO', 'CEO'])) {
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
        exit;
    }
    
    // Get user ID from request
    $targetUserId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    
    if (!$targetUserId) {
        showAlert('Invalid user ID', 'danger');
        header('Location: ' . $_SERVER['HTTP_REFERER'] ?? APP_URL . '/');
        exit;
    }
    
    try {
        // Get user service
        $userService = $container->get(UserService::class);
        
        // Generate new random password
        $newPassword = generateRandomPassword();
        
        // Update user password and set force change flag
        if ($userService->resetUserPassword($targetUserId, $newPassword, true)) {
            // Log password reset
            logAuditAction($_SESSION['user_id'], 'RESET_PASSWORD', 'User', $targetUserId);
            
            showAlert('Password reset successfully. Temporary password: ' . $newPassword, 'success');
        } else {
            showAlert('Failed to reset password', 'danger');
        }
    } catch (Exception $e) {
        error_log("Password reset error: " . $e->getMessage());
        showAlert('An error occurred while resetting password', 'danger');
    }
    
    // Redirect back to previous page
    header('Location: ' . $_SERVER['HTTP_REFERER'] ?? APP_URL . '/');
    exit;
}

/**
 * Display alert message
 * 
 * @param string $message Message to display
 * @param string $type Alert type (success, info, warning, danger)
 */
function showAlert($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Set language for the application
 * 
 * @param string $language Language code ('en' or 'ar')
 */
function setLanguage($language) {
    $_SESSION['language'] = $language;
    
    // Redirect back to previous page
    $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/';
    header('Location: ' . $referer);
    exit;
}

/**
 * Helper function to redirect to the appropriate dashboard based on user role
 */
function redirectToDashboard($role) {
    $redirectMap = [
        'BranchUser' => APP_URL . '/?controller=branch&action=dashboard',
        'OPManager' => APP_URL . '/?controller=op_manager&action=dashboard',
        'CIO' => APP_URL . '/?controller=cio&action=dashboard',
        'CEO' => APP_URL . '/?controller=ceo&action=dashboard'
    ];
    
    if (isset($redirectMap[$role])) {
        error_log("Redirecting user with role $role to " . $redirectMap[$role]);
        header('Location: ' . $redirectMap[$role]);
    } else {
        error_log("Unknown role $role, redirecting to login");
        header('Location: ' . APP_URL . '/?controller=auth&action=login');
    }
    exit;
}

/**
 * Helper function to log audit actions
 */
function logAuditAction($userID, $action, $tableName, $recordID = null) {
    global $container;
    
    try {
        $auditService = $container->get(AuditLoggingService::class);
        $auditService->logAction($userID, $action, $tableName, $recordID);
    } catch (Exception $e) {
        error_log("Audit logging error: " . $e->getMessage());
    }
}
?>