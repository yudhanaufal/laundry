<?php
include '../koneksi.php';
require_once 'update_total_harga.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Akses tidak diizinkan!");
}

// Validasi input wajib
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
    // Hitung dari berat CK
    if (isset($_POST['berat_ck'], $_POST['tarif_ck'], $_POST['jumlah_ck'])) {
        foreach ($_POST['berat_ck'] as $i => $berat) {
            if ($berat > 0) {
                $berat = floatval($berat);
                $tarif = floatval($_POST['tarif_ck'][$i] ?? 0);
                $jumlah = intval($_POST['jumlah_ck'][$i] ?? 0);

                $berat_total += $berat;
                $total_harga_ck += $berat * $tarif;
            }
        }
    }

    // Hitung dari berat DC
    if (isset($_POST['berat_dc'], $_POST['tarif_dc'], $_POST['jumlah_dc'])) {
        foreach ($_POST['berat_dc'] as $i => $berat) {
            if ($berat > 0) {
                $berat = floatval($berat);
                $tarif = floatval($_POST['tarif_dc'][$i] ?? 0);
                $jumlah = intval($_POST['jumlah_dc'][$i] ?? 0);

                $berat_total += $berat;
                $total_harga_dc += $berat * $tarif;
            }
        }
    }

    // Hitung dari pakaian CS (Cuci Satuan - dihitung per pcs)
    if (isset($_POST['pakaian_jenis_paket']) && !empty($_POST['pakaian_jenis_paket'])) {
        foreach ($_POST['pakaian_jenis_paket'] as $i => $jenis_paket) {
            if ($jenis_paket == 'cs') {
                $jumlah = intval($_POST['pakaian_jumlah'][$i] ?? 0);
                $idpaket = intval($_POST['pakaian_id_paket'][$i] ?? 0);

                $q = mysqli_query($koneksi, "SELECT tarif_cs FROM tb_cuci_satuan WHERE id_cs = $idpaket");
                if ($q) {
                    $r = mysqli_fetch_assoc($q);
                    $tarif = floatval($r['tarif_cs'] ?? 0);
                    $total_harga_pakaian += $tarif * $jumlah;
                }
            }
        }
    }

    // Total keseluruhan
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
    mysqli_stmt_bind_param($stmt_update, "iddsdsi",
        $pelanggan_id, $berat_total, $total_harga, $tgl_selesai, $status, $laundry_id, $id
    );

    if (!mysqli_stmt_execute($stmt_update)) {
        throw new Exception("Gagal memperbarui transaksi: " . mysqli_error($koneksi));
    }
    mysqli_stmt_close($stmt_update);

    // Hapus data pakaian lama
    mysqli_query($koneksi, "DELETE FROM pakaian WHERE transaksi_id = $id");

    // Simpan ulang pakaian yang ada
    if (isset($_POST['pakaian_jenis'])) {
        $stmt_pakaian = mysqli_prepare($koneksi, "
            INSERT INTO pakaian (transaksi_id, jenis_paket, id_paket, pakaian_jenis, pakaian_jumlah)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($_POST['pakaian_jenis'] as $i => $jenis) {
            $pakaian_jenis = mysqli_real_escape_string($koneksi, $jenis);
            $pakaian_jumlah = intval($_POST['pakaian_jumlah'][$i]);
            $jenis_paket = $_POST['pakaian_jenis_paket'][$i];
            $id_paket = intval($_POST['pakaian_id_paket'][$i]);

            mysqli_stmt_bind_param($stmt_pakaian, "isisi", $id, $jenis_paket, $id_paket, $pakaian_jenis, $pakaian_jumlah);
            if (!mysqli_stmt_execute($stmt_pakaian)) {
                throw new Exception("Gagal menyimpan data pakaian baru.");
            }
        }
        mysqli_stmt_close($stmt_pakaian);
    }

    mysqli_commit($koneksi);
    header("Location: transaksi.php?update=success");
    exit();

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    die("Terjadi kesalahan saat memperbarui transaksi: " . $e->getMessage());
}
?>
