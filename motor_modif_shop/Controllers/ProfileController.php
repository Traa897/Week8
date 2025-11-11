<?php
/**
 * PROFILE CONTROLLER - FIXED
 * File: motor_modif_shop/controllers/ProfileController.php
 * 
 * FIX: Auto-create customer record jika belum ada
 */

require_once BASE_PATH . 'controllers/BaseController.php';
require_once BASE_PATH . 'helpers/Sanitizer.php';

class ProfileController extends BaseController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * View profile
     */
    public function index() {
        // FIXED: Get or create customer data
        $customer = $this->getOrCreateCustomer();
        
        // Get user account data
        $user = Auth::user();
        
        $this->view('profile/index', [
            'customer' => $customer,
            'user' => $user
        ]);
    }
    
    /**
     * Update profile - FIXED
     */
    public function update() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $data = [
            'name' => Sanitizer::name($_POST['name'] ?? ''),
            'phone' => Sanitizer::phone($_POST['phone'] ?? ''),
            'address' => Sanitizer::stripTags($_POST['address'] ?? ''),
            'city' => Sanitizer::name($_POST['city'] ?? '')
        ];
        
        // Validate
        $errors = [];
        if (empty($data['name'])) {
            $errors['name'] = 'Nama harus diisi';
        }
        
        if (!empty($errors)) {
            setErrors($errors);
            setOld($data);
            $this->redirect('index.php?c=profile&a=index');
            return;
        }
        
        // FIXED: Get or create customer first
        $customer = $this->getOrCreateCustomer();
        
        // Update customer data
        $sql = "UPDATE customers SET name = ?, phone = ?, address = ?, city = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ssssi', 
            $data['name'],
            $data['phone'],
            $data['address'],
            $data['city'],
            $customer['id']  // FIXED: Use customer ID instead of email
        );
        
        if ($stmt->execute()) {
            // Update session
            $_SESSION['full_name'] = $data['name'];
            
            $this->setFlash('success', '✅ Profil berhasil diupdate');
            clearOld();
            clearErrors();
        } else {
            $this->setFlash('danger', '❌ Gagal update profil: ' . $this->db->error);
            setOld($data);
        }
        
        $this->redirect('index.php?c=profile&a=index');
    }
    
    /**
     * Change password
     */
    public function changePassword() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate
        $errors = [];
        
        // Check current password
        $userId = Auth::user()['id'];
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if (!password_verify($currentPassword, $user['password'])) {
            $errors['current_password'] = 'Password lama salah';
        }
        
        if (strlen($newPassword) < 6) {
            $errors['new_password'] = 'Password baru minimal 6 karakter';
        }
        
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Konfirmasi password tidak cocok';
        }
        
        if (!empty($errors)) {
            setErrors($errors);
            $this->redirect('index.php?c=profile&a=index');
            return;
        }
        
        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('si', $hashedPassword, $userId);
        
        if ($stmt->execute()) {
            $this->setFlash('success', '✅ Password berhasil diubah');
            clearErrors();
        } else {
            $this->setFlash('danger', '❌ Gagal mengubah password');
        }
        
        $this->redirect('index.php?c=profile&a=index');
    }
    
    /**
     * FIXED: Get or create customer record
     * Dipanggil otomatis setiap kali akses profile
     */
    private function getOrCreateCustomer() {
        $userId = Auth::user()['id'];
        $userEmail = Auth::user()['username'] . '@customer.com';
        
        // Check if customer exists
        $sql = "SELECT * FROM customers WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Customer exists, return it
            return $result->fetch_assoc();
        }
        
        // Customer TIDAK ADA - CREATE NOW!
        $insertSql = "INSERT INTO customers (name, phone, email, address, city, created_at) 
                      VALUES (?, '', ?, '', '', NOW())";
        $insertStmt = $this->db->prepare($insertSql);
        
        $fullName = Auth::user()['full_name'];
        $insertStmt->bind_param('ss', $fullName, $userEmail);
        
        if ($insertStmt->execute()) {
            // Get the newly created customer
            $newCustomerId = $this->db->insert_id;
            
            $getSql = "SELECT * FROM customers WHERE id = ?";
            $getStmt = $this->db->prepare($getSql);
            $getStmt->bind_param('i', $newCustomerId);
            $getStmt->execute();
            
            return $getStmt->get_result()->fetch_assoc();
        }
        
        // Fallback jika create gagal
        return [
            'id' => 0,
            'name' => Auth::user()['full_name'],
            'phone' => '',
            'email' => $userEmail,
            'address' => '',
            'city' => ''
        ];
    }
}