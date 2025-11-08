<?php
/**
 * AUTHENTICATION HELPER - FIXED FINAL
 * File: motor_modif_shop/helpers/Auth.php
 */

class Auth {
    private static $db;
    
    public static function init($database) {
        self::$db = $database;
    }
    
    /**
     * Login user - FIXED
     */
    public static function login($username, $password) {
        if (!self::$db) {
            return ['success' => false, 'message' => 'Database connection not initialized'];
        }
        
        $sql = "SELECT * FROM users WHERE username = ? AND is_active = 1 LIMIT 1";
        $stmt = self::$db->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Username tidak ditemukan atau akun tidak aktif'];
        }
        
        $user = $result->fetch_assoc();
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Password salah'];
        }
        
        // FIXED: Clear old session first
        $_SESSION = array();
        
        // Set session dengan data lengkap
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Update last login
        $updateSql = "UPDATE users SET updated_at = NOW() WHERE id = ?";
        $updateStmt = self::$db->prepare($updateSql);
        $updateStmt->bind_param('i', $user['id']);
        $updateStmt->execute();
        
        return [
            'success' => true, 
            'message' => 'Login berhasil',
            'role' => $user['role']
        ];
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    /**
     * Check if user is logged in - FIXED dengan strict checking
     */
    public static function check() {
        return isset($_SESSION['logged_in']) && 
               $_SESSION['logged_in'] === true &&
               isset($_SESSION['user_id']) &&
               isset($_SESSION['role']);
    }
    
    /**
     * Get current user data
     */
    public static function user() {
        if (!self::check()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null
        ];
    }
    
    /**
     * Get current user role
     */
    public static function role() {
        return $_SESSION['role'] ?? null;
    }
    
    /**
     * Check if user is developer
     */
    public static function isDeveloper() {
        return self::role() === 'developer';
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        return self::role() === 'admin';
    }
    
    /**
     * Check if user is regular user
     */
    public static function isUser() {
        return self::role() === 'user';
    }
    
    /**
     * Check if user has specific role(s)
     */
    public static function hasRole($roles) {
        if (!is_array($roles)) {
            $roles = [$roles];
        }
        
        return in_array(self::role(), $roles);
    }
    
    /**
     * Require authentication
     */
    public static function requireLogin() {
        if (!self::check()) {
            header('Location: login.php');
            exit;
        }
    }
    
    /**
     * Require specific role
     */
    public static function requireRole($roles) {
        self::requireLogin();
        
        if (!self::hasRole($roles)) {
            setFlash('danger', 'Anda tidak memiliki akses ke halaman ini');
            redirect('index.php');
            exit;
        }
    }
    
    /**
     * Register new user
     */
    public static function register($data) {
        if (!self::$db) {
            return ['success' => false, 'message' => 'Database connection not initialized'];
        }
        
        // Validate
        $errors = [];
        
        if (empty($data['username'])) {
            $errors['username'] = 'Username harus diisi';
        }
        
        if (empty($data['password'])) {
            $errors['password'] = 'Password harus diisi';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Password minimal 6 karakter';
        }
        
        if (empty($data['email'])) {
            $errors['email'] = 'Email harus diisi';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format email tidak valid';
        }
        
        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Nama lengkap harus diisi';
        }
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        // Check if username exists
        $checkSql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $checkStmt = self::$db->prepare($checkSql);
        $checkStmt->bind_param('ss', $data['username'], $data['email']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            return ['success' => false, 'message' => 'Username atau email sudah digunakan'];
        }
        
        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $sql = "INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, 'user')";
        $stmt = self::$db->prepare($sql);
        $stmt->bind_param('ssss', 
            $data['username'],
            $hashedPassword,
            $data['email'],
            $data['full_name']
        );
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registrasi berhasil! Silakan login.'];
        }
        
        return ['success' => false, 'message' => 'Gagal melakukan registrasi'];
    }
}