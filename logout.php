<?php
/**
 * LOGOUT HANDLER - FIXED
 * File: logout.php
 */

session_start();
session_unset();
session_destroy();
session_write_close();

// Hapus cookie session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Redirect ke login dengan fresh session
header('Location: login.php?fresh=1');
exit;