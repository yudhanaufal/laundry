<?php
include 'header.php';
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$data_cs = mysqli_query($koneksi, "SELECT * FROM tb_cuci_satuan JOIN laundry ON laundry.laundry_id = tb_cuci_satuan.laundry_id");
?>

<div class="container" style="margin-top: 30px;">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="pull-left" style="margin-top: 7px;">Daftar Paket Cuci Satuan</h4>
            <div class="pull-right">
                <a href="paket.php" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-arrow-left"></i> Kembali</a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>LAUNDRY</th>
                        <th>NAMA PAKET</th>
                        <th>WAKTU KERJA</th>
                        <th>JUMLAH MIN (PCS)</th>
                        <th>TARIF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($cs = mysqli_fetch_assoc($data_cs)) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($cs['nama_laundry']) ?></td>
                            <td><?= htmlspecialchars($cs['nama_cs']) ?></td>
                            <td><?= htmlspecialchars($cs['waktu_kerja_cs']) ?></td>
                            <td><?= htmlspecialchars($cs['kuantitas_cs']) ?> Pcs</td>
                            <td>Rp <?= number_format($cs['tarif_cs'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>