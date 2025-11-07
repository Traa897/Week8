<?php
/**
 * TEST AUTO-DELETE (>30 HARI)
 * File: test_auto_delete.php
 * 
 * Features:
 * 1. Check produk yang akan dihapus
 * 2. Create dummy data untuk testing
 * 3. Run auto-delete
 * 4. Show statistics
 */

session_start();
define('BASE_PATH', __DIR__ . '/motor_modif_shop/');
require_once BASE_PATH . 'config/database.php';
require_once BASE_PATH . 'models/Product.php';
require_once BASE_PATH . 'helpers/Csrf.php';

$database = new Database();
$db = $database->getConnection();
$productModel = new Product($db);

// Handle POST actions
$action = $_POST['action'] ?? '';
$message = '';
$messageType = '';
$autoDeleteResult = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($action)) {
    Csrf::verifyOrFail($_POST['csrf_token'] ?? '');
    
    switch ($action) {
        case 'create_test_data':
            // Create 3 test products dengan berbagai umur
            $testProducts = [
                ['days' => 35, 'name' => 'Test Product - 35 Hari Lalu'],
                ['days' => 40, 'name' => 'Test Product - 40 Hari Lalu'],
                ['days' => 50, 'name' => 'Test Product - 50 Hari Lalu']
            ];
            
            $created = 0;
            foreach ($testProducts as $test) {
                $sql = "INSERT INTO products 
                        (category_id, supplier_id, code, name, brand, description, price, stock, motor_type, deleted_at, created_at) 
                        VALUES (1, 1, CONCAT('TEST', FLOOR(RAND() * 10000)), ?, 'Test Brand', 
                                'Untuk testing auto-delete', 50000, 10, 'Test', 
                                DATE_SUB(NOW(), INTERVAL ? DAY), 
                                DATE_SUB(NOW(), INTERVAL ? DAY))";
                
                $stmt = $db->prepare($sql);
                $stmt->bind_param('sii', $test['name'], $test['days'], $test['days']);
                
                if ($stmt->execute()) {
                    $created++;
                }
            }
            
            if ($created > 0) {
                $message = "✅ Berhasil create {$created} test products dengan berbagai umur di Recycle Bin";
                $messageType = "success";
            } else {
                $message = "❌ Gagal create test products";
                $messageType = "danger";
            }
            break;
            
        case 'run_auto_delete':
            $autoDeleteResult = $productModel->runAutoDelete();
            $message = $autoDeleteResult['message'];
            $messageType = $autoDeleteResult['success'] ? 'success' : 'danger';
            break;
            
        case 'clear_all_trash':
            // Force delete ALL products in recycle bin (for testing only)
            $sql = "DELETE FROM products WHERE deleted_at IS NOT NULL";
            if ($db->query($sql)) {
                $deletedCount = $db->affected_rows;
                $message = "🗑️ Berhasil clear {$deletedCount} produk dari Recycle Bin";
                $messageType = "warning";
            } else {
                $message = "❌ Gagal clear Recycle Bin";
                $messageType = "danger";
            }
            break;
    }
}

// Get statistics
$stats = [
    'total_trashed' => 0,
    'old_30_days' => 0,
    'safe_to_delete' => 0,
    'locked_by_transactions' => 0,
    'under_30_days' => 0
];

// Total di Recycle Bin
$sql = "SELECT COUNT(*) as total FROM products WHERE deleted_at IS NOT NULL";
$result = $db->query($sql);
$stats['total_trashed'] = $result->fetch_assoc()['total'];

// Produk >30 hari
$sql = "SELECT COUNT(*) as total FROM products 
        WHERE deleted_at IS NOT NULL 
        AND deleted_at <= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$result = $db->query($sql);
$stats['old_30_days'] = $result->fetch_assoc()['total'];

// Produk <30 hari
$stats['under_30_days'] = $stats['total_trashed'] - $stats['old_30_days'];

// Safe to delete (tidak ada di transaksi)
$sql = "SELECT COUNT(*) as total FROM products p
        WHERE p.deleted_at IS NOT NULL 
        AND p.deleted_at <= DATE_SUB(NOW(), INTERVAL 30 DAY)
        AND p.id NOT IN (SELECT DISTINCT product_id FROM transaction_details)";
$result = $db->query($sql);
$stats['safe_to_delete'] = $result->fetch_assoc()['total'];

// Locked by transactions
$stats['locked_by_transactions'] = $stats['old_30_days'] - $stats['safe_to_delete'];

