<!DOCTYPE html>
<html>

<head>
    <title>UMKM LAUNDRY</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <script type="text/javascript" src="../assets/js/jquery.js"></script>
    <script type="text/javascript" src="../assets/js/bootstrap.js"></script>
</head>

<body>
    <?php
    session_start();
    if ($_SESSION['status'] != "login") {
        header("location:../index.php?pesan=belum_login");
        exit();
    }

    include '../koneksi.php';
    $id = $_GET['id'];
    $transaksi = mysqli_query($koneksi, "SELECT t.*, u.nama, u.no_hp, u.alamat, l.no_rekening 
    FROM transaksi t 
    JOIN user u ON t.transaksi_pelanggan = u.id 
    LEFT JOIN laundry l ON t.laundry_id = l.laundry_id 
    WHERE t.transaksi_id = '$id'") or die(mysqli_error($koneksi));
    $t = mysqli_fetch_assoc($transaksi);
    ?>
    <div class="container">
        <div class="col-md-10 col-md-offset-1">
            <center>
                <h2>LAUNDRY</h2>
            </center>
            <a href="transaksi_invoice_cetak.php?id=<?= $id ?>" target="_blank" class="btn btn-primary pull-right">
                <i class="glyphicon glyphicon-print"></i> CETAK
            </a>
            <br><br>
            <table class="table">
                <tr>
                    <th>No. Invoice</th>
                    <td>:</td>
                    <td>INVOICE-<?= $t['transaksi_id'] ?></td>
                </tr>
                <tr>
                    <th>Tgl. Laundry</th>
                    <td>:</td>
                    <td><?= $t['transaksi_tgl'] ?></td>
                </tr>
                <tr>
                    <th>Nama Pelanggan</th>
                    <td>:</td>
                    <td><?= $t['nama'] ?></td>
                </tr>
                <tr>
                    <th>HP</th>
                    <td>:</td>
                    <td><?= $t['no_hp'] ?></td>
                </tr>
                <tr>
                    <th>Alamat</th>
                    <td>:</td>
                    <td><?= $t['alamat'] ?></td>
                </tr>
                <tr>
                    <th>Jumlah Cucian</th>
                    <td>:</td>
                    <td>
                        <?php
                        if ($t['transaksi_berat'] > 0) {
                            echo $t['transaksi_berat'] . " Kg";
                        } else {
                            // Hitung total pcs jika tidak pakai berat
                            $total_pcs = 0;
                            $q_pcs = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total_pcs FROM transaksi_detail WHERE transaksi_id = $id");
                            if ($q_pcs) {
                                $result_pcs = mysqli_fetch_assoc($q_pcs);
                                $total_pcs = $result_pcs['total_pcs'] ?? 0;
                            }
                            echo $total_pcs . " pcs";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Tgl. Selesai</th>
                    <td>:</td>
                    <td><?= $t['transaksi_tgl_selesai'] ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>:</td>
                    <td><span class='label label-info'><?= strtoupper($t['transaksi_status']) ?></span></td>
                </tr>
                <tr>
                    <th>Total Harga</th>
                    <td>:</td>
                    <td>
                        <?php
                        $status_harga = true;
                        if ($t['transaksi_berat'] > 0 && $t['transaksi_total_harga'] > 0) {
                            echo 'Rp. ' . number_format($t['transaksi_total_harga'], 0, ',', '.');
                        } else {
                            $id_transaksi = $t['transaksi_id'];
                            $total_price = 0;
                            $jenis_paket = [];

                            $detail = mysqli_query($koneksi, "SELECT * FROM transaksi_detail WHERE transaksi_id = $id_transaksi");
                            while ($d = mysqli_fetch_assoc($detail)) {
                                $jenis_paket[] = $d['jenis_paket'];

                                if ($d['jenis_paket'] == 'cs') {
                                    $q_price = mysqli_query($koneksi, "SELECT tarif_cs FROM tb_cuci_satuan WHERE id_cs = {$d['id_paket']}");
                                    $rowPrice = mysqli_fetch_assoc($q_price);
                                    $total_price += $rowPrice['tarif_cs'] * $d['jumlah'];
                                }

                                if ($d['jenis_paket'] == 'ck') {
                                    $q_price = mysqli_query($koneksi, "SELECT tarif_ck FROM tb_cuci_komplit WHERE id_ck = {$d['id_paket']}");
                                    $rowPrice = mysqli_fetch_assoc($q_price);
                                    $total_price += $rowPrice['tarif_ck'] * $d['jumlah'];
                                }

                                if ($d['jenis_paket'] == 'dc') {
                                    $q_price = mysqli_query($koneksi, "SELECT tarif_dc FROM tb_dry_clean WHERE id_dc = {$d['id_paket']}");
                                    $rowPrice = mysqli_fetch_assoc($q_price);
                                    $total_price += $rowPrice['tarif_dc'] * $d['jumlah'];
                                }
                            }

                            if (count(array_unique($jenis_paket)) == 1 && in_array('cs', $jenis_paket)) {
                                echo $total_price > 0 ? 'Rp. ' . number_format($total_price, 0, ',', '.') : "<span class='text-danger'>Menunggu admin</span>";
                            } else {
                                $status_harga = false;
                                echo "<span class='text-danger'>Menunggu admin</span>";
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Status Pembayaran</th>
                    <td>:</td>
                    <td>
                        <?php
                        if ($t['status_pembayaran'] == 'lunas') {
                            echo "<span class='label label-success'>LUNAS</span>";
                        } else {
                            echo "<span class='label label-danger'>BELUM BAYAR</span>";
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <th>No. Rekening</th>
                    <td>:</td>
                    <td><?= htmlspecialchars($t['no_rekening'] ?? 'Belum tersedia') ?></td>
                </tr>
            </table>

            <h4 class="text-center">Detail Layanan</h4>
            <?php
            // Paket CK
            $q_ck = mysqli_query($koneksi, "SELECT ck.nama_paket_ck, td.jumlah FROM transaksi_detail td JOIN tb_cuci_komplit ck ON td.id_paket = ck.id_ck WHERE td.transaksi_id = $id AND td.jenis_paket = 'ck'");
            if (mysqli_num_rows($q_ck) > 0) {
                echo "<strong>Cuci Komplit:</strong><ul>";
                while ($ck = mysqli_fetch_assoc($q_ck)) {
                    echo "<li><strong>Paket:</strong> {$ck['nama_paket_ck']} - {$ck['jumlah']} pcs<ul>";
                    $q_pakaian = mysqli_query($koneksi, "SELECT pakaian_jenis, pakaian_jumlah FROM pakaian WHERE transaksi_id = $id AND jenis_paket = 'ck' AND id_paket = (SELECT id_ck FROM tb_cuci_komplit WHERE nama_paket_ck = '" . $ck['nama_paket_ck'] . "' LIMIT 1)");
                    while ($p = mysqli_fetch_assoc($q_pakaian)) {
                        echo "<li>{$p['pakaian_jenis']} - {$p['pakaian_jumlah']} pcs</li>";
                    }
                    echo "</ul></li>";
                }
                echo "</ul>";
            }

            // Paket DC
            $q_dc = mysqli_query($koneksi, "SELECT dc.nama_paket_dc, td.jumlah FROM transaksi_detail td JOIN tb_dry_clean dc ON td.id_paket = dc.id_dc WHERE td.transaksi_id = $id AND td.jenis_paket = 'dc'");
            if (mysqli_num_rows($q_dc) > 0) {
                echo "<strong>Dry Clean:</strong><ul>";
                while ($dc = mysqli_fetch_assoc($q_dc)) {
                    echo "<li><strong>Paket:</strong> {$dc['nama_paket_dc']} - {$dc['jumlah']} pcs<ul>";
                    $q_pakaian = mysqli_query($koneksi, "SELECT pakaian_jenis, pakaian_jumlah FROM pakaian WHERE transaksi_id = $id AND jenis_paket = 'dc' AND id_paket = (SELECT id_dc FROM tb_dry_clean WHERE nama_paket_dc = '" . $dc['nama_paket_dc'] . "' LIMIT 1)");
                    while ($p = mysqli_fetch_assoc($q_pakaian)) {
                        echo "<li>{$p['pakaian_jenis']} - {$p['pakaian_jumlah']} pcs</li>";
                    }
                    echo "</ul></li>";
                }
                echo "</ul>";
            }

            // Paket CS
            $q_cs = mysqli_query($koneksi, "SELECT cs.nama_cs, td.jumlah FROM transaksi_detail td JOIN tb_cuci_satuan cs ON td.id_paket = cs.id_cs WHERE td.transaksi_id = $id AND td.jenis_paket = 'cs'");
            if (mysqli_num_rows($q_cs) > 0) {
                echo "<strong>Cuci Satuan:</strong><ul>";
                while ($cs = mysqli_fetch_assoc($q_cs)) {
                    echo "<li>{$cs['nama_cs']} - {$cs['jumlah']} pcs</li>";
                }
                echo "</ul>";
            }
            ?>

            <br>
            <p>
                <center><i>"SALAM BERSIH, SALAM WANGI"</i></center>
            </p>
        </div>
    </div>
</body>

</html>