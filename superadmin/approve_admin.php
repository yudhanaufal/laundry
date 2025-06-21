<?php
session_start();

// Pastikan koneksi ke database disertakan
include '../koneksi.php';

// Pastikan hanya superadmin yang bisa mengakses halaman ini
if ($_SESSION['level'] != 'superadmin') {
    echo "<script>alert('Anda tidak memiliki akses.'); window.location.href='index.php';</script>";
    exit();
}

// Ambil ID admin yang akan disetujui atau ditolak
$id = isset($_GET['id']) ? $_GET['id'] : 0;
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Validasi status
if ($status != 'approved' && $status != 'rejected') {
    die("Status tidak valid.");
}

// Pastikan koneksi berhasil
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Mulai transaksi untuk memastikan konsistensi data
mysqli_begin_transaction($koneksi);

try {
    $query = "UPDATE admin_pending SET status = ? WHERE admin_id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "si", $status, $id);
    mysqli_stmt_execute($stmt);

    if ($status == 'approved') {
        $query_admin = "SELECT *
                        FROM admin_pending 
                        WHERE admin_id = ?";
        $stmt_admin = mysqli_prepare($koneksi, $query_admin);
        mysqli_stmt_bind_param($stmt_admin, "i", $id);
        mysqli_stmt_execute($stmt_admin);
        $result_admin = mysqli_stmt_get_result($stmt_admin);

        if (mysqli_num_rows($result_admin) > 0) {
            $admin_data = mysqli_fetch_assoc($result_admin);

            $insert_laundry = "INSERT INTO laundry (nama_laundry, alamat) 
                            VALUES (?, ?)";
            $stmt_insert_laundry = mysqli_prepare($koneksi, $insert_laundry);
            mysqli_stmt_bind_param(
                $stmt_insert_laundry,
                "ss",
                $admin_data['nama_laundry'],
                $admin_data['alamat_laundry'],
            );
            mysqli_stmt_execute($stmt_insert_laundry);

            $new_laundry_id = mysqli_insert_id($koneksi);
            mysqli_stmt_close($stmt_insert_laundry);

            $insert_user = "INSERT INTO user (username, password, nama, no_hp, alamat, level, laundry_id) 
                            VALUES (?, ?, ?, ?, ?, 'admin', ?)";
            $stmt_insert_user = mysqli_prepare($koneksi, $insert_user);
            mysqli_stmt_bind_param(
                $stmt_insert_user,
                "sssssi",
                $admin_data['username'],
                $admin_data['password'],
                $admin_data['nama'],
                $admin_data['no_hp'],
                $admin_data['alamat'],
                $new_laundry_id,
            );
            mysqli_stmt_execute($stmt_insert_user);
            mysqli_stmt_close($stmt_insert_user);
        } else {
            throw new Exception('Admin yang dimaksud tidak ditemukan.');
        }
    }

    mysqli_commit($koneksi);

    header("Location: superadmin_approve.php");
    exit();
} catch (Exception $e) {
    mysqli_rollback($koneksi);
    echo "Terjadi kesalahan: " . $e->getMessage();
}

mysqli_close($koneksi);
