<?php
// Configuration for Google OAuth2 Authentication

define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');

// Mendeteksi protocol dan domain secara dinamis untuk Redirect URI
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1')) {
    $redirect_uri = $protocol . $_SERVER['HTTP_HOST'] . "/e-kost-system/modules/auth/google_auth.php";
} else {
    $redirect_uri = $protocol . $_SERVER['HTTP_HOST'] . "/modules/auth/google_auth.php";
}
define('GOOGLE_REDIRECT_URI', $redirect_uri);
?>
