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

// Ambil data pakaian tambahan
$pakaian_query = "SELECT * FROM pakaian WHERE transaksi_id = ?";
$stmt_pakaian = mysqli_prepare($koneksi, $pakaian_query);
mysqli_stmt_bind_param($stmt_pakaian, "i", $id);
mysqli_stmt_execute($stmt_pakaian);
$pakaian_result = mysqli_stmt_get_result($stmt_pakaian);
$pakaian_list = mysqli_fetch_all($pakaian_result, MYSQLI_ASSOC);

// Ambil detail layanan ck dan dc
$ck_list = mysqli_query($koneksi, "
    SELECT ck.nama_paket_ck, td.jumlah, ck.tarif_ck 
    FROM transaksi_detail td
    JOIN tb_cuci_komplit ck ON td.id_paket = ck.id_ck
    WHERE td.transaksi_id = $id AND td.jenis_paket = 'ck'
");

$dc_list = mysqli_query($koneksi, "
    SELECT dc.nama_paket_dc, td.jumlah, dc.tarif_dc 
    FROM transaksi_detail td
    JOIN tb_dry_clean dc ON td.id_paket = dc.id_dc
    WHERE td.transaksi_id = $id AND td.jenis_paket = 'dc'
");
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
                    <label>Data Pakaian (Tambahan)</label>
                    <div id="pakaian-container">
                        <?php foreach ($pakaian_list as $p) { ?>
                            <div class="input-group mb-2">
                                <input type="text" name="pakaian_jenis[]" class="form-control" value="<?= htmlspecialchars($p['pakaian_jenis']) ?>" required>
                                <input type="number" name="pakaian_jumlah[]" class="form-control" value="<?= htmlspecialchars($p['pakaian_jumlah']) ?>" required>
                                <button type="button" class="btn btn-danger remove-pakaian">Hapus</button>
                            </div>
                        <?php } ?>
                    </div>
                    <button type="button" id="add-pakaian" class="btn btn-success">Tambah Pakaian</button>
                </div>

                <div class="form-group">
                    <label>Berat per Layanan</label>
                    <ul>
                        <?php while ($ck = mysqli_fetch_assoc($ck_list)) : ?>
                            <li>
                                <?= htmlspecialchars($ck['nama_paket_ck']) ?> (<?= $ck['jumlah'] ?>x):
                                <input type="number" step="0.1" name="berat_ck[]" class="form-control d-inline-block" style="width: 120px; display: inline;" value="<?= $ck['jumlah'] ?>" required> Kg
                                <input type="hidden" name="tarif_ck[]" value="<?= $ck['tarif_ck'] ?>">
                                <input type="hidden" name="jumlah_ck[]" value="<?= $ck['jumlah'] ?>">
                            </li>
                        <?php endwhile; ?>

                        <?php while ($dc = mysqli_fetch_assoc($dc_list)) : ?>
                            <li>
                                <?= htmlspecialchars($dc['nama_paket_dc']) ?> (<?= $dc['jumlah'] ?>x):
                                <input type="number" step="0.1" name="berat_dc[]" class="form-control d-inline-block" style="width: 120px; display: inline;" value="<?= $dc['jumlah'] ?>" required> Kg
                                <input type="hidden" name="tarif_dc[]" value="<?= $dc['tarif_dc'] ?>">
                                <input type="hidden" name="jumlah_dc[]" value="<?= $dc['jumlah'] ?>">
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div class="form-group">
                    <label>Tanggal Selesai</label>
                    <input type="date" class="form-control" name="tgl_selesai" value="<?= $transaksi['transaksi_tgl_selesai'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" required>
                        <option value="proses" <?= $transaksi['transaksi_status'] == 'proses' ? 'selected' : '' ?>>Proses</option>
                        <option value="selesai" <?= $transaksi['transaksi_status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="diantar" <?= $transaksi['transaksi_status'] == 'diantar' ? 'selected' : '' ?>>Diantar</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="transaksi.php" class="btn btn-default">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById("add-pakaian").addEventListener("click", function () {
    const container = document.getElementById("pakaian-container");
    const div = document.createElement("div");
    div.className = "input-group mb-2";
    div.innerHTML = `
        <input type="text" name="pakaian_jenis[]" class="form-control" placeholder="Jenis Pakaian" required>
        <input type="number" name="pakaian_jumlah[]" class="form-control" placeholder="Jumlah" required>
        <button type="button" class="btn btn-danger remove-pakaian">Hapus</button>
    `;
    container.appendChild(div);
});

document.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-pakaian")) {
        e.target.parentElement.remove();
    }
});
</script>

<?php include 'footer.php'; ?>
