<!-- views/mytransactions/index.php - User Order History -->

<div class="container my-4">
    <h2 class="mb-4"><i class="fas fa-shopping-bag"></i> Pesanan Saya</h2>
    
    <?php if (empty($transactions)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-inbox fa-3x mb-3"></i>
            <h4>Belum Ada Pesanan</h4>
            <p class="mb-3">Anda belum pernah melakukan pemesanan</p>
            <a href="index.php?c=shop&a=index" class="btn btn-primary">
                <i class="fas fa-store"></i> Mulai Belanja
            </a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <p class="text-muted">Total: <strong><?= $total ?></strong> pesanan</p>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode Pesanan</th>
                                <th width="12%">Tanggal</th>
                                <th width="18%">Total</th>
                                <th width="15%">Pembayaran</th>
                                <th width="15%">Status</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = ($page - 1) * 10 + 1;
                            foreach($transactions as $trans): 
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($trans['transaction_code']) ?></strong>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($trans['transaction_date'])) ?>
                                    <br>
                                    <small class="text-muted">
                                        <?= date('H:i', strtotime($trans['created_at'])) ?>
                                    </small>
                                </td>
                                <td>
                                    <strong class="text-success">
                                        <?= formatRupiah($trans['total_amount']) ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php
                                        $paymentLabels = [
                                            'transfer' => 'Transfer',
                                            'cash' => 'COD',
                                            'credit' => 'Kartu'
                                        ];
                                        echo $paymentLabels[$trans['payment_method']] ?? strtoupper($trans['payment_method']);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $statusConfig = [
                                        'pending' => ['color' => 'warning', 'icon' => 'clock', 'text' => 'Menunggu'],
                                        'completed' => ['color' => 'success', 'icon' => 'check-circle', 'text' => 'Selesai'],
                                        'cancelled' => ['color' => 'danger', 'icon' => 'times-circle', 'text' => 'Dibatalkan']
                                    ];
                                    $status = $statusConfig[$trans['status']] ?? ['color' => 'secondary', 'icon' => 'question', 'text' => $trans['status']];
                                    ?>
                                    <span class="badge bg-<?= $status['color'] ?>">
                                        <i class="fas fa-<?= $status['icon'] ?>"></i> <?= $status['text'] ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?c=mytransactions&a=detail&id=<?= $trans['id'] ?>" 
                                       class="btn btn-sm btn-primary" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    
                                    <?php if ($trans['status'] == 'completed'): ?>
                                    <a href="index.php?c=transactions&a=print&id=<?= $trans['id'] ?>" 
                                       class="btn btn-sm btn-success" 
                                       target="_blank" 
                                       title="Cetak Invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="index.php?c=mytransactions&a=index&page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Status Legend -->
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="mb-3"><i class="fas fa-info-circle"></i> Keterangan Status:</h6>
                <div class="row">
                    <div class="col-md-4">
                        <span class="badge bg-warning">
                            <i class="fas fa-clock"></i> Menunggu
                        </span>
                        <p class="small text-muted mb-0">Pesanan sedang diproses</p>
                    </div>
                    <div class="col-md-4">
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle"></i> Selesai
                        </span>
                        <p class="small text-muted mb-0">Pesanan telah selesai</p>
                    </div>
                    <div class="col-md-4">
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle"></i> Dibatalkan
                        </span>
                        <p class="small text-muted mb-0">Pesanan dibatalkan</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>