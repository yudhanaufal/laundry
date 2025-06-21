<?php 
include 'header.php';
include '../koneksi.php';

if (!isset($_SESSION['laundry_id'])) {
    echo "<script>alert('Anda tidak memiliki akses. Silakan login.'); window.location.href='../login.php';</script>";
    exit();
}

$laundry_id = $_SESSION['laundry_id'];
$id = $_GET['id'] ?? null;

if (!$id) {
    echo "<script>alert('ID transaksi tidak ditemukan!'); window.location.href='transaksi.php';</script>";
    exit();
}

// Ambil data transaksi
$query = "SELECT t.*, u.nama FROM transaksi t 
          JOIN user u ON t.transaksi_pelanggan = u.id 
          WHERE t.transaksi_id = ? AND t.laundry_id = ? LIMIT 1";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "ii", $id, $laundry_id);
mysqli_stmt_execute($stmt);
$data = mysqli_stmt_get_result($stmt);
$transaksi = mysqli_fetch_assoc($data);

if (!$transaksi) {
    echo "<script>alert('Transaksi tidak ditemukan!'); window.location.href='transaksi.php';</script>";
    exit();
}

// Ambil data transaksi_detail
$detail_query = "SELECT td.*, 
    CASE 
        WHEN td.jenis_paket = 'ck' THEN (SELECT nama_paket_ck FROM tb_cuci_komplit WHERE id_ck = td.id_paket)
        WHEN td.jenis_paket = 'cs' THEN (SELECT nama_cs FROM tb_cuci_satuan WHERE id_cs = td.id_paket)
        WHEN td.jenis_paket = 'dc' THEN (SELECT nama_paket_dc FROM tb_dry_clean WHERE id_dc = td.id_paket)
    END AS nama_paket,
    CASE
        WHEN td.jenis_paket = 'ck' THEN (SELECT tarif_ck FROM tb_cuci_komplit WHERE id_ck = td.id_paket)
        WHEN td.jenis_paket = 'cs' THEN (SELECT tarif_cs FROM tb_cuci_satuan WHERE id_cs = td.id_paket)
        WHEN td.jenis_paket = 'dc' THEN (SELECT tarif_dc FROM tb_dry_clean WHERE id_dc = td.id_paket)
    END AS tarif
FROM transaksi_detail td
WHERE td.transaksi_id = $id";
$detail_result = mysqli_query($koneksi, $detail_query);

// Ambil data pakaian
$pakaian_result = mysqli_query($koneksi, "SELECT * FROM pakaian WHERE transaksi_id = $id");

$grouped_pakaian = [];
while ($p = mysqli_fetch_assoc($pakaian_result)) {
    $grouped_pakaian[$p['jenis_paket']][$p['id_paket']][] = $p;
}
?>

<div class="container">
    <div class="panel">
        <div class="panel-heading">
            <h4>Edit Transaksi</h4>
        </div>
        <div class="panel-body">
            <form action="transaksi_update.php" method="POST">
                <input type="hidden" name="id" value="<?= $transaksi['transaksi_id'] ?>">
                <input type="hidden" name="pelanggan_id" value="<?= $transaksi['transaksi_pelanggan'] ?>">
                <input type="hidden" name="laundry_id" value="<?= $laundry_id ?>">

                <div class="form-group">
                    <label>Pelanggan</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($transaksi['nama']) ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Detail Layanan dan Berat</label>
                    <ul>
                        <?php while ($paket = mysqli_fetch_assoc($detail_result)) : ?>
                            <li>
                                <strong><?= strtoupper($paket['jenis_paket']) ?> - <?= htmlspecialchars($paket['nama_paket']) ?></strong><br>
                                <?php if ($paket['jenis_paket'] == 'ck' || $paket['jenis_paket'] == 'dc') : ?>
                                    Berat (Kg) 
                                    <input type="number" step="0.1" name="berat_<?= $paket['jenis_paket'] ?>[]" class="form-control d-inline-block" style="width:120px;" required>
                                    <input type="hidden" name="tarif_<?= $paket['jenis_paket'] ?>[]" value="<?= $paket['tarif'] ?>">
                                    <input type="hidden" name="jumlah_<?= $paket['jenis_paket'] ?>[]" value="<?= $paket['jumlah'] ?>">
                                <?php endif; ?>

                                <?php
                                $jenis = $paket['jenis_paket'];
                                $idpaket = $paket['id_paket'];
                                if (isset($grouped_pakaian[$jenis][$idpaket])) {
                                    echo "<ul>";
                                    foreach ($grouped_pakaian[$jenis][$idpaket] as $p) {
                                        echo "<li>" . htmlspecialchars($p['pakaian_jenis']) . " - " . $p['pakaian_jumlah'] . " pcs</li>";
                                        echo '<input type="hidden" name="pakaian_jenis[]" value="' . htmlspecialchars($p['pakaian_jenis']) . '">';
                                        echo '<input type="hidden" name="pakaian_jumlah[]" value="' . htmlspecialchars($p['pakaian_jumlah']) . '">';
                                        echo '<input type="hidden" name="pakaian_jenis_paket[]" value="' . htmlspecialchars($p['jenis_paket']) . '">';
                                        echo '<input type="hidden" name="pakaian_id_paket[]" value="' . htmlspecialchars($p['id_paket']) . '">';
                                    }
                                    echo "</ul>";
                                }
                                ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div class="form-group">
                    <label>Tanggal Selesai</label>
                    <input type="date" class="form-control" name="tgl_selesai" value="<?= $transaksi['transaksi_tgl_selesai'] ?>" required>
                </div>

                <input type="hidden" name="status" value="<?= htmlspecialchars($transaksi['transaksi_status']) ?>">


                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="transaksi.php" class="btn btn-default">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
