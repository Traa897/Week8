<?php
/**
 * MY TRANSACTIONS CONTROLLER - For User Role
 * File: motor_modif_shop/controllers/MyTransactionsController.php
 * 
 * Features:
 * - View own transaction history
 * - View order details
 */

require_once BASE_PATH . 'controllers/BaseController.php';
require_once BASE_PATH . 'models/Transaction.php';

class MyTransactionsController extends BaseController {
    private $transactionModel;
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        $this->transactionModel = new Transaction($db);
    }
    
    /**
     * List user's own transactions
     */
    public function index() {
        // Get customer ID for this user
        $userEmail = Auth::user()['username'] . '@customer.com';
        $sql = "SELECT id FROM customers WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $this->view('mytransactions/index', [
                'transactions' => [],
                'total' => 0
            ]);
            return;
        }
        
        $customer = $result->fetch_assoc();
        $customerId = $customer['id'];
        
        // Get transactions for this customer only
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT t.*, c.name as customer_name, c.phone as customer_phone
                FROM transaksi t
                LEFT JOIN customers c ON t.customer_id = c.id
                WHERE t.customer_id = ?
                ORDER BY t.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $customerId, $limit, $offset);
        $stmt->execute();
        $transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Count total
        $countSql = "SELECT COUNT(*) as total FROM transaksi WHERE customer_id = ?";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->bind_param('i', $customerId);
        $countStmt->execute();
        $total = $countStmt->get_result()->fetch_assoc()['total'];
        $totalPages = ceil($total / $limit);
        
        $this->view('mytransactions/index', [
            'transactions' => $transactions,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    /**
     * View transaction detail
     */
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            $this->setFlash('danger', 'Transaksi tidak ditemukan');
            $this->redirect('index.php?c=mytransactions&a=index');
            return;
        }
        
        // Verify this transaction belongs to current user
        $userEmail = Auth::user()['username'] . '@customer.com';
        $sql = "SELECT id FROM customers WHERE email = ? AND id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('si', $userEmail, $transaction['customer_id']);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows === 0) {
            $this->setFlash('danger', 'Anda tidak memiliki akses ke transaksi ini');
            $this->redirect('index.php?c=mytransactions&a=index');
            return;
        }
        
        $details = $this->transactionModel->getDetails($id);
        
        $this->view('mytransactions/detail', [
            'transaction' => $transaction,
            'details' => $details
        ]);
    }
}