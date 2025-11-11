<?php
// Get current user info
$currentUser = Auth::user();
$role = Auth::role();

// Define role badge colors
$roleBadges = [
    'developer' => 'danger',
    'admin' => 'warning',
    'user' => 'success'
];

// Define role icons
$roleIcons = [
    'developer' => 'fa-code',
    'admin' => 'fa-user-shield',
    'user' => 'fa-user'
];

// Check if user role (pembeli)
$isUserRole = Auth::isUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patra Jaya Variasi - <?= ucfirst($role ?? 'System') ?></title>
    
    <?= Csrf::metaTag() ?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: <?= $isUserRole ? '#f0f2f5' : '#f8f9fa' ?>;
        }
        
        /* Navbar untuk USER berbeda dengan Admin/Dev */
        .navbar-user {
            background: linear-gradient(135deg, #3493e6ff 0%, #3493e6ff 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-admin {
            background-color: #212529;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.3rem;
        }
        
        /* Sidebar hanya untuk Admin/Dev */
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #212529;
            padding: 20px 0;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #495057;
            color: #fff;
        }
        .sidebar i {
            width: 25px;
        }
        
        /* Content wrapper */
        .content-wrapper {
            padding: 20px;
        }
        
        /* Shop cards untuk USER */
        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
        }
        
        /* Cart badge */
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }
        
        /* Admin card styles */
        .card {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .stat-card {
            border-left: 4px solid;
        }
        .stat-card.primary { border-left-color: #0d6efd; }
        .stat-card.success { border-left-color: #198754; }
        .stat-card.warning { border-left-color: #ffc107; }
        .stat-card.danger { border-left-color: #dc3545; }
        
        .table {
            background-color: #fff;
        }
        .table thead {
            background-color: #212529;
            color: #fff;
        }
        
        .user-info {
            background: rgba(255,255,255,0.1);
            padding: 10px 15px;
            border-radius: 8px;
        }
        
        .sidebar .menu-section {
            color: #6c757d;
            font-size: 0.75rem;
            text-transform: uppercase;
            padding: 10px 20px 5px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    
<?php if ($isUserRole): ?>
    <!-- NAVBAR UNTUK USER (Pembeli) -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-user">
        <div class="container">
            <a class="navbar-brand" href="index.php?c=shop&a=index">
                <i class="fas fa-motorcycle"></i> Patra Jaya Variasi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['c']) && $_GET['c'] == 'shop') ? 'active' : '' ?>" 
                           href="index.php?c=shop&a=index">
                            <i class="fas fa-home"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['c']) && $_GET['c'] == 'mytransactions') ? 'active' : '' ?>" 
                           href="index.php?c=mytransactions&a=index">
                            <i class="fas fa-shopping-bag"></i> Pesanan Saya
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item position-relative me-3">
                        <a class="nav-link btn btn-light text-dark" href="index.php?c=checkout&a=cart">
                            <i class="fas fa-shopping-cart"></i> Keranjang
                            <?php 
                            $cartCount = 0;
                            if (isset($_SESSION['cart'])) {
                                foreach ($_SESSION['cart'] as $item) {
                                    $cartCount += $item['quantity'];
                                }
                            }
                            if ($cartCount > 0):
                            ?>
                            <span class="cart-badge"><?= $cartCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-info" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas <?= $roleIcons[$role] ?>"></i>
                            <strong><?= htmlspecialchars($currentUser['full_name']) ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="index.php?c=profile&a=index">
                                    <i class="fas fa-user-circle"></i> Profil Saya
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Content langsung tanpa sidebar untuk USER -->
    <div class="content-wrapper">
        
<?php else: ?>
    <!-- NAVBAR UNTUK ADMIN & DEVELOPER -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-admin">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-motorcycle"></i> Patra Jaya Variasi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-info" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas <?= $roleIcons[$role] ?>"></i>
                            <strong><?= htmlspecialchars($currentUser['full_name']) ?></strong>
                            <span class="badge bg-<?= $roleBadges[$role] ?> ms-2"><?= ucfirst($role) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item disabled" href="#">
                                    <i class="fas fa-user"></i> <?= htmlspecialchars($currentUser['username']) ?>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fas fa-sign-out-alt text-danger"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- LAYOUT DENGAN SIDEBAR untuk Admin/Dev -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 px-0 sidebar">
                <a href="index.php" class="<?= (!isset($_GET['c']) || $_GET['c'] == 'dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                
                <div class="menu-section">Master Data</div>
                
                <a href="index.php?c=suppliers&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'suppliers') ? 'active' : '' ?>">
                    <i class="fas fa-truck"></i> Suppliers
                </a>
                <a href="index.php?c=categories&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'categories') ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i> Kategori
                </a>
                <a href="index.php?c=products&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'products') ? 'active' : '' ?>">
                    <i class="fas fa-box"></i> Produk
                </a>
                <a href="index.php?c=customers&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'customers') ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Pelanggan
                </a>
                
                <div class="menu-section">Transaksi</div>
                
                <!-- PENDING ORDERS - TANPA BADGE COUNT -->
                <a href="index.php?c=transactions&a=pending" class="<?= (isset($_GET['c']) && $_GET['c'] == 'transactions' && isset($_GET['a']) && $_GET['a'] == 'pending') ? 'active' : '' ?>">
                    <i class="fas fa-clock text-warning"></i> Pending Orders
                </a>
                
                <a href="index.php?c=transactions&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'transactions' && (!isset($_GET['a']) || $_GET['a'] == 'index')) ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i> Semua Transaksi
                </a>
                
                <div class="menu-section">System</div>
                <a href="index.php?c=recyclebin&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'recyclebin') ? 'active' : '' ?>">
                    <i class="fas fa-trash-restore"></i> Recycle Bin
                </a>
                
                <?php if (Auth::isDeveloper()): ?>
                <div class="menu-section">Developer</div>
                <a href="index.php?c=users&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'users') ? 'active' : '' ?>">
                    <i class="fas fa-users-cog"></i> Manage Users
                </a>
                <a href="seed_data.php" target="_blank">
                    <i class="fas fa-database"></i> Seed Data
                </a>
                <a href="test_auto_delete.php" target="_blank">
                    <i class="fas fa-clock"></i> Test Auto-Delete
                </a>
                <a href="csrf-token-inspector.php" target="_blank">
                    <i class="fas fa-shield-alt"></i> CSRF Inspector
                </a>
                <a href="debug_recyclebin.php" target="_blank">
                    <i class="fas fa-bug"></i> Debug Recycle
                </a>
                <?php endif; ?>
                
                <hr class="text-muted my-3">
                
                <a href="logout.php" style="color: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

            <div class="col-md-10 content-wrapper">
<?php endif; ?>
                
                <?php if ($flash = getFlash()): ?>
                    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                        <?= $flash['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>