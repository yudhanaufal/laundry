<?php
include '../koneksi.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Perhatikan bahwa ini hanya contoh, dan pastikan Anda menggunakan prepared statements untuk keamanan.
    mysqli_query($koneksi, "DELETE FROM user WHERE id = '$id'");
    
    header("location:pelanggan.php");
} else {
    echo "ID tidak ditemukan";
}
?>
