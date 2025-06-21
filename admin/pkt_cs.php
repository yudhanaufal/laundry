<?php
include 'header.php';
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$laundry_id = $_SESSION['laundry_id'];
$data_cs = mysqli_query($koneksi, "SELECT * FROM tb_cuci_satuan WHERE laundry_id = '$laundry_id'");
?>

<div class="container" style="margin-top: 30px;">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="pull-left" style="margin-top: 7px;">Daftar Paket Cuci Satuan</h4>
            <div class="pull-right">
                <a href="paket.php" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-arrow-left"></i> Kembali</a>
                <a href="pkt_cs_tambah.php" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i> Tambah Paket</a>
            </div>
        </div>
        <div class="panel-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NAMA PAKET</th>
                        <th>WAKTU KERJA</th>
                        <th>JUMLAH MIN (PCS)</th>
                        <th>TARIF</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    while ($cs = mysqli_fetch_assoc($data_cs)) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($cs['nama_cs']) ?></td>
                            <td><?= htmlspecialchars($cs['waktu_kerja_cs']) ?></td>
                            <td><?= htmlspecialchars($cs['kuantitas_cs']) ?> Pcs</td>
                            <td>Rp <?= number_format($cs['tarif_cs'], 0, ',', '.') ?></td>
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

<?php include 'footer.php'; ?>
