<?php
session_start();
include '../koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['laundry_id']) || $_SESSION['level'] != 'admin') {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$laundry_id = $_SESSION['laundry_id'];
$pemilik_nama = trim($_POST['pemilik_nama']);
$pemilik_hp = trim($_POST['pemilik_hp']);

// Validasi input tidak boleh kosong
if (empty($pemilik_nama) || empty($pemilik_hp)) {
    echo "Error: Semua kolom harus diisi!";
    exit();
}

// Validasi nomor HP harus angka
if (!preg_match('/^[0-9]+$/', $pemilik_hp)) {
    echo "Error: Nomor HP harus berisi angka saja!";
    exit();
}

// Gunakan prepared statement untuk keamanan
$query = $koneksi->prepare("INSERT INTO pemilik (pemilik_nama, pemilik_hp, laundry_id) VALUES (?, ?, ?)");
$query->bind_param("ssi", $pemilik_nama, $pemilik_hp, $laundry_id);

if ($query->execute()) {
    header("location: pemilik.php?pesan=sukses");
    exit();
} else {
    echo "Error: " . $query->error;
}
?>
