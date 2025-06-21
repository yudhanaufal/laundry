<?php
include 'header.php';
include '../koneksi.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$laundry_id = $_SESSION['laundry_id'];
$level = $_SESSION['level']; // Ambil level user

// Ambil data pemilik berdasarkan laundry_id (hanya pemilik yang sesuai)
$data = mysqli_query($koneksi, "SELECT * FROM pemilik WHERE laundry_id = '$laundry_id'");

?>
<style>
    .container-paket {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 30px;
        margin: 40px auto;
        max-width: 1000px;
    }

    .col-paket {
        background-color: #f7f7f7;
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        width: 260px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .col-paket:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .col-paket img {
        width: 120px;
        margin-bottom: 20px;
    }

    .col-paket h4 {
        font-size: 16px;
        color:rgb(12, 12, 12);
        font-weight: 600;
        margin: 0;
        text-decoration: none;
    }

    .paket {
        text-decoration: none;
        color: inherit;
    }
</style>

<div class="container">
    <div class="panel">
        <div class="panel-heading">
            <h4>Daftar Paket</h4>
        </div>
        <div class="panel-body">
            <div class="container-paket">
                <div class="col-paket">
                    <a href="pkt_ck.php" class="paket">
                        <img src="../assets/img/cuci_komplit.png" alt="cuci komplit">
                        <h4>Daftar Paket Cuci Komplit</h4>
                    </a>
                </div>
                <div class="col-paket">
                    <a href="pkt_dc.php" class="paket">
                        <img src="../assets/img/dry_clean.png" alt="dry clean">
                        <h4>Daftar Paket Dry Clean</h4>
                    </a>
                </div>
                <div class="col-paket">
                    <a href="pkt_cs.php" class="paket">
                        <img src="../assets/img/kemeja_2.png" alt="cuci satuan">
                        <h4>Daftar Paket Cuci Satuan</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>