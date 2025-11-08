<!-- views/checkout/cart.php - Shopping Cart -->

<div class="container my-4">
    <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h2>
    
    <?php if (empty($cart)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
            <h4>Keranjang Belanja Kosong</h4>
            <p class="mb-3">Anda belum menambahkan produk ke keranjang</p>
            <a href="index.php?c=shop&a=index" class="btn btn-primary">
                <i class="fas fa-store"></i> Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Cart Items -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th width="15%">Harga</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="15%">Subtotal</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart as $productId => $item): ?>
                                <tr data-product-id="<?= $productId ?>">
                                    <td>
                                        <strong><?= htmlspecialchars($item['name']) ?></strong><br>
                                        <small class="text-muted">Kode: <?= htmlspecialchars($item['code']) ?></small>
                                    </td>
                                    <td><?= formatRupiah($item['price']) ?></td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm qty-input" 
                                               value="<?= $item['quantity'] ?>" 
                                               min="1" 
                                               data-product-id="<?= $productId ?>"
                                               data-price="<?= $item['price'] ?>">
                                    </td>
                                    <td class="subtotal">
                                        <strong><?= formatRupiah($item['price'] * $item['quantity']) ?></strong>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger remove-item" 
                                                data-product-id="<?= $productId ?>"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div class="d-flex justify-content-between">
                            <a href="index.php?c=shop&a=index" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Lanjut Belanja
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator"></i> Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong id="subtotal"><?= formatRupiah($subtotal) ?></strong>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Total:</h5>
                            <h4 class="text-success" id="total"><?= formatRupiah($subtotal) ?></h4>
                        </div>
                        
                        <div class="d-grid">
                            <a href="index.php?c=checkout&a=index" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Lanjut ke Checkout
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Info -->
                <div class="alert alert-info mt-3">
                    <small>
                        <i class="fas fa-info-circle"></i> 
                        <strong>Catatan:</strong> Harga dan ketersediaan produk dapat berubah sewaktu-waktu.
                    </small>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Update quantity
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value);
            const price = parseFloat(this.dataset.price);
            
            if (quantity < 1) {
                this.value = 1;
                return;
            }
            
            // Update subtotal display
            const row = this.closest('tr');
            const subtotalEl = row.querySelector('.subtotal strong');
            subtotalEl.textContent = formatRupiah(price * quantity);
            
            // Update cart total
            updateTotal();
            
            // Send AJAX to update session
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            formData.append('csrf_token', csrfToken);
            
            fetch('index.php?c=checkout&a=updateCart', {
                method: 'POST',
                body: formData
            });
        });
    });
    
    // Remove item
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            if (!confirm('Hapus produk ini dari keranjang?')) return;
            
            const productId = this.dataset.productId;
            const row = this.closest('tr');
            
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('csrf_token', csrfToken);
            
            fetch('index.php?c=checkout&a=removeFromCart', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    row.remove();
                    updateTotal();
                    
                    // Check if cart is empty
                    const tbody = document.querySelector('tbody');
                    if (tbody.children.length === 0) {
                        location.reload();
                    }
                    
                    // Update cart badge
                    updateCartBadge();
                }
            });
        });
    });
    
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.qty-input').forEach(input => {
            const quantity = parseInt(input.value);
            const price = parseFloat(input.dataset.price);
            total += quantity * price;
        });
        
        document.getElementById('subtotal').textContent = formatRupiah(total);
        document.getElementById('total').textContent = formatRupiah(total);
    }
    
    function updateCartBadge() {
        let count = 0;
        document.querySelectorAll('.qty-input').forEach(input => {
            count += parseInt(input.value);
        });
        
        const cartBadge = document.querySelector('.cart-badge');
        if (cartBadge) {
            if (count > 0) {
                cartBadge.textContent = count;
            } else {
                cartBadge.remove();
            }
        }
    }
    
    function formatRupiah(angka) {
        return 'Rp ' + Math.floor(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
});
</script>