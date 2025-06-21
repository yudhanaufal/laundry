<!DOCTYPE html>
<html>
<head>
    <title>SISTEM INFORMASI UMKM LAUNDRY</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <script type="text/javascript" src="../assets/js/jquery.js"></script>
    <script type="text/javascript" src="../assets/js/bootstrap.js"></script>
</head>
<body>
    <?php
    session_start();
    include '../koneksi.php';

    if (!isset($_SESSION['laundry_id'])) {
        echo "<script>alert('Anda tidak memiliki akses. Silakan login.'); window.location.href='../login.php';</script>";
        exit();
    }

    $laundry_id = $_SESSION['laundry_id'];
    ?>

    <div class="container">
        <center><h2>LAUNDRY</h2></center>
        <br/><br/>

        <?php
        if (isset($_GET['dari']) && isset($_GET['sampai'])) {
            $dari = $_GET['dari'];
            $sampai = $_GET['sampai'];
            ?>

            <h4>Data Laporan Laundry dari <b><?php echo $dari; ?></b> sampai <b><?php echo $sampai; ?></b></h4>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Berat / Pcs</th>
                        <th>Total Harga</th>
                        <th>Selesai</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT t.*, u.nama FROM transaksi t JOIN user u ON t.transaksi_pelanggan = u.id WHERE t.laundry_id = ? AND DATE(t.transaksi_tgl) >= ? AND DATE(t.transaksi_tgl) <= ? ORDER BY t.transaksi_id DESC";
                    $stmt = mysqli_prepare($koneksi, $query);
                    mysqli_stmt_bind_param($stmt, "iss", $laundry_id, $dari, $sampai);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $id_transaksi = $row['transaksi_id'];
                        $pcs = 0;
                        $berat_total = 0;
                        $jenis_paket = [];
                        $total_price = 0;

                        $detail = mysqli_query($koneksi, "SELECT * FROM transaksi_detail WHERE transaksi_id = $id_transaksi");
                        while ($d = mysqli_fetch_assoc($detail)) {
                            $jenis_paket[] = $d['jenis_paket'];
                            if ($d['jenis_paket'] == 'cs') {
                                $pcs += $d['jumlah'];
                                $q = mysqli_query($koneksi, "SELECT tarif_cs FROM tb_cuci_satuan WHERE id_cs = {$d['id_paket']}");
                                $p = mysqli_fetch_assoc($q);
                                $total_price += $p['tarif_cs'] * $d['jumlah'];
                            } elseif ($d['jenis_paket'] == 'ck') {
                                $berat_total += $row['transaksi_berat'];
                                $q = mysqli_query($koneksi, "SELECT tarif_ck FROM tb_cuci_komplit WHERE id_ck = {$d['id_paket']}");
                                $p = mysqli_fetch_assoc($q);
                                $total_price += $p['tarif_ck'] * $d['jumlah'];
                            } elseif ($d['jenis_paket'] == 'dc') {
                                $berat_total += $row['transaksi_berat'];
                                $q = mysqli_query($koneksi, "SELECT tarif_dc FROM tb_dry_clean WHERE id_dc = {$d['id_paket']}");
                                $p = mysqli_fetch_assoc($q);
                                $total_price += $p['tarif_dc'] * $d['jumlah'];
                            }
                        }
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>INVOICE-<?= htmlspecialchars($row['transaksi_id']) ?></td>
                        <td><?= htmlspecialchars($row['transaksi_tgl']) ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td>
                            <?php
                            $unique_jenis = array_unique($jenis_paket);
                            if (count($unique_jenis) > 1) {
                                echo $berat_total . ' Kg, ' . $pcs . ' pcs';
                            } elseif (in_array('cs', $unique_jenis)) {
                                echo $pcs . ' pcs';
                            } else {
                                echo $berat_total . ' Kg';
                            }
                            ?>
                        </td>
                        <td>Rp. <?= number_format($total_price, 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($row['transaksi_tgl_selesai']) ?></td>
                        <td><?= strtoupper($row['transaksi_status']) ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } ?>
    </div>
    <script type="text/javascript">
        window.print();
    </script>
</body>
</html>
