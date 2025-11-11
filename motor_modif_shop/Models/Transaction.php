<?php
/**
 * TRANSACTION MODEL - FIXED VERSION
 * File: motor_modif_shop/models/Transaction.php
 */

class Transaction {
    private $conn;
    private $table = 'transaksi';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all transactions with optional filters
     */
    public function all($search = '', $page = 1, $limit = 10, $status = '') {
        $offset = ($page - 1) * $limit;
        $search = "%$search%";
        
        $sql = "SELECT t.*, c.name as customer_name, c.phone as customer_phone
                FROM {$this->table} t
                LEFT JOIN customers c ON t.customer_id = c.id
                WHERE (t.transaction_code LIKE ? OR c.name LIKE ?)";
        
        if ($status) {
            $sql .= " AND t.status = '" . $this->conn->real_escape_string($status) . "'";
        }
        
        $sql .= " ORDER BY t.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssii', $search, $search, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Count transactions with optional filters
     */
    public function count($search = '', $status = '') {
        $search = "%$search%";
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} t
                LEFT JOIN customers c ON t.customer_id = c.id
                WHERE (t.transaction_code LIKE ? OR c.name LIKE ?)";
        
        if ($status) {
            $sql .= " AND t.status = '" . $this->conn->real_escape_string($status) . "'";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ss', $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
    
    /**
     * Count by specific status
     */
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
    
    /**
     * Find transaction by ID
     */
    public function find($id) {
        $sql = "SELECT t.*, c.name as customer_name, c.email, c.phone, c.address
                FROM {$this->table} t
                LEFT JOIN customers c ON t.customer_id = c.id
                WHERE t.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Get transaction details (items)
     */
    public function getDetails($transactionId) {
        $sql = "SELECT td.*, p.name as product_name, p.code as product_code
                FROM transaction_details td
                LEFT JOIN products p ON td.product_id = p.id
                WHERE td.transaction_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $transactionId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Create new transaction
     */
    public function create($data, $items) {
        $this->conn->begin_transaction();
        
        try {
            $transactionCode = generateTransactionCode();
            
            $sql = "INSERT INTO {$this->table} 
                    (customer_id, transaction_code, transaction_date, total_amount, payment_method, status, notes) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('issdsss', 
                $data['customer_id'],
                $transactionCode,
                $data['transaction_date'],
                $data['total_amount'],
                $data['payment_method'],
                $data['status'],
                $data['notes']
            );
            
            if (!$stmt->execute()) {
                throw new Exception('Gagal menyimpan transaksi');
            }
            
            $transactionId = $this->conn->insert_id;
            
            $sqlDetail = "INSERT INTO transaction_details 
                          (transaction_id, product_id, quantity, price, subtotal) 
                          VALUES (?, ?, ?, ?, ?)";
            
            $stmtDetail = $this->conn->prepare($sqlDetail);
            
            foreach ($items as $item) {
                $subtotal = $item['quantity'] * $item['price'];
                
                $stmtDetail->bind_param('iiidd', 
                    $transactionId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price'],
                    $subtotal
                );
                
                if (!$stmtDetail->execute()) {
                    throw new Exception('Gagal menyimpan detail transaksi');
                }
                
                // Update stock
                $sqlUpdateStock = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stmtStock = $this->conn->prepare($sqlUpdateStock);
                $stmtStock->bind_param('ii', $item['quantity'], $item['product_id']);
                
                if (!$stmtStock->execute()) {
                    throw new Exception('Gagal update stok produk');
                }
            }
            
            $this->conn->commit();
            
            return ['success' => true, 'id' => $transactionId, 'code' => $transactionCode];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Update transaction status with admin notes
     */
    public function updateStatus($id, $status, $notes = '') {
        $adminNotes = !empty($notes) ? "\n\n[Admin]: " . $notes : '';
        
        $sql = "UPDATE {$this->table} 
                SET status = ?, 
                    notes = CONCAT(IFNULL(notes, ''), ?) 
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('ssi', $status, $adminNotes, $id);
        
        return ['success' => $stmt->execute()];
    }
    
    /**
     * Delete transaction (only pending)
     */
    public function delete($id) {
        $transaction = $this->find($id);
        
        if (!$transaction) {
            return ['success' => false, 'message' => 'Transaksi tidak ditemukan'];
        }
        
        if ($transaction['status'] != 'pending') {
            return ['success' => false, 'message' => 'Hanya transaksi pending yang bisa dihapus'];
        }
        
        $this->conn->begin_transaction();
        
        try {
            $details = $this->getDetails($id);
            
            // Restore stock
            foreach ($details as $detail) {
                $sqlRestore = "UPDATE products SET stock = stock + ? WHERE id = ?";
                $stmtRestore = $this->conn->prepare($sqlRestore);
                $stmtRestore->bind_param('ii', $detail['quantity'], $detail['product_id']);
                $stmtRestore->execute();
            }
            
            // Delete transaction
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            
            $this->conn->commit();
            
            return ['success' => true];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}