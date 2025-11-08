<!-- views/mytransactions/detail.php - Order Detail for User -->

<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?c=mytransactions&a=index">Pesanan Saya</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($transaction['transaction_code']) ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-md-8">
            <h2 class="mb-4">
                <i class="fas fa-receipt"></i> Detail Pesanan
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <?php if ($transaction['status'] == 'completed'): ?>
            <a href="index.php?c=transactions&a=print&id=<?= $transaction['id'] ?>" 
               class="btn btn-success" target="_blank">
                <i class="fas fa-print"></i> Cetak Invoice
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row">
        <!-- Informasi Pesanan -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150"><strong>Kode Pesanan:</strong></td>
                            <td>
                                <span class="badge bg-dark fs-6">
                                    <?= htmlspecialchars($transaction['transaction_code']) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Pesan:</strong></td>
                            <td><?= formatTanggal($transaction['transaction_date']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Waktu:</strong></td>
                            <td><?= date('H:i:s', strtotime($transaction['created_at'])) ?> WIB</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <?php 
                                $statusConfig = [
                                    'pending' => ['color' => 'warning', 'icon' => 'clock', 'text' => 'Menunggu Konfirmasi'],
                                    'completed' => ['color' => 'success', 'icon' => 'check-circle', 'text' => 'Pesanan Selesai'],
                                    'cancelled' => ['color' => 'danger', 'icon' => 'times-circle', 'text' => 'Dibatalkan']
                                ];
                                $status = $statusConfig[$transaction['status']] ?? ['color' => 'secondary', 'icon' => 'question', 'text' => $transaction['status']];
                                ?>
                                <span class="badge bg-<?= $status['color'] ?> fs-6">
                                    <i class="fas fa-<?= $status['icon'] ?>"></i> <?= $status['text'] ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Pembayaran:</strong></td>
                            <td>
                                <span class="badge bg-info">
                                    <?php
                                    $paymentLabels = [
                                        'transfer' => 'Transfer Bank',
                                        'cash' => 'Bayar di Toko (COD)',
                                        'credit' => 'Kartu Kredit/Debit'
                                    ];
                                    echo $paymentLabels[$transaction['payment_method']] ?? strtoupper($transaction['payment_method']);
                                    ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Informasi Pengiriman -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-shipping-fast"></i> Informasi Pengiriman</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="100"><strong>Nama:</strong></td>
                            <td><?= htmlspecialchars($transaction['customer_name']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Telepon:</strong></td>
                            <td><?= htmlspecialchars($transaction['phone']) ?></td>
                        </tr>
                        <tr>
                            <td><strong>Alamat:</strong></td>
                            <td><?= htmlspecialchars($transaction['address']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Detail Produk -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-box"></i> Produk yang Dipesan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th width="15%">Harga</th>
                                    <th width="10%">Qty</th>
                                    <th width="20%">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($details as $item): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-barcode"></i> <?= htmlspecialchars($item['product_code']) ?>
                                        </small>
                                    </td>
                                    <td><?= formatRupiah($item['price']) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?= $item['quantity'] ?></span>
                                    </td>
                                    <td>
                                        <strong><?= formatRupiah($item['subtotal']) ?></strong>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <td colspan="3" class="text-end">
                                        <strong>Total Pembayaran:</strong>
                                    </td>
                                    <td>
                                        <h5 class="text-success mb-0">
                                            <strong><?= formatRupiah($transaction['total_amount']) ?></strong>
                                        </h5>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php if ($transaction['notes']): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-comment"></i> Catatan</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($transaction['notes'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Status Information -->
    <?php if ($transaction['status'] == 'pending'): ?>
    <div class="alert alert-warning mt-4">
        <h5 class="alert-heading">
            <i class="fas fa-clock"></i> Menunggu Konfirmasi Pembayaran
        </h5>
        <hr>
        <p class="mb-0">
            Pesanan Anda sedang dalam proses verifikasi. 
            Silakan lakukan pembayaran sesuai dengan metode yang dipilih:
        </p>
        
        <?php if ($transaction['payment_method'] == 'transfer'): ?>
        <div class="mt-3">
            <strong>Transfer ke:</strong>
            <ul class="mb-0">
                <li>Bank BCA: 1234567890 a.n. Patra Jaya Variasi</li>
                <li>Bank Mandiri: 9876543210 a.n. Patra Jaya Variasi</li>
            </ul>
            <small class="text-muted">Setelah transfer, hubungi admin untuk konfirmasi.</small>
        </div>
        <?php elseif ($transaction['payment_method'] == 'cash'): ?>
        <div class="mt-3">
            <strong>Alamat Toko:</strong>
            <p class="mb-0">Jl. Soekarno Hatta Km. 21 RT 41, Balikpapan Utara</p>
            <small class="text-muted">Jam Operasional: Senin - Sabtu, 08:00 - 17:00</small>
        </div>
        <?php endif; ?>
    </div>
    <?php elseif ($transaction['status'] == 'completed'): ?>
    <div class="alert alert-success mt-4">
        <h5 class="alert-heading">
            <i class="fas fa-check-circle"></i> Pesanan Selesai
        </h5>
        <p class="mb-0">
            Terima kasih telah berbelanja di Patra Jaya Variasi! 
            Pesanan Anda telah selesai diproses.
        </p>
    </div>
    <?php elseif ($transaction['status'] == 'cancelled'): ?>
    <div class="alert alert-danger mt-4">
        <h5 class="alert-heading">
            <i class="fas fa-times-circle"></i> Pesanan Dibatalkan
        </h5>
        <p class="mb-0">Pesanan ini telah dibatalkan.</p>
    </div>
    <?php endif; ?>
    
    <!-- Action Buttons -->
    <div class="mt-4 text-center">
        <a href="index.php?c=mytransactions&a=index" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pesanan
        </a>
        
        <a href="index.php?c=shop&a=index" class="btn btn-primary btn-lg">
            <i class="fas fa-shopping-bag"></i> Belanja Lagi
        </a>
    </div>
</div>