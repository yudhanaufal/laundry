<?php 
include 'header.php'; 
include '../koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

?>

<div class="container">
    <!-- Panel Data Pelanggan -->
    <div class="panel">
        <div class="panel-heading">
            <h4>Daftar Pelanggan</h4>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Pelanggan</th>
                            <th>Username</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                            <th>Laundry</th> <!-- Tambahan kolom Laundry -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Pastikan tabel laundry ada sebelum menggunakan JOIN
                        $query_check = "SHOW TABLES LIKE 'laundry'";
                        $check_result = mysqli_query($koneksi, $query_check);

                        if (mysqli_num_rows($check_result) > 0) {
                            // Jika tabel laundry ada, gunakan LEFT JOIN
                            $query = "
                                SELECT user.nama, user.username, user.no_hp, user.alamat, laundry.nama_laundry 
                                FROM user
                                LEFT JOIN laundry ON user.laundry_id = laundry.laundry_id
                                WHERE user.level = 'pengguna'
                            ";
                        } else {
                            // Jika tabel laundry tidak ada, ambil data tanpa JOIN
                            $query = "
                                SELECT user.nama, user.username, user.no_hp, user.alamat, NULL as nama_laundry
                                FROM user
                                WHERE user.level = 'pengguna'
                            ";
                        }

                        $result = mysqli_query($koneksi, $query);

                        if (!$result) {
                            die("<tr><td colspan='6' class='text-center'>Error: " . mysqli_error($koneksi) . "</td></tr>");
                        }

                        $no = 1;
                        while ($d = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($d['nama']); ?></td>
                                <td><?php echo htmlspecialchars($d['username']); ?></td>
                                <td><?php echo htmlspecialchars($d['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars($d['alamat']); ?></td>
                                <td><?php echo htmlspecialchars($d['nama_laundry'] ?? 'Tidak diketahui'); ?></td> <!-- Nama Laundry -->
                            </tr>
                        <?php
                        }

                        // Jika tidak ada pelanggan yang ditemukan
                        if ($no == 1) {
                            echo "<tr><td colspan='6' class='text-center'>Belum ada pelanggan</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
