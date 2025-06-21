<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id'])) {
    die("Akses ditolak. Silakan login.");
}

$user_id = $_SESSION['id'];


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $laundry_id = $_POST['laundry_id'];

    $tgl_transaksi = date('Y-m-d');
    $tgl_selesai = $_POST['transaksi_tgl_selesai'] ?? date('Y-m-d', strtotime('+2 days'));
    $status = 'proses';

    $paket = $_POST['paket'] ?? [];
    $jumlah = $_POST['jumlah'] ?? [];
    $pakaian_jenis = $_POST['pakaian_jenis'] ?? [];

    if (empty($paket) || empty($jumlah)) {
        die("Data tidak lengkap.");
    }

    $total_harga = 0;
    $berat_total = 0; // Berat default 0, nanti dihitung dari berat input admin (edit)
    $status = 'menunggu'; // Default status is 'menunggu'

    // Insert transaksi dulu
    $stmt = mysqli_prepare($koneksi, "
        INSERT INTO transaksi (
            transaksi_tgl, transaksi_pelanggan, transaksi_berat,
            transaksi_total_harga, transaksi_tgl_selesai, transaksi_status, laundry_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    mysqli_stmt_bind_param(
        $stmt,
        "sidsssi",
        $tgl_transaksi,
        $user_id,
        $berat_total,
        $total_harga,
        $tgl_selesai,
        $status,
        $laundry_id
    );

    if (!mysqli_stmt_execute($stmt)) {
        die("Gagal menyimpan transaksi: " . mysqli_error($koneksi));
    }

    $transaksi_id = mysqli_insert_id($koneksi);
    mysqli_stmt_close($stmt);

    // Proses masing-masing paket
    foreach ($paket as $i => $paket_val) {
        list($jenis, $id_paket) = explode('-', $paket_val);
        $id_paket = intval($id_paket);
        $jml = intval($jumlah[$i]);
        $nama_pakaian = trim($pakaian_jenis[$i] ?? '');

        if ($jml <= 0) continue;

        $tarif = 0;
        if ($jenis === 'ck') {
            $q = mysqli_query($koneksi, "SELECT tarif_ck AS tarif FROM tb_cuci_komplit WHERE id_ck = $id_paket AND laundry_id = $laundry_id");
        } elseif ($jenis === 'cs') {
            $q = mysqli_query($koneksi, "SELECT tarif_cs AS tarif FROM tb_cuci_satuan WHERE id_cs = $id_paket AND laundry_id = $laundry_id");
        } elseif ($jenis === 'dc') {
            $q = mysqli_query($koneksi, "SELECT tarif_dc AS tarif FROM tb_dry_clean WHERE id_dc = $id_paket AND laundry_id = $laundry_id");
        } else {
            continue;
        }

        if ($row = mysqli_fetch_assoc($q)) {
            $tarif = intval($row['tarif']);
        }

        $subtotal = $tarif * $jml;
        $total_harga += $subtotal;

        // Simpan ke transaksi_detail
        mysqli_query($koneksi, "
            INSERT INTO transaksi_detail (transaksi_id, jenis_paket, id_paket, jumlah, subtotal)
            VALUES ($transaksi_id, '$jenis', $id_paket, $jml, $subtotal)
        ");
        // Kalau paket CK atau DC, simpan juga ke pakaian
        if (($jenis === 'ck' || $jenis === 'dc') && !empty($nama_pakaian)) {
            mysqli_query($koneksi, "
                INSERT INTO pakaian (transaksi_id, jenis_paket, id_paket, pakaian_jenis, pakaian_jumlah)
                VALUES ($transaksi_id, '$jenis', $id_paket, '" . mysqli_real_escape_string($koneksi, $nama_pakaian) . "', $jml)
            ");
        }
    }

    // Update transaksi total harga
    mysqli_query($koneksi, "
        UPDATE transaksi SET 
            transaksi_total_harga = $total_harga
        WHERE transaksi_id = $transaksi_id
    ");

    header("Location: transaksi.php?success=1");
    exit();
} else {
    die("Akses tidak valid.");
}
