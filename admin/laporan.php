<?php include 'header.php'; ?>

<div class="container">
    <div class="panel">
        <div class="panel-heading">
            <h4>Filter Laporan</h4>
        </div>
        <div class="panel-body">
            <!-- Form Filter Laporan -->
            <form action="laporan.php" method="get">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Dari Tanggal</th>
                        <th>Sampai Tanggal</th>
                        <th width="1%"></th>
                    </tr>
                    <tr>
                        <td>
                            <input type="date" name="tgl_dari" class="form-control" required>
                        </td>
                        <td>
                            <input type="date" name="tgl_sampai" class="form-control" required>
                        </td>
                        <td>
                            <input type="submit" class="btn btn-primary" value="Filter">
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    </div>

    <br />

    <?php
    if (isset($_GET['tgl_dari']) && isset($_GET['tgl_sampai'])) {
        $dari = $_GET['tgl_dari'];
        $sampai = $_GET['tgl_sampai'];

        // Ambil laundry_id dari session admin yang login
        if (isset($_SESSION['laundry_id'])) {
            $laundry_id = $_SESSION['laundry_id'];

            // Query untuk mengambil data transaksi berdasarkan tanggal filter dan laundry_id
            $data = mysqli_query($koneksi, "
                SELECT t.*, u.nama 
                FROM transaksi t 
                JOIN user u ON t.transaksi_pelanggan = u.id 
                WHERE t.laundry_id = $laundry_id 
                AND (DATE(t.transaksi_tgl) BETWEEN '$dari' AND '$sampai') 
                ORDER BY t.transaksi_id DESC
            ");
    ?>
            <div class="panel">
                <div class="panel-heading">
                    <h4>Data Laporan Laundry dari <b><?php echo htmlspecialchars($dari); ?></b> sampai <b><?php echo htmlspecialchars($sampai); ?></b></h4>
                </div>
                <div class="panel-body">
                    <!-- Tombol Cetak Laporan -->
                    <a target="_blank" href="cetak_print.php?dari=<?php echo $dari; ?>&sampai=<?php echo $sampai; ?>" class="btn btn-sm btn-primary">
                        <i class="glyphicon glyphicon-print"></i> CETAK
                    </a>
                    <br /><br />

                    <!-- Tabel Laporan -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="1%">No</th>
                                    <th>Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Berat (Kg) / Pcs</th>
                                    <th>Tgl. Selesai</th>
                                    <th>Harga</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($d = mysqli_fetch_array($data)) {
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td>INVOICE-<?php echo htmlspecialchars($d['transaksi_id']); ?></td>
                                        <td><?php echo htmlspecialchars($d['transaksi_tgl']); ?></td>
                                        <td><?php echo htmlspecialchars($d['nama']); ?></td>
                                        <td>
                                            <?php
                                            // Menampilkan berat atau jumlah pcs untuk Cuci Satuan dan Cuci Komplit
                                            $id_transaksi = $d['transaksi_id'];
                                            $detail = mysqli_query($koneksi, "SELECT * FROM transaksi_detail WHERE transaksi_id = $id_transaksi");

                                            $pcs = 0; // Menyimpan jumlah pcs untuk Cuci Satuan
                                            $berat_total = $d['transaksi_berat']; // Menyimpan total berat untuk layanan lain

                                            while ($d_detail = mysqli_fetch_assoc($detail)) {
                                                if ($d_detail['jenis_paket'] == 'cs') { // Jika jenis paket adalah Cuci Satuan
                                                    $pcs += $d_detail['jumlah']; // Menambahkan jumlah pcs
                                                }
                                            }

                                            // Jika ada layanan Cuci Satuan dan layanan lainnya, tampilkan pcs dan berat
                                            if ($pcs > 0 && $berat_total > 0) {
                                                echo $berat_total . ' Kg, ' . $pcs . ' pcs'; // Menampilkan pcs dan berat
                                            } elseif ($pcs > 0) {
                                                echo $pcs . ' pcs'; // Menampilkan hanya pcs untuk Cuci Satuan
                                            } else {
                                                echo $berat_total > 0 ? $berat_total . ' Kg' : "<span class='text-danger'>Menunggu admin</span>"; // Jika bukan Cuci Satuan, tampilkan berat
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($d['transaksi_tgl_selesai']); ?></td>
                                        <td>
                                            <?php echo "Rp. " . number_format($d['transaksi_total_harga'], 0, ',', '.'); ?>
                                        </td>
                                        <td>
                                            <?php
                                            // Menampilkan status transaksi dengan warna sesuai
                                            $status_label = [
                                                'menunggu' => 'danger',
                                                'proses' => 'warning',
                                                'selesai' => 'success',
                                                'diantar' => 'primary'
                                            ];

                                            if (isset($status_label[$d['transaksi_status']])) {
                                                $label = $status_label[$d['transaksi_status']];
                                                echo "<div class='label label-{$label}'>" . strtoupper($d['transaksi_status']) . "</div>";
                                            } else {
                                                echo "<div class='label label-danger'>Menunggu</div>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    <?php
        }
    }
    ?>
</div>

<?php include 'footer.php'; ?>