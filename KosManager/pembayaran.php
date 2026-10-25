<?php
require_once 'data_handler.php';

$pesan = '';
$tipe_pesan = '';

// Konfirmasi bayar (ubah status jadi Lunas)
if (isset($_GET['lunas'])) {
    $id = (int)$_GET['lunas'];
    $pembayaran = bacaData(FILE_PEMBAYARAN);
    foreach ($pembayaran as &$b) {
        if ($b['id'] == $id) {
            $b['status'] = 'Lunas';
            $pesan = 'Pembayaran ' . $b['nama'] . ' dikonfirmasi Lunas.';
            $tipe_pesan = 'success';
            break;
        }
    }
    tulisData(FILE_PEMBAYARAN, $pembayaran);
}

// Buat tagihan baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'buat_tagihan') {
    $nama = bersihkan($_POST['nama'] ?? '');
    $no_kamar = bersihkan($_POST['no_kamar'] ?? '');
    $periode = bersihkan($_POST['periode'] ?? '');
    $jumlah = (int)($_POST['jumlah'] ?? 0);
    $jatuh_tempo = bersihkan($_POST['jatuh_tempo'] ?? '');

    $errors = [];
    if ($nama === '') $errors[] = 'Pilih penghuni.';
    if ($periode === '') $errors[] = 'Periode wajib diisi.';
    if ($jumlah < 100000) $errors[] = 'Jumlah minimal Rp 100.000.';
    if ($jatuh_tempo === '') $errors[] = 'Jatuh tempo wajib diisi.';

    if (empty($errors)) {
        $pembayaran = bacaData(FILE_PEMBAYARAN);
        $id_baru = empty($pembayaran) ? 1 : max(array_column($pembayaran, 'id')) + 1;
        $pembayaran[] = [
            'id' => $id_baru,
            'nama' => $nama,
            'no_kamar' => $no_kamar,
            'periode' => $periode,
            'jumlah' => $jumlah,
            'jatuh_tempo' => $jatuh_tempo,
            'status' => 'Belum Bayar'
        ];
        tulisData(FILE_PEMBAYARAN, $pembayaran);
        $pesan = 'Tagihan berhasil dibuat.';
        $tipe_pesan = 'success';
    } else {
        $pesan = implode(' ', $errors);
        $tipe_pesan = 'danger';
    }
}

$daftar = bacaData(FILE_PEMBAYARAN);
$penghuni_aktif = array_filter(bacaData(FILE_PENGHUNI), function($p) { return $p['status'] === 'Aktif'; });

$total_tagihan = 0;
$sudah = 0;
$belum = 0;
foreach ($daftar as $b) {
    $total_tagihan += $b['jumlah'];
    if ($b['status'] === 'Lunas') $sudah += $b['jumlah'];
    else $belum += $b['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - KosManager</title>
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
            <a href="form_pendaftaran.php"><i class="bi bi-person-plus"></i> Daftar Penghuni</a>
            <a href="pembayaran.php" class="active"><i class="bi bi-cash-stack"></i> Pembayaran</a>
            <hr class="mx-3 my-3 border-light opacity-25">
            <a href="index.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <span class="fw-semibold fs-5">Pencatatan Pembayaran</span>
            <span class="text-muted small">Admin Kos</span>
        </div>
        <div class="content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Tagihan & Pembayaran</h4>
                    <p class="text-muted mb-0">Kelola tagihan sewa dan konfirmasi pembayaran</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTagihan">
                    <i class="bi bi-plus-lg me-1"></i> Buat Tagihan
                </button>
            </div>

            <?php if ($pesan): ?>
            <div class="alert alert-<?php echo $tipe_pesan; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($pesan); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">Total Tagihan</div>
                        <div class="fs-4 fw-bold"><?php echo formatRupiah($total_tagihan); ?></div>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">Sudah Dibayar</div>
                        <div class="fs-4 fw-bold text-success"><?php echo formatRupiah($sudah); ?></div>
                    </div></div>
                </div>
                <div class="col-md-4">
                    <div class="card"><div class="card-body">
                        <div class="text-muted small">Belum Lunas</div>
                        <div class="fs-4 fw-bold text-danger"><?php echo formatRupiah($belum); ?></div>
                    </div></div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Penghuni</th>
                                    <th>Kamar</th>
                                    <th>Periode</th>
                                    <th>Jumlah</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($daftar)): ?>
                                <tr><td colspan="7" class="text-center text-muted">Belum ada tagihan</td></tr>
                            <?php else: foreach ($daftar as $b): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($b['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($b['no_kamar']); ?></td>
                                    <td><?php echo htmlspecialchars($b['periode']); ?></td>
                                    <td><?php echo formatRupiah($b['jumlah']); ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($b['jatuh_tempo'])); ?></td>
                                    <td>
                                        <?php if ($b['status'] === 'Lunas'): ?>
                                            <span class="badge bg-success">Lunas</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Belum Bayar</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($b['status'] !== 'Lunas'): ?>
                                        <a href="pembayaran.php?lunas=<?php echo $b['id']; ?>" class="btn btn-sm btn-success"
                                           onclick="return confirm('Konfirmasi sudah bayar?')">Konfirmasi</a>
                                        <?php else: ?>
                                        <span class="text-muted small">—</span>
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

    <div class="modal fade" id="modalTagihan" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="pembayaran.php">
                    <input type="hidden" name="aksi" value="buat_tagihan">
                    <div class="modal-header">
                        <h5 class="modal-title">Buat Tagihan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Penghuni</label>
                            <select class="form-select" name="nama" id="pilih_penghuni" required onchange="isiKamar()">
                                <option value="">-- Pilih Penghuni --</option>
                                <?php foreach ($penghuni_aktif as $p): ?>
                                <option value="<?php echo htmlspecialchars($p['nama']); ?>"
                                        data-kamar="<?php echo htmlspecialchars($p['no_kamar']); ?>"
                                        data-harga="<?php echo (int)$p['harga_sewa']; ?>">
                                    <?php echo htmlspecialchars($p['nama'] . ' (' . $p['no_kamar'] . ')'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <input type="hidden" name="no_kamar" id="no_kamar_tagihan">
                        <div class="mb-3">
                            <label class="form-label">Periode (YYYY-MM)</label>
                            <input type="month" class="form-control" name="periode" value="<?php echo date('Y-m'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah Tagihan</label>
                            <input type="number" class="form-control" name="jumlah" id="jumlah_tagihan" min="100000" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Jatuh Tempo</label>
                            <input type="date" class="form-control" name="jatuh_tempo" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Buat Tagihan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function isiKamar() {
        var sel = document.getElementById('pilih_penghuni');
        var opt = sel.options[sel.selectedIndex];
        document.getElementById('no_kamar_tagihan').value = opt.dataset.kamar || '';
        document.getElementById('jumlah_tagihan').value = opt.dataset.harga || '';
    }
    </script>
</body>
</html>
