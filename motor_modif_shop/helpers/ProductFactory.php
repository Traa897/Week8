<?php
/**
 * FACTORY PATTERN - ProductFactory
 * Week 7 Requirement: 5 methods
 * 
 * File: motor_modif_shop/helpers/ProductFactory.php
 * 
 * Methods:
 * 1. create()
 * 2. createMany()
 * 3. createForMotorType()
 * 4. createForSupplier()
 * 5. createInRange()
 */

class ProductFactory {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * 1. CREATE - Buat produk baru
     */
    public function create($data) {
        $sql = "INSERT INTO products 
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
        
        return $stmt->execute();
    }
    
    /**
     * 2. CREATE_MANY - Bulk insert multiple products
     */
    public function createMany($products) {
        $this->db->begin_transaction();
        
        try {
            foreach ($products as $product) {
                if (!$this->create($product)) {
                    throw new Exception('Failed to create product');
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * 3. CREATE_FOR_MOTOR_TYPE - Buat produk untuk tipe motor tertentu
     */
    public function createForMotorType($motorType, $data) {
        $data['motor_type'] = $motorType;
        
        // Generate code berdasarkan motor type jika tidak ada
        if (!isset($data['code']) || empty($data['code'])) {
            $prefix = strtoupper(substr($motorType, 0, 3));
            $data['code'] = $prefix . rand(1000, 9999);
        }
        
        return $this->create($data);
    }
    
    /**
     * 4. CREATE_FOR_SUPPLIER - Buat produk dari supplier tertentu
     */
    public function createForSupplier($supplierId, $data) {
        $data['supplier_id'] = $supplierId;
        
        // Generate code jika tidak ada
        if (!isset($data['code']) || empty($data['code'])) {
            $data['code'] = 'SPR' . rand(1000, 9999);
        }
        
        return $this->create($data);
    }
    
    /**
     * 5. CREATE_IN_RANGE - Buat produk dalam range waktu
     */
    public function createInRange($startDate, $endDate, $count = 10) {
        $products = [];
        
        // Data realistis untuk Motor Modif Shop
        $categories = [1, 2, 3, 4];
        $suppliers = [1, 2, 3];
        $motorTypes = ['Vario 150', 'Beat', 'Scoopy', 'PCX', 'Nmax', 'Mio', 'Aerox'];
        $brands = ['Yamaha', 'Honda', 'Kawasaki', 'Suzuki', 'KTC', 'Takegawa'];
        $productNames = [
            'Knalpot Racing',
            'Ban Tubeless',
            'Velg Racing',
            'Spion Lipat',
            'Lampu LED',
            'Jok Custom',
            'Handgrip Racing',
            'Cover Body',
            'Shock Breaker',
            'CVT Racing',
            'Kampas Rem',
            'Filter Udara',
            'Busi Iridium',
            'Oli Mesin',
            'Gear Set'
        ];
        
        for ($i = 0; $i < $count; $i++) {
            $products[] = [
                'category_id' => $categories[array_rand($categories)],
                'supplier_id' => $suppliers[array_rand($suppliers)],
                'code' => 'SPR' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'name' => $productNames[array_rand($productNames)] . ' ' . ($i + 1),
                'brand' => $brands[array_rand($brands)],
                'description' => 'Produk berkualitas tinggi untuk motor modifikasi',
                'price' => rand(50000, 2000000),
                'stock' => rand(5, 100),
                'motor_type' => $motorTypes[array_rand($motorTypes)],
                'image' => ''
            ];
        }
        
        return $this->createMany($products);
    }
}
