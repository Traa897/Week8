<!-- views/transactions/index.php - WITH STATUS FILTER -->

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2>Data Transaksi</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="index.php?c=transactions&a=create" class="btn btn-success">
                <i class="fas fa-plus"></i> Transaksi Baru
            </a>
        </div>
    </div>

    <!-- STATUS TABS -->
    <div class="card mb-3">
        <div class="card-body">
            <ul class="nav nav-pills mb-3">
                <li class="nav-item">
                    <a class="nav-link <?= empty($status) ? 'active' : '' ?>" 
                       href="index.php?c=transactions&a=index">
                        <i class="fas fa-list"></i> Semua
                        <span class="badge bg-secondary ms-1"><?= $total ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $status === 'pending' ? 'active' : '' ?>" 
                       href="index.php?c=transactions&a=index&status=pending">
                        <i class="fas fa-clock"></i> Pending
                        <span class="badge bg-warning text-dark ms-1"><?= $pendingCount ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $status === 'completed' ? 'active' : '' ?>" 
                       href="index.php?c=transactions&a=index&status=completed">
                        <i class="fas fa-check-circle"></i> Completed
                        <span class="badge bg-success ms-1"><?= $completedCount ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $status === 'cancelled' ? 'active' : '' ?>" 
                       href="index.php?c=transactions&a=index&status=cancelled">
                        <i class="fas fa-times-circle"></i> Cancelled
                        <span class="badge bg-danger ms-1"><?= $cancelledCount ?></span>
                    </a>
                </li>
            </ul>

            <!-- SEARCH -->
            <form method="GET" action="index.php">
                <input type="hidden" name="c" value="transactions">
                <input type="hidden" name="a" value="index">
                <?php if ($status): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
                <?php endif; ?>
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

    <div class="card">
        <div class="card-body">
            <p class="text-muted">
                Menampilkan: <?= $total ?> transaksi
                <?php if ($status): ?>
                    (<?= strtoupper($status) ?>)
                <?php endif; ?>
            </p>
            
            <?php if (empty($transactions)): ?>
                <div class="alert alert-info">Belum ada transaksi</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode Transaksi</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Telepon</th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = ($page - 1) * 10 + 1;
                            foreach($transactions as $trans): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($trans['transaction_code']) ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($trans['transaction_date'])) ?></td>
                                <td><?= htmlspecialchars($trans['customer_name']) ?></td>
                                <td><?= htmlspecialchars($trans['customer_phone']) ?></td>
                                <td><strong><?= formatRupiah($trans['total_amount']) ?></strong></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= strtoupper($trans['payment_method']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $badgeColor = [
                                        'pending' => 'warning',
                                        'completed' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    ?>
                                    <span class="badge bg-<?= $badgeColor[$trans['status']] ?>">
                                        <?= strtoupper($trans['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?c=transactions&a=detail&id=<?= $trans['id'] ?>" 
                                       class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($trans['status'] == 'pending'): ?>
                                    <form method="POST" action="index.php?c=transactions&a=delete" 
                                          style="display:inline;" 
                                          onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                        <input type="hidden" name="id" value="<?= $trans['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="index.php?c=transactions&a=index&page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= $status ?>">
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
</div>