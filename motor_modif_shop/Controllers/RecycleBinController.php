<?php
/**
 * RECYCLE BIN CONTROLLER - COMPLETE WITH BULK ACTIONS
 */

require_once BASE_PATH . 'controllers/BaseController.php';
require_once BASE_PATH . 'models/Product.php';

class RecycleBinController extends BaseController {
    private $productModel;
    
    public function __construct($db) {
        $this->productModel = new Product($db);
    }
    
    public function index() {
        $search = isset($_GET['search']) ? clean($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        
        $products = $this->productModel->getTrashed($search, $page, $limit);
        $total = $this->productModel->countTrashed($search);
        $totalPages = ceil($total / $limit);
        
        $this->view('recyclebin/index', [
            'products' => $products,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }
    
    public function restore() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $result = $this->productModel->restore($id);
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('danger', $result['message']);
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
    
    public function restoreAll() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $result = $this->productModel->restoreAll();
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('danger', $result['message']);
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
    
    public function forceDelete() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $result = $this->productModel->forceDelete($id);
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('danger', $result['message']);
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
    
    public function empty() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $result = $this->productModel->emptyTrash();
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('danger', $result['message']);
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
    
    public function autoDelete() {
        $result = $this->productModel->runAutoDelete();
        
        if ($result['success']) {
            if ($result['deleted'] > 0) {
                $message = "Auto-delete berhasil: {$result['deleted']} produk dihapus otomatis (>30 hari di Recycle Bin)";
                if ($result['skipped'] > 0) {
                    $message .= ". {$result['skipped']} produk dilewati (masih ada di transaksi).";
                }
                $this->setFlash('success', $message);
            } else {
                $this->setFlash('info', $result['message']);
            }
        } else {
            $this->setFlash('danger', 'Gagal menjalankan auto-delete');
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
    
    // ========================================
    // BULK ACTIONS (NEW - Week 7 Requirement)
    // ========================================
    
    /**
     * Bulk Restore - Kembalikan multiple produk sekaligus
     */
    public function bulkRestore() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $idsJson = $_POST['ids'] ?? '[]';
        $ids = json_decode($idsJson, true);
        
        if (empty($ids) || !is_array($ids)) {
            $this->setFlash('danger', 'Tidak ada produk yang dipilih');
            $this->redirect('index.php?c=recyclebin&a=index');
            return;
        }
        
        // Convert to integers for security
        $ids = array_map('intval', $ids);
        
        $result = $this->productModel->restoreBulk($ids);
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('danger', $result['message']);
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
    
    /**
     * Bulk Delete - Hapus permanen multiple produk sekaligus
     */
    public function bulkDelete() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $idsJson = $_POST['ids'] ?? '[]';
        $ids = json_decode($idsJson, true);
        
        if (empty($ids) || !is_array($ids)) {
            $this->setFlash('danger', 'Tidak ada produk yang dipilih');
            $this->redirect('index.php?c=recyclebin&a=index');
            return;
        }
        
        // Convert to integers for security
        $ids = array_map('intval', $ids);
        
        $result = $this->productModel->forceDeleteBulk($ids);
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
        } else {
            $this->setFlash('danger', $result['message']);
        }
        
        $this->redirect('index.php?c=recyclebin&a=index');
    }
}