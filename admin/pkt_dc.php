<?php
include 'header.php';
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$laundry_id = $_SESSION['laundry_id'];
$data_dc = mysqli_query($koneksi, "SELECT * FROM tb_dry_clean WHERE laundry_id = '$laundry_id'");
?>

<div class="container" style="margin-top: 30px;">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="pull-left" style="margin-top: 7px;">Daftar Paket Dry Clean</h4>
            <div class="pull-right">
                <a href="paket.php" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-arrow-left"></i> Kembali</a>
                <a href="pkt_dc_tambah.php" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> Tambah Paket</a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Paket</th>
                        <th scope="col">Waktu Kerja</th>
                        <th scope="col">Berat Min (Kg)</th>
                        <th scope="col">Tarif</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php while ($dc = mysqli_fetch_assoc($data_dc)) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($dc['nama_paket_dc']) ?></td>
                            <td><?= htmlspecialchars($dc['waktu_kerja_dc']) ?></td>
                            <td><?= htmlspecialchars($dc['kuantitas_dc']) ?> Kg</td>
                            <td><?= number_format($dc['tarif_dc'], 0, ',', '.') ?></td>
                            <td>
                                <a href="pkt_cs_edit.php?id_cs=<?= $cs['id_cs'] ?>" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
                                <a href="pkt_cs_hapus.php?id_cs=<?= $cs['id_cs'] ?>" class="btn btn-danger btn-xs" onclick="return confirm('Yakin akan menghapus?');"><i class="glyphicon glyphicon-trash"></i> Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<?php include 'footer.php'; ?>