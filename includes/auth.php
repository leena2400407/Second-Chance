<?php
/**
 * Authentication and Session Helper
 * Second Chance University Portal
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if the user is currently logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if current user is an Admin (role = 'admin')
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Get current user data array
 */
function currentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    $fname = $_SESSION['fname'] ?? '';
    $lname = $_SESSION['lname'] ?? '';
    $fullName = trim($fname . ' ' . $lname);
    if (empty($fullName)) {
        $fullName = $_SESSION['name'] ?? 'User';
    }

    return [
        'id'       => $_SESSION['user_id'] ?? null,
        'fname'    => $fname,
        'lname'    => $lname,
        'name'     => $fullName,
        'email'    => $_SESSION['email'] ?? '',
        'phone'    => $_SESSION['phone'] ?? '',
        'role'     => $_SESSION['role'] ?? 'user',
        'is_admin' => isAdmin(),
    ];
}

/**
 * Require login guard
 */
function requireLogin($redirect = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect?error=login_required");
        exit();
    }
}

/**
 * Require admin guard (role = 'admin')
 */
function requireAdmin($redirect = 'login.php?type=admin') {
    requireLogin();
    if (!isAdmin()) {
        header("Location: $redirect?error=unauthorized_admin");
        exit();
    }
}

/**
 * Set flash message
 */
function setFlashMessage($key, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_' . $key] = $message;
}

/**
 * Get and clear flash message
 */
function getFlashMessage($key) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash_' . $key])) {
        $msg = $_SESSION['flash_' . $key];
        unset($_SESSION['flash_' . $key]);
        return $msg;
    }
    return null;
}
?>
