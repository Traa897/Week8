<?php
/**
 * MAIN INDEX WITH 3-ROLE AUTHENTICATION
 * File: index.php
 * 
 * Role-based access control:
 * - Developer: Full access to everything
 * - Admin: Manage products, categories, suppliers, customers, transactions, recycle bin
 * - User: Only shop view and their own transactions
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

// Extra check untuk mencegah redirect loop
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}

// Require login
Auth::requireLogin();

$controller = isset($_GET['c']) ? clean($_GET['c']) : 'dashboard';
$action = isset($_GET['a']) ? clean($_GET['a']) : 'index';

// ========================================
// ROLE-BASED ACCESS CONTROL (3 ROLES)
// ========================================

// Define access rules for each controller
$accessRules = [
    // Dashboard - berbeda per role
    'dashboard' => ['developer', 'admin', 'user'],
    
    // Shop - hanya untuk user
    'shop' => ['user'],
    
    // Products
    'products' => [
        'index' => ['developer', 'admin'],
        'create' => ['developer', 'admin'],
        'store' => ['developer', 'admin'],
        'edit' => ['developer', 'admin'],
        'update' => ['developer', 'admin'],
        'delete' => ['developer', 'admin']
    ],
    
    // Categories - admin & developer
    'categories' => ['developer', 'admin'],
    
    // Suppliers - admin & developer
    'suppliers' => ['developer', 'admin'],
    
    // Customers - admin & developer
    'customers' => ['developer', 'admin'],
    
    // Transactions
    'transactions' => [
        'index' => ['developer', 'admin'],
        'detail' => ['developer', 'admin'],
        'delete' => ['developer', 'admin'],
        'print' => ['developer', 'admin']
    ],
    
    // User Transactions - untuk user melihat transaksi mereka sendiri
    'mytransactions' => ['user'],
    
    // Checkout - user saja
    'checkout' => ['user'],
    
    // Profile - user untuk edit profil
    'profile' => ['user'],
    
    // Recycle Bin - admin & developer
    'recyclebin' => ['developer', 'admin'],
    
    // Users Management - developer only
    'users' => ['developer'],
    
    // Settings - developer only
    'settings' => ['developer']
];

// Check access
$hasAccess = false;

if ($controller == 'dashboard' || $controller == '') {
    $hasAccess = Auth::hasRole($accessRules['dashboard']);
} else {
    if (isset($accessRules[$controller])) {
        // Check if it's action-specific rules
        if (is_array($accessRules[$controller]) && isset($accessRules[$controller][$action])) {
            $hasAccess = Auth::hasRole($accessRules[$controller][$action]);
        } elseif (is_array($accessRules[$controller]) && !isset($accessRules[$controller][$action])) {
            // Action not explicitly defined, deny access
            $hasAccess = false;
        } else {
            $hasAccess = Auth::hasRole($accessRules[$controller]);
        }
    }
}

// Deny access if user doesn't have permission
if (!$hasAccess) {
    setFlash('danger', '❌ Anda tidak memiliki akses ke halaman ini. Role Anda: <strong>' . Auth::role() . '</strong>');
    redirect('index.php');
    exit;
}

// ========================================
// DASHBOARD - BERBEDA PER ROLE
// ========================================

if ($controller == 'dashboard' || $controller == '') {
    
    // USER ROLE: Redirect ke shop
    if (Auth::isUser()) {
        redirect('index.php?c=shop&a=index');
        exit;
    }
    
    // ADMIN & DEVELOPER: Show admin dashboard
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
    
} else {
    // ========================================
    // CONTROLLERS
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
}

$database->close();