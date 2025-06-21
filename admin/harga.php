<?php include 'header.php'; ?>
<div class="container">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel">
            <div class="panel-heading">
                <h4>Pengaturan Harga Laundry</h4>
            </div>
            <div class="panel-body">
                <?php
                include '../koneksi.php';

                if(isset($_GET['pesan']) && $_GET['pesan'] == "berhasil"){
                    echo "<div class='alert alert-info'>Berhasil Update</div>";
                }
                ?>

                <form method="post" action="harga_update.php">
                    <input type="hidden" name="laundry_id" value="<?php echo $_SESSION['laundry_id']; ?>">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis Layanan</th>
                                <th>Harga</th>
                                <th>Satuan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $laundry_id = $_SESSION['laundry_id']; // Ambil laundry_id dari session
                            $data = mysqli_query($koneksi, "SELECT * FROM harga WHERE laundry_id = '$laundry_id' ORDER BY harga_id ASC");
                            while($d = mysqli_fetch_array($data)){
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <input type="hidden" name="harga_id[]" value="<?php echo $d['harga_id']; ?>">
                                    <input type="text" class="form-control" name="jenis_harga[]" value="<?php echo $d['jenis_harga']; ?>" required>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="harga[]" value="<?php echo $d['harga']; ?>" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="satuan[]" value="<?php echo $d['satuan']; ?>" required>
                                </td>
                                <td>
                                    <a href="harga_hapus.php?id=<?php echo $d['harga_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    
                    <!-- Tambah Layanan Baru -->
                    <h4>Tambah Layanan Baru</h4>
                    <table class="table table-bordered">
                        <tr>
                            <td><input type="text" class="form-control" name="jenis_harga_baru" placeholder="Jenis Layanan" required></td>
                            <td><input type="number" class="form-control" name="harga_baru" placeholder="Harga" required></td>
                            <td><input type="text" class="form-control" name="satuan_baru" placeholder="Satuan" required></td>
                            <td><input type="submit" class="btn btn-success" name="tambah" value="Tambah"></td>
                        </tr>
                    </table>

                    <br/>
                    <input type="submit" class="btn btn-primary" name="update" value="Update Harga">
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
