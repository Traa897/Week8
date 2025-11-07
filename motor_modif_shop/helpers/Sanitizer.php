<?php
/**
 * SANITIZER ENHANCEMENT (6%)
 * Week 7 Requirement: 3 methods baru
 * 
 * File: motor_modif_shop/helpers/Sanitizer.php
 * 
 * Methods:
 * 1. phone() - Format +62-XXX-XXXX-XXXX
 * 2. name() - Capitalize, hapus angka/special chars
 * 3. alphanumeric() - Only letters & numbers
 */

class Sanitizer {
    /**
     * 1. PHONE - Format +62-XXX-XXXX-XXXX
     * Sanitize nomor telepon Indonesia
     */
    public static function phone($value) {
        // Hapus semua karakter kecuali angka
        $cleaned = preg_replace('/[^0-9]/', '', $value);
        
        // Ubah awalan 0 menjadi 62
        if (substr($cleaned, 0, 1) === '0') {
            $cleaned = '62' . substr($cleaned, 1);
        }
        
        // Format: +62-XXX-XXXX-XXXX
        if (strlen($cleaned) >= 10) {
            return '+' . substr($cleaned, 0, 2) . '-' . 
                   substr($cleaned, 2, 3) . '-' . 
                   substr($cleaned, 5, 4) . '-' . 
                   substr($cleaned, 9);
        }
        
        return $value; // Return original jika format tidak valid
    }
    
    /**
     * 2. NAME - Capitalize, hapus angka/special chars
     * Sanitize nama (hanya huruf dan spasi)
     */
    public static function name($value) {
        // Hapus angka dan karakter spesial, kecuali spasi dan apostrof
        $cleaned = preg_replace('/[^a-zA-Z\s\']/', '', $value);
        
        // Trim whitespace berlebih
        $cleaned = preg_replace('/\s+/', ' ', trim($cleaned));
        
        // Capitalize setiap kata
        return ucwords(strtolower($cleaned));
    }
    
    /**
     * 3. ALPHANUMERIC - Only letters & numbers
     * Sanitize untuk kode produk, username, dll
     */
    public static function alphanumeric($value) {
        // Hanya huruf dan angka
        return preg_replace('/[^a-zA-Z0-9]/', '', $value);
    }
    
    // ========================================
    // ADDITIONAL SANITIZER METHODS
    // ========================================
    
    /**
     * Email sanitization
     */
    public static function email($value) {
        return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * URL sanitization
     */
    public static function url($value) {
        return filter_var(trim($value), FILTER_SANITIZE_URL);
    }
    
    /**
     * Strip HTML tags
     */
    public static function stripTags($value) {
        return strip_tags(trim($value));
    }
    
    /**
     * Escape HTML untuk output
     */
    public static function escape($value) {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
