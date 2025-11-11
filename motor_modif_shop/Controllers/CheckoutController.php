<?php
/**
 * CHECKOUT CONTROLLER - FIXED
 * File: motor_modif_shop/controllers/CheckoutController.php
 * 
 * FIX: Konsistensi dengan ProfileController
 */

require_once BASE_PATH . 'controllers/BaseController.php';
require_once BASE_PATH . 'models/Product.php';
require_once BASE_PATH . 'models/Transaction.php';
require_once BASE_PATH . 'models/Customer.php';

class CheckoutController extends BaseController {
    private $productModel;
    private $transactionModel;
    private $customerModel;
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        $this->productModel = new Product($db);
        $this->transactionModel = new Transaction($db);
        $this->customerModel = new Customer($db);
    }
    
    /**
     * Add product to cart (AJAX)
     */
    public function addToCart() {
        header('Content-Type: application/json');
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        $product = $this->productModel->find($productId);
        
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
            exit;
        }
        
        if ($product['stock'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Stok tidak mencukupi']);
            exit;
        }
        
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Add to cart or update quantity
        $cartKey = $productId;
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'code' => $product['code'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image' => $product['image']
            ];
        }
        
        // Calculate cart total
        $cartTotal = 0;
        $cartCount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
            $cartCount += $item['quantity'];
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Produk ditambahkan ke keranjang',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ]);
        exit;
    }
    
    /**
     * View cart
     */
    public function cart() {
        $cart = $_SESSION['cart'] ?? [];
        
        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $this->view('checkout/cart', [
            'cart' => $cart,
            'subtotal' => $subtotal
        ]);
    }
    
    /**
     * Update cart quantity (AJAX)
     */
    public function updateCart() {
        header('Content-Type: application/json');
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            if (isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            }
        }
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    /**
     * Remove from cart (AJAX)
     */
    public function removeFromCart() {
        header('Content-Type: application/json');
        
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    /**
     * Checkout page - FIXED
     */
    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        
        if (empty($cart)) {
            $this->setFlash('warning', 'Keranjang belanja Anda kosong');
            $this->redirect('index.php?c=shop&a=index');
            return;
        }
        
        // FIXED: Get or create customer record
        $customer = $this->getOrCreateCustomer();
        
        // Calculate totals
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $this->view('checkout/index', [
            'cart' => $cart,
            'customer' => $customer,
            'subtotal' => $subtotal
        ]);
    }
    
    /**
     * Process checkout - FIXED
     */
    public function process() {
        Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
        
        $cart = $_SESSION['cart'] ?? [];
        
        if (empty($cart)) {
            $this->setFlash('danger', 'Keranjang belanja kosong');
            $this->redirect('index.php?c=shop&a=index');
            return;
        }
        
        // FIXED: Get customer (must exist now)
        $customer = $this->getOrCreateCustomer();
        
        // VALIDATION: Check if customer has complete profile
        if (empty($customer['phone']) || empty($customer['address']) || empty($customer['city'])) {
            $this->setFlash('warning', '⚠️ Lengkapi profil Anda terlebih dahulu sebelum checkout!');
            $this->redirect('index.php?c=profile&a=index');
            return;
        }
        
        // Prepare transaction data
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }
        
        $data = [
            'customer_id' => $customer['id'],
            'transaction_date' => date('Y-m-d'),
            'total_amount' => $totalAmount,
            'payment_method' => clean($_POST['payment_method'] ?? 'transfer'),
            'status' => 'pending',
            'notes' => clean($_POST['notes'] ?? '')
        ];
        
        // Convert cart to items array
        $items = [];
        foreach ($cart as $item) {
            $items[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ];
        }
        
        // Create transaction
        $result = $this->transactionModel->create($data, $items);
        
        if ($result['success']) {
            // Clear cart
            unset($_SESSION['cart']);
            
            $this->setFlash('success', '✅ Pesanan berhasil dibuat! Kode: ' . $result['code']);
            $this->redirect('index.php?c=mytransactions&a=detail&id=' . $result['id']);
        } else {
            $this->setFlash('danger', '❌ Gagal membuat pesanan: ' . $result['message']);
            $this->redirect('index.php?c=checkout&a=index');
        }
    }
    
    /**
     * FIXED: Get or create customer record
     * Konsisten dengan ProfileController
     */
    private function getOrCreateCustomer() {
        $userEmail = Auth::user()['username'] . '@customer.com';
        
        // Check if customer exists
        $sql = "SELECT * FROM customers WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('s', $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        // Create new customer
        $customerData = [
            'name' => Auth::user()['full_name'],
            'phone' => '',
            'email' => $userEmail,
            'address' => '',
            'city' => ''
        ];
        
        $this->customerModel->create($customerData);
        
        // Get the newly created customer
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}