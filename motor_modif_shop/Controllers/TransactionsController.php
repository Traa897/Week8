<?php
/**
 * TRANSACTIONS CONTROLLER - WITH CONFIRMATION SYSTEM
 * File: motor_modif_shop/controllers/TransactionsController.php
 * 
 * ADDED:
 * - confirmPayment() - Konfirmasi pembayaran
 * - rejectPayment() - Reject pembayaran
 * - pending() - List transaksi pending
 */

require_once BASE_PATH . 'controllers/BaseController.php';
require_once BASE_PATH . 'models/Transaction.php';
require_once BASE_PATH . 'models/Customer.php';
require_once BASE_PATH . 'models/Product.php';

class TransactionsController extends BaseController {
    private $transactionModel;
    private $customerModel;
    private $productModel;
    
    public function __construct($db) {
        $this->transactionModel = new Transaction($db);
        $this->customerModel = new Customer($db);
        $this->productModel = new Product($db);
    }
    
    public function index() {
        $search = isset($_GET['search']) ? clean($_GET['search']) : '';
        $status = isset($_GET['status']) ? clean($_GET['status']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        
        $transactions = $this->transactionModel->all($search, $page, $limit, $status);
        $total = $this->transactionModel->count($search, $status);
        $totalPages = ceil($total / $limit);
        
        // Get count by status
        $pendingCount = $this->transactionModel->countByStatus('pending');
        $completedCount = $this->transactionModel->countByStatus('completed');
        $cancelledCount = $this->transactionModel->countByStatus('cancelled');
        
        $this->view('transactions/index', [
            'transactions' => $transactions,
            'search' => $search,
            'status' => $status,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'pendingCount' => $pendingCount,
            'completedCount' => $completedCount,
            'cancelledCount' => $cancelledCount
        ]);
    }
    
    /**
     * NEW: List pending transactions
     */
    public function pending() {
        $search = isset($_GET['search']) ? clean($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        
        $transactions = $this->transactionModel->all($search, $page, $limit, 'pending');
        $total = $this->transactionModel->countByStatus('pending');
        $totalPages = ceil($total / $limit);
        
        $this->view('transactions/pending', [
            'transactions' => $transactions,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }
    
    /**
     * NEW: Confirm payment (approve transaction)
     */
    public function confirmPayment() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $notes = clean($_POST['admin_notes'] ?? '');
        
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            $this->setFlash('danger', '❌ Transaksi tidak ditemukan');
            $this->redirect('index.php?c=transactions&a=pending');
            return;
        }
        
        if ($transaction['status'] !== 'pending') {
            $this->setFlash('warning', '⚠️ Transaksi ini sudah diproses sebelumnya');
            $this->redirect('index.php?c=transactions&a=detail&id=' . $id);
            return;
        }
        
        // Update status to completed
        $result = $this->transactionModel->updateStatus($id, 'completed', $notes);
        
        if ($result['success']) {
            $this->setFlash('success', '✅ Pembayaran berhasil dikonfirmasi! Transaksi: ' . $transaction['transaction_code']);
            $this->redirect('index.php?c=transactions&a=detail&id=' . $id);
        } else {
            $this->setFlash('danger', '❌ Gagal konfirmasi pembayaran: ' . $result['message']);
            $this->redirect('index.php?c=transactions&a=pending');
        }
    }
    
    /**
     * NEW: Reject payment (cancel transaction)
     */
    public function rejectPayment() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $reason = clean($_POST['reject_reason'] ?? '');
        
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            $this->setFlash('danger', '❌ Transaksi tidak ditemukan');
            $this->redirect('index.php?c=transactions&a=pending');
            return;
        }
        
        if ($transaction['status'] !== 'pending') {
            $this->setFlash('warning', '⚠️ Transaksi ini sudah diproses sebelumnya');
            $this->redirect('index.php?c=transactions&a=detail&id=' . $id);
            return;
        }
        
        // Begin transaction for rollback safety
        $db = $this->productModel->db;
        $db->begin_transaction();
        
        try {
            // Get transaction details for stock restoration
            $details = $this->transactionModel->getDetails($id);
            
            // Restore stock - FIXED: Direct SQL update instead of updateStock
            foreach ($details as $detail) {
                $sql = "UPDATE products SET stock = stock + ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('ii', $detail['quantity'], $detail['product_id']);
                if (!$stmt->execute()) {
                    throw new Exception('Gagal restore stok produk');
                }
            }
            
            // Update status to cancelled
            $result = $this->transactionModel->updateStatus($id, 'cancelled', $reason);
            
            if (!$result['success']) {
                throw new Exception('Gagal update status transaksi');
            }
            
            $db->commit();
            
            $this->setFlash('success', '✅ Transaksi berhasil dibatalkan. Stok produk dikembalikan.');
            $this->redirect('index.php?c=transactions&a=detail&id=' . $id);
            
        } catch (Exception $e) {
            $db->rollback();
            $this->setFlash('danger', '❌ Gagal membatalkan transaksi: ' . $e->getMessage());
            $this->redirect('index.php?c=transactions&a=pending');
        }
    }
    
