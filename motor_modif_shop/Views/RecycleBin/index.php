<!-- views/recyclebin/index.php - COMPLETE FIX -->

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h2><i class="fas fa-trash-restore"></i> Recycle Bin - Produk Terhapus</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="index.php?c=products&a=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Produk
            </a>
            <?php if ($total > 0): ?>
            <button type="button" class="btn btn-success" onclick="restoreAllConfirm()">
                <i class="fas fa-undo-alt"></i> Kembalikan Semua
            </button>
            <a href="index.php?c=recyclebin&a=autoDelete" class="btn btn-warning"
               onclick="return confirm('⚠️ Jalankan auto-delete untuk produk >30 hari?\n\nProduk yang terhubung ke transaksi akan dilewati.')">
                <i class="fas fa-clock"></i> Auto-Delete (>30 hari)
            </a>
            <button type="button" class="btn btn-danger" onclick="emptyTrashConfirm()">
                <i class="fas fa-trash-alt"></i> Kosongkan Recycle Bin
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- BULK ACTION BUTTONS -->
    <?php if ($total > 0): ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <label class="form-check-label">
                        <input type="checkbox" id="selectAll" class="form-check-input me-2">
                        <strong>Select All</strong>
                    </label>
                    <span id="selectedCount" class="ms-3 text-muted">(0 selected)</span>
                </div>
                <div class="col-md-6 text-end">
                    <button type="button" id="bulkRestoreBtn" class="btn btn-success" disabled>
                        <i class="fas fa-undo-alt"></i> Restore Selected
                    </button>
                    <button type="button" id="bulkDeleteBtn" class="btn btn-danger" disabled>
                        <i class="fas fa-trash"></i> Delete Selected
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TABLE PRODUK TERHAPUS -->
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Total: <?= $total ?> produk di Recycle Bin</p>
            
            <?php if (empty($products)): ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                    <h4>Recycle Bin Kosong</h4>
                    <p class="mb-0">Tidak ada produk yang dihapus.</p>
                    <hr class="my-4">
                    <a href="index.php?c=products&a=index" class="btn btn-primary">
                        <i class="fas fa-box"></i> Lihat Semua Produk
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="3%">
                                    <input type="checkbox" id="selectAllTable" class="form-check-input">
                                </th>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Dihapus Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = ($page - 1) * 10 + 1;
                            foreach($products as $product): 
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input product-checkbox" 
                                           value="<?= $product['id'] ?>">
                                </td>
                                <td><?= $no++ ?></td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($product['code']) ?></span></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><span class="badge bg-info"><?= htmlspecialchars($product['category_name']) ?></span></td>
                                <td>
                                    <?php 
                                    $deletedAt = strtotime($product['deleted_at']);
                                    $daysAgo = floor((time() - $deletedAt) / (60 * 60 * 24));
                                    echo date('d/m/Y H:i', $deletedAt);
                                    ?>
                                    <br>
                                    <small class="text-muted">(<?= $daysAgo ?> hari yang lalu)</small>
                                    <?php if ($daysAgo >= 30): ?>
                                    <br><span class="badge bg-danger"><i class="fas fa-fire"></i> >30 hari</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- RESTORE BUTTON - DIRECT FORM -->
                                    <form method="POST" action="index.php?c=recyclebin&a=restore" 
                                          style="display:inline;"
                                          onsubmit="return confirm('Kembalikan produk ini?')">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success" title="Kembalikan">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    
                                    <!-- DELETE BUTTON - DIRECT FORM -->
                                    <form method="POST" action="index.php?c=recyclebin&a=forceDelete" 
                                          style="display:inline;"
                                          onsubmit="return confirm('⚠️ PERINGATAN!\n\nProduk akan dihapus PERMANEN!\nData yang sudah dihapus TIDAK BISA dikembalikan.\n\nLanjutkan?')">
                                        <?= Csrf::field() ?>
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus Permanen">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
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
                                <a class="page-link" href="index.php?c=recyclebin&a=index&page=<?= $i ?>&search=<?= urlencode($search) ?>">
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

<!-- HIDDEN FORMS FOR BULK ACTIONS -->
<form id="bulkRestoreForm" method="POST" action="index.php?c=recyclebin&a=bulkRestore" style="display:none;">
    <?= Csrf::field() ?>
    <input type="hidden" name="ids" id="bulkRestoreIds">
