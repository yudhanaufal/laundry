<?php include 'header.php'; ?>
<div class="container">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel">
            <div class="panel-heading text-center">
                <h4>Daftar Harga Laundry</h4>
            </div>
            <div class="panel-body">
                <?php
                include '../koneksi.php';

                // Menampilkan notifikasi jika update berhasil
                if (isset($_GET['pesan']) && $_GET['pesan'] == "berhasil") {
                    echo "<div class='alert alert-info text-center'>Harga berhasil diperbarui</div>";
                }

                // Pastikan hanya harga dari laundry yang terkait dengan user ditampilkan
                $laundry_id = $_SESSION['laundry_id'];
                $query = "SELECT * FROM harga WHERE laundry_id = ? ORDER BY harga_id ASC";
                $stmt = mysqli_prepare($koneksi, $query);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "i", $laundry_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                }
                ?>

                <table class="table table-bordered">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Jenis Layanan</th>
                            <th>Harga (Rp)</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($d = mysqli_fetch_array($result)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($d['jenis_harga']); ?></td>
                                <td><?php echo number_format($d['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($d['satuan']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="text-center">
                    <a href="index.php" class="btn btn-primary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>