<?php

class Product {
    public $db; // CHANGED: dari private jadi public
    private $table = 'products';
    
    public function __construct($db) {
        $this->db = $db; // Now accessible from ShopController
    }
    
    
    // Get all products (exclude deleted)
    public function all($search = '', $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $search = "%$search%";
        
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.deleted_at IS NULL
                AND (p.name LIKE ? OR p.code LIKE ? OR p.motor_type LIKE ?)
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssii', $search, $search, $search, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    // Get trashed products (soft deleted)
    public function getTrashed($search = '', $page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        $search = "%$search%";
        
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.deleted_at IS NOT NULL
                AND (p.name LIKE ? OR p.code LIKE ? OR p.motor_type LIKE ?)
                ORDER BY p.deleted_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssii', $search, $search, $search, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function count($search = '') {
        $search = "%$search%";
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table}
                WHERE deleted_at IS NULL
                AND (name LIKE ? OR code LIKE ? OR motor_type LIKE ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sss', $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
    
    public function countTrashed($search = '') {
        $search = "%$search%";
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table}
                WHERE deleted_at IS NOT NULL
                AND (name LIKE ? OR code LIKE ? OR motor_type LIKE ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sss', $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
    
    public function find($id) {
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.id = ? AND p.deleted_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function findTrashed($id) {
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.id = ? AND p.deleted_at IS NOT NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function create($data) {
        $errors = $this->validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $sql = "INSERT INTO {$this->table} 
                (category_id, supplier_id, code, name, brand, description, price, stock, motor_type, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iissssdiss', 
            $data['category_id'],
            $data['supplier_id'],
            $data['code'],
            $data['name'],
            $data['brand'],
            $data['description'],
            $data['price'],
            $data['stock'],
            $data['motor_type'],
            $data['image']
        );
        
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->db->insert_id];
        }
        
        return ['success' => false, 'message' => 'Gagal menyimpan data'];
    }
    
    public function update($id, $data) {
        $errors = $this->validate($data, $id);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $sql = "UPDATE {$this->table} SET 
                category_id = ?, supplier_id = ?, code = ?, name = ?, 
                brand = ?, description = ?, price = ?, stock = ?, 
                motor_type = ?, image = ?
                WHERE id = ? AND deleted_at IS NULL";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iissssdissi', 
            $data['category_id'],
            $data['supplier_id'],
            $data['code'],
            $data['name'],
            $data['brand'],
            $data['description'],
            $data['price'],
            $data['stock'],
            $data['motor_type'],
            $data['image'],
            $id
        );
        
        return ['success' => $stmt->execute()];
    }
    
    // SOFT DELETE - Move to recycle bin
    public function delete($id) {
        $checkProduct = "SELECT id FROM {$this->table} WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($checkProduct);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan atau sudah dihapus'];
        }
        
        $sql = "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Produk berhasil dipindahkan ke Recycle Bin'];
        }
        
        return ['success' => false, 'message' => 'Gagal menghapus produk'];
    }
    
    // RESTORE from recycle bin
    public function restore($id) {
        $sql = "UPDATE {$this->table} SET deleted_at = NULL WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Produk berhasil dikembalikan'];
        }
        
