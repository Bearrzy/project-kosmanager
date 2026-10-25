<?php
/**
 * data_handler.php
 * Menyimpan & membaca data kamar, penghuni, pembayaran via file JSON
 * (tanpa database - cocok untuk localhost / tugas kuliah)
 */

session_start();

define('DATA_DIR', __DIR__ . '/data/');
define('FILE_KAMAR', DATA_DIR . 'kamar.json');
define('FILE_PENGHUNI', DATA_DIR . 'penghuni.json');
define('FILE_PEMBAYARAN', DATA_DIR . 'pembayaran.json');

// Pastikan folder data ada
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

/** Baca file JSON, return array */
function bacaData($file) {
    if (!file_exists($file)) {
        return [];
    }
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

/** Tulis array ke file JSON */
function tulisData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/** Format Rupiah */
function formatRupiah($angka) {
    return 'Rp ' . number_format((int)$angka, 0, ',', '.');
}

/** Bersihkan input */
function bersihkan($data) {
    return htmlspecialchars(stripslashes(trim($data ?? '')), ENT_QUOTES, 'UTF-8');
}

/** Inisialisasi data contoh jika kosong */
function initDataJikaKosong() {
    // Kamar
    if (!file_exists(FILE_KAMAR) || empty(bacaData(FILE_KAMAR))) {
        $kamar = [
            ['id' => 1, 'no_kamar' => 'A-01', 'tipe' => 'Standar', 'harga' => 1200000, 'fasilitas' => 'AC, Kamar Mandi Dalam', 'status' => 'Terisi'],
            ['id' => 2, 'no_kamar' => 'A-02', 'tipe' => 'Standar', 'harga' => 1200000, 'fasilitas' => 'AC, Kamar Mandi Dalam', 'status' => 'Kosong'],
            ['id' => 3, 'no_kamar' => 'B-01', 'tipe' => 'VIP', 'harga' => 1800000, 'fasilitas' => 'AC, KM Dalam, WiFi, TV', 'status' => 'Terisi'],
            ['id' => 4, 'no_kamar' => 'B-02', 'tipe' => 'VIP', 'harga' => 1800000, 'fasilitas' => 'AC, KM Dalam, WiFi, TV', 'status' => 'Kosong'],
            ['id' => 5, 'no_kamar' => 'C-01', 'tipe' => 'Deluxe', 'harga' => 2500000, 'fasilitas' => 'AC, KM Dalam, WiFi, TV, Kulkas', 'status' => 'Maintenance'],
            ['id' => 6, 'no_kamar' => 'C-02', 'tipe' => 'Deluxe', 'harga' => 2500000, 'fasilitas' => 'AC, KM Dalam, WiFi, TV, Kulkas', 'status' => 'Terisi'],
        ];
        tulisData(FILE_KAMAR, $kamar);
    }

    // Penghuni
    if (!file_exists(FILE_PENGHUNI) || empty(bacaData(FILE_PENGHUNI))) {
        $penghuni = [
            ['id' => 1, 'nama' => 'Ahmad Rizki', 'no_hp' => '081234567890', 'no_kamar' => 'A-01', 'tipe_kamar' => 'Standar', 'tanggal_masuk' => '2026-03-01', 'lama_sewa' => 12, 'harga_sewa' => 1200000, 'total_bayar' => 13680000, 'status' => 'Aktif'],
            ['id' => 2, 'nama' => 'Siti Putri', 'no_hp' => '081298765432', 'no_kamar' => 'B-01', 'tipe_kamar' => 'VIP', 'tanggal_masuk' => '2026-05-15', 'lama_sewa' => 6, 'harga_sewa' => 1800000, 'total_bayar' => 9720000, 'status' => 'Aktif'],
            ['id' => 3, 'nama' => 'Budi Wibowo', 'no_hp' => '082112345678', 'no_kamar' => 'C-02', 'tipe_kamar' => 'Deluxe', 'tanggal_masuk' => '2026-08-10', 'lama_sewa' => 12, 'harga_sewa' => 2500000, 'total_bayar' => 25500000, 'status' => 'Aktif'],
        ];
        tulisData(FILE_PENGHUNI, $penghuni);
    }

    // Pembayaran
    if (!file_exists(FILE_PEMBAYARAN) || empty(bacaData(FILE_PEMBAYARAN))) {
        $pembayaran = [
            ['id' => 1, 'nama' => 'Ahmad Rizki', 'no_kamar' => 'A-01', 'periode' => '2026-10', 'jumlah' => 1200000, 'jatuh_tempo' => '2026-10-15', 'status' => 'Belum Bayar'],
            ['id' => 2, 'nama' => 'Siti Putri', 'no_kamar' => 'B-01', 'periode' => '2026-10', 'jumlah' => 1800000, 'jatuh_tempo' => '2026-10-18', 'status' => 'Belum Bayar'],
            ['id' => 3, 'nama' => 'Budi Wibowo', 'no_kamar' => 'C-02', 'periode' => '2026-10', 'jumlah' => 2500000, 'jatuh_tempo' => '2026-10-10', 'status' => 'Lunas'],
        ];
        tulisData(FILE_PEMBAYARAN, $pembayaran);
    }
}

initDataJikaKosong();

/** Ambil kamar kosong saja */
function getKamarKosong() {
    $kamar = bacaData(FILE_KAMAR);
    return array_filter($kamar, function ($k) {
        return $k['status'] === 'Kosong';
    });
}

/** Cari kamar by no_kamar */
function cariKamar($no_kamar) {
    $kamar = bacaData(FILE_KAMAR);
    foreach ($kamar as $k) {
        if ($k['no_kamar'] === $no_kamar) {
            return $k;
        }
    }
    return null;
}

/** Update status kamar */
function updateStatusKamar($no_kamar, $status) {
    $kamar = bacaData(FILE_KAMAR);
    foreach ($kamar as &$k) {
        if ($k['no_kamar'] === $no_kamar) {
            $k['status'] = $status;
            break;
        }
    }
    tulisData(FILE_KAMAR, $kamar);
}

/** Hitung statistik dashboard */
function getStatistik() {
    $kamar = bacaData(FILE_KAMAR);
    $penghuni = bacaData(FILE_PENGHUNI);
    $pembayaran = bacaData(FILE_PEMBAYARAN);

    $total_kamar = count($kamar);
    $terisi = 0;
    $kosong = 0;
    $maintenance = 0;
    foreach ($kamar as $k) {
        if ($k['status'] === 'Terisi') $terisi++;
        elseif ($k['status'] === 'Kosong') $kosong++;
        else $maintenance++;
    }

    $penghuni_aktif = 0;
    foreach ($penghuni as $p) {
        if ($p['status'] === 'Aktif') $penghuni_aktif++;
    }

    $total_tagihan = 0;
    $sudah_bayar = 0;
    $belum_lunas = 0;
    foreach ($pembayaran as $b) {
        $total_tagihan += $b['jumlah'];
        if ($b['status'] === 'Lunas') {
            $sudah_bayar += $b['jumlah'];
        } else {
            $belum_lunas += $b['jumlah'];
        }
    }

    return [
        'total_kamar' => $total_kamar,
        'terisi' => $terisi,
        'kosong' => $kosong,
        'maintenance' => $maintenance,
        'penghuni_aktif' => $penghuni_aktif,
        'total_tagihan' => $total_tagihan,
        'sudah_bayar' => $sudah_bayar,
        'belum_lunas' => $belum_lunas,
        'okupansi' => $total_kamar > 0 ? round(($terisi / $total_kamar) * 100) : 0,
    ];
}
