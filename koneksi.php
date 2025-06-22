<?php
$servername = getenv('DB_HOST') ?: 'db';
$username = getenv('DB_USER') ?: 'user_laundry';
$password = getenv('DB_PASS') ?: 'password_user_laundry_yang_kuat';
$dbname = getenv('DB_NAME') ?: 'db_laundry';

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
// echo "Koneksi berhasil"; // Hapus di produksi
?>