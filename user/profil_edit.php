<?php include 'header.php'; ?>

<div class="container">
    <br/>
    <br/>
    <br/>
    <div class="col-md-5 col-md-offset-3">
        <div class="panel">
            <div class="panel-heading">
                <h4>Edit Profil Saya</h4>
            </div>
            <div class="panel-body">
                <?php
                include '../koneksi.php';

                // Pastikan user sudah login
                if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
                    header("location: ../index.php?pesan=belum_login");
                    exit();
                }

                // Ambil id user dari session
                $id = $_SESSION['id'];

                // Ambil data user dari database berdasarkan id
                $stmt = $koneksi->prepare("SELECT * FROM user WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($d = $result->fetch_assoc()) {
                ?>
                    <form method="post" action="profil_update.php">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                            <input type="text" class="form-control" name="nama" placeholder="Masukkan nama..." value="<?php echo htmlspecialchars($d['nama']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>No HP</label>
                            <input type="text" class="form-control" name="hp" placeholder="Masukkan no. HP..." value="<?php echo htmlspecialchars($d['no_hp']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea class="form-control" name="alamat" placeholder="Masukkan alamat..." required><?php echo htmlspecialchars($d['alamat']); ?></textarea>
                        </div>
                        <br/>
                        <input type="submit" class="btn btn-primary" value="Update">
                    </form>
                <?php
                } else {
                    echo "<p class='text-center'>Data tidak ditemukan.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
