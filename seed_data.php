<?php
/**
 * SEED DATA - Factory Pattern Demo
 * File: Week8/seed_data.php
 * 
 * Generate:
 * - 50 Products
 * - 20 Customers
 */

session_start();
define('BASE_PATH', __DIR__ . '/motor_modif_shop/');

require_once BASE_PATH . 'config/database.php';
require_once BASE_PATH . 'helpers/ProductFactory.php';
require_once BASE_PATH . 'helpers/CustomerFactory.php';

$database = new Database();
$db = $database->getConnection();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seed Data - Factory Pattern Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { padding: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .result-box { 
            background: white; 
            padding: 25px; 
            border-radius: 10px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 25px;
        }
        .step-title { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; 
            padding: 15px 20px; 
            border-radius: 8px; 
            margin-bottom: 20px;
        }
        .badge-custom { font-size: 0.9rem; padding: 8px 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="text-white mb-3"><i class="fas fa-database"></i> Seed Data Generator</h1>
            <p class="text-white">Factory Pattern Demo - Motor Modif Shop</p>
        </div>

        <?php
        // ========================================
        // STEP 1: ProductFactory - 50 Products
        // ========================================
        ?>
        <div class="result-box">
            <div class="step-title">
                <h3 class="mb-0"><i class="fas fa-box"></i> Step 1: Generate 50 Products</h3>
            </div>
            
            <?php
            $productFactory = new ProductFactory($db);
            
            echo "<h5><i class='fas fa-spinner fa-spin'></i> Creating Products...</h5>";
            
            $result = $productFactory->createInRange('2024-01-01', '2024-12-31', 50);
            
            if ($result) {
                $checkSql = "SELECT COUNT(*) as total FROM products WHERE deleted_at IS NULL";
                $checkResult = $db->query($checkSql);
                $row = $checkResult->fetch_assoc();
                $totalProducts = $row['total'];
                
                echo "<div class='alert alert-success mt-3'>";
                echo "<i class='fas fa-check-circle'></i> <strong>SUCCESS!</strong><br>";
                echo "✅ 50 products berhasil dibuat<br>";
                echo "📊 Total produk di database: <strong>{$totalProducts}</strong>";
                echo "</div>";
                
                $sampleSql = "SELECT * FROM products WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT 5";
                $sampleResult = $db->query($sampleSql);
                
                echo "<h6 class='mt-4'>Sample Products (5 terakhir):</h6>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-sm table-hover'>";
                echo "<thead class='table-dark'><tr><th>Code</th><th>Name</th><th>Brand</th><th>Motor Type</th><th>Price</th><th>Stock</th></tr></thead>";
                echo "<tbody>";
                
                while ($product = $sampleResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><span class='badge bg-primary'>{$product['code']}</span></td>";
                    echo "<td>{$product['name']}</td>";
                    echo "<td>{$product['brand']}</td>";
                    echo "<td><span class='badge bg-info'>{$product['motor_type']}</span></td>";
                    echo "<td><strong>Rp " . number_format($product['price'], 0, ',', '.') . "</strong></td>";
                    echo "<td><span class='badge bg-success'>{$product['stock']}</span></td>";
                    echo "</tr>";
                }
                
                echo "</tbody></table></div>";
                
            } else {
                echo "<div class='alert alert-danger mt-3'>";
                echo "<i class='fas fa-times-circle'></i> <strong>FAILED!</strong><br>";
                echo "❌ Gagal membuat products. Cek database connection.";
                echo "</div>";
            }
            ?>
        </div>

        <?php
        // ========================================
        // STEP 2: CustomerFactory - 20 Customers
        // ========================================
        ?>
        <div class="result-box">
            <div class="step-title">
                <h3 class="mb-0"><i class="fas fa-users"></i> Step 2: Generate 20 Customers</h3>
            </div>
            
            <?php
            $customerFactory = new CustomerFactory($db);
            
            echo "<h5><i class='fas fa-spinner fa-spin'></i> Creating Customers...</h5>";
            
            $customers = [];
            $cities = ['Jakarta', 'Balikpapan', 'Samarinda', 'Surabaya', 'Bandung', 'Semarang', 'Medan', 'Makassar'];
            $firstNames = ['Ahmad', 'Budi', 'Citra', 'Dedi', 'Eka', 'Fitri', 'Gita', 'Hadi', 'Indra', 'Joko'];
            $lastNames = ['Santoso', 'Wijaya', 'Pratama', 'Rahman', 'Setiawan', 'Nugraha', 'Kurniawan', 'Putra', 'Kusuma', 'Hidayat'];
            
            for ($i = 0; $i < 20; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $city = $cities[array_rand($cities)];
                
                $customers[] = [
                    'name' => $firstName . ' ' . $lastName,
                    'phone' => '08' . rand(1000000000, 9999999999),
                    'email' => strtolower($firstName) . '.' . strtolower($lastName) . '@example.com',
                    'address' => 'Jl. ' . $lastName . ' No. ' . rand(1, 200),
                    'city' => $city
                ];
            }
            
            $result = $customerFactory->createMany($customers);
            
            if ($result) {
                $checkSql = "SELECT COUNT(*) as total FROM customers";
                $checkResult = $db->query($checkSql);
                $row = $checkResult->fetch_assoc();
                $totalCustomers = $row['total'];
                
                echo "<div class='alert alert-success mt-3'>";
                echo "<i class='fas fa-check-circle'></i> <strong>SUCCESS!</strong><br>";
                echo "✅ 20 customers berhasil dibuat<br>";
                echo "📊 Total customers di database: <strong>{$totalCustomers}</strong>";
                echo "</div>";
                
                $sampleSql = "SELECT * FROM customers ORDER BY created_at DESC LIMIT 5";
                $sampleResult = $db->query($sampleSql);
                
                echo "<h6 class='mt-4'>Sample Customers (5 terakhir):</h6>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-sm table-hover'>";
                echo "<thead class='table-dark'><tr><th>Name</th><th>Phone</th><th>Email</th><th>City</th></tr></thead>";
                echo "<tbody>";
                
                while ($customer = $sampleResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><strong>{$customer['name']}</strong></td>";
                    echo "<td>{$customer['phone']}</td>";
                    echo "<td><small>{$customer['email']}</small></td>";
                    echo "<td><span class='badge bg-warning text-dark'>{$customer['city']}</span></td>";
                    echo "</tr>";
                }
                
                echo "</tbody></table></div>";
                
            } else {
                echo "<div class='alert alert-danger mt-3'>";
                echo "<i class='fas fa-times-circle'></i> <strong>FAILED!</strong><br>";
                echo "❌ Gagal membuat customers.";
                echo "</div>";
            }
            ?>
        </div>

        <?php
        // ========================================
        // SUMMARY
        // ========================================
        ?>
        <div class="result-box">
            <div class="step-title">
                <h3 class="mb-0"><i class="fas fa-chart-bar"></i> Summary</h3>
            </div>
            
            <?php
            $productCount = $db->query("SELECT COUNT(*) as total FROM products WHERE deleted_at IS NULL")->fetch_assoc()['total'];
            $customerCount = $db->query("SELECT COUNT(*) as total FROM customers")->fetch_assoc()['total'];
            $categoryCount = $db->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc()['total'];
            $supplierCount = $db->query("SELECT COUNT(*) as total FROM suppliers")->fetch_assoc()['total'];
            $transactionCount = $db->query("SELECT COUNT(*) as total FROM transaksi")->fetch_assoc()['total'];
            ?>
            
            <div class="row text-center g-3 mb-4">
                <div class="col-md-2">
                    <div class="p-4 bg-primary text-white rounded shadow">
                        <h2 class="mb-0"><?= $productCount ?></h2>
                        <p class="mb-0"><i class="fas fa-box"></i> Products</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-4 bg-success text-white rounded shadow">
                        <h2 class="mb-0"><?= $customerCount ?></h2>
                        <p class="mb-0"><i class="fas fa-users"></i> Customers</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-4 bg-info text-white rounded shadow">
                        <h2 class="mb-0"><?= $categoryCount ?></h2>
                        <p class="mb-0"><i class="fas fa-tags"></i> Categories</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-4 bg-warning text-dark rounded shadow">
                        <h2 class="mb-0"><?= $supplierCount ?></h2>
                        <p class="mb-0"><i class="fas fa-truck"></i> Suppliers</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="p-4 bg-danger text-white rounded shadow">
                        <h2 class="mb-0"><?= $transactionCount ?></h2>
                        <p class="mb-0"><i class="fas fa-shopping-cart"></i> Transactions</p>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="text-center">
                <h4 class="text-success mb-3"><i class="fas fa-check-circle"></i> Factory Pattern Demo Completed!</h4>
                <p class="text-muted">Database berhasil di-seed dengan data dummy menggunakan Factory Pattern</p>
                <a href="index.php" class="btn btn-primary btn-lg mt-3">
                    <i class="fas fa-home"></i> Ke Dashboard
                </a>
            </div>
        </div>

    </div>
</body>
</html>

<?php
$database->close();
?>