<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEM INFORMASI UMKM LAUNDRY - Registrasi Admin Laundry</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</head>

<body style="background: #f8f9fa;">

    <div class="container">
        <center>
            <h2>REGISTRASI ADMIN LAUNDRY</h2>
            <img src="assets/img/5.png" class="rounded-circle img-fluid" style="max-width: 400px; height: auto;">
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
            ?>

            <!-- Form Registrasi Admin Laundry -->
            <form action="proses_registrasi_admin.php" method="POST" enctype="multipart/form-data">
                <div class="panel">
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                        </div>

                        <div class="form-group">
                            <label for="no_hp">Nomor HP</label>
                            <input type="number" name="no_hp" id="no_hp" class="form-control" placeholder="6281234567890" pattern="62\d{9,}" title="Nomor harus diawali dengan 62 dan diikuti 9-15 digit angka" required>
                            <small class="form-text text-muted">Masukkan nomor HP dengan awalan <strong>62</strong> (contoh: 6281234567890).</small>
                        </div>

                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control" placeholder="Masukkan alamat lengkap" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="nama_laundry">Nama Laundry</label>
                            <input type="text" name="nama_laundry" id="nama_laundry" class="form-control" placeholder="Masukkan nama laundry" required>
                        </div>

                        <div class="form-group">
                            <label for="alamat_laundry">Alamat Laundry</label>
                            <textarea name="alamat_laundry" id="alamat_laundry" class="form-control" placeholder="Masukkan alamat laundry" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="bukti_laundry">Bukti Laundry (Opsional)</label>
                            <input type="file" name="bukti_laundry" id="bukti_laundry" class="form-control">
                            <small class="form-text text-muted">Opsional: Upload bukti tempat laundry seperti foto KTP atau bukti usaha.</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Daftar Admin Laundry</button>
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