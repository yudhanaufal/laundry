<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEM INFORMASI UMKM LAUNDRY</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</head>

<body style="background: #f8f9fa;">


    <div class="container">
        <!-- Form Login -->
        <center>
            <div class="login-brand">
                <img src="assets/img/5.png" class="rounded-circle img-fluid" style="max-width: 400px; height: auto;">
            </div>
        </center>
        <div class="col-md-4 col-md-offset-4">
            <?php
            session_start();

            // Menampilkan pesan berdasarkan parameter 'pesan'
            if (isset($_GET['pesan'])) {
                $pesan = $_GET['pesan'];
                if ($pesan == "gagal") {
                    echo "<div class='alert alert-danger'>Login gagal! Username atau password salah.</div>";
                } elseif ($pesan == "logout") {
                    echo "<div class='alert alert-info'>Anda telah berhasil logout.</div>";
                } elseif ($pesan == "belum_login") {
                    echo "<div class='alert alert-warning'>Anda harus login untuk mengakses halaman admin.</div>";
                }
            }
            ?>
            <form action="login.php" method="post">
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
                        <button type="submit" class="btn btn-primary btn-block">Log In</button>
                    </div>
                </div>
            </form>

            <!-- Link Registrasi -->
            <div class="text-center">
                <p>Belum punya akun?
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="text-decoration: none;">Daftar di sini <span class="caret"></span></a>
                </p>
                <!-- Dropdown untuk memilih tipe registrasi -->
                <ul class="dropdown-menu" role="menu" style="display: none;">
                    <li><a href="registrasi_admin.php">Daftar sebagai Admin</a></li>
                    <li><a href="registrasi.php">Daftar sebagai Pelanggan</a></li>
                </ul>
            </div>

            <!-- Jangan lupa untuk tambahkan script jQuery untuk membuat dropdown berfungsi -->
            <script>
                $(document).ready(function() {
                    $(".dropdown-toggle").click(function() {
                        $(".dropdown-menu").toggle(); // Menampilkan atau menyembunyikan dropdown
                    });
                });
            </script>
        </div>
    </div>
</body>

</html>