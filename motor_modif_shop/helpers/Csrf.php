<?php
/**
 * CSRF PROTECTION
 * Week 7 Requirement
 * 
 * File: motor_modif_shop/helpers/Csrf.php
 * 
 * Methods:
 * - generateToken()
 * - getToken()
 * - verify($token)
 * - verifyOrFail($token) ← Digunakan di controller
 * - field() ← Digunakan di view form
 * - metaTag() ← Digunakan di header
 */

class Csrf {
    /**
     * Generate CSRF token dan simpan di session
     */
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Get current CSRF token from session
     */
    public static function getToken() {
        return $_SESSION['csrf_token'] ?? null;
    }
    
    /**
     * Verify CSRF token - return boolean
     */
    public static function verify($token) {
        $sessionToken = self::getToken();
        
        if (!$sessionToken || !$token) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }
    
    /**
     * Verify CSRF token - throw exception jika gagal
     * Digunakan di controller
     */
    public static function verifyOrFail($token) {
        if (!self::verify($token)) {
            http_response_code(403);
            die('CSRF token validation failed. Request blocked for security reasons.');
        }
    }
    
    /**
     * Generate hidden input field dengan CSRF token
     * Digunakan di form view
     */
    public static function field() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * Generate meta tag untuk AJAX requests
     */
    public static function metaTag() {
        $token = self::generateToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }
}
