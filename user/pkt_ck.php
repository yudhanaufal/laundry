<?php
include 'header.php';
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$data_ck = mysqli_query($koneksi, "SELECT * FROM tb_cuci_komplit JOIN laundry ON laundry.laundry_id = tb_cuci_komplit.laundry_id");
?>

<div class="container mt-4">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="pull-left" style="margin-top: 7px;">Daftar Paket Cuci Komplit</h4>
            <div class="pull-right">
                <a href="paket.php" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-arrow-left"></i> Kembali</a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>NO</th>
                        <th>LAUNDRY</th>
                        <th>NAMA PAKET</th>
                        <th>WAKTU KERJA</th>
                        <th>BERAT MIN (KG)</th>
                        <th>TARIF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($ck = mysqli_fetch_assoc($data_ck)) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($ck['nama_laundry']) ?></td>
                            <td><?= htmlspecialchars($ck['nama_paket_ck']) ?></td>
                            <td><?= htmlspecialchars($ck['waktu_kerja_ck']) ?></td>
                            <td><?= htmlspecialchars($ck['kuantitas_ck']) ?> Kg</td>
                            <td>Rp <?= number_format($ck['tarif_ck'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>