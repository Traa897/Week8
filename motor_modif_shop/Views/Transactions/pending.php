<!-- views/transactions/pending.php - Transaksi Menunggu Konfirmasi -->

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2><i class="fas fa-clock text-warning"></i> Transaksi Menunggu Konfirmasi</h2>
            <p class="text-muted">Daftar pesanan yang menunggu verifikasi pembayaran</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="index.php?c=transactions&a=index" class="btn btn-secondary">
                <i class="fas fa-list"></i> Semua Transaksi
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-warning d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <h5 class="mb-1">
                        Ada <strong><?= $total ?></strong> transaksi menunggu konfirmasi
                    </h5>
                    <p class="mb-0">Segera verifikasi pembayaran untuk menyelesaikan pesanan pelanggan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="index.php">
                <input type="hidden" name="c" value="transactions">
                <input type="hidden" name="a" value="pending">
                <div class="row">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari berdasarkan kode transaksi atau nama pelanggan..." 
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($transactions)): ?>
                <div class="alert alert-success text-center py-5">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                    <h4>✅ Tidak Ada Transaksi Pending</h4>
                    <p class="mb-0">Semua transaksi sudah diproses</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode Transaksi</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Telepon</th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Waktu Tunggu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = ($page - 1) * 10 + 1;
                            foreach($transactions as $trans): 
                                // Calculate waiting time
                                $createdTime = strtotime($trans['created_at']);
                                $now = time();
                                $waitingHours = floor(($now - $createdTime) / 3600);
                                $waitingDays = floor($waitingHours / 24);
                                
                                // Color based on waiting time
                                $waitingClass = 'success';
                                if ($waitingHours > 48) $waitingClass = 'danger';
                                else if ($waitingHours > 24) $waitingClass = 'warning';
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($trans['transaction_code']) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($trans['created_at'])) ?>
                                    </small>
                                </td>
                                <td><?= date('d/m/Y', strtotime($trans['transaction_date'])) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($trans['customer_name']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($trans['customer_phone']) ?></td>
                                <td>
                                    <strong class="text-success">
                                        <?= formatRupiah($trans['total_amount']) ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php
                                        $paymentLabels = [
                                            'transfer' => 'Transfer Bank',
                                            'cash' => 'Bayar di Toko',
                                            'credit' => 'Kartu Kredit'
                                        ];
                                        echo $paymentLabels[$trans['payment_method']] ?? strtoupper($trans['payment_method']);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $waitingClass ?>">
                                        <?php if ($waitingDays > 0): ?>
                                            <?= $waitingDays ?> hari
                                        <?php else: ?>
                                            <?= $waitingHours ?> jam
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?c=transactions&a=detail&id=<?= $trans['id'] ?>" 
                                       class="btn btn-sm btn-primary" title="Cek Detail & Konfirmasi">
                                        <i class="fas fa-eye"></i> Cek
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mt-3">
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="index.php?c=transactions&a=pending&page=<?= $i ?>&search=<?= urlencode($search) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card mt-3">
        <div class="card-body">
            <h6><i class="fas fa-info-circle"></i> Cara Verifikasi Pembayaran:</h6>
            <ol class="mb-0">
                <li>Klik <strong>"Cek"</strong> pada transaksi yang ingin diverifikasi</li>
                <li>Periksa detail pesanan dan metode pembayaran</li>
                <li>Verifikasi bukti transfer (jika transfer bank)</li>
                <li>Klik <strong>"Konfirmasi Pembayaran"</strong> jika valid, atau <strong>"Tolak"</strong> jika tidak valid</li>
            </ol>
        </div>
    </div>
</div>