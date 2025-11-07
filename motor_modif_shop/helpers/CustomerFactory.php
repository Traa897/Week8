<?php
/**
 * FACTORY PATTERN - CustomerFactory
 * Week 7 Requirement: 4 methods
 * 
 * File: motor_modif_shop/helpers/CustomerFactory.php
 * 
 * Methods:
 * 1. create()
 * 2. createMany()
 * 3. createByCity()
 * 4. createWithDetails()
 */

class CustomerFactory {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * 1. CREATE - Buat customer baru
     */
    public function create($data) {
        $sql = "INSERT INTO customers (name, phone, email, address, city) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('sssss', 
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['address'],
            $data['city']
        );
        
        return $stmt->execute();
    }
    
    /**
     * 2. CREATE_MANY - Bulk insert multiple customers
     */
    public function createMany($customers) {
        $this->db->begin_transaction();
        
        try {
            foreach ($customers as $customer) {
                if (!$this->create($customer)) {
                    throw new Exception('Failed to create customer');
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
     * 3. CREATE_BY_CITY - Buat customer berdasarkan kota
     */
    public function createByCity($city, $data) {
        $data['city'] = $city;
        return $this->create($data);
    }
    
    /**
     * 4. CREATE_WITH_DETAILS - Buat customer dengan data lengkap
     */
    public function createWithDetails($name, $phone, $email, $address, $city) {
        $data = [
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'city' => $city
        ];
        
        return $this->create($data);
    }
}
