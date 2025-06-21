<?php
include '../koneksi.php';
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

// Ambil data dari form
$id = $_POST['id'];
$nama = trim($_POST['nama']);
$no_hp = trim($_POST['hp']);
$alamat = trim($_POST['alamat']);

// Validasi sederhana
if (empty($nama) || empty($no_hp) || empty($alamat)) {
    header("location: profil_edit.php?pesan=error_kosong");
    exit();
}

// Update data dengan prepared statement
$stmt = $koneksi->prepare("UPDATE user SET nama = ?, no_hp = ?, alamat = ? WHERE id = ?");
$stmt->bind_param("sssi", $nama, $no_hp, $alamat, $id);

if ($stmt->execute()) {
    header("location: profil.php?pesan=update_berhasil");
} else {
    header("location: profil_edit.php?pesan=update_gagal");
}
?>
