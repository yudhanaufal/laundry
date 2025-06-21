<?php
include '../koneksi.php';

function update_total_harga($transaksi_id)
{
    global $koneksi;

    $transaksi_id = intval($transaksi_id);

    $berat_total = 0;
    $total_harga = 0;

    // Ambil semua detail transaksi
    $detail = mysqli_query($koneksi, "SELECT * FROM transaksi_detail WHERE transaksi_id = $transaksi_id");

    while ($d = mysqli_fetch_assoc($detail)) {
        $jenis = $d['jenis_paket'];
        $id_paket = intval($d['id_paket']);
        $jumlah = floatval($d['jumlah']); // untuk CS saja

        if ($jenis == 'ck') {
            // CK: Hitung harga berdasarkan berat
            $q = mysqli_query($koneksi, "SELECT tarif_ck FROM tb_cuci_komplit WHERE id_ck = $id_paket LIMIT 1");
            $row = mysqli_fetch_assoc($q);
            $tarif = floatval($row['tarif_ck'] ?? 0);

            // Ambil berat dari pakaian untuk jenis 'ck'
            $berat_q = mysqli_query($koneksi, "SELECT SUM(pakaian_jumlah) as total_berat FROM pakaian WHERE transaksi_id = $transaksi_id AND jenis_paket = 'ck' AND id_paket = $id_paket");
            $berat_row = mysqli_fetch_assoc($berat_q);
            $berat = floatval($berat_row['total_berat'] ?? 0);

            $total_harga += $tarif * $berat;
            $berat_total += $berat;
        } elseif ($jenis == 'dc') {
            // DC: Hitung harga berdasarkan berat
            $q = mysqli_query($koneksi, "SELECT tarif_dc FROM tb_dry_clean WHERE id_dc = $id_paket LIMIT 1");
            $row = mysqli_fetch_assoc($q);
            $tarif = floatval($row['tarif_dc'] ?? 0);

            $berat_q = mysqli_query($koneksi, "SELECT SUM(pakaian_jumlah) as total_berat FROM pakaian WHERE transaksi_id = $transaksi_id AND jenis_paket = 'dc' AND id_paket = $id_paket");
            $berat_row = mysqli_fetch_assoc($berat_q);
            $berat = floatval($berat_row['total_berat'] ?? 0);

            $total_harga += $tarif * $berat;
            $berat_total += $berat;
        } elseif ($jenis == 'cs') {
            // CS: Hitung harga berdasarkan jumlah pcs
            $q = mysqli_query($koneksi, "SELECT tarif_cs FROM tb_cuci_satuan WHERE id_cs = $id_paket LIMIT 1");
            $row = mysqli_fetch_assoc($q);
            $tarif = floatval($row['tarif_cs'] ?? 0);

            $total_harga += $tarif * $jumlah;
            // CS tidak menambah berat
        }
    }

    // Update ke tabel transaksi
    $stmt = mysqli_prepare($koneksi, "
UPDATE transaksi SET 
    transaksi_berat = ?, 
    transaksi_total_harga = ?
WHERE transaksi_id = ?
");
    mysqli_stmt_bind_param($stmt, "ddi", $berat_total, $total_harga, $transaksi_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
