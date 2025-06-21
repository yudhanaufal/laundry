<?php
include 'header.php';
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$data_dc = mysqli_query($koneksi, "SELECT * FROM tb_dry_clean JOIN laundry ON laundry.laundry_id = tb_dry_clean.laundry_id");
?>

<div class="container" style="margin-top: 30px;">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="pull-left" style="margin-top: 7px;">Daftar Paket Dry Clean</h4>
            <div class="pull-right">
                <a href="paket.php" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-arrow-left"></i> Kembali</a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Laundry</th>
                        <th scope="col">Nama Paket</th>
                        <th scope="col">Waktu Kerja</th>
                        <th scope="col">Berat Min (Kg)</th>
                        <th scope="col">Tarif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($dc = mysqli_fetch_assoc($data_dc)) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($dc['nama_laundry']) ?></td>
                            <td><?= htmlspecialchars($dc['nama_paket_dc']) ?></td>
                            <td><?= htmlspecialchars($dc['waktu_kerja_dc']) ?></td>
                            <td><?= htmlspecialchars($dc['kuantitas_dc']) ?> Kg</td>
                            <td><?= number_format($dc['tarif_dc'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<?php include 'footer.php'; ?>