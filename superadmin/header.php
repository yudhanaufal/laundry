<?php
session_start();
include '../koneksi.php'; // Pastikan koneksi ke database disertakan

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../index.php?pesan=belum_login");
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']);

// Ambil data nama laundry dari database berdasarkan `laundry_id` pengguna
$nama_laundry = "LAUNDRY"; // Default jika tidak ditemukan

if (isset($_SESSION['laundry_id'])) {
    $laundry_id = $_SESSION['laundry_id'];
    $query = mysqli_query($koneksi, "SELECT nama_laundry FROM laundry WHERE laundry_id = '$laundry_id'");

    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $nama_laundry = $data['nama_laundry'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo strtoupper($nama_laundry); ?></title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <script type="text/javascript" src="../assets/js/jquery.js"></script>
    <script type="text/javascript" src="../assets/js/bootstrap.js"></script>
</head>
<body style="background: #f0f0f0">

<nav class="navbar navbar-inverse" style="border-radius: 0px">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php"><?php echo strtoupper($nama_laundry); ?></a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li class="<?= ($current_page == 'index.php') ? 'active' : '' ?>"><a href="index.php"><i class="glyphicon glyphicon-home"></i> Dashboard</a></li>
                <li class="<?= ($current_page == 'admin.php') ? 'active' : '' ?>"><a href="admin.php"><i class="glyphicon glyphicon-knight"></i> Admin</a></li>
                <li class="<?= ($current_page == 'pelanggan.php') ? 'active' : '' ?>"><a href="pelanggan.php"><i class="glyphicon glyphicon-user"></i> Pelanggan</a></li>
                <li class="<?= ($current_page == 'superadmin_approve.php') ? 'active' : '' ?>"><a href="superadmin_approve.php"><i class="glyphicon glyphicon-list-alt"></i> Laporan</a></li>
                <li class="dropdown <?= ($current_page == 'harga.php' || $current_page == 'ganti_password.php') ? 'active' : '' ?>"> 
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <i class="glyphicon glyphicon-wrench"></i> Pengaturan <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="<?= ($current_page == 'ganti_password.php') ? 'active' : '' ?>"><a href="ganti_password.php"><i class="glyphicon glyphicon-lock"></i> Ganti Password</a></li>
                    </ul>
                </li>
                <li><a href="logout.php"><i class="glyphicon glyphicon-log-out"></i> Log Out</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><p class="navbar-text">Halo, <b><?php echo $_SESSION['username']; ?></b> !</p></li>
            </ul>
        </div>
    </div>
</nav>
