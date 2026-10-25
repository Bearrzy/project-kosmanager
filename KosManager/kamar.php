<?php
require_once 'data_handler.php';

$pesan = '';
$tipe_pesan = '';

// ===== PROSES TAMBAH KAMAR (PHP) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    $no_kamar  = bersihkan($_POST['no_kamar'] ?? '');
    $tipe      = bersihkan($_POST['tipe'] ?? '');
    $harga     = (int)($_POST['harga'] ?? 0);
    $fasilitas = bersihkan($_POST['fasilitas'] ?? '');
    $status    = bersihkan($_POST['status'] ?? 'Kosong');

    $errors = [];
    if ($no_kamar === '') $errors[] = 'Nomor kamar wajib diisi.';
    if ($tipe === '') $errors[] = 'Tipe kamar wajib dipilih.';
    if ($harga < 100000) $errors[] = 'Harga minimal Rp 100.000.';

    // Cek duplikat
    $kamar = bacaData(FILE_KAMAR);
    foreach ($kamar as $k) {
        if (strtoupper($k['no_kamar']) === strtoupper($no_kamar)) {
            $errors[] = 'Nomor kamar sudah ada.';
            break;
        }
    }

    if (empty($errors)) {
        $id_baru = 1;
        if (!empty($kamar)) {
            $ids = array_column($kamar, 'id');
            $id_baru = max($ids) + 1;
        }
        $kamar[] = [
            'id' => $id_baru,
            'no_kamar' => $no_kamar,
            'tipe' => $tipe,
            'harga' => $harga,
            'fasilitas' => $fasilitas,
            'status' => $status
        ];
        tulisData(FILE_KAMAR, $kamar);
        $pesan = "Kamar $no_kamar berhasil ditambahkan.";
        $tipe_pesan = 'success';
    } else {
        $pesan = implode(' ', $errors);
        $tipe_pesan = 'danger';
    }
}

// ===== PROSES HAPUS KAMAR =====
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $kamar = bacaData(FILE_KAMAR);
    $baru = [];
    $nama_hapus = '';
    foreach ($kamar as $k) {
        if ($k['id'] == $id_hapus) {
            if ($k['status'] === 'Terisi') {
                $pesan = 'Kamar masih terisi, tidak bisa dihapus.';
                $tipe_pesan = 'warning';
                $baru[] = $k;
            } else {
                $nama_hapus = $k['no_kamar'];
            }
        } else {
            $baru[] = $k;
        }
    }
    if ($nama_hapus !== '') {
        tulisData(FILE_KAMAR, $baru);
        $pesan = "Kamar $nama_hapus dihapus.";
        $tipe_pesan = 'success';
    }
}

$daftar_kamar = bacaData(FILE_KAMAR);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kamar - KosManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="bi bi-house-heart-fill me-2"></i>KosManager</div>
        <div class="sidebar-menu">
            <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="kamar.php" class="active"><i class="bi bi-door-open"></i> Manajemen Kamar</a>
            <a href="penghuni.php"><i class="bi bi-people"></i> Data Penghuni</a>
            <a href="form_pendaftaran.php"><i class="bi bi-person-plus"></i> Daftar Penghuni</a>
            <a href="pembayaran.php"><i class="bi bi-cash-stack"></i> Pembayaran</a>
            <hr class="mx-3 my-3 border-light opacity-25">
            <a href="index.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="topbar">
            <span class="fw-semibold fs-5">Manajemen Kamar</span>
            <span class="text-muted small">Admin Kos</span>
        </div>
        <div class="content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Data Kamar</h4>
                    <p class="text-muted mb-0">Kelola data kamar dan status ketersediaan</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Kamar
                </button>
            </div>

            <?php if ($pesan): ?>
            <div class="alert alert-<?php echo $tipe_pesan; ?> alert-dismissible fade show">
                <?php echo htmlspecialchars($pesan); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>No. Kamar</th>
                                    <th>Tipe</th>
                                    <th>Harga/Bulan</th>
                                    <th>Fasilitas</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($daftar_kamar)): ?>
                                <tr><td colspan="6" class="text-center text-muted">Belum ada data kamar</td></tr>
                            <?php else: foreach ($daftar_kamar as $k): ?>
                                <tr>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($k['no_kamar']); ?></td>
                                    <td><?php echo htmlspecialchars($k['tipe']); ?></td>
                                    <td><?php echo formatRupiah($k['harga']); ?></td>
                                    <td><small><?php echo htmlspecialchars($k['fasilitas']); ?></small></td>
                                    <td>
                                        <?php
                                        $cls = 'badge-status-kosong';
                                        if ($k['status'] === 'Terisi') $cls = 'badge-status-terisi';
                                        if ($k['status'] === 'Maintenance') $cls = 'badge-status-maintenance';
                                        ?>
                                        <span class="badge <?php echo $cls; ?>"><?php echo htmlspecialchars($k['status']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($k['status'] !== 'Terisi'): ?>
                                        <a href="kamar.php?hapus=<?php echo $k['id']; ?>" class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Hapus kamar <?php echo htmlspecialchars($k['no_kamar']); ?>?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                        <?php else: ?>
                                        <span class="text-muted small">Terisi</span>
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

    <!-- Modal Tambah Kamar -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="kamar.php">
                    <input type="hidden" name="aksi" value="tambah">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kamar Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nomor Kamar</label>
                            <input type="text" class="form-control" name="no_kamar" placeholder="Contoh: A-03" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipe Kamar</label>
                            <select class="form-select" name="tipe" required>
                                <option value="">-- Pilih --</option>
                                <option value="Standar">Standar</option>
                                <option value="VIP">VIP</option>
                                <option value="Deluxe">Deluxe</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Sewa / Bulan</label>
                            <input type="number" class="form-control" name="harga" min="100000" step="50000" placeholder="1500000" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fasilitas</label>
                            <textarea class="form-control" name="fasilitas" rows="2" placeholder="AC, Kamar Mandi Dalam..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="Kosong">Kosong</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
