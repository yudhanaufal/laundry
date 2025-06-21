<?php
session_start();
include '../koneksi.php';

// Pastikan user sudah login dan memiliki level admin/superadmin
if (!isset($_SESSION['id']) || ($_SESSION['level'] != 'admin' && $_SESSION['level'] != 'superadmin')) {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$laundry_id = $_SESSION['laundry_id']; // Ambil laundry_id dari session

// Update layanan yang sudah ada
if (isset($_POST['update']) && isset($_POST['harga_id'])) {
    $id = $_POST['harga_id']; // Array harga_id
    $jenis_harga = $_POST['jenis_harga']; // Array jenis layanan
    $harga = $_POST['harga']; // Array harga baru
    $satuan = $_POST['satuan']; // Array satuan

    $query = "UPDATE harga SET jenis_harga = ?, harga = ?, satuan = ? WHERE harga_id = ? AND laundry_id = ?";
    $stmt = mysqli_prepare($koneksi, $query);

    if ($stmt) {
        for ($i = 0; $i < count($id); $i++) {
            $jenis_baru = htmlspecialchars($jenis_harga[$i]);
            $harga_baru = (int) $harga[$i];
            $satuan_baru = htmlspecialchars($satuan[$i]);
            $id_harga = (int) $id[$i];

            mysqli_stmt_bind_param($stmt, "sisii", $jenis_baru, $harga_baru, $satuan_baru, $id_harga, $laundry_id);
            mysqli_stmt_execute($stmt);
        }

        mysqli_stmt_close($stmt);
        header("location:harga.php?pesan=berhasil");
        exit();
    } else {
        echo "Gagal memperbarui harga: " . mysqli_error($koneksi);
    }
}

// Tambah layanan baru
if (isset($_POST['tambah']) && !empty($_POST['jenis_harga_baru']) && !empty($_POST['harga_baru']) && !empty($_POST['satuan_baru'])) {
    $jenis_harga_baru = htmlspecialchars($_POST['jenis_harga_baru']);
    $harga_baru = (int) $_POST['harga_baru'];
    $satuan_baru = htmlspecialchars($_POST['satuan_baru']);

    $query_tambah = "INSERT INTO harga (jenis_harga, harga, satuan, laundry_id) VALUES (?, ?, ?, ?)";
    $stmt_tambah = mysqli_prepare($koneksi, $query_tambah);

    if ($stmt_tambah) {
        mysqli_stmt_bind_param($stmt_tambah, "sisi", $jenis_harga_baru, $harga_baru, $satuan_baru, $laundry_id);
        mysqli_stmt_execute($stmt_tambah);
        mysqli_stmt_close($stmt_tambah);

        header("location:harga.php?pesan=berhasil");
        exit();
    } else {
        echo "Gagal menambahkan harga: " . mysqli_error($koneksi);
    }
}

echo "Data tidak valid!";
?>
