<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEM INFORMASI UMKM LAUNDRY - Registrasi</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</head>

<body style="background: #f8f9fa;">


    <div class="container">
        <center>
            <h2>REGISTRASI</h2>
            <div class="login-brand">
                <img src="assets/img/5.png" class="rounded-circle img-fluid" style="max-width: 300px; height: auto;">
            </div>
        </center>
        <div class="col-md-4 col-md-offset-4">
            <?php
            session_start();
            include 'koneksi.php';

            if (isset($_GET['pesan'])) {
                $pesan = $_GET['pesan'];
                if ($pesan == "gagal") {
                    echo "<div class='alert alert-danger'>Registrasi gagal! Silakan coba lagi.</div>";
                } elseif ($pesan == "logout") {
                    echo "<div class='alert alert-info'>Anda telah berhasil logout.</div>";
                } elseif ($pesan == "belum_login") {
                    echo "<div class='alert alert-warning'>Anda harus login untuk mengakses halaman admin.</div>";
                }
            }

            // Ambil daftar laundry
            $query_laundry = mysqli_query($koneksi, "SELECT * FROM laundry");
            ?>

            <!-- Form Registrasi -->
            <form action="proses_registrasi.php" method="post">
                <div class="panel">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                        </div>
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="form-group">
                            <label for="no_hp">Nomor HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" placeholder="6281234567890" pattern="62\d{9,}" title="Nomor harus diawali dengan 62 dan diikuti 9-15 digit angka" required>
                            <small class="form-text text-muted">Masukkan nomor HP dengan awalan <strong>62</strong> (contoh: 6281234567890).</small>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="laundry">Pilih Tempat Laundry</label>
                            <select class="form-control" id="laundry" name="laundry_id" required>
                                <option value="">-- Pilih Laundry --</option>
                                <?php
                                while ($row = mysqli_fetch_assoc($query_laundry)) {
                                    echo "<option value='" . $row['laundry_id'] . "'>" . $row['nama_laundry'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                    </div>
                </div>
            </form>

            <div class="text-center">
                <p>Sudah punya akun? <a href="index.php">Masuk di sini</a></p>
            </div>
        </div>
    </div>
</body>

</html>