<?php
include 'header.php';
include '../koneksi.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$laundry_id = $_SESSION['laundry_id'];
$level = $_SESSION['level']; // Ambil level user

// Ambil data pemilik berdasarkan laundry_id (hanya pemilik yang sesuai)
$data = mysqli_query($koneksi, "SELECT * FROM pemilik WHERE laundry_id = '$laundry_id'");

?>
<div class="container">
    <div class="panel">
        <div class="panel-heading">
            <h4>Data Pemilik</h4>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="1%">No</th>
                            <th>Nama</th>
                            <th>HP</th>
                            <?php if ($level == 'admin') { ?> <!-- Hanya admin yang bisa mengedit atau menghapus -->
                                <th width="15%">Aksi</th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($d = mysqli_fetch_array($data)) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($d['pemilik_nama']); ?></td>
                                <td><?php echo htmlspecialchars($d['pemilik_hp']); ?></td>
                                <?php if ($level == 'admin') { ?>
                                    <td>
                                        <a href="pemilik_edit.php?id=<?php echo $d['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="pemilik_hapus.php?id=<?php echo $d['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?');">Hapus</a>
                                    </td>
                                <?php } ?>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php if ($level == 'admin') { ?>
                <a href="pemilik_tambah.php" class="btn btn-primary">Tambah Pemilik</a>
            <?php } ?>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
