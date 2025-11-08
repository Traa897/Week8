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
    'user' => 'fa-shopping-cart'
];
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
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.3rem;
        }
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
        .content-wrapper {
            padding: 20px;
        }
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
        .user-info {
            background: rgba(255,255,255,0.1);
            padding: 10px 15px;
            border-radius: 8px;
        }
        .dropdown-menu {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 px-0 sidebar">
                <a href="index.php" class="<?= (!isset($_GET['c']) || $_GET['c'] == 'dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                
                <?php if (Auth::hasRole(['developer', 'admin', 'user'])): ?>
                <div class="menu-section">Master Data</div>
                <?php endif; ?>
                
                <?php if (Auth::hasRole(['developer', 'admin'])): ?>
                <a href="index.php?c=suppliers&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'suppliers') ? 'active' : '' ?>">
                    <i class="fas fa-truck"></i> Suppliers
                </a>
                <a href="index.php?c=categories&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'categories') ? 'active' : '' ?>">
                    <i class="fas fa-tags"></i> Kategori
                </a>
                <?php endif; ?>
                
                <a href="index.php?c=products&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'products') ? 'active' : '' ?>">
                    <i class="fas fa-box"></i> Produk
                </a>
                
                <?php if (Auth::hasRole(['developer', 'admin'])): ?>
                <a href="index.php?c=customers&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'customers') ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Pelanggan
                </a>
                <?php endif; ?>
                
                <?php if (Auth::hasRole(['developer', 'admin', 'user'])): ?>
                <div class="menu-section">Transaksi</div>
                <a href="index.php?c=transactions&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'transactions') ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i> Transaksi
                </a>
                <?php endif; ?>
                
                <?php if (Auth::hasRole(['developer', 'admin'])): ?>
                <div class="menu-section">System</div>
                <a href="index.php?c=recyclebin&a=index" class="<?= (isset($_GET['c']) && $_GET['c'] == 'recyclebin') ? 'active' : '' ?>">
                    <i class="fas fa-trash-restore"></i> Recycle Bin
                </a>
                <?php endif; ?>
                
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
                <?php if ($flash = getFlash()): ?>
                    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
                        <?= $flash['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>