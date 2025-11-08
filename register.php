<?php
/**
 * REGISTER PAGE
 * File: register.php
 */

session_start();
define('BASE_PATH', __DIR__ . '/motor_modif_shop/');

require_once BASE_PATH . 'config/database.php';
require_once BASE_PATH . 'helpers/Auth.php';
require_once BASE_PATH . 'helpers/Csrf.php';
require_once BASE_PATH . 'helpers/functions.php';
require_once BASE_PATH . 'helpers/Sanitizer.php';

$database = new Database();
$db = $database->getConnection();
Auth::init($db);

// Redirect if already logged in
if (Auth::check()) {
    redirect('index.php');
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
    
    $data = [
        'username' => Sanitizer::alphanumeric($_POST['username'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'email' => Sanitizer::email($_POST['email'] ?? ''),
        'full_name' => Sanitizer::name($_POST['full_name'] ?? '')
    ];
    
    // Check password confirmation
    if ($data['password'] !== ($_POST['password_confirmation'] ?? '')) {
        setErrors(['password' => 'Password dan konfirmasi password tidak sama']);
        setOld($data);
    } else {
        $result = Auth::register($data);
        
        if ($result['success']) {
            setFlash('success', $result['message']);
            redirect('login.php');
        } else {
            if (isset($result['errors'])) {
                setErrors($result['errors']);
            } else {
                setFlash('danger', $result['message']);
            }
            setOld($data);
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
    <title>Register - Patra Jaya Variasi</title>
    <?= Csrf::metaTag() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .register-container {
            max-width: 500px;
            width: 100%;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .register-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .register-body {
            padding: 40px 30px;
        }
        .form-control {
            padding: 12px 20px;
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: bold;
            border: none;
            width: 100%;
            transition: transform 0.3s;
        }
        .btn-register:hover {
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
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <i class="fas fa-user-plus"></i>
                <h2>Daftar Akun Baru</h2>
                <p class="mb-0">Patra Jaya Variasi</p>
            </div>
            
            <div class="register-body">
                <?php if ($flash = getFlash()): ?>
                <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
                    <?= $flash['message'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="register.php">
                    <?= Csrf::field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <input type="text" name="full_name" class="form-control with-icon <?= getError('full_name') ? 'is-invalid' : '' ?>" 
                                   placeholder="Masukkan nama lengkap" value="<?= old('full_name') ?>" required>
                        </div>
                        <?php if (getError('full_name')): ?>
                        <div class="text-danger small mt-1"><?= getError('full_name') ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Username</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" name="username" class="form-control with-icon <?= getError('username') ? 'is-invalid' : '' ?>" 
                                   placeholder="Masukkan username" value="<?= old('username') ?>" required>
                        </div>
                        <?php if (getError('username')): ?>
                        <div class="text-danger small mt-1"><?= getError('username') ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control with-icon <?= getError('email') ? 'is-invalid' : '' ?>" 
                                   placeholder="Masukkan email" value="<?= old('email') ?>" required>
                        </div>
                        <?php if (getError('email')): ?>
                        <div class="text-danger small mt-1"><?= getError('email') ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control with-icon <?= getError('password') ? 'is-invalid' : '' ?>" 
                                   placeholder="Minimal 6 karakter" required>
                        </div>
                        <?php if (getError('password')): ?>
                        <div class="text-danger small mt-1"><?= getError('password') ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Konfirmasi Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password_confirmation" class="form-control with-icon" 
                                   placeholder="Ulangi password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-plus"></i> Daftar Sekarang
                    </button>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted mb-0">
                        Sudah punya akun? 
                        <a href="login.php" class="text-decoration-none fw-bold">Login di sini</a>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p class="text-white small mb-0">
                &copy; 2024 Patra Jaya Variasi. Powered by Patra Ananda 1061
            </p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>