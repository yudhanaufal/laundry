<?php
session_start();
include '../koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

// Pastikan ada laundry_id dalam sesi
if (!isset($_SESSION['laundry_id'])) {
    echo "Error: Laundry ID tidak ditemukan.";
    exit();
}

$laundry_id = $_SESSION['laundry_id'];
$username = trim($_POST['username']);
$password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
$nama = trim($_POST['nama']);
$no_hp = trim($_POST['no_hp']);
$alamat = trim($_POST['alamat']);
$level = 'pelanggan'; // Set level pelanggan secara default

// Validasi input tidak boleh kosong
if (empty($username) || empty($_POST['password']) || empty($nama) || empty($no_hp) || empty($alamat)) {
    echo "Error: Semua kolom harus diisi!";
    exit();
}

// Cek apakah username sudah digunakan
$cek_username = $koneksi->prepare("SELECT id FROM user WHERE username = ?");
$cek_username->bind_param("s", $username);
$cek_username->execute();
$cek_username->store_result();

if ($cek_username->num_rows > 0) {
    echo "Error: Username sudah terdaftar!";
    exit();
}

// Gunakan prepared statement untuk mencegah SQL Injection
$query = $koneksi->prepare("INSERT INTO user (username, password, nama, no_hp, alamat, laundry_id, level) VALUES (?, ?, ?, ?, ?, ?, ?)");
$query->bind_param("sssssis", $username, $password, $nama, $no_hp, $alamat, $laundry_id, $level);

if ($query->execute()) {
    header("location: pelanggan.php?pesan=sukses");
} else {
    echo "Error: " . $query->error;
}
?>
