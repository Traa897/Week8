<?php
/**
 * HELPER FUNCTIONS
 * File: motor_modif_shop/helpers/functions.php
 * 
 * UPDATE: Hapus fungsi uploadImage() karena tidak dipakai
 */

// ========================================
// SESSION & FLASH MESSAGES
// ========================================

function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ========================================
// OLD INPUT (untuk form validation)
// ========================================

function setOld($data) {
    $_SESSION['old'] = $data;
}

function old($key, $default = '') {
    if (isset($_SESSION['old'][$key])) {
        return $_SESSION['old'][$key];
    }
    return $default;
}

function clearOld() {
    unset($_SESSION['old']);
}

// ========================================
// ERROR HANDLING
// ========================================

function setErrors($errors) {
    $_SESSION['errors'] = $errors;
}

function getError($key) {
    if (isset($_SESSION['errors'][$key])) {
        return $_SESSION['errors'][$key];
    }
    return null;
}

function clearErrors() {
    unset($_SESSION['errors']);
}

// ========================================
// NAVIGATION & SECURITY
// ========================================

function redirect($url) {
    // Pastikan tidak ada output sebelum redirect
    if (!headers_sent()) {
        header("Location: $url");
        exit;
    }
}

function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// ========================================
// FORMATTING HELPERS
// ========================================

function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function formatTanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// ========================================
// BUSINESS LOGIC HELPERS
// ========================================

function generateTransactionCode() {
    return 'TRX' . date('Ymd') . rand(1000, 9999);
}
