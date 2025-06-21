<?php
include 'header.php';
include '../koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['level'])) {
    echo "<script>alert('Akses ditolak!'); window.location='../login.php';</script>";
    exit;
}

// Pastikan hanya superadmin dan admin yang bisa mengakses halaman ini
if ($_SESSION['level'] !== 'superadmin' && $_SESSION['level'] !== 'admin') {
    echo "<script>alert('Akses ditolak!'); window.location='../index.php';</script>";
    exit;
}

?>
<div class="container">
    <div class="panel">
        <div class="panel-heading">
            <h4>Data Admin</h4>
        </div>
        <div class="panel-body">

            <!-- Tabel Data Admin -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Laundry</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                            <th>Nama Pemilik</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query untuk mengambil data admin dan menghubungkannya dengan pemilik
                        $query = "SELECT user.*, laundry.nama_laundry
                                  FROM user
                                  JOIN laundry ON laundry.laundry_id = user.laundry_id
                                  WHERE user.level IN ('admin')";

                        $data = mysqli_query($koneksi, $query);
                        $no = 1;

                        while ($d = mysqli_fetch_array($data)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($d['nama_laundry']); ?></td>
                                <td><?php echo htmlspecialchars($d['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars($d['alamat']); ?></td>
                                <td><?php echo htmlspecialchars($d['nama']) ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>