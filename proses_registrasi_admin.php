<?php
session_start();
include 'koneksi.php'; // Pastikan koneksi ke database sudah benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $nama = trim($_POST["nama"]);
    $no_hp = trim($_POST["no_hp"]);
    $alamat = trim($_POST["alamat"]);
    $nama_laundry = trim($_POST["nama_laundry"]);
    $alamat_laundry = trim($_POST["alamat_laundry"]);

    // Pastikan password tidak kosong
    if (empty($password)) {
        die("Password tidak boleh kosong!");
    }

    // Enkripsi password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Proses upload bukti laundry (opsional)
    $bukti_laundry = $_FILES["bukti_laundry"]["name"];
    if ($bukti_laundry) {
        $target_dir = "uploads/"; // Direktori untuk menyimpan file yang di-upload
        $safe_file_name = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $bukti_laundry);
        $target_file = $target_dir . $safe_file_name;

        // Cek apakah file berhasil di-upload
        if (!move_uploaded_file($_FILES["bukti_laundry"]["tmp_name"], $target_file)) {
            die("Gagal meng-upload bukti laundry.");
        }
    }

    // Insert data ke tabel admin_pending
    $sql = "INSERT INTO admin_pending (username, password, nama, no_hp, alamat, nama_laundry, alamat_laundry, bukti_laundry, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    
    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssss", $username, $hashed_password, $nama, $no_hp, $alamat, $nama_laundry, $alamat_laundry, $bukti_laundry);

    // Eksekusi query untuk insert data
    if (mysqli_stmt_execute($stmt)) {
        // Jika berhasil, beri pesan dan redirect
        echo "Registrasi admin berhasil, menunggu persetujuan superadmin.";
        header("Location: index.php"); // Ganti dengan halaman yang sesuai setelah registrasi
        exit();
    } else {
        // Jika gagal, tampilkan error
        die("Gagal insert ke database: " . mysqli_error($koneksi));
    }

    // Tutup statement dan koneksi
    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
}
?>
