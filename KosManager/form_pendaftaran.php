<?php
require_once 'data_handler.php';
$kamar_kosong = getKamarKosong();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Penghuni - KosManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="bi bi-house-heart-fill me-2"></i>KosManager</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="kamar.php"><i class="bi bi-door-open"></i> Manajemen Kamar</a>
            <a href="penghuni.php"><i class="bi bi-people"></i> Data Penghuni</a>
            <a href="form_pendaftaran.php" class="active"><i class="bi bi-person-plus"></i> Daftar Penghuni</a>
            <a href="pembayaran.php"><i class="bi bi-cash-stack"></i> Pembayaran</a>
            <hr class="mx-3 my-3 border-light opacity-25">
            <a href="index.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <span class="fw-semibold fs-5">Pendaftaran Penghuni Baru</span>
            <span class="text-muted small">Admin Kos</span>
        </div>
        <div class="content">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-1">Form Pendaftaran Penghuni</h5>
                            <p class="text-muted mb-4">Isi data lengkap. Estimasi biaya dihitung JavaScript, hasil final oleh PHP.</p>

                            <?php if (empty($kamar_kosong)): ?>
                            <div class="alert alert-warning">
                                Tidak ada kamar kosong. <a href="kamar.php">Tambah kamar</a> dulu atau ubah status kamar.
                            </div>
                            <?php endif; ?>

                            <!-- FORM POST minimal 5 input (JavaScript + PHP) -->
                            <form action="proses.php" method="POST" id="formPendaftaran">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nama Lengkap</label>
                                        <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama lengkap" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Nomor HP</label>
                                        <input type="text" class="form-control" name="no_hp" id="no_hp" placeholder="08xxxxxxxxxx" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Pilih Kamar (kosong)</label>
                                        <!-- JavaScript: isi harga otomatis saat pilih kamar -->
                                        <select class="form-select" name="no_kamar" id="no_kamar" required>
                                            <option value="">-- Pilih Kamar --</option>
                                            <?php foreach ($kamar_kosong as $k): ?>
                                            <option value="<?php echo htmlspecialchars($k['no_kamar']); ?>"
                                                    data-harga="<?php echo (int)$k['harga']; ?>"
                                                    data-tipe="<?php echo htmlspecialchars($k['tipe']); ?>">
                                                <?php echo htmlspecialchars($k['no_kamar'] . ' - ' . $k['tipe'] . ' (' . formatRupiah($k['harga']) . ')'); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tipe Kamar</label>
                                        <select class="form-select" name="tipe_kamar" id="tipe_kamar" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="Standar">Standar</option>
                                            <option value="VIP">VIP</option>
                                            <option value="Deluxe">Deluxe</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tanggal Masuk</label>
                                        <input type="date" class="form-control" name="tanggal_masuk" id="tanggal_masuk" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Lama Sewa (bulan)</label>
                                        <input type="number" class="form-control" name="lama_sewa" id="lama_sewa" min="1" max="24" placeholder="6" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Harga Sewa / Bulan (Rp)</label>
                                        <input type="number" class="form-control" name="harga_sewa" id="harga_sewa" min="100000" step="50000" placeholder="Otomatis dari kamar" required>
                                    </div>
                                </div>

                                <!-- ESTIMASI - dikerjakan JAVASCRIPT (sebelum submit) -->
                                <div id="estimasi-biaya" class="estimasi-box" style="display:none;">
                                    <h6 class="fw-bold mb-2"><i class="bi bi-calculator me-1"></i> Estimasi Biaya (JavaScript)</h6>
                                    <div class="d-flex justify-content-between"><span>Total Biaya:</span><span id="est-total">Rp 0</span></div>
                                    <div class="d-flex justify-content-between"><span>Diskon:</span><span id="est-diskon">-</span></div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-semibold">Total Setelah Diskon:</span>
                                        <span id="est-akhir" class="total">Rp 0</span>
                                    </div>
                                    <small class="text-muted d-block mt-2">* Dihitung JS di browser. Final dihitung ulang PHP di server.</small>
                                </div>

                                <div class="mt-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary btn-primary-gradient px-4" <?php echo empty($kamar_kosong)?'disabled':''; ?>>
                                        <i class="bi bi-person-plus me-1"></i> Daftarkan Penghuni
                                    </button>
                                    <a href="penghuni.php" class="btn btn-outline-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>