    public function create() {
        $customers = $this->customerModel->all();
        $products = $this->productModel->getAllActive();
        
        $this->view('transactions/create', [
            'customers' => $customers,
            'products' => $products
        ]);
    }
    
    public function store() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        if (empty($_POST['items'])) {
            $this->setFlash('danger', 'Tidak ada produk yang dipilih');
            $this->redirect('index.php?c=transactions&a=create');
            return;
        }
        
        $items = json_decode($_POST['items'], true);
        
        if (empty($items)) {
            $this->setFlash('danger', 'Data produk tidak valid');
            $this->redirect('index.php?c=transactions&a=create');
            return;
        }
        
        $transactionDate = $_POST['transaction_date'] ?? date('Y-m-d');
        
        $data = [
            'customer_id' => clean($_POST['customer_id'] ?? ''),
            'transaction_date' => $transactionDate,
            'total_amount' => clean($_POST['total_amount'] ?? 0),
            'payment_method' => clean($_POST['payment_method'] ?? 'cash'),
            'status' => 'completed', // Admin creates = auto completed
            'notes' => clean($_POST['notes'] ?? '')
        ];
        
        $result = $this->transactionModel->create($data, $items);
        
        if ($result['success']) {
            $this->setFlash('success', 'Transaksi berhasil! Kode: ' . $result['code']);
            $this->redirect('index.php?c=transactions&a=detail&id=' . $result['id']);
        } else {
            $this->setFlash('danger', $result['message']);
            $this->redirect('index.php?c=transactions&a=create');
        }
    }
    
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            $this->setFlash('danger', 'Transaksi tidak ditemukan');
            $this->redirect('index.php?c=transactions&a=index');
            return;
        }
        
        $details = $this->transactionModel->getDetails($id);
        
        $this->view('transactions/detail', [
            'transaction' => $transaction,
            'details' => $details
        ]);
    }
    
    public function delete() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $result = $this->transactionModel->delete($id);
        
        if ($result['success']) {
            $this->setFlash('success', 'Transaksi berhasil dihapus');
        } else {
            $message = $result['message'] ?? 'Transaksi gagal dihapus';
            $this->setFlash('danger', $message);
        }
        
        $this->redirect('index.php?c=transactions&a=index');
    }
    
    public function getProduct() {
        header('Content-Type: application/json');
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $this->productModel->find($id);
        
        if ($product) {
            echo json_encode([
                'success' => true,
                'data' => $product
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ]);
        }
        exit;
    }
    
    public function print() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $transaction = $this->transactionModel->find($id);
        
        if (!$transaction) {
            $this->setFlash('danger', 'Transaksi tidak ditemukan');
            $this->redirect('index.php?c=transactions&a=index');
            return;
        }
        
        $details = $this->transactionModel->getDetails($id);
        
        include BASE_PATH . 'views/transactions/print.php';
        exit;
    }
}