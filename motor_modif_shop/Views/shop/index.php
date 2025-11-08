<!-- views/shop/index.php - Product Catalog for User -->

<div class="container my-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h2><i class="fas fa-store"></i> Katalog Produk Sparepart Motor</h2>
            <p class="text-muted">Temukan sparepart terbaik untuk motor Anda</p>
        </div>
    </div>
    
    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="index.php">
                <input type="hidden" name="c" value="shop">
                <input type="hidden" name="a" value="index">
                
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari produk (nama, kode, tipe motor)..." 
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                    
                    <div class="col-md-4">
                        <select name="category" class="form-select">
                            <option value="">Semua Kategori</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Results Info -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">
            Menampilkan <strong><?= count($products) ?></strong> dari <strong><?= $total ?></strong> produk
        </p>
        
        <?php if ($search || $categoryId): ?>
        <a href="index.php?c=shop&a=index" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-times"></i> Reset Filter
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Products Grid -->
    <?php if (empty($products)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-box-open fa-3x mb-3"></i>
            <h4>Produk Tidak Ditemukan</h4>
            <p class="mb-0">Coba ubah kata kunci pencarian atau filter kategori</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($products as $product): ?>
            <div class="col-md-3">
                <div class="card product-card">
                    <!-- Product Image -->
                    <div class="product-image d-flex align-items-center justify-content-center">
                        <?php if ($product['image']): ?>
                            <img src="motor_modif_shop/public/uploads/products/<?= htmlspecialchars($product['image']) ?>" 
                                 class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <i class="fas fa-box fa-4x text-muted"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body">
                        <!-- Category Badge -->
                        <span class="badge bg-info mb-2">
                            <?= htmlspecialchars($product['category_name']) ?>
                        </span>
                        
                        <!-- Product Name -->
                        <h6 class="card-title">
                            <a href="index.php?c=shop&a=detail&id=<?= $product['id'] ?>" 
                               class="text-decoration-none text-dark">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h6>
                        
                        <!-- Product Code -->
                        <p class="text-muted small mb-2">
                            <i class="fas fa-barcode"></i> <?= htmlspecialchars($product['code']) ?>
                        </p>
                        
                        <!-- Motor Type -->
                        <?php if ($product['motor_type']): ?>
                        <p class="text-muted small mb-2">
                            <i class="fas fa-motorcycle"></i> <?= htmlspecialchars($product['motor_type']) ?>
                        </p>
                        <?php endif; ?>
                        
                        <!-- Price -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-success mb-0">
                                <?= formatRupiah($product['price']) ?>
                            </h5>
                            <span class="badge <?= $product['stock'] > 10 ? 'bg-success' : 'bg-warning' ?>">
                                Stok: <?= $product['stock'] ?>
                            </span>
                        </div>
                        
                        <!-- Actions -->
                        <div class="d-grid gap-2">
                            <a href="index.php?c=shop&a=detail&id=<?= $product['id'] ?>" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            
                            <?php if ($product['stock'] > 0): ?>
                            <button type="button" class="btn btn-primary btn-sm add-to-cart" 
                                    data-id="<?= $product['id'] ?>"
                                    data-name="<?= htmlspecialchars($product['name']) ?>"
                                    data-price="<?= $product['price'] ?>"
                                    data-stock="<?= $product['stock'] ?>">
                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                            </button>
                            <?php else: ?>
                            <button class="btn btn-secondary btn-sm" disabled>
                                <i class="fas fa-times"></i> Stok Habis
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?c=shop&a=index&page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= $categoryId ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Add to Cart Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const stock = parseInt(this.dataset.stock);
            
            // Simple quantity input
            const quantity = prompt(`Berapa jumlah "${productName}" yang ingin ditambahkan?\n\nStok tersedia: ${stock}`, '1');
            
            if (quantity === null) return; // User cancelled
            
            const qty = parseInt(quantity);
            
            if (isNaN(qty) || qty <= 0) {
                alert('Jumlah tidak valid!');
                return;
            }
            
            if (qty > stock) {
                alert(`Stok tidak mencukupi! Stok tersedia: ${stock}`);
                return;
            }
            
            // Send AJAX request
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', qty);
            formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
            
            fetch('index.php?c=checkout&a=addToCart', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Update cart badge
                    const cartBadge = document.querySelector('.cart-badge');
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                    } else {
                        // Create badge if doesn't exist
                        const cartLink = document.querySelector('a[href*="checkout"]');
                        if (cartLink) {
                            const badge = document.createElement('span');
                            badge.className = 'cart-badge';
                            badge.textContent = data.cart_count;
                            cartLink.style.position = 'relative';
                            cartLink.appendChild(badge);
                        }
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            });
        });
    });
});
</script>