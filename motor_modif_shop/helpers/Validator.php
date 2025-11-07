<?php
/**
 * BUILDER PATTERN - Validator Class
 * Week 7 Requirement: 6 validation rules baru
 * 
 * File: motor_modif_shop/helpers/Validator.php
 * 
 * Usage:
 * $validator = new Validator($_POST, $db);
 * $validator->field('price')->required()->numeric()->between(1000, 10000000);
 * if ($validator->validate()) { ... }
 */

class Validator {
    private $data = [];
    private $errors = [];
    private $field;
    private $db;
    
    public function __construct($data, $db = null) {
        $this->data = $data;
        $this->db = $db;
    }
    
    /**
     * Set field yang akan divalidasi (Builder Pattern)
     */
    public function field($fieldName) {
        $this->field = $fieldName;
        return $this;
    }
    
    // ========================================
    // 6 VALIDATION RULES BARU (REQUIREMENT)
    // ========================================
    
    /**
     * 1. NUMERIC - Validasi angka (int/float)
     */
    public function numeric() {
        $value = $this->data[$this->field] ?? null;
        
        if ($value !== null && $value !== '' && !is_numeric($value)) {
            $this->errors[$this->field] = ucfirst($this->field) . ' harus berupa angka';
        }
        
        return $this;
    }
    
    /**
     * 2. BETWEEN - Nilai antara min-max
     */
    public function between($min, $max) {
        $value = $this->data[$this->field] ?? null;
        
        if ($value !== null && ($value < $min || $value > $max)) {
            $this->errors[$this->field] = ucfirst($this->field) . " harus antara {$min} dan {$max}";
        }
        
        return $this;
    }
    
    /**
     * 3. IN - Nilai dalam whitelist array
     */
    public function in($array) {
        $value = $this->data[$this->field] ?? null;
        
        if ($value !== null && !in_array($value, $array)) {
            $this->errors[$this->field] = ucfirst($this->field) . ' tidak valid';
        }
        
        return $this;
    }
    
    /**
     * 4. UNIQUE - Unique check di database
     */
    public function unique($table, $column, $exceptId = null) {
        if (!$this->db) {
            $this->errors[$this->field] = 'Database connection tidak tersedia untuk unique validation';
            return $this;
        }
        
        $value = $this->data[$this->field] ?? null;
        
        if ($value === null || $value === '') {
            return $this;
        }
        
        $sql = "SELECT id FROM {$table} WHERE {$column} = ?";
        if ($exceptId) {
            $sql .= " AND id != ?";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($exceptId) {
            $stmt->bind_param('si', $value, $exceptId);
        } else {
            $stmt->bind_param('s', $value);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $this->errors[$this->field] = ucfirst($this->field) . ' sudah digunakan';
        }
        
        return $this;
    }
    
    /**
     * 5. CONFIRMED - Field sama dengan {field}_confirmation
     */
    public function confirmed() {
        $value = $this->data[$this->field] ?? null;
        $confirmField = $this->field . '_confirmation';
        $confirmValue = $this->data[$confirmField] ?? null;
        
        if ($value !== $confirmValue) {
            $this->errors[$this->field] = ucfirst($this->field) . ' tidak sama dengan konfirmasi';
        }
        
        return $this;
    }
    
    /**
     * 6. DATE_FORMAT - Validasi format tanggal
     */
    public function dateFormat($format = 'Y-m-d') {
        $value = $this->data[$this->field] ?? null;
        
        if ($value === null || $value === '') {
            return $this;
        }
        
        $date = DateTime::createFromFormat($format, $value);
        
        if (!$date || $date->format($format) !== $value) {
            $this->errors[$this->field] = ucfirst($this->field) . ' format tanggal tidak valid (gunakan ' . $format . ')';
        }
        
        return $this;
    }
    
    // ========================================
    // ADDITIONAL HELPER RULES
    // ========================================
    
    public function required() {
        $value = $this->data[$this->field] ?? null;
        
        if ($value === null || $value === '') {
            $this->errors[$this->field] = ucfirst($this->field) . ' harus diisi';
        }
        
        return $this;
    }
    
    public function email() {
        $value = $this->data[$this->field] ?? null;
        
        if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$this->field] = 'Format email tidak valid';
        }
        
        return $this;
    }
    
    public function minLength($length) {
        $value = $this->data[$this->field] ?? '';
        
        if (strlen($value) < $length) {
            $this->errors[$this->field] = ucfirst($this->field) . " minimal {$length} karakter";
        }
        
        return $this;
    }
    
    public function maxLength($length) {
        $value = $this->data[$this->field] ?? '';
        
        if (strlen($value) > $length) {
            $this->errors[$this->field] = ucfirst($this->field) . " maksimal {$length} karakter";
        }
        
        return $this;
    }
    
    // ========================================
    // VALIDATION EXECUTION
    // ========================================
    
    public function validate() {
        return empty($this->errors);
    }
    
    public function fails() {
        return !$this->validate();
    }
    
    public function getErrors() {
        return $this->errors;
    }
}
