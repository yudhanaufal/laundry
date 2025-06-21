<?php
include '../koneksi.php';
$id = $_POST['id'];
$nama = $_POST['nama'];
$hp = $_POST['hp'];
mysqli_query($koneksi,"update pemilik set pemilik_nama='$nama', pemilik_hp='$hp' where id='$id'");
header("location:pemilik.php");
?>
