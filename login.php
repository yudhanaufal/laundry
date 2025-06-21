<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        header("location: index.php?pesan=gagal");
        exit();
    }

    $username = trim(mysqli_real_escape_string($koneksi, $_POST['username']));
    $password = $_POST['password'];

    // Periksa apakah database terhubung
    if (!$koneksi) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    // Ambil data user berdasarkan username
    $query = "SELECT * FROM user WHERE username = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    // Cek apakah username ditemukan
    if ($row) {
        // Verifikasi password yang sudah di-hash
        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true); // Hindari session hijacking

            $_SESSION['id'] = $row['id'];
            $_SESSION['laundry_id'] = $row['laundry_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['status'] = "login";
            $_SESSION['level'] = $row['level'];

            // Redirect berdasarkan level pengguna
            switch ($row['level']) {
                case "superadmin":
                    header("location: superadmin/index.php");
                    break;
                case "admin":
                    header("location: admin/index.php");
                    break;
                case "pengguna":
                    header("location: user/index.php");
                    break;
                default:
                    session_destroy();
                    header("location: index.php?pesan=gagal");
            }
            exit();
        } else {
            header("location: index.php?pesan=gagal");
            exit();
        }
    } else {
        header("location: index.php?pesan=gagal");
        exit();
    }
}
