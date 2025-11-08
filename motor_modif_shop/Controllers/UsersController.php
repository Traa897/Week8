<?php
/**
 * USERS CONTROLLER
 * File: motor_modif_shop/controllers/UsersController.php
 * 
 * Only accessible by Developer role
 */

require_once BASE_PATH . 'controllers/BaseController.php';

class UsersController extends BaseController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        
        // Ensure only developer can access
        if (!Auth::isDeveloper()) {
            $this->setFlash('danger', '❌ Access Denied! Only Developer can manage users.');
            $this->redirect('index.php');
            exit;
        }
    }
    
    public function index() {
        $search = isset($_GET['search']) ? clean($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $searchParam = "%$search%";
        
        $sql = "SELECT * FROM users 
                WHERE username LIKE ? OR email LIKE ? OR full_name LIKE ?
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssii', $searchParam, $searchParam, $searchParam, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        
        // Count total
        $countSql = "SELECT COUNT(*) as total FROM users 
                     WHERE username LIKE ? OR email LIKE ? OR full_name LIKE ?";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->bind_param('sss', $searchParam, $searchParam, $searchParam);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $total = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($total / $limit);
        
        $this->view('users/index', [
            'users' => $users,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }
    
    public function create() {
        $this->view('users/create');
    }
    
    public function store() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $data = [
            'username' => Sanitizer::alphanumeric($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'email' => Sanitizer::email($_POST['email'] ?? ''),
            'full_name' => Sanitizer::name($_POST['full_name'] ?? ''),
            'role' => clean($_POST['role'] ?? 'user')
        ];
        
        // Validate
        $errors = [];
        
        if (empty($data['username'])) {
            $errors['username'] = 'Username harus diisi';
        } else {
            $checkSql = "SELECT id FROM users WHERE username = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->bind_param('s', $data['username']);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                $errors['username'] = 'Username sudah digunakan';
            }
        }
        
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors['password'] = 'Password minimal 6 karakter';
        }
        
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email tidak valid';
        }
        
        if (!in_array($data['role'], ['developer', 'admin', 'user'])) {
            $errors['role'] = 'Role tidak valid';
        }
        
        if (!empty($errors)) {
            setErrors($errors);
            setOld($data);
            $this->redirect('index.php?c=users&a=create');
            return;
        }
        
        // Hash password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert
        $sql = "INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssss', 
            $data['username'],
            $hashedPassword,
            $data['email'],
            $data['full_name'],
            $data['role']
        );
        
        if ($stmt->execute()) {
            $this->setFlash('success', '✅ User berhasil ditambahkan');
            clearOld();
            clearErrors();
            $this->redirect('index.php?c=users&a=index');
        } else {
            $this->setFlash('danger', '❌ Gagal menambahkan user');
            setOld($data);
            $this->redirect('index.php?c=users&a=create');
        }
    }
    
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            $this->setFlash('danger', 'User tidak ditemukan');
            $this->redirect('index.php?c=users&a=index');
            return;
        }
        
        $this->view('users/edit', ['user' => $user]);
    }
    
    public function update() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        $data = [
            'username' => Sanitizer::alphanumeric($_POST['username'] ?? ''),
            'email' => Sanitizer::email($_POST['email'] ?? ''),
            'full_name' => Sanitizer::name($_POST['full_name'] ?? ''),
            'role' => clean($_POST['role'] ?? 'user'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        // Update password only if provided
        $password = $_POST['password'] ?? '';
        
        if (!empty($password)) {
            if (strlen($password) < 6) {
                setErrors(['password' => 'Password minimal 6 karakter']);
                setOld($data);
                $this->redirect('index.php?c=users&a=edit&id=' . $id);
                return;
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, password = ?, email = ?, full_name = ?, role = ?, is_active = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('sssssii', 
                $data['username'],
                $hashedPassword,
                $data['email'],
                $data['full_name'],
                $data['role'],
                $data['is_active'],
                $id
            );
        } else {
            $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, role = ?, is_active = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param('ssssii', 
                $data['username'],
                $data['email'],
                $data['full_name'],
                $data['role'],
                $data['is_active'],
                $id
            );
        }
        
        if ($stmt->execute()) {
            $this->setFlash('success', '✅ User berhasil diupdate');
            clearOld();
            clearErrors();
        } else {
            $this->setFlash('danger', '❌ Gagal update user');
            setOld($data);
        }
        
        $this->redirect('index.php?c=users&a=index');
    }
    
    public function delete() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        // Prevent deleting own account
        if ($id == Auth::user()['id']) {
            $this->setFlash('danger', '❌ Anda tidak bisa menghapus akun sendiri');
            $this->redirect('index.php?c=users&a=index');
            return;
        }
        
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            $this->setFlash('success', '✅ User berhasil dihapus');
        } else {
            $this->setFlash('danger', '❌ Gagal menghapus user');
        }
        
        $this->redirect('index.php?c=users&a=index');
    }
}