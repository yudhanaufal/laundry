<?php
include '../koneksi.php';
session_start();

if (!isset($_SESSION['level']) || !in_array($_SESSION['level'], ['admin', 'superadmin'])) {
    echo "unauthorized";
    exit;
}

$id = intval($_POST['id'] ?? 0);
$status_pembayaran = $_POST['status_pembayaran'] ?? '';

if ($id <= 0 || !in_array($status_pembayaran, ['belum_bayar', 'lunas'])) {
    echo "invalid_input";
    exit;
}

$query = "UPDATE transaksi SET status_pembayaran = ? WHERE transaksi_id = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "si", $status_pembayaran, $id);

if (mysqli_stmt_execute($stmt)) {
    echo "success";
} else {
    echo "failed";
}
