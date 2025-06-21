<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start();

    // Pastikan user sudah login
    if (!isset($_SESSION['id'])) {
        header("Location: login.php");
        exit();
    }

    $id = $_SESSION['id'];
    $password_baru = $_POST['password_baru'];

    // Gunakan password_hash() untuk menghasilkan hash baru dari password
    $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);

    // Gunakan prepared statement untuk mencegah SQL injection
    $stmt = $koneksi->prepare("UPDATE user SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: ganti_password.php?pesan=oke");
    exit();
}
?>