// Get old products list
$sql = "SELECT p.id, p.code, p.name, p.deleted_at,
        DATEDIFF(NOW(), p.deleted_at) as days_in_trash,
        (SELECT COUNT(*) FROM transaction_details WHERE product_id = p.id) as transaction_count
        FROM products p
        WHERE p.deleted_at IS NOT NULL 
        AND p.deleted_at <= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY p.deleted_at ASC";
$oldProducts = $db->query($sql)->fetch_all(MYSQLI_ASSOC);

// Get recent products (<30 days)
$sql = "SELECT p.id, p.code, p.name, p.deleted_at,
        DATEDIFF(NOW(), p.deleted_at) as days_in_trash
        FROM products p
        WHERE p.deleted_at IS NOT NULL 
        AND p.deleted_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
        ORDER BY p.deleted_at DESC
        LIMIT 5";
$recentProducts = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Auto-Delete (>30 hari)</title>
    <?= Csrf::metaTag() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 40px;
            min-height: 100vh;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        .stat-box {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 100%;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
        }
        .action-card {
            transition: transform 0.3s;
        }
        .action-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h1 class="mb-0"><i class="fas fa-clock"></i> Test Auto-Delete (>30 hari)</h1>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Informasi:</strong> 
                    Tool ini untuk testing fitur auto-delete. Produk yang sudah >30 hari di Recycle Bin akan dihapus permanen.
                    Produk yang masih ada di transaksi akan dilewati (skip).
                </div>
                
                <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?> alert-dismissible fade show">
                    <strong><?= $message ?></strong>
                    <?php if ($autoDeleteResult && !empty($autoDeleteResult['deleted_products'])): ?>
                        <hr>
                        <h6>Produk yang Dihapus:</h6>
                        <ul class="mb-0">
                            <?php foreach ($autoDeleteResult['deleted_products'] as $prod): ?>
                                <li><strong><?= htmlspecialchars($prod['code']) ?></strong> - <?= htmlspecialchars($prod['name']) ?> (<?= $prod['days'] ?> hari)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    
                    <?php if ($autoDeleteResult && !empty($autoDeleteResult['skipped_products'])): ?>
                        <hr>
                        <h6>Produk yang Di-skip (ada di transaksi):</h6>
                        <ul class="mb-0">
                            <?php foreach ($autoDeleteResult['skipped_products'] as $prod): ?>
                                <li><strong><?= htmlspecialchars($prod['code']) ?></strong> - <?= htmlspecialchars($prod['name']) ?> (<?= $prod['transactions'] ?> transaksi)</li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- STATISTICS -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-chart-bar"></i> Statistik Recycle Bin</h4>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="stat-box">
                            <i class="fas fa-trash fa-2x text-secondary"></i>
                            <div class="stat-number text-secondary"><?= $stats['total_trashed'] ?></div>
                            <div class="stat-label">Total Trashed</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-box">
                            <i class="fas fa-fire fa-2x text-danger"></i>
                            <div class="stat-number text-danger"><?= $stats['old_30_days'] ?></div>
                            <div class="stat-label">>30 Hari</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-box">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                            <div class="stat-number text-success"><?= $stats['safe_to_delete'] ?></div>
                            <div class="stat-label">Bisa Dihapus</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-box">
                            <i class="fas fa-lock fa-2x text-warning"></i>
                            <div class="stat-number text-warning"><?= $stats['locked_by_transactions'] ?></div>
                            <div class="stat-label">Locked</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-box">
                            <i class="fas fa-clock fa-2x text-info"></i>
                            <div class="stat-number text-info"><?= $stats['under_30_days'] ?></div>
                            <div class="stat-label"><30 Hari</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-box">
                            <i class="fas fa-percentage fa-2x text-primary"></i>
                            <div class="stat-number text-primary">
                                <?php 
                                if ($stats['total_trashed'] > 0) {
                                    echo round(($stats['safe_to_delete'] / $stats['total_trashed']) * 100);
                                } else {
                                    echo 0;
                                }
                                ?>%
                            </div>
                            <div class="stat-label">Deletable</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ACTIONS -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card action-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                        <h5>Create Test Data</h5>
                        <p class="text-muted">Buat 3 produk dummy dengan umur 35, 40, dan 50 hari</p>
                        <form method="POST">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="create_test_data">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-database"></i> Create Test Products
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card action-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-robot fa-3x text-danger mb-3"></i>
                        <h5>Run Auto-Delete</h5>
                        <p class="text-muted">Hapus otomatis produk >30 hari (skip yang ada di transaksi)</p>
                        <form method="POST" onsubmit="return confirm('⚠️ PERINGATAN!\n\nJalankan auto-delete sekarang?\n\n• Produk >30 hari akan dihapus PERMANEN\n• Produk yang ada di transaksi akan di-skip\n• Data yang dihapus TIDAK BISA dikembalikan\n\nLanjutkan?')">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="run_auto_delete">
                            <button type="submit" class="btn btn-danger btn-lg w-100">
                                <i class="fas fa-trash-alt"></i> Run Auto-Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card action-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-broom fa-3x text-warning mb-3"></i>
                        <h5>Clear All Trash</h5>
                        <p class="text-muted">Hapus SEMUA produk di Recycle Bin (untuk testing)</p>
                        <form method="POST" onsubmit="return confirm('⚠️ BAHAYA!\n\nHapus SEMUA produk di Recycle Bin?\n\nIni akan menghapus SEMUA tanpa cek transaksi!\n\nHanya untuk testing. Lanjutkan?')">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="clear_all_trash">
                            <button type="submit" class="btn btn-warning btn-lg w-100">
                                <i class="fas fa-exclamation-triangle"></i> Clear All (Danger!)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- OLD PRODUCTS LIST -->
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0"><i class="fas fa-fire"></i> Produk >30 Hari (Will be Deleted)</h4>
            </div>
            <div class="card-body">
                <?php if (empty($oldProducts)): ?>
                    <div class="alert alert-success text-center py-4">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h5>✅ Tidak ada produk yang >30 hari</h5>
                        <p class="mb-0">Semua produk di Recycle Bin masih di bawah 30 hari</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-exclamation-triangle"></i> Ditemukan <?= count($oldProducts) ?> produk</strong> 
                        yang akan dihapus otomatis
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Code</th>
                                    <th>Name</th>
                                    <th width="15%">Deleted At</th>
                                    <th width="12%">Days in Trash</th>
                                    <th width="12%">In Transactions?</th>
                                    <th width="12%">Will Delete?</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($oldProducts as $product): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($product['code']) ?></span></td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($product['deleted_at'])) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-fire"></i> <?= $product['days_in_trash'] ?> hari
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($product['transaction_count'] > 0): ?>
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-lock"></i> Yes (<?= $product['transaction_count'] ?>)
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> No
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($product['transaction_count'] > 0): ?>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-ban"></i> Will Skip
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">
                                                <i class="fas fa-trash"></i> Will Delete
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- RECENT PRODUCTS -->
        <?php if (!empty($recentProducts)): ?>
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0"><i class="fas fa-check-circle"></i> Produk <30 Hari (Safe)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Deleted At</th>
                                <th>Days in Trash</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentProducts as $product): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($product['code']) ?></span></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($product['deleted_at'])) ?></td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="fas fa-clock"></i> <?= $product['days_in_trash'] ?> hari
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="fas fa-shield-alt"></i> Safe (<?= 30 - $product['days_in_trash'] ?> hari lagi)
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- INFO -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0"><i class="fas fa-lightbulb"></i> Cara Kerja Auto-Delete</h4>
            </div>
            <div class="card-body">
                <h5>Algoritma:</h5>
                <ol class="mb-0">
                    <li><strong>Query Database:</strong> <code>SELECT * FROM products WHERE deleted_at <= NOW() - 30 DAY</code></li>
                    <li><strong>Check Transaksi:</strong> Untuk setiap produk, cek di <code>transaction_details</code></li>
                    <li><strong>Keputusan:</strong>
                        <ul>
                            <li><span class="badge bg-danger">DELETE</span> jika tidak ada di transaksi</li>
                            <li><span class="badge bg-warning">SKIP</span> jika ada di transaksi (preserve historical data)</li>
                        </ul>
                    </li>
                    <li><strong>Delete Images:</strong> Hapus file gambar dari server</li>
                    <li><strong>Summary:</strong> Return jumlah deleted & skipped</li>
                </ol>
            </div>
        </div>
        
        <!-- NAVIGATION -->
        <div class="text-center mt-4">
            <a href="index.php?c=recyclebin&a=index" class="btn btn-primary btn-lg me-2">
                <i class="fas fa-trash-restore"></i> Buka Recycle Bin
            </a>
            <a href="index.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-home"></i> Ke Dashboard
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $database->close(); ?>