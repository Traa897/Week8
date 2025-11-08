<?php
/**
 * SHOP CONTROLLER - For User Role
 * File: motor_modif_shop/controllers/ShopController.php
 * 
 * Features:
 * - Browse products (catalog)
 * - Search & filter products
 * - View product details
 */

require_once BASE_PATH . 'controllers/BaseController.php';
require_once BASE_PATH . 'models/Product.php';
require_once BASE_PATH . 'models/Category.php';

class ShopController extends BaseController {
    private $productModel;
    private $categoryModel;
    
    public function __construct($db) {
        $this->productModel = new Product($db);
        $this->categoryModel = new Category($db);
    }
    
    /**
     * Shop homepage - product catalog
     */
    public function index() {
        $search = isset($_GET['search']) ? clean($_GET['search']) : '';
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 12; // 12 products per page
        
        // Get products with filters
        $products = $this->getFilteredProducts($search, $categoryId, $page, $limit);
        $total = $this->countFilteredProducts($search, $categoryId);
        $totalPages = ceil($total / $limit);
        
        // Get all categories for filter
        $categories = $this->categoryModel->all();
        
        $this->view('shop/index', [
            'products' => $products,
            'categories' => $categories,
            'search' => $search,
            'categoryId' => $categoryId,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }
    
    /**
     * Product detail page
     */
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->setFlash('danger', 'Produk tidak ditemukan');
            $this->redirect('index.php?c=shop&a=index');
            return;
        }
        
        $this->view('shop/detail', ['product' => $product]);
    }
    
    /**
     * Get filtered products
     */
    private function getFilteredProducts($search, $categoryId, $page, $limit) {
        $offset = ($page - 1) * $limit;
        $search = "%$search%";
        
        $sql = "SELECT p.*, c.name as category_name, s.name as supplier_name 
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.deleted_at IS NULL
                AND p.stock > 0
                AND (p.name LIKE ? OR p.code LIKE ? OR p.motor_type LIKE ?)";
        
        if ($categoryId > 0) {
            $sql .= " AND p.category_id = " . $categoryId;
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->productModel->db->prepare($sql);
        $stmt->bind_param('sssii', $search, $search, $search, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Count filtered products
     */
    private function countFilteredProducts($search, $categoryId) {
        $search = "%$search%";
        
        $sql = "SELECT COUNT(*) as total FROM products p
                WHERE p.deleted_at IS NULL
                AND p.stock > 0
                AND (p.name LIKE ? OR p.code LIKE ? OR p.motor_type LIKE ?)";
        
        if ($categoryId > 0) {
            $sql .= " AND p.category_id = " . $categoryId;
        }
        
        $stmt = $this->productModel->db->prepare($sql);
        $stmt->bind_param('sss', $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'];
    }
}