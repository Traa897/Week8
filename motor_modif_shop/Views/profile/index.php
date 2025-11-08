<!-- views/profile/index.php - User Profile Management -->

<div class="container my-4">
    <h2 class="mb-4"><i class="fas fa-user-circle"></i> Profil Saya</h2>
    
    <div class="row">
        <!-- Edit Profil -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit"></i> Informasi Pribadi</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?c=profile&a=update">
                        <?= Csrf::field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" 
                                   class="form-control <?= getError('name') ? 'is-invalid' : '' ?>" 
                                   value="<?= old('name', $customer['name'] ?? Auth::user()['full_name']) ?>" 
                                   required>
                            <?php if (getError('name')): ?>
                                <div class="invalid-feedback"><?= getError('name') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" 
                                   value="<?= htmlspecialchars($user['username']) ?>" 
                                   readonly>
                            <small class="text-muted">
                                <i class="fas fa-lock"></i> Username tidak dapat diubah
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" 
                                   class="form-control <?= getError('phone') ? 'is-invalid' : '' ?>" 
                                   value="<?= old('phone', $customer['phone'] ?? '') ?>" 
                                   placeholder="08123456789">
                            <?php if (getError('phone')): ?>
                                <div class="invalid-feedback"><?= getError('phone') ?></div>
                            <?php endif; ?>
                            <small class="text-muted">
                                Format: 08XXXXXXXXXX (otomatis diformat menjadi +62-XXX-XXXX-XXXX)
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="address" 
                                      class="form-control <?= getError('address') ? 'is-invalid' : '' ?>" 
                                      rows="3"
                                      placeholder="Jl. Contoh No. 123, RT/RW"><?= old('address', $customer['address'] ?? '') ?></textarea>
                            <?php if (getError('address')): ?>
                                <div class="invalid-feedback"><?= getError('address') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kota</label>
                            <input type="text" name="city" 
                                   class="form-control <?= getError('city') ? 'is-invalid' : '' ?>" 
                                   value="<?= old('city', $customer['city'] ?? '') ?>" 
                                   placeholder="Balikpapan">
                            <?php if (getError('city')): ?>
                                <div class="invalid-feedback"><?= getError('city') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Ganti Password -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-key"></i> Ganti Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="index.php?c=profile&a=changePassword">
                        <?= Csrf::field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" 
                                   class="form-control <?= getError('current_password') ? 'is-invalid' : '' ?>" 
                                   required>
                            <?php if (getError('current_password')): ?>
                                <div class="invalid-feedback"><?= getError('current_password') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" 
                                   class="form-control <?= getError('new_password') ? 'is-invalid' : '' ?>" 
                                   required
                                   minlength="6">
                            <?php if (getError('new_password')): ?>
                                <div class="invalid-feedback"><?= getError('new_password') ?></div>
                            <?php else: ?>
                                <small class="text-muted">Minimal 6 karakter</small>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" 
                                   class="form-control <?= getError('confirm_password') ? 'is-invalid' : '' ?>" 
                                   required
                                   minlength="6">
                            <?php if (getError('confirm_password')): ?>
                                <div class="invalid-feedback"><?= getError('confirm_password') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                <strong>Tips Keamanan:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Gunakan kombinasi huruf besar, kecil, dan angka</li>
                                    <li>Jangan gunakan password yang mudah ditebak</li>
                                    <li>Ganti password secara berkala</li>
                                </ul>
                            </small>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-lg">
                                <i class="fas fa-lock"></i> Ganti Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Informasi Akun -->
            <div class="card mt-3">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Informasi Akun</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td width="150"><strong>Role:</strong></td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="fas fa-user"></i> Pembeli
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Bergabung:</strong></td>
                            <td>
                                <?php 
                                // Get user registration date from users table
                                $userId = Auth::user()['id'];
                                $sql = "SELECT created_at FROM users WHERE id = ?";
                                $stmt = $GLOBALS['db']->prepare($sql);
                                $stmt->bind_param('i', $userId);
                                $stmt->execute();
                                $userInfo = $stmt->get_result()->fetch_assoc();
                                
                                if ($userInfo) {
                                    echo date('d/m/Y', strtotime($userInfo['created_at']));
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="mb-3"><i class="fas fa-link"></i> Menu Cepat</h5>
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="index.php?c=shop&a=index" class="btn btn-outline-primary w-100">
                        <i class="fas fa-store"></i> Belanja
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="index.php?c=checkout&a=cart" class="btn btn-outline-primary w-100">
                        <i class="fas fa-shopping-cart"></i> Keranjang
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="index.php?c=mytransactions&a=index" class="btn btn-outline-primary w-100">
                        <i class="fas fa-shopping-bag"></i> Pesanan Saya
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="logout.php" class="btn btn-outline-danger w-100">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Password confirmation validation
document.querySelector('form[action*="changePassword"]')?.addEventListener('submit', function(e) {
    const newPassword = document.querySelector('input[name="new_password"]').value;
    const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Konfirmasi password tidak cocok!');
        return false;
    }
});
</script>