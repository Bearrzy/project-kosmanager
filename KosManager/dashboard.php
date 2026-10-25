<?php
require_once 'data_handler.php';
$stat = getStatistik();
$penghuni = bacaData(FILE_PENGHUNI);
$pembayaran = bacaData(FILE_PEMBAYARAN);
$penghuni_terbaru = array_slice(array_reverse($penghuni), 0, 5);
$tagihan_belum = array_filter($pembayaran, function($b) { return $b['status'] !== 'Lunas'; });
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - KosManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="bi bi-house-heart-fill me-2"></i>KosManager</div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="kamar.php"><i class="bi bi-door-open"></i> Manajemen Kamar</a>
            <a href="penghuni.php"><i class="bi bi-people"></i> Data Penghuni</a>
            <a href="form_pendaftaran.php"><i class="bi bi-person-plus"></i> Daftar Penghuni</a>
            <a href="pembayaran.php"><i class="bi bi-cash-stack"></i> Pembayaran</a>
            <hr class="mx-3 my-3 border-light opacity-25">
            <a href="index.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <span class="fw-semibold fs-5">Dashboard</span>
            <span class="text-muted small">Admin Kos</span>
        </div>
        <div class="content">
            <h4 class="fw-bold mb-1">Selamat Datang, Admin!</h4>
            <p class="text-muted mb-4">Ringkasan operasional kos (data real-time).</p>

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-door-open"></i></div>
                            <div>
                                <div class="text-muted small">Total Kamar</div>
                                <div class="fs-4 fw-bold"><?php echo $stat['total_kamar']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <div class="text-muted small">Penghuni Aktif</div>
                                <div class="fs-4 fw-bold"><?php echo $stat['penghuni_aktif']; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-cash-coin"></i></div>
                            <div>
                                <div class="text-muted small">Sudah Dibayar</div>
                                <div class="fs-5 fw-bold"><?php echo formatRupiah($stat['sudah_bayar']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-triangle"></i></div>
                            <div>
                                <div class="text-muted small">Belum Lunas</div>
                                <div class="fs-5 fw-bold"><?php echo formatRupiah($stat['belum_lunas']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-white border-0 pt-3 px-4">
                            <h6 class="fw-bold mb-0">Status Kamar</h6>
                        </div>
                        <div class="card-body px-4">
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <div class="fs-3 fw-bold text-success"><?php echo $stat['terisi']; ?></div>
                                    <small class="text-muted">Terisi</small>
                                </div>
                                <div class="col-4">
                                    <div class="fs-3 fw-bold text-primary"><?php echo $stat['kosong']; ?></div>
                                    <small class="text-muted">Kosong</small>
                                </div>
                                <div class="col-4">
                                    <div class="fs-3 fw-bold text-warning"><?php echo $stat['maintenance']; ?></div>
                                    <small class="text-muted">Maintenance</small>
                                </div>
                            </div>
                            <div class="progress" style="height:12px;border-radius:10px;">
                                <?php
                                $t = $stat['total_kamar'] ?: 1;
                                $p1 = round(($stat['terisi']/$t)*100);
                                $p2 = round(($stat['kosong']/$t)*100);
                                $p3 = 100 - $p1 - $p2;
                                ?>
                                <div class="progress-bar bg-success" style="width:<?php echo $p1; ?>%"><?php echo $stat['okupansi']; ?>%</div>
                                <div class="progress-bar bg-primary" style="width:<?php echo $p2; ?>%"></div>
                                <div class="progress-bar bg-warning" style="width:<?php echo $p3; ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header bg-white border-0 pt-3 px-4 d-flex justify-content-between">
                            <h6 class="fw-bold mb-0">Penghuni Terbaru</h6>
                            <a href="penghuni.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body px-4">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr><th>Nama</th><th>Kamar</th><th>Tanggal Masuk</th><th>Status</th></tr>
                                </thead>
                                <tbody>
                                <?php if (empty($penghuni_terbaru)): ?>
                                    <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
                                <?php else: foreach ($penghuni_terbaru as $p): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($p['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($p['no_kamar']); ?></td>
                                        <td><?php echo date('d-m-Y', strtotime($p['tanggal_masuk'])); ?></td>
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

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-white border-0 pt-3 px-4">
                            <h6 class="fw-bold mb-0">Tagihan Belum Lunas</h6>
                        </div>
                        <div class="card-body px-4">
                            <?php if (empty($tagihan_belum)): ?>
                                <p class="text-muted small mb-0">Semua tagihan sudah lunas.</p>
                            <?php else: foreach ($tagihan_belum as $b): ?>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                    <div>
                                        <div class="fw-semibold"><?php echo htmlspecialchars($b['nama']); ?></div>
                                        <small class="text-muted">Kamar <?php echo htmlspecialchars($b['no_kamar']); ?></small>
                                    </div>
                                    <span class="badge bg-danger"><?php echo formatRupiah($b['jumlah']); ?></span>
                                </div>
                            <?php endforeach; endif; ?>
                            <a href="pembayaran.php" class="btn btn-outline-primary btn-sm w-100 mt-2">Lihat Semua</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
