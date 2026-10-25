<?php
/**
 * proses.php - PHP server-side
 * BAGIAN YANG DIKERJAKAN OLEH PHP:
 * - Terima POST, validasi, hitung diskon, simpan penghuni, update status kamar, buat tagihan
 */
require_once 'data_handler.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form_pendaftaran.php');
    exit;
}

// ===== VARIABLE dari POST =====
$nama          = bersihkan($_POST['nama'] ?? '');
$no_kamar      = bersihkan($_POST['no_kamar'] ?? '');
$tanggal_masuk = bersihkan($_POST['tanggal_masuk'] ?? '');
$lama_sewa     = (int)($_POST['lama_sewa'] ?? 0);
$harga_sewa    = (int)($_POST['harga_sewa'] ?? 0);
$no_hp         = bersihkan($_POST['no_hp'] ?? '');
$tipe_kamar    = bersihkan($_POST['tipe_kamar'] ?? '');

// ===== ARRAY error =====
$errors = [];

if ($nama === '') $errors[] = 'Nama lengkap wajib diisi.';
if ($no_kamar === '') $errors[] = 'Nomor kamar wajib dipilih.';
if ($tanggal_masuk === '') $errors[] = 'Tanggal masuk wajib diisi.';
if ($lama_sewa < 1 || $lama_sewa > 24) $errors[] = 'Lama sewa harus 1–24 bulan.';
if ($harga_sewa < 100000) $errors[] = 'Harga sewa minimal Rp 100.000.';
if ($no_hp === '') {
    $errors[] = 'Nomor HP wajib diisi.';
} elseif (!preg_match('/^[0-9]{10,13}$/', $no_hp)) {
    $errors[] = 'Nomor HP harus angka 10–13 digit.';
}
if ($tipe_kamar === '') $errors[] = 'Tipe kamar wajib dipilih.';

// Cek kamar masih kosong
$info_kamar = cariKamar($no_kamar);
if ($info_kamar === null) {
    $errors[] = 'Kamar tidak ditemukan.';
} elseif ($info_kamar['status'] !== 'Kosong') {
    $errors[] = 'Kamar sudah tidak kosong.';
}

// ===== OPERATOR & PERCABANGAN =====
$total_biaya = $harga_sewa * $lama_sewa;
$persen_diskon = 0;
$diskon = 0;

if ($tipe_kamar == 'Deluxe' && $lama_sewa >= 12) {
    $persen_diskon = 15;
    $diskon = $total_biaya * 0.15;
} elseif ($tipe_kamar == 'VIP' && $lama_sewa >= 6) {
    $persen_diskon = 10;
    $diskon = $total_biaya * 0.10;
} elseif ($lama_sewa >= 12) {
    $persen_diskon = 5;
    $diskon = $total_biaya * 0.05;
}
$total_setelah_diskon = $total_biaya - $diskon;

if (empty($errors)) {
    // Simpan penghuni
    $penghuni = bacaData(FILE_PENGHUNI);
    $id_baru = 1;
    if (!empty($penghuni)) {
        $id_baru = max(array_column($penghuni, 'id')) + 1;
    }
    $penghuni[] = [
        'id' => $id_baru,
        'nama' => $nama,
        'no_hp' => $no_hp,
        'no_kamar' => $no_kamar,
        'tipe_kamar' => $tipe_kamar,
        'tanggal_masuk' => $tanggal_masuk,
        'lama_sewa' => $lama_sewa,
        'harga_sewa' => $harga_sewa,
        'total_bayar' => (int)$total_setelah_diskon,
        'status' => 'Aktif'
    ];
    tulisData(FILE_PENGHUNI, $penghuni);

    // Update status kamar -> Terisi
    updateStatusKamar($no_kamar, 'Terisi');

    // Buat tagihan bulan ini
    $pembayaran = bacaData(FILE_PEMBAYARAN);
    $id_bayar = 1;
    if (!empty($pembayaran)) {
        $id_bayar = max(array_column($pembayaran, 'id')) + 1;
    }
    $pembayaran[] = [
        'id' => $id_bayar,
        'nama' => $nama,
        'no_kamar' => $no_kamar,
        'periode' => date('Y-m'),
        'jumlah' => $harga_sewa,
        'jatuh_tempo' => date('Y-m-d', strtotime('+14 days')),
        'status' => 'Belum Bayar'
    ];
    tulisData(FILE_PEMBAYARAN, $pembayaran);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pendaftaran - KosManager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="hasil-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <?php if (!empty($errors)): ?>
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">Validasi Gagal (PHP Server-side)</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <strong>Kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $e): ?>
                                <li><?php echo htmlspecialchars($e); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <a href="form_pendaftaran.php" class="btn btn-outline-secondary">Kembali ke Form</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Pendaftaran Berhasil (PHP)</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Data disimpan, kamar diubah jadi <strong>Terisi</strong>, tagihan dibuat.</p>
                        <table class="table table-bordered">
                            <tr><th width="40%">Nama</th><td><?php echo htmlspecialchars($nama); ?></td></tr>
                            <tr><th>Nomor Kamar</th><td><?php echo htmlspecialchars($no_kamar); ?></td></tr>
                            <tr><th>Tipe</th><td><span class="badge bg-primary"><?php echo htmlspecialchars($tipe_kamar); ?></span></td></tr>
                            <tr><th>Tanggal Masuk</th><td><?php echo date('d-m-Y', strtotime($tanggal_masuk)); ?></td></tr>
                            <tr><th>Lama Sewa</th><td><?php echo $lama_sewa; ?> bulan</td></tr>
                            <tr><th>Harga / Bulan</th><td><?php echo formatRupiah($harga_sewa); ?></td></tr>
                            <tr><th>No. HP</th><td><?php echo htmlspecialchars($no_hp); ?></td></tr>
                            <tr class="table-light"><th>Total Biaya</th><td><strong><?php echo formatRupiah($total_biaya); ?></strong></td></tr>
                            <tr class="table-warning"><th>Diskon (<?php echo $persen_diskon; ?>%)</th><td><?php echo formatRupiah($diskon); ?></td></tr>
                            <tr class="table-success"><th>Total Setelah Diskon</th><td><strong class="fs-5"><?php echo formatRupiah($total_setelah_diskon); ?></strong></td></tr>
                        </table>
                        <div class="alert alert-info small">
                            Diskon: Deluxe≥12bln=15% · VIP≥6bln=10% · Sewa≥12bln=5%
                        </div>
                        <a href="form_pendaftaran.php" class="btn btn-primary">Daftar Lagi</a>
                        <a href="penghuni.php" class="btn btn-outline-secondary">Data Penghuni</a>
                        <a href="dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
