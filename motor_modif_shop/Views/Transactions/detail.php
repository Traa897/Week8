<!-- views/transactions/detail.php - WITH ADMIN CONFIRMATION -->

<div class="container">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Detail Transaksi</h2>
        </div>
        <div class="col-md-6 text-end">
            <?php if ($transaction['status'] == 'completed'): ?>
            <a href="index.php?c=transactions&a=print&id=<?= $transaction['id'] ?>" 
               class="btn btn-success" target="_blank">
                <i class="fas fa-print"></i> Cetak Invoice
            </a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- ADMIN ACTION BUTTONS (ONLY FOR PENDING) -->
    <?php if (Auth::isAdmin() || Auth::isDeveloper()): ?>
        <?php if ($transaction['status'] == 'pending'): ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Transaksi Menunggu Konfirmasi Admin
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- CONFIRM BUTTON -->
                            <div class="col-md-6">
                                <button type="button" class="btn btn-success btn-lg w-100" 
                                        data-bs-toggle="modal" data-bs-target="#confirmModal">
                                    <i class="fas fa-check-circle"></i> 
                                    Konfirmasi Pembayaran
                                </button>
                            </div>
                            
                            <!-- REJECT BUTTON -->
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger btn-lg w-100" 
                                        data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="fas fa-times-circle"></i> 
                                    Tolak Pembayaran
                                </button>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3 mb-0">
                            <strong><i class="fas fa-info-circle"></i> Petunjuk:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Periksa metode pembayaran dan detail pesanan</li>
                                <li>Jika transfer: Verifikasi bukti transfer dari pelanggan</li>
                                <li>Jika COD: Pastikan pelanggan siap mengambil barang</li>
                                <li><strong>Konfirmasi</strong> jika pembayaran valid</li>
                                <li><strong>Tolak</strong> jika ada masalah dengan pembayaran</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <h3>Patra Jaya Variasi</h3>
                <p class="mb-0">Toko Sparepart Motor</p>
                <p class="text-muted">Jl. Soekarno Hatta 21 | Telp: 081351319657</p>
                <hr>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Informasi Transaksi:</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150">Kode Transaksi</td>
                            <td>: <strong><?= htmlspecialchars($transaction['transaction_code']) ?></strong></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: <?= formatTanggal($transaction['transaction_date']) ?></td>
                        </tr>
                        <tr>
                            <td>Pembayaran</td>
                            <td>: <span class="badge bg-info"><?= strtoupper($transaction['payment_method']) ?></span></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: 
                                <?php 
                                $badgeColor = [
                                    'pending' => 'warning',
                                    'completed' => 'success',
                                    'cancelled' => 'danger'
                                ];
                                ?>
                                <span class="badge bg-<?= $badgeColor[$transaction['status']] ?>">
                                    <?= strtoupper($transaction['status']) ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Data Pelanggan:</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="100">Nama</td>
                            <td>: <?= htmlspecialchars($transaction['customer_name']) ?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td>: <?= htmlspecialchars($transaction['email']) ?></td>
                        </tr>
                        <tr>
                            <td>Telepon</td>
                            <td>: <?= htmlspecialchars($transaction['phone']) ?></td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>: <?= htmlspecialchars($transaction['address']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <h6>Detail Produk:</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Kode</th>
                            <th>Nama Produk</th>
                            <th width="15%">Harga</th>
                            <th width="10%">Qty</th>
                            <th width="15%">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach($details as $item): 
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><strong><?= htmlspecialchars($item['product_code']) ?></strong></td>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td class="text-end"><?= formatRupiah($item['price']) ?></td>
                            <td class="text-center"><?= $item['quantity'] ?></td>
                            <td class="text-end"><strong><?= formatRupiah($item['subtotal']) ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
                            <td class="text-end">
                                <h5 class="mb-0 text-success">
                                    <strong><?= formatRupiah($transaction['total_amount']) ?></strong>
                                </h5>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <?php if ($transaction['notes']): ?>
            <div class="mt-3">
                <h6>Catatan:</h6>
                <div class="alert alert-light">
                    <?= nl2br(htmlspecialchars($transaction['notes'])) ?>
                </div>
            </div>
            <?php endif; ?>

            <hr>
            <p class="text-center text-muted mb-0">
                <small>Terima kasih atas pembelian Anda!</small>
            </p>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?c=transactions&a=confirmPayment">
                <?= Csrf::field() ?>
                <input type="hidden" name="id" value="<?= $transaction['id'] ?>">
                
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle"></i>
                        <strong>Yakin pembayaran sudah valid?</strong>
                    </div>
                    
                    <p>Transaksi: <strong><?= htmlspecialchars($transaction['transaction_code']) ?></strong></p>
                    <p>Customer: <strong><?= htmlspecialchars($transaction['customer_name']) ?></strong></p>
                    <p>Total: <strong><?= formatRupiah($transaction['total_amount']) ?></strong></p>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Admin (Opsional)</label>
                        <textarea name="admin_notes" class="form-control" rows="3" 
                                  placeholder="Contoh: Pembayaran dikonfirmasi via transfer BCA"></textarea>
                    </div>
                    
                    <div class="alert alert-warning mb-0">
                        <small>
                            <strong>⚠️ Perhatian:</strong>
                            <ul class="mb-0 mt-1">
                                <li>Status akan berubah menjadi <strong>COMPLETED</strong></li>
                                <li>Invoice dapat dicetak</li>
                                <li>Aksi ini <strong>tidak bisa dibatalkan</strong></li>
                            </ul>
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Ya, Konfirmasi Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL TOLAK -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-times-circle"></i> Tolak Pembayaran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?c=transactions&a=rejectPayment">
                <?= Csrf::field() ?>
                <input type="hidden" name="id" value="<?= $transaction['id'] ?>">
                
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Yakin ingin menolak transaksi ini?</strong>
                    </div>
                    
                    <p>Transaksi: <strong><?= htmlspecialchars($transaction['transaction_code']) ?></strong></p>
                    <p>Customer: <strong><?= htmlspecialchars($transaction['customer_name']) ?></strong></p>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="reject_reason" class="form-control" rows="4" 
                                  placeholder="Jelaskan alasan penolakan..." required></textarea>
                        <small class="text-muted">Alasan ini akan dilihat oleh pelanggan</small>
                    </div>
                    
                    <div class="alert alert-warning mb-0">
                        <small>
                            <strong>⚠️ Perhatian:</strong>
                            <ul class="mb-0 mt-1">
                                <li>Status akan berubah menjadi <strong>CANCELLED</strong></li>
                                <li>Stok produk akan <strong>dikembalikan</strong></li>
                                <li>Pelanggan perlu membuat pesanan baru</li>
                            </ul>
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Ya, Tolak Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style media="print">
    .btn, nav, .sidebar, footer, .modal, .card.border-warning { display: none !important; }
    .content-wrapper { padding: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
</style>