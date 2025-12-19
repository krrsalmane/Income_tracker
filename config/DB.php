<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$db_server = "localhost";
$db_user = "root";
$db_password = "root@123";
$db_name = "finance_tracker";

$conn = mysqli_connect($db_server, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set UTF-8 encoding
mysqli_set_charset($conn, "utf8mb4");

// ========================================
// AUTHENTICATION FUNCTIONS
// ========================================

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user info
 */
function getCurrentUser($conn) {
    if (!isLoggedIn()) {
        return null;
    }
    
    $user_id = getCurrentUserId();
    $sql = "SELECT id, full_name, email, user_unique_id FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_fetch_assoc($result);
}

/**
 * Redirect to login page
 */
function redirectToLogin() {
    header("Location: auth/login.php");
    exit();
}

/**
 * Require login (protect pages)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirectToLogin();
    }
}

// ========================================
// HELPER FUNCTIONS
// ========================================

/**
 * Clean user input
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate 6-digit OTP
 */
function generateOTP() {
    return sprintf("%06d", mt_rand(0, 999999));
}

/**
 * Generate unique user ID
 */
function generateUniqueUserId($conn) {
    do {
        $unique_id = 'USR' . str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        
        $sql = "SELECT id FROM users WHERE user_unique_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $unique_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
    } while (mysqli_num_rows($result) > 0);
    
    return $unique_id;
}

/**
 * Get user IP address
 */
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

/**
 * Send email (for development, logs to error_log)
 */
function sendEmail($to, $subject, $message) {
    // For development - log the OTP
    error_log("===== EMAIL =====");
    error_log("To: $to");
    error_log("Subject: $subject");
    error_log("Message: $message");
    error_log("=================");
    
    return true;
}

/**
 * Display flash messages
 */
function displayFlashMessage() {
    if (isset($_SESSION['success_message'])) {
        echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">';
        echo $_SESSION['success_message'];
        echo '</div>';
        unset($_SESSION['success_message']);
    }
    
    if (isset($_SESSION['error_message'])) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">';
        echo $_SESSION['error_message'];
        echo '</div>';
        unset($_SESSION['error_message']);
    }
}

/**
 * Format money
 */
function formatMoney($amount) {
    return number_format($amount, 2, '.', ',');
}
?>