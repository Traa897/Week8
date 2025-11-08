<?php
/**
 * LOGOUT HANDLER
 * File: logout.php
 */

session_start();
define('BASE_PATH', __DIR__ . '/motor_modif_shop/');

require_once BASE_PATH . 'helpers/Auth.php';
require_once BASE_PATH . 'helpers/functions.php';

Auth::logout();
setFlash('success', '✅ Anda berhasil logout. Sampai jumpa!');
redirect('login.php');