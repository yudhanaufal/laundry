<?php
include '../koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

// Pastikan admin memiliki laundry_id
if (!isset($_SESSION['laundry_id'])) {
    echo "Error: Laundry ID tidak ditemukan.";
    exit();
}

$laundry_id = $_SESSION['laundry_id'];

// Ambil data dari form
$username = trim($_POST['username']);
$password = trim($_POST['password']);
$nama = trim($_POST['nama']);
$no_hp = trim($_POST['no_hp']);
$alamat = trim($_POST['alamat']);
$level = 'pengguna'; // Level default pelanggan

// Validasi input tidak boleh kosong
if (empty($username) || empty($password) || empty($nama) || empty($no_hp) || empty($alamat)) {
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
$cek_username->close();

// Hash password sebelum disimpan
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Gunakan prepared statement untuk mencegah SQL Injection
$query = $koneksi->prepare("INSERT INTO user (username, password, nama, no_hp, alamat, level, laundry_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
$query->bind_param("ssssssi", $username, $hashed_password, $nama, $no_hp, $alamat, $level, $laundry_id);

if ($query->execute()) {
    header("location: pelanggan.php?pesan=sukses");
} else {
    echo "Error: " . $query->error;
}

// Tutup statement
$query->close();
?>
