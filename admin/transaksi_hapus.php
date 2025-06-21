<?php
session_start();
if (!isset($_SESSION['username']) || ($_SESSION['level'] != 'admin' && $_SESSION['level'] != 'superadmin')) {
    header("Location: login.php");
    exit();
}
include '../koneksi.php';

$id = $_GET['id'];
$laundry_id = $_SESSION['laundry_id'];

// Hapus transaksi hanya jika sesuai dengan laundry_id
mysqli_query($koneksi, "DELETE FROM transaksi WHERE transaksi_id = '$id' AND laundry_id = '$laundry_id'");

header("Location: transaksi.php");

?>
