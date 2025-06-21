<?php
include 'header.php';
include '../koneksi.php';

if (!isset($_SESSION['laundry_id'])) {
    header("location: ../login.php");
    exit();
}

$laundry_id = $_SESSION['laundry_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_rek = mysqli_real_escape_string($koneksi, $_POST['no_rekening']);
    mysqli_query($koneksi, "UPDATE laundry SET no_rekening = '$no_rek' WHERE laundry_id = $laundry_id");
    echo "<script>alert('Nomor rekening diperbarui!'); window.location.href='pengaturan_rekening.php';</script>";
    exit();
}

$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT no_rekening FROM laundry WHERE laundry_id = $laundry_id"));
?>

<div class="container">
    <div class="panel">
        <div class="panel-heading"><h4>Pengaturan Nomor Rekening</h4></div>
        <div class="panel-body">
            <form method="POST">
                <div class="form-group">
                    <label>Nomor Rekening:</label>
                    <input type="text" name="no_rekening" class="form-control" value="<?= htmlspecialchars($data['no_rekening']) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