</form>

<form id="bulkDeleteForm" method="POST" action="index.php?c=recyclebin&a=bulkDelete" style="display:none;">
    <?= Csrf::field() ?>
    <input type="hidden" name="ids" id="bulkDeleteIds">
</form>

<form id="restoreAllForm" method="POST" action="index.php?c=recyclebin&a=restoreAll" style="display:none;">
    <?= Csrf::field() ?>
</form>

<form id="emptyTrashForm" method="POST" action="index.php?c=recyclebin&a=empty" style="display:none;">
    <?= Csrf::field() ?>
</form>

<script>
// ============================================
// SELECT ALL FUNCTIONALITY
// ============================================
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateBulkButtons();
});

document.getElementById('selectAllTable')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    document.getElementById('selectAll').checked = this.checked;
    updateBulkButtons();
});

// Update buttons when individual checkbox changed
document.querySelectorAll('.product-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkButtons);
});

function updateBulkButtons() {
    const checked = document.querySelectorAll('.product-checkbox:checked');
    const count = checked.length;
    
    document.getElementById('selectedCount').textContent = `(${count} selected)`;
    document.getElementById('bulkRestoreBtn').disabled = count === 0;
    document.getElementById('bulkDeleteBtn').disabled = count === 0;
    
    // Update "Select All" checkbox state
    const allCheckboxes = document.querySelectorAll('.product-checkbox');
    const selectAll = document.getElementById('selectAll');
    const selectAllTable = document.getElementById('selectAllTable');
    if (selectAll) selectAll.checked = count === allCheckboxes.length && count > 0;
    if (selectAllTable) selectAllTable.checked = count === allCheckboxes.length && count > 0;
}

// ============================================
// BULK RESTORE
// ============================================
document.getElementById('bulkRestoreBtn')?.addEventListener('click', function() {
    const checked = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                         .map(cb => cb.value);
    
    if (checked.length === 0) {
        alert('Pilih produk yang ingin dikembalikan');
        return;
    }
    
    if (confirm(`Kembalikan ${checked.length} produk terpilih?`)) {
        document.getElementById('bulkRestoreIds').value = JSON.stringify(checked);
        document.getElementById('bulkRestoreForm').submit();
    }
});

// ============================================
// BULK DELETE
// ============================================
document.getElementById('bulkDeleteBtn')?.addEventListener('click', function() {
    const checked = Array.from(document.querySelectorAll('.product-checkbox:checked'))
                         .map(cb => cb.value);
    
    if (checked.length === 0) {
        alert('Pilih produk yang ingin dihapus');
        return;
    }
    
    if (confirm(`⚠️ PERINGATAN!\n\nHapus PERMANEN ${checked.length} produk terpilih?\n\nData yang sudah dihapus TIDAK BISA dikembalikan!\n\nLanjutkan?`)) {
        document.getElementById('bulkDeleteIds').value = JSON.stringify(checked);
        document.getElementById('bulkDeleteForm').submit();
    }
});

// ============================================
// RESTORE ALL
// ============================================
function restoreAllConfirm() {
    if (confirm('Kembalikan SEMUA produk dari Recycle Bin?')) {
        document.getElementById('restoreAllForm').submit();
    }
}

// ============================================
// EMPTY TRASH
// ============================================
function emptyTrashConfirm() {
    if (confirm('⚠️ PERINGATAN BAHAYA!\n\nIni akan MENGHAPUS PERMANEN semua produk di Recycle Bin!\n\nProduk yang terhubung ke transaksi akan dilewati.\n\nData yang dihapus TIDAK BISA dikembalikan!\n\nLanjutkan?')) {
        document.getElementById('emptyTrashForm').submit();
    }
}

// Initialize button states
updateBulkButtons();
</script>

<style>
/* Highlight selected rows */
.product-checkbox:checked {
    transform: scale(1.2);
}

tr:has(.product-checkbox:checked) {
    background-color: #fff3cd !important;
}

/* Button animations */
.btn:active {
    transform: scale(0.95);
}

/* Badge animations */
.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.1);
}
</style>