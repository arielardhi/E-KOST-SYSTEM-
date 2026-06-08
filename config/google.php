<?php
// Configuration for Google OAuth2 Authentication

// Masukkan Client ID dan Client Secret dari Google Developer Console Anda
define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');

// Mendeteksi protocol dan domain secara dinamis untuk Redirect URI
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$redirect_uri = $protocol . $_SERVER['HTTP_HOST'] . "/e-kost-system/modules/auth/google_auth.php";
define('GOOGLE_REDIRECT_URI', $redirect_uri);
?>
