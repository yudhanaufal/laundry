<!DOCTYPE html>
<html>

<head>
    <title>UMKM LAUNDRY</title>
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
    <script type="text/javascript" src="../assets/js/jquery.js"></script>
    <script type="text/javascript" src="../assets/js/bootstrap.js"></script>
</head>

<body>
    <!-- Cek apakah sudah login -->
    <?php
    session_start();
    if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
        header("location:../index.php?pesan=belum_login");
    }
    ?>

    <?php
    include '../koneksi.php';
    ?>
    <div class="container">
        <div class="col-md-10 col-md-offset-1">
            <?php
            $id = $_GET['id'];
            $transaksi = mysqli_query($koneksi, "SELECT * FROM transaksi, user WHERE transaksi_id='$id' AND transaksi_pelanggan=id");
            while ($t = mysqli_fetch_array($transaksi)) {
            ?>
                <center>
                    <h2>LAUNDRY</h2>
                </center>
                <h3>INVOICE-<?php echo $t['transaksi_id']; ?></h3>
                <br />
                <table class="table">
                    <tr>
                        <th width="20%">Tgl. Laundry</th>
                        <th>:</th>
                        <td><?php echo $t['transaksi_tgl']; ?></td>
                    </tr>
                    <tr>
                        <th>Nama Pelanggan</th>
                        <th>:</th>
                        <td><?php echo $t['nama']; ?></td>
                    </tr>
                    <tr>
                        <th>HP</th>
                        <th>:</th>
                        <td><?php echo $t['no_hp']; ?></td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <th>:</th>
                        <td><?php echo $t['alamat']; ?></td>
                    </tr>
                    <tr>
                        <th>Berat Cucian (Kg)</th>
                        <th>:</th>
                        <td><?php echo $t['transaksi_berat']; ?></td>
                    </tr>
                    <tr>
                        <th>Tgl. Selesai</th>
                        <th>:</th>
                        <td><?php echo $t['transaksi_tgl_selesai']; ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <th>:</th>
                        <td>
                            <?php
                            // Cek apakah admin atau superadmin
                            if ($_SESSION['level'] == 'admin' || $_SESSION['level'] == 'superadmin') {
                            ?>
                                <select class="form-control status-dropdown badge-select" data-id="<?= $t['transaksi_id'] ?>">
                                    <option value="menunggu" <?= $t['transaksi_status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                    <option value="lunas" <?= $t['transaksi_status'] == 'lunas' ? 'selected' : '' ?>>Lunas</option>
                                    <option value="proses" <?= $t['transaksi_status'] == 'proses' ? 'selected' : '' ?>>Proses</option>
                                    <option value="selesai" <?= $t['transaksi_status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="diantar" <?= $t['transaksi_status'] == 'diantar' ? 'selected' : '' ?>>Diantar</option>
                                </select>
                            <?php
                            } else {
                                // Jika bukan admin, tampilkan status sebagai label
                                switch ($t['transaksi_status']) {
                                    case 'menunggu':
                                        echo "<div class='label label-default'>MENUNGGU</div>";
                                        break;
                                    case 'lunas':
                                        echo "<div class='label label-primary'>LUNAS</div>";
                                        break;
                                    case 'proses':
                                        echo "<div class='label label-warning'>PROSES</div>";
                                        break;
                                    case 'selesai':
                                        echo "<div class='label label-success'>SELESAI</div>";
                                        break;
                                    case 'diantar':
                                        echo "<div class='label label-info'>DIANTAR</div>";
                                        break;
                                    default:
                                        echo "<div class='label label-default'>TIDAK DIKETAHUI</div>";
                                        break;
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Harga</th>
                        <td>:</td>
                        <td>Rp. <?= number_format($t['transaksi_total_harga'], 0, ',', '.') ?> ,-</td>
                    </tr>
                </table>
                <br />
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
                </table>
                <br />
                <p>
                    <center><i>" SALAM BERSIH, SALAM WANGI ".</i></center>
                </p>
            <?php
            }
            ?>
        </div>
    </div>
    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>