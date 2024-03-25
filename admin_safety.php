<?php
// Start output buffering
ob_start();

define('ADMIN_LOGIN', 'admin');
define('ADMIN_PASSWORD', 'admin');

// Check if the credentials are set and match
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])
    || ($_SERVER['PHP_AUTH_USER'] != ADMIN_LOGIN)
    || ($_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD)) {
    
    // If not, send the headers to prompt for login
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Our Blog"');
    
    // Use ob_end_clean() to discard the output buffer, ensuring no HTML is sent before this
    ob_end_clean();
    
    // Exit and send the denial message
    exit("Access Denied: Username and password required.");
}

// If authentication is successful, clear the buffer and continue
// This is where your script continues if authentication passes
ob_end_flush();

// The rest of your secure content would go here
?>
