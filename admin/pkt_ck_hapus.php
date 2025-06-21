<?php
include 'header.php';
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$laundry_id = $_SESSION['laundry_id'];
$id_ck = isset($_GET['id_ck']) ? intval($_GET['id_ck']) : 0;


// Proses hapus
$hapus = mysqli_query($koneksi, "DELETE FROM tb_cuci_komplit WHERE id_ck = '$id_ck' AND laundry_id = '$laundry_id'");
?>

<?php if ($hapus) : ?>
    <div class="alert alert-success text-center" style="margin-top: 20px;">
        Paket berhasil dihapus! <a href="pkt_ck.php" class="alert-link">Lihat Daftar</a>
    </div>
<?php else : ?>
    <div class="alert alert-danger text-center" style="margin-top: 20px;">
        Gagal menghapus paket. <a href="pkt_ck.php" class="alert-link">Kembali</a>
    </div>
<?php endif; ?>


<?php include 'footer.php'; ?>
