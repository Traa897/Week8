<!-- views/checkout/index.php - Checkout Page -->

<div class="container my-4">
    <h2 class="mb-4"><i class="fas fa-credit-card"></i> Checkout</h2>
    
    <form method="POST" action="index.php?c=checkout&a=process">
        <?= Csrf::field() ?>
        
        <div class="row">
            <!-- Informasi Pengiriman -->
            <div class="col-md-7">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast"></i> Informasi Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($customer['name'] ?? Auth::user()['full_name']) ?>" readonly>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Untuk mengubah nama, silakan update di 
                                <a href="index.php?c=profile&a=index">halaman profil</a>
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>" readonly>
                            <?php if (empty($customer['phone'])): ?>
                            <small class="text-danger">
                                <i class="fas fa-exclamation-triangle"></i> Nomor telepon belum diisi. 
                                Silakan lengkapi di <a href="index.php?c=profile&a=index">halaman profil</a>
                            </small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control" rows="3" readonly><?= htmlspecialchars($customer['address'] ?? '') ?></textarea>
                            <?php if (empty($customer['address'])): ?>
                            <small class="text-danger">
                                <i class="fas fa-exclamation-triangle"></i> Alamat belum diisi. 
                                Silakan lengkapi di <a href="index.php?c=profile&a=index">halaman profil</a>
                            </small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kota</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($customer['city'] ?? '') ?>" readonly>
                            <?php if (empty($customer['city'])): ?>
                            <small class="text-danger">
                                <i class="fas fa-exclamation-triangle"></i> Kota belum diisi. 
                                Silakan lengkapi di <a href="index.php?c=profile&a=index">halaman profil</a>
                            </small>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (empty($customer['phone']) || empty($customer['address']) || empty($customer['city'])): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Perhatian:</strong> Data pengiriman belum lengkap. 
                            <a href="index.php?c=profile&a=index" class="alert-link">Lengkapi sekarang</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Metode Pembayaran -->
                <div class="card mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-wallet"></i> Metode Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" value="transfer" id="transfer" checked>
                            <label class="form-check-label" for="transfer">
                                <i class="fas fa-university"></i> <strong>Transfer Bank</strong>
                                <small class="d-block text-muted">Transfer ke rekening toko</small>
                            </label>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="payment_method" value="cash" id="cash">
                            <label class="form-check-label" for="cash">
                                <i class="fas fa-money-bill-wave"></i> <strong>Bayar di Toko (COD)</strong>
                                <small class="d-block text-muted">Bayar saat mengambil barang di toko</small>
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" value="credit" id="credit">
                            <label class="form-check-label" for="credit">
                                <i class="fas fa-credit-card"></i> <strong>Kartu Kredit/Debit</strong>
                                <small class="d-block text-muted">Pembayaran online</small>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Catatan -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-comment"></i> Catatan Pesanan (Opsional)</h5>
                    </div>
                    <div class="card-body">
                        <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan catatan untuk pesanan Anda..."></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Ringkasan Pesanan -->
            <div class="col-md-5">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt"></i> Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="mb-3" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($cart as $item): ?>
                            <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                                <div class="flex-grow-1">
                                    <strong><?= htmlspecialchars($item['name']) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= formatRupiah($item['price']) ?> x <?= $item['quantity'] ?>
                                    </small>
                                </div>
                                <div class="text-end">
                                    <strong><?= formatRupiah($item['price'] * $item['quantity']) ?></strong>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <hr>
                        
                        <!-- Total -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong><?= formatRupiah($subtotal) ?></strong>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ongkir:</span>
                            <strong class="text-success">GRATIS</strong>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <h5>Total Pembayaran:</h5>
                            <h4 class="text-success"><?= formatRupiah($subtotal) ?></h4>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Buat Pesanan
                            </button>
                            <a href="index.php?c=checkout&a=cart" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Keranjang
                            </a>
                        </div>
                        
                        <!-- Info -->
                        <div class="alert alert-info mt-3 mb-0">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                Pesanan Anda akan diproses setelah pembayaran dikonfirmasi.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const hasPhone = <?= !empty($customer['phone']) ? 'true' : 'false' ?>;
    const hasAddress = <?= !empty($customer['address']) ? 'true' : 'false' ?>;
    const hasCity = <?= !empty($customer['city']) ? 'true' : 'false' ?>;
    
    if (!hasPhone || !hasAddress || !hasCity) {
        e.preventDefault();
        alert('Mohon lengkapi data pengiriman di halaman profil terlebih dahulu!');
        if (confirm('Buka halaman profil sekarang?')) {
            window.location.href = 'index.php?c=profile&a=index';
        }
        return false;
    }
    
    return confirm('Pastikan data pesanan sudah benar. Lanjutkan?');
});
</script>