<?php
include '../koneksi.php';
$id = $_POST['id'];
$nama = $_POST['nama'];
$no_hp = $_POST['no_hp'];
$alamat = $_POST['alamat'];
mysqli_query($koneksi,"update user set nama='$nama', no_hp='$no_hp', alamat='$alamat' where id='$id'");
header("location:pelanggan.php");
?>
