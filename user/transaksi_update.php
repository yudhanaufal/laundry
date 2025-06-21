<?php
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Akses tidak diizinkan!");
}

// Validasi wajib
$required_fields = ['id', 'pelanggan_id', 'tgl_selesai', 'status', 'laundry_id'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
        die("Data tidak lengkap! Pastikan semua field diisi.");
    }
}

$id = intval($_POST['id']);
$pelanggan_id = intval($_POST['pelanggan_id']);
$tgl_selesai = mysqli_real_escape_string($koneksi, $_POST['tgl_selesai']);
$status = mysqli_real_escape_string($koneksi, $_POST['status']);
$laundry_id = intval($_POST['laundry_id']);

$berat_total = 0;
$total_harga_ck = 0;
$total_harga_dc = 0;
$total_harga_pakaian = 0;

mysqli_begin_transaction($koneksi);

try {
    // Hitung berat & harga dari Cuci Komplit
    if (isset($_POST['berat_ck'], $_POST['tarif_ck'], $_POST['jumlah_ck'])) {
        foreach ($_POST['berat_ck'] as $i => $berat) {
            $berat = floatval($berat);
            $tarif = floatval($_POST['tarif_ck'][$i] ?? 0);

            $berat_total += $berat;
            $total_harga_ck += $berat * $tarif;
        }
    }

    // Hitung berat & harga dari Dry Clean
    if (isset($_POST['berat_dc'], $_POST['tarif_dc'], $_POST['jumlah_dc'])) {
        foreach ($_POST['berat_dc'] as $i => $berat) {
            $berat = floatval($berat);
            $tarif = floatval($_POST['tarif_dc'][$i] ?? 0);

            $berat_total += $berat;
            $total_harga_dc += $berat * $tarif;
        }
    }

    // Hapus data pakaian lama
    $stmt_delete = mysqli_prepare($koneksi, "DELETE FROM pakaian WHERE transaksi_id = ?");
    mysqli_stmt_bind_param($stmt_delete, "i", $id);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);

    // Masukkan ulang pakaian tambahan
    if (isset($_POST['pakaian_jenis'], $_POST['pakaian_jumlah'])) {
        $pakaian_jenis = $_POST['pakaian_jenis'];
        $pakaian_jumlah = $_POST['pakaian_jumlah'];

        $stmt_insert = mysqli_prepare($koneksi, "
            INSERT INTO pakaian (transaksi_id, pakaian_jenis, pakaian_jumlah) 
            VALUES (?, ?, ?)
        ");

        foreach ($pakaian_jenis as $i => $jenis) {
            $jenis = trim($pakaian_jenis[$i]);
            $jumlah = intval($pakaian_jumlah[$i]);

            if (!empty($jenis) && $jumlah > 0) {
                mysqli_stmt_bind_param($stmt_insert, "isi", $id, $jenis, $jumlah);
                mysqli_stmt_execute($stmt_insert);
            }
        }
        mysqli_stmt_close($stmt_insert);
    }

    // Total harga semua
    $total_harga = $total_harga_ck + $total_harga_dc + $total_harga_pakaian;

    // Update transaksi utama
    $query_update = "UPDATE transaksi SET 
        transaksi_pelanggan = ?, 
        transaksi_berat = ?, 
        transaksi_total_harga = ?, 
        transaksi_tgl_selesai = ?, 
        transaksi_status = ?, 
        laundry_id = ?
        WHERE transaksi_id = ?";

    $stmt_update = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt_update, "idsssii",
        $pelanggan_id, $berat_total, $total_harga, $tgl_selesai, $status, $laundry_id, $id
    );

    if (!mysqli_stmt_execute($stmt_update)) {
        throw new Exception("Gagal memperbarui transaksi: " . mysqli_error($koneksi));
    }
    mysqli_stmt_close($stmt_update);

    mysqli_commit($koneksi);
    header("Location: transaksi.php?update=success");
    exit();

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    die("Terjadi kesalahan saat memperbarui transaksi: " . $e->getMessage());
}
?>
