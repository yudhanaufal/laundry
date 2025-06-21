<?php 
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan sanitasi input
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $nama = trim($_POST["nama"]);
    $no_hp = trim($_POST["no_hp"]);
    $alamat = trim($_POST["alamat"]);
    $level = isset($_POST["level"]) ? $_POST["level"] : 'pengguna';
    
    // Pastikan password tidak kosong
    if (empty($password)) {
        die("Password tidak boleh kosong!");
    }

    // Enkripsi password sebelum disimpan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Laundry ID hanya diperlukan untuk admin dan pengguna
    $laundry_id = NULL;  // Defaultkan laundry_id ke NULL untuk superadmin
    if ($level !== 'superadmin') {
        // Cek jika laundry_id ada dalam input dan valid
        if (empty($_POST["laundry_id"])) {
            die("Laundry ID wajib dipilih untuk Admin atau Pengguna.");
        }

        $laundry_id = intval($_POST["laundry_id"]);

        // Cek apakah laundry_id ada di database
        $cek_laundry = mysqli_prepare($koneksi, "SELECT laundry_id FROM laundry WHERE laundry_id = ?");
        mysqli_stmt_bind_param($cek_laundry, "i", $laundry_id);
        mysqli_stmt_execute($cek_laundry);
        mysqli_stmt_store_result($cek_laundry);

        if (mysqli_stmt_num_rows($cek_laundry) == 0) {
            die("Laundry ID tidak valid.");
        }
        mysqli_stmt_close($cek_laundry);
    }

    // Cek apakah username sudah ada di database
    $cek_user = mysqli_prepare($koneksi, "SELECT id FROM user WHERE username = ?");
    mysqli_stmt_bind_param($cek_user, "s", $username);
    mysqli_stmt_execute($cek_user);
    mysqli_stmt_store_result($cek_user);

    if (mysqli_stmt_num_rows($cek_user) > 0) {
        mysqli_stmt_close($cek_user);
        die("Username sudah digunakan.");
    }
    mysqli_stmt_close($cek_user);

    // Insert data ke dalam database menggunakan prepared statement
    $sql = "INSERT INTO user (username, password, nama, no_hp, alamat, level, laundry_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssi", $username, $hashed_password, $nama, $no_hp, $alamat, $level, $laundry_id);

    if (mysqli_stmt_execute($stmt)) {
        // Jika registrasi berhasil, redirect ke login.php
        header("Location: index.php");
        exit();
    } else {
        die("Gagal insert ke database: " . mysqli_error($koneksi));
    }

    // Tutup koneksi
    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
}
?>
