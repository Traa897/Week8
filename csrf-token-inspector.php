<?php
/**
 * CSRF TOKEN INSPECTOR
 * File: Week8/csrf-token-inspector.php
 * 
 * Untuk Screenshot: Token di Inspect Element + Hasil Test
 */

session_start();
define('BASE_PATH', __DIR__ . '/motor_modif_shop/');
require_once BASE_PATH . 'helpers/Csrf.php';

// Generate token
$token = Csrf::generateToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSRF Token Inspector - Screenshot Helper</title>
    <?= Csrf::metaTag() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            min-height: 100vh;
        }
        .container {
            max-width: 1000px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            margin-bottom: 20px;
        }
        .token-display {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            font-family: monospace;
            word-break: break-all;
            border: 2px solid #0d6efd;
        }
        .step-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .step-number {
            background: #0d6efd;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="text-white"><i class="fas fa-shield-alt"></i> CSRF Token Inspector</h1>
            <p class="text-white">Screenshot Helper untuk Testing CSRF Protection</p>
        </div>

        <!-- STEP 1: Token di Session -->
        <div class="step-card">
            <h3><span class="step-number">1</span> CSRF Token di Session</h3>
            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> Untuk Screenshot:</strong> 
                Buka Developer Tools (F12) → Application → Session Storage → localhost
            </div>
            <div class="token-display">
                <strong>Token:</strong> <?= htmlspecialchars($token) ?>
            </div>
        </div>

        <!-- STEP 2: Form dengan Token (VALID) -->
        <div class="step-card">
            <h3><span class="step-number">2</span> Form dengan CSRF Token (VALID)</h3>
            <div class="alert alert-success">
                <strong><i class="fas fa-check-circle"></i> Expected Result:</strong> 
                Submit berhasil, data tersimpan
            </div>
            
            <form method="POST" action="index.php?c=categories&a=store" class="p-4 bg-light rounded">
                <?= Csrf::field() ?>
                
                <div class="mb-3">
                    <label class="form-label">Nama Kategori:</label>
                    <input type="text" name="name" class="form-control" value="Test CSRF - WITH TOKEN" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Deskripsi:</label>
                    <textarea name="description" class="form-control" rows="2">Kategori ini dibuat dengan CSRF token yang valid</textarea>
                </div>
                
                <!-- TAMPILKAN TOKEN DI HTML -->
                <div class="alert alert-primary">
                    <strong><i class="fas fa-key"></i> CSRF Token (Hidden Input):</strong>
                    <div class="token-display mt-2">
                        <code>&lt;input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>"&gt;</code>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-check"></i> Submit dengan Token (HARUS BERHASIL)
                </button>
            </form>
            
            <div class="mt-3">
                <strong>📸 Screenshot ini:</strong>
                <ul>
                    <li>Inspect element pada form → lihat hidden input csrf_token</li>
                    <li>Klik submit → screenshot halaman sukses atau redirect</li>
                </ul>
            </div>
        </div>

        <!-- STEP 3: Test Attack (NO TOKEN) -->
        <div class="step-card">
            <h3><span class="step-number">3</span> Form TANPA Token (ATTACK)</h3>
            <div class="alert alert-danger">
                <strong><i class="fas fa-exclamation-triangle"></i> Expected Result:</strong> 
                Request ditolak dengan error 403 Forbidden
            </div>
            
            <div class="text-center p-4 bg-light rounded">
                <p class="mb-3">Gunakan external file untuk test attack:</p>
                <a href="csrf-test.html" target="_blank" class="btn btn-danger btn-lg">
                    <i class="fas fa-bomb"></i> Buka CSRF Attack Test
                </a>
            </div>
            
            <div class="mt-3">
                <strong>📸 Screenshot ini:</strong>
                <ul>
                    <li>Buka csrf-test.html di browser</li>
                    <li>Inspect element → lihat TIDAK ADA csrf_token</li>
                    <li>Klik submit → screenshot error 403</li>
                    <li>Screenshot pesan: "CSRF token validation failed"</li>
                </ul>
            </div>
        </div>

        <!-- STEP 4: Verification -->
        <div class="step-card">
            <h3><span class="step-number">4</span> Verifikasi di Database</h3>
            <div class="alert alert-info">
                <strong><i class="fas fa-database"></i> Check Database:</strong>
            </div>
            <ol>
                <li><strong>Test dengan token:</strong> Data kategori "Test CSRF - WITH TOKEN" <strong>HARUS ADA</strong> di database</li>
                <li><strong>Test tanpa token:</strong> Data "HACKED PRODUCT - CSRF ATTACK" <strong>TIDAK BOLEH ADA</strong></li>
            </ol>
            
            <div class="text-center mt-4">
                <a href="index.php?c=categories&a=index" class="btn btn-primary btn-lg me-2">
                    <i class="fas fa-list"></i> Cek Kategori
                </a>
                <a href="index.php?c=products&a=index" class="btn btn-primary btn-lg">
                    <i class="fas fa-box"></i> Cek Products
                </a>
            </div>
        </div>

        <!-- DOKUMENTASI -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="fas fa-book"></i> Dokumentasi Screenshot untuk Laporan</h4>
            </div>
            <div class="card-body">
                <h5>📸 Screenshot yang Dibutuhkan:</h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Screenshot</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><strong>Token di Inspect Element</strong></td>
                            <td>
                                Buka form "Test CSRF - WITH TOKEN" → Klik kanan form → Inspect<br>
                                Screenshot: <code>&lt;input type="hidden" name="csrf_token" value="..."&gt;</code>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td><strong>Submit Berhasil (WITH TOKEN)</strong></td>
                            <td>
                                Submit form dengan token → Screenshot halaman sukses<br>
                                Atau screenshot redirect ke list kategori dengan flash message "berhasil"
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td><strong>Attack Form (NO TOKEN)</strong></td>
                            <td>
                                Buka csrf-test.html → Inspect form<br>
                                Screenshot: Tidak ada <code>csrf_token</code> di form
                            </td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td><strong>Attack Ditolak (403)</strong></td>
                            <td>
                                Submit attack form → Screenshot error 403<br>
                                Pesan: "CSRF token validation failed. Request blocked for security reasons."
                            </td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td><strong>Verifikasi Database</strong></td>
                            <td>
                                Screenshot phpMyAdmin atau list produk<br>
                                Tunjukkan: Data "Test CSRF" ADA, data "HACKED PRODUCT" TIDAK ADA
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-home"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</body>
</html>