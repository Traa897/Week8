<?php
/**
 * MAIN INDEX - FIXED ACCESS CONTROL
 * File: index.php
 */

session_start();
define('BASE_PATH', __DIR__ . '/motor_modif_shop/');

require_once BASE_PATH . 'helpers/functions.php';
require_once BASE_PATH . 'helpers/Validator.php';
require_once BASE_PATH . 'helpers/Sanitizer.php';
require_once BASE_PATH . 'helpers/DateHelper.php';
require_once BASE_PATH . 'helpers/Csrf.php';
require_once BASE_PATH . 'helpers/Auth.php';
require_once BASE_PATH . 'config/database.php';

$database = new Database();
$db = $database->getConnection();
Auth::init($db);

// Check login - FIXED: lebih strict
if (!Auth::check()) {
    header('Location: login.php');
    exit;
}

// Get controller & action
$controller = isset($_GET['c']) ? clean($_GET['c']) : 'dashboard';
$action = isset($_GET['a']) ? clean($_GET['a']) : 'index';

// ========================================
// ROLE-BASED ACCESS CONTROL - FIXED
// ========================================

$accessRules = [
    // Dashboard - semua role bisa akses
    'dashboard' => ['developer', 'admin', 'user'],
    
    // Shop - HANYA USER
    'shop' => ['user'],
    
    // Products - Developer & Admin
    'products' => ['developer', 'admin'],
    
    // Categories - Developer & Admin (FIXED)
    'categories' => ['developer', 'admin'],
    
    // Suppliers - Developer & Admin (FIXED)
    'suppliers' => ['developer', 'admin'],
    
    // Customers - Developer & Admin (FIXED)
    'customers' => ['developer', 'admin'],
    
    // Transactions - Developer & Admin
    'transactions' => ['developer', 'admin'],
    
    // User Transactions - HANYA USER
    'mytransactions' => ['user'],
    
    // Checkout - HANYA USER
    'checkout' => ['user'],
    
    // Profile - HANYA USER
    'profile' => ['user'],
    
    // Recycle Bin - Developer & Admin (FIXED)
    'recyclebin' => ['developer', 'admin'],
    
    // Users Management - HANYA DEVELOPER
    'users' => ['developer'],
    
    // Settings - HANYA DEVELOPER
    'settings' => ['developer']
];

// ========================================
// CHECK ACCESS - FIXED LOGIC
// ========================================

$hasAccess = false;
$currentRole = Auth::role();

// Dashboard special handling
if ($controller == 'dashboard' || $controller == '') {
    $hasAccess = true; // Semua role bisa akses dashboard
} else {
    // Check controller access
    if (isset($accessRules[$controller])) {
        $allowedRoles = $accessRules[$controller];
        
        // Check if current role is in allowed roles
        if (in_array($currentRole, $allowedRoles)) {
            $hasAccess = true;
        }
    }
}

// DENY ACCESS if user doesn't have permission
if (!$hasAccess) {
    setFlash('danger', '❌ Anda tidak memiliki akses ke halaman ini. Role Anda: <strong>' . $currentRole . '</strong>');
    redirect('index.php');
    exit;
}

// ========================================
// DASHBOARD ROUTING - FIXED
// ========================================

if ($controller == 'dashboard' || $controller == '') {
    
    // USER: Redirect ke shop (FIXED - pastikan tidak loop)
    if (Auth::isUser()) {
        // Cek apakah sudah di shop
        if (!isset($_GET['c']) || $_GET['c'] !== 'shop') {
            header('Location: index.php?c=shop&a=index');
            exit;
        }
    }
    
    // ADMIN & DEVELOPER: Show admin dashboard
    if (Auth::isAdmin() || Auth::isDeveloper()) {
        require_once BASE_PATH . 'models/Product.php';
        require_once BASE_PATH . 'models/Customer.php';
        require_once BASE_PATH . 'models/Transaction.php';
        
        $productModel = new Product($db);
        $customerModel = new Customer($db);
        $transactionModel = new Transaction($db);
        
        $totalProducts = $productModel->count('');
        $totalCustomers = $customerModel->count('');
        $totalTransactions = $transactionModel->count('');
        
        $sql = "SELECT SUM(total_amount) as total FROM transaksi WHERE status = 'completed'";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        $totalRevenue = $row['total'] ?? 0;
        
        include BASE_PATH . 'views/layouts/header.php';
        include BASE_PATH . 'views/dashboard.php';
        include BASE_PATH . 'views/layouts/footer.php';
        
        $database->close();
        exit;
    }
}

// ========================================
// CONTROLLERS - FIXED
// ========================================

$controllerFile = BASE_PATH . 'controllers/' . ucfirst($controller) . 'Controller.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    $controllerClass = ucfirst($controller) . 'Controller';
    $controllerObject = new $controllerClass($db);
    
    if (method_exists($controllerObject, $action)) {
        $controllerObject->$action();
    } else {
        die("Action '$action' tidak ditemukan di controller '$controller'");
    }
} else {
    die("Controller '$controller' tidak ditemukan");
}

$database->close();