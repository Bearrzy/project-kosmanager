<?php
require_once 'data_handler.php';
$daftar = bacaData(FILE_PENGHUNI);

// Filter sederhana
$cari = bersihkan($_GET['cari'] ?? '');
$status_filter = bersihkan($_GET['status'] ?? '');
if ($cari !== '' || $status_filter !== '') {
    $daftar = array_filter($daftar, function($p) use ($cari, $status_filter) {
        $ok = true;
        if ($cari !== '') {
            $ok = stripos($p['nama'], $cari) !== false || stripos($p['no_hp'], $cari) !== false;
        }
        if ($ok && $status_filter !== '') {
            $ok = $p['status'] === $status_filter;
        }
        return $ok;
    });
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penghuni - KosManager</title>
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
            <a href="penghuni.php" class="active"><i class="bi bi-people"></i> Data Penghuni</a>
            <a href="form_pendaftaran.php"><i class="bi bi-person-plus"></i> Daftar Penghuni</a>
            <a href="pembayaran.php"><i class="bi bi-cash-stack"></i> Pembayaran</a>
            <hr class="mx-3 my-3 border-light opacity-25">
            <a href="index.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <span class="fw-semibold fs-5">Data Penghuni</span>
            <span class="text-muted small">Admin Kos</span>
        </div>
        <div class="content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Daftar Penghuni</h4>
                    <p class="text-muted mb-0">Rekapitulasi data penghuni kos</p>
                </div>
                <a href="form_pendaftaran.php" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i> Daftar Penghuni Baru
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="cari" placeholder="Cari nama / nomor HP..." value="<?php echo htmlspecialchars($cari); ?>">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="status">
                                <option value="">Semua Status</option>
                                <option value="Aktif" <?php echo $status_filter==='Aktif'?'selected':''; ?>>Aktif</option>
                                <option value="Keluar" <?php echo $status_filter==='Keluar'?'selected':''; ?>>Keluar</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-outline-primary w-100">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>No. HP</th>
                                    <th>Kamar</th>
                                    <th>Tgl Masuk</th>
                                    <th>Lama Sewa</th>
                                    <th>Total Bayar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($daftar)): ?>
                                <tr><td colspan="7" class="text-center text-muted">Tidak ada data</td></tr>
                            <?php else: foreach ($daftar as $p): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($p['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($p['no_hp']); ?></td>
                                    <td><?php echo htmlspecialchars($p['no_kamar']); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($p['tanggal_masuk'])); ?></td>
                                    <td><?php echo (int)$p['lama_sewa']; ?> bulan</td>
                                    <td><?php echo formatRupiah($p['total_bayar'] ?? 0); ?></td>
                                    <td>
                                        <?php if ($p['status'] === 'Aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo htmlspecialchars($p['status']); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