        return ['success' => false, 'message' => 'Gagal mengembalikan produk'];
    }
    
    // RESTORE ALL from recycle bin
    public function restoreAll() {
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE deleted_at IS NOT NULL";
        $result = $this->db->query($countSql);
        $row = $result->fetch_assoc();
        $total = $row['total'];
        
        if ($total == 0) {
            return ['success' => false, 'message' => 'Tidak ada produk di Recycle Bin'];
        }
        
        $sql = "UPDATE {$this->table} SET deleted_at = NULL WHERE deleted_at IS NOT NULL";
        
        if ($this->db->query($sql)) {
            $restoredCount = $this->db->affected_rows;
            return ['success' => true, 'message' => "Berhasil mengembalikan $restoredCount produk dari Recycle Bin"];
        }
        
        return ['success' => false, 'message' => 'Gagal mengembalikan produk'];
    }
    
    // PERMANENT DELETE from recycle bin
    public function forceDelete($id) {
        $product = $this->findTrashed($id);
        
        if (!$product) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan di Recycle Bin'];
        }
        
        $check = "SELECT COUNT(*) as total FROM transaction_details WHERE product_id = ?";
        $stmt = $this->db->prepare($check);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['total'] > 0) {
            return ['success' => false, 'message' => 'Produk tidak bisa dihapus permanen karena sudah ada di transaksi historis. Biarkan di Recycle Bin atau gunakan fitur Restore.'];
        }
        
        if ($product['image']) {
            $imagePath = 'motor_modif_shop/public/uploads/products/' . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        $sql = "DELETE FROM {$this->table} WHERE id = ? AND deleted_at IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Produk berhasil dihapus permanen'];
        }
        
        return ['success' => false, 'message' => 'Gagal menghapus produk'];
    }
    
    // EMPTY recycle bin - FIXED SQL SYNTAX
    public function emptyTrash() {
        // Get products yang tidak ada di transaksi
        $sql = "SELECT p.id, p.image FROM {$this->table} p
                WHERE p.deleted_at IS NOT NULL
                AND p.id NOT IN (SELECT DISTINCT product_id FROM transaction_details)";
        
        $result = $this->db->query($sql);
        $products = $result->fetch_all(MYSQLI_ASSOC);
        
        if (empty($products)) {
            return ['success' => false, 'message' => 'Tidak ada produk yang bisa dihapus permanen (semua produk di Recycle Bin masih terhubung dengan transaksi)'];
        }
        
        // Delete images
        foreach ($products as $product) {
            if ($product['image']) {
                $imagePath = 'motor_modif_shop/public/uploads/products/' . $product['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
        
        // Delete products
        $sql = "DELETE FROM {$this->table} 
                WHERE deleted_at IS NOT NULL
                AND id NOT IN (SELECT DISTINCT product_id FROM transaction_details)";
        
        if ($this->db->query($sql)) {
            $deletedCount = $this->db->affected_rows;
            if ($deletedCount > 0) {
                return ['success' => true, 'message' => "Berhasil menghapus $deletedCount produk dari Recycle Bin"];
            } else {
                return ['success' => false, 'message' => 'Semua produk di Recycle Bin masih terhubung dengan transaksi dan tidak bisa dihapus permanen'];
            }
        }
        
        return ['success' => false, 'message' => 'Gagal mengosongkan Recycle Bin'];
    }

    public function autoDeleteOld($days = 30) {
        $checkSql = "SELECT p.id, p.name, p.code, p.image, p.deleted_at,
                     (SELECT COUNT(*) FROM transaction_details WHERE product_id = p.id) as transaction_count
                     FROM {$this->table} p
                     WHERE p.deleted_at IS NOT NULL 
                     AND p.deleted_at <= DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        $stmt = $this->db->prepare($checkSql);
        $stmt->bind_param('i', $days);
        $stmt->execute();
        $result = $stmt->get_result();
        $oldProducts = $result->fetch_all(MYSQLI_ASSOC);
        
        if (empty($oldProducts)) {
            return [
                'success' => true, 
                'deleted' => 0,
                'skipped' => 0,
                'message' => "Tidak ada produk yang perlu dihapus otomatis (>{$days} hari di Recycle Bin)"
            ];
        }
        
        $deleted = 0;
        $skipped = 0;
        $deletedProducts = [];
        
        foreach ($oldProducts as $product) {
            if ($product['transaction_count'] > 0) {
                $skipped++;
                continue;
            }
            
            if (!empty($product['image'])) {
                $imagePath = 'motor_modif_shop/public/uploads/products/' . $product['image'];
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
            
            $deleteSql = "DELETE FROM {$this->table} WHERE id = ?";
            $deleteStmt = $this->db->prepare($deleteSql);
            $deleteStmt->bind_param('i', $product['id']);
            
            if ($deleteStmt->execute()) {
                $deleted++;
                $deletedProducts[] = $product['name'];
            }
        }
        
        return [
            'success' => true,
            'deleted' => $deleted,
            'skipped' => $skipped,
            'products' => $deletedProducts,
            'message' => "Auto-delete selesai: {$deleted} produk dihapus, {$skipped} produk dilewati (masih ada di transaksi)"
        ];
    }
    
    public function runAutoDelete() {
        return $this->autoDeleteOld(30);
    }

    public function restoreBulk($ids) {
        if (empty($ids) || !is_array($ids)) {
            return ['success' => false, 'message' => 'Tidak ada produk yang dipilih.'];
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));
        
        $sql = "UPDATE {$this->table} SET deleted_at = NULL WHERE id IN ($placeholders) AND deleted_at IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($types, ...$ids); 
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => "Berhasil mengembalikan " . $stmt->affected_rows . " produk."];
        }
        return ['success' => false, 'message' => 'Gagal mengembalikan produk.'];
    }

    public function forceDeleteBulk($ids) {
        if (empty($ids) || !is_array($ids)) {
            return ['success' => false, 'message' => 'Tidak ada produk yang dipilih.'];
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $checkSql = "SELECT DISTINCT product_id FROM transaction_details WHERE product_id IN ($placeholders)";
        $stmtCheck = $this->db->prepare($checkSql);
        $stmtCheck->bind_param($types, ...$ids);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $lockedIds = array_column($result->fetch_all(MYSQLI_ASSOC), 'product_id');
        
        $deletableIds = array_diff($ids, $lockedIds);
        
        if (empty($deletableIds)) {
            return ['success' => false, 'message' => 'Semua produk yang dipilih terikat transaksi dan tidak bisa dihapus permanen.'];
        }
        
        $deletablePlaceholders = implode(',', array_fill(0, count($deletableIds), '?'));
        $deletableTypes = str_repeat('i', count($deletableIds));
        
        $sql = "DELETE FROM {$this->table} WHERE id IN ($deletablePlaceholders) AND deleted_at IS NOT NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param($deletableTypes, ...$deletableIds);
        
        $stmt->execute();
        $deletedCount = $stmt->affected_rows;
        $lockedCount = count($lockedIds);
        
        $message = "Berhasil menghapus $deletedCount produk secara permanen. ";
        if ($lockedCount > 0) {
            $message .= "$lockedCount produk tidak bisa dihapus (terikat transaksi).";
        }

        return ['success' => true, 'message' => $message];
    }

    private function validate($data, $id = null) {
        $errors = [];
        
        if (empty($data['code'])) {
            $errors['code'] = 'Kode produk harus diisi';
        } else {
            $sql = "SELECT id FROM {$this->table} WHERE code = ? AND deleted_at IS NULL";
            if ($id) {
                $sql .= " AND id != ?";
            }
            $stmt = $this->db->prepare($sql);
            if ($id) {
                $stmt->bind_param('si', $data['code'], $id);
            } else {
                $stmt->bind_param('s', $data['code']);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $errors['code'] = 'Kode produk sudah digunakan';
            }
        }
        
        if (empty($data['name'])) {
            $errors['name'] = 'Nama produk harus diisi';
        }
        
        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Kategori harus dipilih';
        }
        
        if (empty($data['supplier_id'])) {
            $errors['supplier_id'] = 'Supplier harus dipilih';
        }
        
        if (empty($data['price']) || $data['price'] <= 0) {
            $errors['price'] = 'Harga harus diisi dan lebih dari 0';
        }
        
        if (!isset($data['stock']) || $data['stock'] < 0) {
            $errors['stock'] = 'Stok harus diisi dan tidak boleh negatif';
        }
        
        return $errors;
    }
    
    public function updateStock($id, $quantity) {
        $sql = "UPDATE {$this->table} SET stock = stock - ? WHERE id = ? AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('ii', $quantity, $id);
        return $stmt->execute();
    }

    public function getAllActive() {
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM {$this->table} p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.deleted_at IS NULL
                AND p.stock > 0
                ORDER BY p.name ASC";
        
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

