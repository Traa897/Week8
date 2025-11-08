<?php
/**
 * PROFILE CONTROLLER - For User Role
 * File: motor_modif_shop/controllers/ProfileController.php
 * 
 * Features:
 * - View profile
 * - Edit profile (name, address, phone)
 * - Change password
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
        // Get customer data
        $userEmail = Auth::user()['username'] . '@customer.com';
        $sql = "SELECT * FROM customers WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $userEmail);
        $stmt->execute();
        $customer = $stmt->get_result()->fetch_assoc();
        
        // Get user account data
        $user = Auth::user();
        
        $this->view('profile/index', [
            'customer' => $customer,
            'user' => $user
        ]);
    }
    
    /**
     * Update profile
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
        
        // Update customer data
        $userEmail = Auth::user()['username'] . '@customer.com';
        $sql = "UPDATE customers SET name = ?, phone = ?, address = ?, city = ? WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssss', 
            $data['name'],
            $data['phone'],
            $data['address'],
            $data['city'],
            $userEmail
        );
        
        if ($stmt->execute()) {
            // Update full_name in session if name changed
            $_SESSION['full_name'] = $data['name'];
            
            $this->setFlash('success', '✅ Profil berhasil diupdate');
            clearOld();
            clearErrors();
        } else {
            $this->setFlash('danger', '❌ Gagal update profil');
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
}