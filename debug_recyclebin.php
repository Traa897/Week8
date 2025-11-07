<?php
/**
 * DEBUG RECYCLE BIN
 * File: debug_recyclebin.php
 * 
 * Cek status database & troubleshoot masalah
 */

session_start();
define('BASE_PATH', __DIR__ . '/motor_modif_shop/');
require_once BASE_PATH . 'config/database.php';

$database = new Database();
$db = $database->getConnection();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Recycle Bin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            min-height: 100vh;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        .sql-code {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #0d6efd;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h1 class="mb-0"><i class="fas fa-bug"></i> Debug Recycle Bin</h1>
            </div>
            <div class="card-body">
                
                <!-- CHECK 1: Products di Recycle Bin -->
                <h4><i class="fas fa-trash"></i> Check 1: Products di Recycle Bin</h4>
                <?php
                $sql = "SELECT COUNT(*) as total FROM products WHERE deleted_at IS NOT NULL";
                $result = $db->query($sql);
                $row = $result->fetch_assoc();
                $totalTrashed = $row['total'];
                ?>
                <div class="alert alert-info">
                    <strong>Total produk di Recycle Bin:</strong> <?= $totalTrashed ?>
                </div>
                
                <?php if ($totalTrashed > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Deleted At</th>
                                <th>Days Ago</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT id, code, name, deleted_at, 
                                    DATEDIFF(NOW(), deleted_at) as days_ago 
                                    FROM products 
                                    WHERE deleted_at IS NOT NULL 
                                    ORDER BY deleted_at DESC 
                                    LIMIT 10";
                            $result = $db->query($sql);
                            while ($product = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= htmlspecialchars($product['code']) ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= $product['deleted_at'] ?></td>
                                <td>
                                    <?php if ($product['days_ago'] > 30): ?>
                                        <span class="badge bg-danger"><?= $product['days_ago'] ?> hari</span>
                                    <?php else: ?>
                                        <span class="badge bg-success"><?= $product['days_ago'] ?> hari</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                
                <hr>
                
                <!-- CHECK 2: Products in Transactions -->
                <h4><i class="fas fa-link"></i> Check 2: Products Linked to Transactions</h4>
                <?php
                $sql = "SELECT COUNT(DISTINCT product_id) as total 
                        FROM transaction_details td
                        INNER JOIN products p ON td.product_id = p.id
                        WHERE p.deleted_at IS NOT NULL";
                $result = $db->query($sql);
                $row = $result->fetch_assoc();
                $linkedProducts = $row['total'];
                ?>
                <div class="alert alert-warning">
                    <strong>Produk di Recycle Bin yang terhubung ke transaksi:</strong> <?= $linkedProducts ?>
                    <br><small>Produk ini TIDAK BISA dihapus permanen</small>
                </div>
                
                <?php if ($linkedProducts > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Product ID</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Jumlah Transaksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT p.id, p.code, p.name, 
                                    COUNT(td.id) as transaction_count
                                    FROM products p
                                    INNER JOIN transaction_details td ON p.id = td.product_id
                                    WHERE p.deleted_at IS NOT NULL
                                    GROUP BY p.id, p.code, p.name
                                    LIMIT 10";
                            $result = $db->query($sql);
                            while ($product = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= htmlspecialchars($product['code']) ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><span class="badge bg-danger"><?= $product['transaction_count'] ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                
                <hr>
                
                <!-- CHECK 3: Products Safe to Delete -->
                <h4><i class="fas fa-check-circle"></i> Check 3: Products Safe to Delete Permanently</h4>
                <?php
                $sql = "SELECT COUNT(*) as total 
                        FROM products p
                        WHERE p.deleted_at IS NOT NULL
                        AND p.id NOT IN (SELECT DISTINCT product_id FROM transaction_details)";
                $result = $db->query($sql);
                $row = $result->fetch_assoc();
                $safeToDelete = $row['total'];
                ?>
                <div class="alert alert-success">
                    <strong>Produk yang BISA dihapus permanen:</strong> <?= $safeToDelete ?>
                </div>
                
                <?php if ($safeToDelete > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Deleted At</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT p.id, p.code, p.name, p.deleted_at
                                    FROM products p
                                    WHERE p.deleted_at IS NOT NULL
                                    AND p.id NOT IN (SELECT DISTINCT product_id FROM transaction_details)
                                    LIMIT 10";
                            $result = $db->query($sql);
                            while ($product = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $product['id'] ?></td>
                                <td><?= htmlspecialchars($product['code']) ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= $product['deleted_at'] ?></td>
                                <td><span class="badge bg-success"><i class="fas fa-check"></i> Safe to delete</span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
                
                <hr>
                
                <!-- CHECK 4: Test Query Empty Trash -->
                <h4><i class="fas fa-code"></i> Check 4: Test Query Empty Trash</h4>
                <div class="sql-code">
-- Query yang digunakan untuk Empty Trash:
SELECT p.id, p.image 
FROM products p
WHERE p.deleted_at IS NOT NULL
AND p.id NOT IN (
    SELECT DISTINCT product_id 
    FROM transaction_details
);

-- Result: <?= $safeToDelete ?> rows
                </div>
                
                <hr>
                
                <!-- CHECK 5: CSRF Token -->
                <h4><i class="fas fa-shield-alt"></i> Check 5: CSRF Token Status</h4>
                <div class="alert alert-info">
                    <strong>CSRF Token di Session:</strong>
                    <?php if (isset($_SESSION['csrf_token'])): ?>
                        <span class="badge bg-success">✓ ADA</span>
                        <br>
                        <small>Token: <?= substr($_SESSION['csrf_token'], 0, 20) ?>...</small>
                    <?php else: ?>
                        <span class="badge bg-danger">✗ TIDAK ADA</span>
                        <br>
                        <small>CSRF token belum di-generate. Akses halaman dengan form terlebih dahulu.</small>
                    <?php endif; ?>
                </div>
                
                <hr>
                
                <!-- ACTION BUTTONS -->
                <div class="text-center">
                    <a href="index.php?c=recyclebin&a=index" class="btn btn-primary btn-lg me-2">
                        <i class="fas fa-trash-restore"></i> Buka Recycle Bin
                    </a>
                    <a href="index.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-home"></i> Ke Dashboard
                    </a>
                </div>
                
            </div>
        </div>
        
        <!-- SOLUTION CARD -->
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="fas fa-lightbulb"></i> Troubleshooting</h4>
            </div>
            <div class="card-body">
                <h5>Jika tombol delete tidak berfungsi:</h5>
                <ol>
                    <li>
                        <strong>Cek Browser Console (F12)</strong>
                        <ul>
                            <li>Buka Developer Tools → Console</li>
                            <li>Cari error JavaScript atau AJAX</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Cek Network Tab (F12)</strong>
                        <ul>
                            <li>Buka Network tab</li>
                            <li>Klik tombol delete</li>
                            <li>Lihat apakah ada request yang failed (merah)</li>
                            <li>Cek response code (harus 200 atau 302)</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Test Manual</strong>
                        <ul>
                            <li>Klik kanan tombol → Inspect Element</li>
                            <li>Pastikan form memiliki <code>csrf_token</code> hidden input</li>
                            <li>Pastikan action URL benar</li>
                        </ul>
                    </li>
                    <li>
                        <strong>Cek Apache Error Log</strong>
                        <ul>
                            <li>Lokasi: <code>xampp/apache/logs/error.log</code></li>
                            <li>Cari error terbaru setelah klik delete</li>
                        </ul>
                    </li>
                </ol>
                
                <div class="alert alert-danger mt-3">
                    <strong><i class="fas fa-exclamation-triangle"></i> Masalah Umum:</strong>
                    <ul class="mb-0">
                        <li><strong>403 Forbidden:</strong> CSRF token tidak valid atau tidak ada</li>
                        <li><strong>No response:</strong> JavaScript error atau form tidak submit</li>
                        <li><strong>500 Error:</strong> SQL error atau PHP error di controller/model</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php $database->close(); ?>