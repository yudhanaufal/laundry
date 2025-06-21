<?php
include 'header.php';
include '../koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

// Ambil laundry_id dari sesi
$laundry_id = $_SESSION['laundry_id'];

?>

<div class="container">
    <div class="panel">
        <div class="panel-heading">
            <h4>Data Pelanggan</h4>
        </div>
        <div class="panel-body">
            <!-- Tabel Data Pelanggan -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="1%">No</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_pelanggan = mysqli_prepare($koneksi, "SELECT DISTINCT transaksi_pelanggan FROM transaksi WHERE laundry_id = ?");
                        mysqli_stmt_bind_param($query_pelanggan, "i", $laundry_id);
                        mysqli_stmt_execute($query_pelanggan);
                        mysqli_stmt_store_result($query_pelanggan);
                        mysqli_stmt_bind_result($query_pelanggan, $pelanggan_id);

                        $daftar_pelanggan = [];
                        while (mysqli_stmt_fetch($query_pelanggan)) {
                            $daftar_pelanggan[] = $pelanggan_id;
                        }

                        mysqli_stmt_close($query_pelanggan);

                        $no = 1;
                        if (!empty($daftar_pelanggan)) {
                            $id_list = implode(',', array_map('intval', $daftar_pelanggan));

                            $query = "SELECT * FROM user WHERE id IN ($id_list)";
                            $result = $koneksi->query($query);

                            while ($d = $result->fetch_assoc()) {
                        ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($d['nama']); ?></td>
                                    <td><?php echo htmlspecialchars($d['no_hp']); ?></td>
                                    <td><?php echo htmlspecialchars($d['alamat']); ?></td>
                                </tr>
                        <?php
                            }
                        }

                        if ($no == 1) {
                            echo "<tr><td colspan='5' class='text-center'>Belum ada pelanggan</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>