<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set default CEO role for direct access if not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 19; // CEO user ID from database
    $_SESSION['user_name'] = 'Osama Hanafy';
    $_SESSION['user_role'] = 'CEO';
} 