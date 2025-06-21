<?php
include 'header.php';
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$laundry_id = $_SESSION['laundry_id'];

// Proses Tambah Paket Cuci Satuan
if (isset($_POST['tambah'])) {
    $nama_cs = $_POST['nama_cs'];
    $waktu_kerja_cs = $_POST['waktu_kerja_cs'];
    $kuantitas_cs = $_POST['kuantitas_cs'];
    $tarif_cs = $_POST['tarif_cs'];

    $query = "INSERT INTO tb_cuci_satuan (laundry_id, nama_cs, waktu_kerja_cs, kuantitas_cs, tarif_cs)
          VALUES ('$laundry_id', '$nama_cs', '$waktu_kerja_cs', '$kuantitas_cs', '$tarif_cs')";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<div class='alert alert-success text-center'>Paket berhasil ditambahkan! <a href='pkt_cs.php'>Lihat Daftar</a></div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Gagal menambahkan paket.</div>";
    }
}
?>

<div class="container mt-4">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h4 class="pull-left" style="margin-top: 7px;">Tambah Paket Cuci Satuan</h4>
            <div class="pull-right">
                <a href="pkt_cs.php" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-arrow-left"></i> Kembali</a>
            </div>
        </div>
        <div class="panel-body">
            <form method="post" action="" class="form-horizontal">

                <div class="form-group">
                    <label class="col-sm-3 control-label">Nama Paket</label>
                    <div class="col-sm-6">
                        <input type="text" name="nama_cs" class="form-control" placeholder="Nama paket" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Waktu Kerja</label>
                    <div class="col-sm-6">
                        <input type="text" name="waktu_kerja_cs" class="form-control" placeholder="Durasi Kerja" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Jumlah Min(PCS)</label>
                    <div class="col-sm-6">
                        <input type="number" name="kuantitas_cs" class="form-control" placeholder="Jumlah Pakaian" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">Tarif</label>
                    <div class="col-sm-6">
                        <input type="number" name="tarif_cs" class="form-control" placeholder="Harga (Rp)" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-6">
                        <button type="submit" name="tambah" class="btn btn-success">
                            <i class="glyphicon glyphicon-plus"></i> Tambah Paket
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
