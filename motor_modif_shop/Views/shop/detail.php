<!-- views/shop/detail.php - Product Detail -->

<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?c=shop&a=index">Katalog</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Image -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-body text-center p-5" style="background: #f8f9fa;">
                    <?php if ($product['image']): ?>
                        <img src="motor_modif_shop/public/uploads/products/<?= htmlspecialchars($product['image']) ?>" 
                             class="img-fluid" alt="<?= htmlspecialchars($product['name']) ?>"
                             style="max-height: 400px;">
                    <?php else: ?>
                        <i class="fas fa-box fa-10x text-muted"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <!-- Category Badge -->
                    <span class="badge bg-info mb-2">
                        <?= htmlspecialchars($product['category_name']) ?>
                    </span>
                    
                    <!-- Product Name -->
                    <h2 class="mb-3"><?= htmlspecialchars($product['name']) ?></h2>
                    
                    <!-- Product Code -->
                    <p class="text-muted mb-2">
                        <i class="fas fa-barcode"></i> Kode: <strong><?= htmlspecialchars($product['code']) ?></strong>
                    </p>
                    
                    <!-- Brand -->
                    <?php if ($product['brand']): ?>
                    <p class="text-muted mb-2">
                        <i class="fas fa-tag"></i> Brand: <strong><?= htmlspecialchars($product['brand']) ?></strong>
                    </p>
                    <?php endif; ?>
                    
                    <!-- Motor Type -->
                    <?php if ($product['motor_type']): ?>
                    <p class="text-muted mb-3">
                        <i class="fas fa-motorcycle"></i> Cocok untuk: <strong><?= htmlspecialchars($product['motor_type']) ?></strong>
                    </p>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <!-- Price -->
                    <div class="mb-4">
                        <p class="text-muted mb-1">Harga:</p>
                        <h3 class="text-success mb-0"><?= formatRupiah($product['price']) ?></h3>
                    </div>
                    
                    <!-- Stock -->
                    <div class="mb-4">
                        <p class="text-muted mb-1">Ketersediaan:</p>
                        <?php if ($product['stock'] > 10): ?>
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check-circle"></i> Stok Tersedia (<?= $product['stock'] ?>)
                            </span>
                        <?php elseif ($product['stock'] > 0): ?>
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="fas fa-exclamation-triangle"></i> Stok Terbatas (<?= $product['stock'] ?>)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger fs-6">
                                <i class="fas fa-times-circle"></i> Stok Habis
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Supplier -->
                    <div class="mb-4">
                        <p class="text-muted mb-1">Supplier:</p>
                        <p class="mb-0"><strong><?= htmlspecialchars($product['supplier_name']) ?></strong></p>
                    </div>
                    
                    <hr>
                    
                    <!-- Add to Cart Form -->
                    <?php if ($product['stock'] > 0): ?>
                    <form id="addToCartForm">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Jumlah:</label>
                                <input type="number" id="quantity" class="form-control" 
                                       value="1" min="1" max="<?= $product['stock'] ?>" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                            </button>
                            <a href="index.php?c=checkout&a=cart" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-shopping-cart"></i> Lihat Keranjang
                            </a>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Produk ini sedang tidak tersedia
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Description -->
            <?php if ($product['description']): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Deskripsi Produk</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Back Button -->
    <div class="mt-4">
        <a href="index.php?c=shop&a=index" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Katalog
        </a>
    </div>
</div>

<script>
document.getElementById('addToCartForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const quantity = parseInt(document.getElementById('quantity').value);
    const stock = <?= $product['stock'] ?>;
    
    if (quantity > stock) {
        alert('Jumlah melebihi stok tersedia!');
        return;
    }
    
    const formData = new FormData();
    formData.append('product_id', <?= $product['id'] ?>);
    formData.append('quantity', quantity);
    formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch('index.php?c=checkout&a=addToCart', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message + '\n\nTotal item di keranjang: ' + data.cart_count);
            
            // Update cart badge
            const cartBadge = document.querySelector('.cart-badge');
            if (cartBadge) {
                cartBadge.textContent = data.cart_count;
            }
            
            // Reset quantity
            document.getElementById('quantity').value = 1;
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
});
</script>