<?php
/**
 * LOGIN PAGE - FIXED
 * File: login.php
 */

// CLEAR OLD SESSION FIRST (Fix untuk redirect loop)
if (isset($_GET['fresh'])) {
    session_start();
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(), '', time()-3600, '/');
    header('Location: login.php');
    exit;
}

session_start();

// Regenerate session ID untuk keamanan
if (!isset($_SESSION['session_started'])) {
    session_regenerate_id(true);
    $_SESSION['session_started'] = true;
}

define('BASE_PATH', __DIR__ . '/motor_modif_shop/');

require_once BASE_PATH . 'config/database.php';
require_once BASE_PATH . 'helpers/Auth.php';
require_once BASE_PATH . 'helpers/Csrf.php';
require_once BASE_PATH . 'helpers/functions.php';

$database = new Database();
$db = $database->getConnection();
Auth::init($db);

// Redirect if already logged in (dengan extra check)
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Handle login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF token. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = 'Username dan password harus diisi';
        } else {
            $result = Auth::login($username, $password);
            
            if ($result['success']) {
                // Regenerate session ID setelah login untuk keamanan
                session_regenerate_id(true);
                
                setFlash('success', 'Selamat datang, ' . ($_SESSION['full_name'] ?? 'User') . '!');
                
                // Redirect dengan absolute path
                header('Location: index.php');
                exit;
            } else {
                $error = $result['message'];
            }
        }
    }
}

$database->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Patra Jaya Variasi</title>
    <?= Csrf::metaTag() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ffffffff 0%rgba(255, 255, 255, 1)29 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            max-width: 450px;
            width: 100%;
        }
            .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #4877d4ff 0%, #4877d4ff 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 4rem;
            margin-bottom: 15px;
        }
        .login-header h2 {
            margin: 0;
            font-weight: bold;
        }
        .login-body {
            padding: 40px 30px;
        }
        .form-control {
            padding: 12px 20px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #4877d4ff;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #4877d4ff 0%, #4877d4ff 100%);
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
            border: none;
            width: 100%;
            transition: transform 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            color: white;
        }
        .input-group-text {
            border: 2px solid #e0e0e0;
            border-right: none;
            background: white;
            border-radius: 10px 0 0 10px;
        }
        .form-control.with-icon {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .demo-accounts {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .demo-accounts h6 {
            color: #344aaaff;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .demo-account {
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid;
            cursor: pointer;
            transition: all 0.3s;
        }
        .demo-account:hover {
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .demo-account.developer { border-left-color: #dc3545; }
        .demo-account.admin { border-left-color: #ffc107; }
        .demo-account.user { border-left-color: #198754; }
        .demo-account small {
            display: block;
            color: #6c757d;
            margin-top: 3px;
        }
        .clear-session-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 0.85rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: all 0.3s;
        }
        .clear-session-link:hover {
            background: #bb2d3b;
            color: white;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-motorcycle"></i>
                <h2>Patra Jaya Variasi</h2>
                <p class="mb-0">Tempatnya Modif Motor #1</p>
            </div>
            
            <div class="login-body">
                <h4 class="text-center mb-4">Login ke Sistem</h4>
                
                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <?= Csrf::field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" name="username" id="username" class="form-control with-icon" 
                                   placeholder="Masukkan username" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="password" class="form-control with-icon" 
                                   placeholder="Masukkan password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted mb-0">
                        Belum punya akun? 
                        <a href="register.php" class="text-decoration-none fw-bold">Daftar Sekarang</a>
                    </p>
                </div>
                
                <!-- DEMO ACCOUNTS -->
                <div class="demo-accounts">
                    <h6><i class="fas fa-key"></i> Akun Demo</h6>
                    <p class="text-muted small mb-3">Klik untuk mengisi otomatis (Password: <strong>password</strong>)</p>
                    
                    <div class="demo-account developer" onclick="fillLogin('developer', 'password')">
                        <strong><i class="fas fa-code"></i> Developer</strong>
                        <small>Full access - Manage system, users, settings</small>
                    </div>
                    
                    <div class="demo-account admin" onclick="fillLogin('admin', 'password')">
                        <strong><i class="fas fa-user-shield"></i> Admin</strong>
                        <small>Manage products, categories, suppliers, transactions</small>
                    </div>
                    
                    <div class="demo-account user" onclick="fillLogin('user', 'password')">
                        <strong><i class="fas fa-shopping-cart"></i> User</strong>
                        <small>View products & create transactions</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-white small mb-0">
                &copy; 2024 Patra Jaya Variasi. Powered by Patra Ananda 1061
            </p>
        </div>
    </div>
    
    <!-- Emergency Clear Session Button -->
    <a href="login.php?fresh=1" class="clear-session-link" title="Klik ini jika stuck di redirect loop">
        <i class="fas fa-sync-alt"></i> Reset Session
    </a>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function fillLogin(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
            document.getElementById('username').focus();
        }
    </script>
</body>
</html>