<?php
class SessionManager {
    public static function startSession($user) {
        session_start();
        $_SESSION['user'] = [
            'id' => $user->userId,
            'role' => $user->userRole,
            'branchId' => $user->branchId,
            'email' => $user->userEmail
        ];
    }

    public static function isAuthenticated() {
        return isset($_SESSION['user']);
    }

    public static function getCurrentUser() {
        return $_SESSION['user'] ?? null;
    }

    public static function endSession() {
        session_unset();
        session_destroy();
    }
}
?>