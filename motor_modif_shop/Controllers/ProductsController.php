<?php
/**
 * PRODUCTS CONTROLLER - FIXED
 * Added CSRF protection to delete()
 */

require_once BASE_PATH . 'controllers/BaseController.php';
require_once BASE_PATH . 'models/Product.php';
require_once BASE_PATH . 'models/Category.php';
require_once BASE_PATH . 'models/Supplier.php';

class ProductsController extends BaseController {
    private $productModel;
    private $categoryModel;
    private $supplierModel;
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        $this->productModel = new Product($db);
        $this->categoryModel = new Category($db);
        $this->supplierModel = new Supplier($db);
    }
    
    public function index() {
        $search = isset($_GET['search']) ? clean($_GET['search']) : '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        
        $products = $this->productModel->all($search, $page, $limit);
        $total = $this->productModel->count($search);
        $totalPages = ceil($total / $limit);
        
        $this->view('products/index', [
            'products' => $products,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total
        ]);
    }
    
    public function create() {
        $categories = $this->categoryModel->all();
        $suppliers = $this->supplierModel->all();
        
        $this->view('products/create', [
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }
    
    public function store() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $data = [
            'category_id' => clean($_POST['category_id'] ?? ''),
            'supplier_id' => clean($_POST['supplier_id'] ?? ''),
            'code' => Sanitizer::alphanumeric(strtoupper($_POST['code'] ?? '')),
            'name' => Sanitizer::stripTags($_POST['name'] ?? ''),
            'brand' => Sanitizer::stripTags($_POST['brand'] ?? ''),
            'description' => Sanitizer::stripTags($_POST['description'] ?? ''),
            'price' => clean($_POST['price'] ?? 0),
            'stock' => clean($_POST['stock'] ?? 0),
            'motor_type' => Sanitizer::stripTags($_POST['motor_type'] ?? ''),
            'image' => ''
        ];
        
        $validator = new Validator($data, $this->db);
        
        $validator->field('code')
                  ->required()
                  ->unique('products', 'code');
        
        $validator->field('name')
                  ->required()
                  ->minLength(3)
                  ->maxLength(100);
        
        $validator->field('category_id')
                  ->required()
                  ->numeric();
        
        $validator->field('supplier_id')
                  ->required()
                  ->numeric();
        
        $validator->field('price')
                  ->required()
                  ->numeric()
                  ->between(1000, 100000000);
        
        $validator->field('stock')
                  ->required()
                  ->numeric()
                  ->between(0, 10000);
        
        if ($validator->fails()) {
            setErrors($validator->getErrors());
            setOld($data);
            $this->redirect('index.php?c=products&a=create');
            return;
        }
        
        $result = $this->productModel->create($data);
        
        if ($result['success']) {
            $this->setFlash('success', 'Produk berhasil ditambahkan');
            clearOld();
            clearErrors();
            $this->redirect('index.php?c=products&a=index');
        } else {
            if (isset($result['errors'])) {
                setErrors($result['errors']);
            }
            setOld($data);
            $this->redirect('index.php?c=products&a=create');
        }
    }
    
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->setFlash('danger', 'Produk tidak ditemukan');
            $this->redirect('index.php?c=products&a=index');
            return;
        }
        
        $categories = $this->categoryModel->all();
        $suppliers = $this->supplierModel->all();
        
        $this->view('products/edit', [
            'product' => $product,
            'categories' => $categories,
            'suppliers' => $suppliers
        ]);
    }
    
    public function update() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->setFlash('danger', 'Produk tidak ditemukan');
            $this->redirect('index.php?c=products&a=index');
            return;
        }
        
        $data = [
            'category_id' => clean($_POST['category_id'] ?? ''),
            'supplier_id' => clean($_POST['supplier_id'] ?? ''),
            'code' => Sanitizer::alphanumeric(strtoupper($_POST['code'] ?? '')),
            'name' => Sanitizer::stripTags($_POST['name'] ?? ''),
            'brand' => Sanitizer::stripTags($_POST['brand'] ?? ''),
            'description' => Sanitizer::stripTags($_POST['description'] ?? ''),
            'price' => clean($_POST['price'] ?? 0),
            'stock' => clean($_POST['stock'] ?? 0),
            'motor_type' => Sanitizer::stripTags($_POST['motor_type'] ?? ''),
            'image' => $product['image']
        ];
        
        $validator = new Validator($data, $this->db);
        
        $validator->field('code')
                  ->required()
                  ->unique('products', 'code', $id);
        
        $validator->field('price')
                  ->required()
                  ->numeric()
                  ->between(1000, 100000000);
        
        $validator->field('stock')
                  ->required()
                  ->numeric()
                  ->between(0, 10000);
        
        if ($validator->fails()) {
            setErrors($validator->getErrors());
            setOld($data);
            $this->redirect('index.php?c=products&a=edit&id=' . $id);
            return;
        }
        
        $result = $this->productModel->update($id, $data);
        
        if ($result['success']) {
            $this->setFlash('success', 'Produk berhasil diupdate');
            clearOld();
            clearErrors();
            $this->redirect('index.php?c=products&a=index');
        } else {
            if (isset($result['errors'])) {
                setErrors($result['errors']);
            }
            setOld($data);
            $this->redirect('index.php?c=products&a=edit&id=' . $id);
        }
    }
    
    public function delete() {
        // CSRF PROTECTION (ADDED)
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $result = $this->productModel->delete($id);
    
        if ($result['success']) {
            $this->setFlash('success', 'Produk berhasil dipindahkan ke Recycle Bin');
        } else {
            $message = $result['message'] ?? 'Produk gagal dihapus';
            $this->setFlash('danger', $message);
        }
    
        $this->redirect('index.php?c=products&a=index');
    }
}