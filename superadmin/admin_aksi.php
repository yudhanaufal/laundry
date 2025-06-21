<?php
include '../koneksi.php';

// Pastikan hanya super_admin yang bisa melakukan aksi  
if (!isset($_SESSION['level']) || $_SESSION['level'] !== 'super_admin') {
    echo "<script>alert('Akses ditolak!'); window.location.href='admin.php';</script>";
    exit;
}

// Tambah Admin  
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['admin_nama']);
    $password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
    $hp = mysqli_real_escape_string($koneksi, $_POST['admin_hp']);
    $pemilik_id = !empty($_POST['pemilik_id']) ? mysqli_real_escape_string($koneksi, $_POST['pemilik_id']) : "NULL";

    $query = "INSERT INTO admin (admin_nama, admin_username, admin_password, admin_hp, pemilik_id)   
              VALUES ('$nama', '$username', '$password', '$hp', $pemilik_id)";

    if (mysqli_query($koneksi, $query)) {
        header("Location: admin.php?status=sukses");
    } else {
        echo "<script>alert('Gagal menambahkan admin!'); window.history.back();</script>";
    }
}

// Edit Admin  
if (isset($_POST['edit'])) {
    $id = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['admin_nama']);
    $hp = mysqli_real_escape_string($koneksi, $_POST['admin_hp']);
    $pemilik_id = !empty($_POST['pemilik_id']) ? mysqli_real_escape_string($koneksi, $_POST['pemilik_id']) : "NULL";

    if (!empty($_POST['admin_password'])) {
        $password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
        $query = "UPDATE admin SET admin_nama='$nama',   
                  admin_password='$password', admin_hp='$hp', pemilik_id=$pemilik_id WHERE id='$id'";
    } else {
        $query = "UPDATE admin SET admin_nama='$nama', admin_username='$username',  
                  admin_hp='$hp', pemilik_id=$pemilik_id WHERE id='$id'";
    }

    if (mysqli_query($koneksi, $query)) {
        header("Location: admin.php?status=updated");
    } else {
        echo "<script>alert('Gagal mengedit admin!'); window.history.back();</script>";
    }
}

// Hapus Admin  
if (isset($_GET['hapus']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $query = "DELETE FROM admin WHERE id='$id'";

    if (mysqli_query($koneksi, $query)) {
        header("Location: admin.php?status=deleted");
    } else {
        echo "<script>alert('Gagal menghapus admin!'); window.history.back();</script>";
    }
}
