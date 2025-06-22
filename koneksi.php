<?php
// koneksi.php
$servername = getenv('DB_HOST') ?: 'db'; // Mengambil dari variabel lingkungan DB_HOST, default 'db'
$username = getenv('DB_USER') ?: 'user_laundry'; // Mengambil dari variabel lingkungan DB_USER
$password = getenv('DB_PASS') ?: 'password_user_laundry_yang_kuat'; // Mengambil dari variabel lingkungan DB_PASS
$dbname = getenv('DB_NAME') ?: 'db_laundry'; // Mengambil dari variabel lingkungan DB_NAME

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
// echo "Koneksi berhasil"; // Komentar atau hapus baris ini di produksi
?>