<?php
/**
 * DATE HELPER (4%)
 * Week 7 Requirement: 5 methods baru
 * 
 * File: motor_modif_shop/helpers/DateHelper.php
 * 
 * Methods:
 * 1. format($date, $format)
 * 2. age($birthdate)
 * 3. diffHuman($date)
 * 4. toMysql($date)
 * 5. isWeekend($date)
 */

class DateHelper {
    /**
     * 1. FORMAT - Format($date, $format)
     * Format tanggal ke berbagai format
     */
    public static function format($date, $format = 'd/m/Y') {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date($format, $timestamp);
    }
    
    /**
     * 2. AGE - Age($birthdate)
     * Hitung umur dari tanggal lahir
     */
    public static function age($birthdate) {
        $birthDate = new DateTime($birthdate);
        $today = new DateTime('today');
        return $birthDate->diff($today)->y;
    }
    
    /**
     * 3. DIFF_HUMAN - DiffHuman($date)
     * Selisih waktu dalam format human-readable
     */
    public static function diffHuman($date) {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return $diff . ' detik yang lalu';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' menit yang lalu';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' jam yang lalu';
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . ' hari yang lalu';
        } elseif ($diff < 2592000) {
            return floor($diff / 604800) . ' minggu yang lalu';
        } elseif ($diff < 31536000) {
            return floor($diff / 2592000) . ' bulan yang lalu';
        } else {
            return floor($diff / 31536000) . ' tahun yang lalu';
        }
    }
    
    /**
     * 4. TO_MYSQL - ToMysql($date)
     * Convert tanggal ke format MySQL (Y-m-d)
     */
    public static function toMysql($date) {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date('Y-m-d', $timestamp);
    }
    
    /**
     * 5. IS_WEEKEND - IsWeekend($date)
     * Cek apakah tanggal adalah weekend
     */
    public static function isWeekend($date) {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        $dayOfWeek = date('N', $timestamp); // 1 (Monday) - 7 (Sunday)
        return ($dayOfWeek >= 6); // 6 = Saturday, 7 = Sunday
    }
    
    // ========================================
    // ADDITIONAL DATE HELPER METHODS
    // ========================================
    
    /**
     * Get bulan dalam bahasa Indonesia
     */
    public static function monthIndo($monthNumber) {
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        return $months[$monthNumber] ?? '';
    }
    
    /**
     * Get hari dalam bahasa Indonesia
     */
    public static function dayIndo($date) {
        $days = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        $dayName = date('l', $timestamp);
        return $days[$dayName] ?? '';
    }
    
    /**
     * Format tanggal Indonesia lengkap
     */
    public static function formatIndo($date) {
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        
        $day = self::dayIndo($timestamp);
        $dateNum = date('d', $timestamp);
        $month = self::monthIndo(date('n', $timestamp));
        $year = date('Y', $timestamp);
        
        return "{$day}, {$dateNum} {$month} {$year}";
    }
}
