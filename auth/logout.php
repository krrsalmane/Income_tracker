<!-- <?php
session_start();

// Destroy all session data
$_SESSION = array();

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Destroy session
session_destroy();

// Redirect to login
header("Location: login.php");
exit();
?> 