<?php
include 'header.php';
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$laundry_id = $_SESSION['laundry_id'];
$id_dc = isset($_GET['id_dc']) ? intval($_GET['id_dc']) : 0;


// Proses hapus
$hapus = mysqli_query($koneksi, "DELETE FROM tb_dry_clean WHERE id_dc = '$id_dc' AND laundry_id = '$laundry_id'");
?>

<?php if ($hapus) : ?>
    <div class="alert alert-success text-center" style="margin-top: 20px;">
        Paket berhasil dihapus! <a href="pkt_dc.php" class="alert-link">Lihat Daftar</a>
    </div>
<?php else : ?>
    <div class="alert alert-danger text-center" style="margin-top: 20px;">
        Gagal menghapus paket. <a href="pkt_dc.php" class="alert-link">Kembali</a>
    </div>
<?php endif; ?>


<?php include 'footer.php'; ?>
