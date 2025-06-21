<?php
include '../koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Akses tidak valid.");
}

$pelanggan_id = filter_input(INPUT_POST, 'pelanggan_id', FILTER_SANITIZE_NUMBER_INT);
$tgl_selesai = filter_input(INPUT_POST, 'transaksi_tgl_selesai', FILTER_SANITIZE_STRING);
$berat = filter_input(INPUT_POST, 'transaksi_berat', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$laundry_id = $_SESSION['laundry_id'] ?? null;

if (!$pelanggan_id || !$tgl_selesai || !$berat || !$laundry_id) {
    die("Data tidak lengkap!");
}

$tgl_hari_ini = date("Y-m-d");
$status = 'proses';

mysqli_begin_transaction($koneksi);

try {
    $total_harga_awal = 0;

    $jenis_paket = $_POST['jenis_paket'] ?? [];
    $id_paket = $_POST['id_paket'] ?? [];
    $jumlah = $_POST['jumlah'] ?? [];
    $subtotal = $_POST['subtotal'] ?? [];

    foreach ($jenis_paket as $i => $jenis) {
        $paket_id = intval($id_paket[$i] ?? 0);
        $jml = intval($jumlah[$i] ?? 0);

        $harga_per_paket = 0;
        if ($jenis == 'cs') {
            $q_harga = mysqli_query($koneksi, "SELECT tarif_cs FROM tb_cuci_satuan WHERE id_cs = $paket_id AND laundry_id = $laundry_id");
            $rowPrice = mysqli_fetch_assoc($q_harga);
            $harga_per_paket = $rowPrice['tarif_cs'] ?? 0;
        } elseif ($jenis == 'ck') {
            $q_harga = mysqli_query($koneksi, "SELECT tarif_ck FROM tb_cuci_komplit WHERE id_ck = $paket_id AND laundry_id = $laundry_id");
            $rowPrice = mysqli_fetch_assoc($q_harga);
            $harga_per_paket = $rowPrice['tarif_ck'] ?? 0;
        } elseif ($jenis == 'dc') {
            $q_harga = mysqli_query($koneksi, "SELECT tarif_dc FROM tb_dry_clean WHERE id_dc = $paket_id AND laundry_id = $laundry_id");
            $rowPrice = mysqli_fetch_assoc($q_harga);
            $harga_per_paket = $rowPrice['tarif_dc'] ?? 0;
        }

        $subtotal[$i] = $harga_per_paket * $jml;
        $total_harga_awal += $subtotal[$i];
    }

    $stmt_transaksi = mysqli_prepare($koneksi, "
        INSERT INTO transaksi (
            transaksi_tgl, transaksi_pelanggan, transaksi_berat,
            transaksi_total_harga, transaksi_tgl_selesai, transaksi_status, laundry_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    mysqli_stmt_bind_param(
        $stmt_transaksi,
        "sidsssi",
        $tgl_hari_ini,
        $pelanggan_id,
        $berat,
        $total_harga_awal,
        $tgl_selesai,
        $status,
        $laundry_id
    );

    if (!mysqli_stmt_execute($stmt_transaksi)) {
        throw new Exception("Gagal menyimpan transaksi utama.");
    }

    $transaksi_id = mysqli_insert_id($koneksi);
    mysqli_stmt_close($stmt_transaksi);

    $stmt_detail = mysqli_prepare($koneksi, "
        INSERT INTO transaksi_detail (transaksi_id, jenis_paket, id_paket, jumlah, subtotal)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($jenis_paket as $i => $jenis) {
        $paket_id = intval($id_paket[$i] ?? 0);
        $jml = intval($jumlah[$i] ?? 0);
        $sub = $subtotal[$i];

        if ($jml > 0 && $sub >= 0) {
            mysqli_stmt_bind_param($stmt_detail, "isiii", $transaksi_id, $jenis, $paket_id, $jml, $sub);
            if (!mysqli_stmt_execute($stmt_detail)) {
                throw new Exception("Gagal menyimpan transaksi detail.");
            }
        }
    }
    mysqli_stmt_close($stmt_detail);

    $pakaian_jenis = $_POST['pakaian_jenis'] ?? [];
    $pakaian_jumlah = $_POST['pakaian_jumlah'] ?? [];
    $jenis_paket_pakaian = $_POST['jenis_paket'] ?? [];
    $id_paket_pakaian = $_POST['id_paket'] ?? [];

    if (!empty($pakaian_jenis)) {
        $stmt_pakaian = mysqli_prepare($koneksi, "
            INSERT INTO pakaian (transaksi_id, jenis_paket, id_paket, pakaian_jenis, pakaian_jumlah)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($pakaian_jenis as $i => $jenis) {
            $jenis = mysqli_real_escape_string($koneksi, $jenis);
            $jml = intval($pakaian_jumlah[$i] ?? 0);
            $paket = $jenis_paket_pakaian[$i] ?? '';
            $idpaket = intval($id_paket_pakaian[$i] ?? 0);

            if (!empty($jenis) && $jml > 0 && $paket && $idpaket > 0) {
                mysqli_stmt_bind_param($stmt_pakaian, "isisi", $transaksi_id, $paket, $idpaket, $jenis, $jml);
                if (!mysqli_stmt_execute($stmt_pakaian)) {
                    throw new Exception("Gagal menyimpan data pakaian.");
                }
            }
        }
        mysqli_stmt_close($stmt_pakaian);
    }

    $stmt_update_total = mysqli_prepare($koneksi, "
        UPDATE transaksi SET transaksi_total_harga = ? WHERE transaksi_id = ?
    ");
    mysqli_stmt_bind_param($stmt_update_total, "di", $total_harga_awal, $transaksi_id);
    if (!mysqli_stmt_execute($stmt_update_total)) {
        throw new Exception("Gagal mengupdate total harga.");
    }
    mysqli_stmt_close($stmt_update_total);

    mysqli_commit($koneksi);
    header("Location: transaksi.php");
    exit();
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    die("Terjadi kesalahan: " . $e->getMessage());
}
