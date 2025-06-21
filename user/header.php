<?php
include '../koneksi.php'; // Pastikan file koneksi database sudah di-include
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../index.php?pesan=belum_login");
    exit();
}

// Ambil laundry_id dari session
$laundry_id = $_SESSION['laundry_id'];

// Ambil nama laundry dari database
$query = "SELECT nama_laundry FROM laundry WHERE laundry_id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $laundry_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Jika ditemukan, gunakan nama laundry dari database
$nama_laundry = $row ? $row['nama_laundry'] : "User";

// Deteksi halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);
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
                    <li class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
                        <a href="index.php"><i class="glyphicon glyphicon-home"></i> Dashboard</a>
                    </li>
                    <li class="<?= ($current_page == 'profil.php') ? 'active' : '' ?>">
                        <a href="profil.php"><i class="glyphicon glyphicon-user"></i> Profil</a>
                    </li>
                    <li class="<?= ($current_page == 'transaksi.php') ? 'active' : '' ?>">
                        <a href="transaksi.php"><i class="glyphicon glyphicon-shopping-cart"></i> Transaksi</a>
                    </li>
                    <li class="dropdown <?= ($current_page == 'paket.php' || $current_page == 'ganti_password.php') ? 'active' : '' ?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="glyphicon glyphicon-wrench"></i> Pengaturan <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="<?= ($current_page == 'paket.php') ? 'active' : '' ?>">
                                <a href="paket.php"><i class="glyphicon glyphicon-usd"></i> Harga</a>
                            </li>
                            <li class="<?= ($current_page == 'ganti_password.php') ? 'active' : '' ?>">
                                <a href="ganti_password.php"><i class="glyphicon glyphicon-lock"></i> Ganti Password</a>
                            </li>
                        </ul>
                    </li>
                    <li><a href="logout.php"><i class="glyphicon glyphicon-log-out"></i> Log Out</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <p class="navbar-text">Halo, <b><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'User'; ?></b>!</p>
                    </li>
                </ul>
            </div>
        </div>
    </nav>