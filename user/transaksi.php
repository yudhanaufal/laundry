<?php
include 'header.php';
include '../koneksi.php';

if (!isset($_SESSION['id'])) {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

$user_id = $_SESSION['id'];
?>

<div class="container">
    <div class="panel">
        <div class="panel-heading">
            <h4>Data Transaksi Laundry</h4>
        </div>
        <div class="panel-body">
            <a href="transaksi_tambah.php" class="btn btn-sm btn-info pull-right">+ Transaksi Baru</a>
            <br><br>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th>Tempat Laundry</th>
                            <th>Berat (Kg) / Pcs</th>
                            <th>Total Harga</th>
                            <th>Tgl. Selesai</th>
                            <th>Status</th>
                            <th>Status Pembayaran</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT t.*, u.nama, l.nama_laundry 
          FROM transaksi t
          JOIN user u ON t.transaksi_pelanggan = u.id
          JOIN laundry l ON t.laundry_id = l.laundry_id
          WHERE t.transaksi_pelanggan = ?
          ORDER BY t.transaksi_id DESC";

                        $stmt = mysqli_prepare($koneksi, $query);
                        mysqli_stmt_bind_param($stmt, "i", $user_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>INVOICE-<?= htmlspecialchars($row['transaksi_id']) ?></td>
                                <td><?= htmlspecialchars($row['transaksi_tgl']) ?></td>
                                <td><?= htmlspecialchars($row['nama_laundry']) ?></td>
                                <td>
                                    <?php
                                    // Menampilkan berat atau jumlah pcs untuk Cuci Satuan
                                    if ($row['transaksi_berat'] > 0) {
                                        echo $row['transaksi_berat'] . ' Kg'; // Untuk layanan selain Cuci Satuan
                                    } else {
                                        // Hitung jumlah pcs untuk Cuci Satuan
                                        $id_transaksi = $row['transaksi_id'];
                                        $detail = mysqli_query($koneksi, "SELECT * FROM transaksi_detail WHERE transaksi_id = $id_transaksi");

                                        $pcs = 0; // Menyimpan jumlah pcs
                                        $jenis_paket = []; // Menyimpan jenis paket yang ada dalam transaksi
                                        while ($d = mysqli_fetch_assoc($detail)) {
                                            $jenis_paket[] = $d['jenis_paket']; // Menambahkan jenis paket ke array
                                            if ($d['jenis_paket'] == 'cs') { // Jika jenis paket adalah Cuci Satuan
                                                $pcs += $d['jumlah']; // Tambah jumlah pcs
                                            }
                                        }

                                        // Jika hanya ada 1 jenis layanan dan Cuci Satuan yang dipilih, tampilkan pcs
                                        if (count(array_unique($jenis_paket)) == 1 && in_array('cs', $jenis_paket)) {
                                            echo $pcs . ' pcs'; // Menampilkan jumlah pcs untuk Cuci Satuan
                                        } else {
                                            echo "<span class='text-danger'>Menunggu admin</span>"; // Jika ada lebih dari 1 jenis layanan
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    // Menampilkan total harga berdasarkan jenis layanan
                                    if ($row['transaksi_berat'] > 0 && $row['transaksi_total_harga'] > 0) {
                                        echo 'Rp. ' . number_format($row['transaksi_total_harga'], 0, ',', '.'); // Untuk layanan selain Cuci Satuan
                                    } else {
                                        // Menghitung harga untuk Cuci Satuan berdasarkan jumlah pcs
                                        $id_transaksi = $row['transaksi_id'];
                                        $total_price = 0;
                                        $jenis_paket = []; // Menyimpan jenis paket yang ada dalam transaksi

                                        $detail = mysqli_query($koneksi, "SELECT * FROM transaksi_detail WHERE transaksi_id = $id_transaksi");
                                        while ($d = mysqli_fetch_assoc($detail)) {
                                            $jenis_paket[] = $d['jenis_paket']; // Menambahkan jenis paket ke array
                                            if ($d['jenis_paket'] == 'cs') { // Cuci Satuan
                                                $q_price = mysqli_query($koneksi, "SELECT tarif_cs FROM tb_cuci_satuan WHERE id_cs = {$d['id_paket']}");
                                                $rowPrice = mysqli_fetch_assoc($q_price);
                                                $total_price += $rowPrice['tarif_cs'] * $d['jumlah']; // Menghitung total harga berdasarkan jumlah pcs
                                            }
                                            if ($d['jenis_paket'] == 'ck') { // Cuci Komplit
                                                $q_price = mysqli_query($koneksi, "SELECT tarif_ck FROM tb_cuci_komplit WHERE id_ck = {$d['id_paket']}");
                                                $rowPrice = mysqli_fetch_assoc($q_price);
                                                $total_price += $rowPrice['tarif_ck'] * $d['jumlah']; // Menghitung total harga berdasarkan jumlah pcs
                                            }
                                            if ($d['jenis_paket'] == 'dc') { // Dry Clean
                                                $q_price = mysqli_query($koneksi, "SELECT tarif_dc FROM tb_dry_clean WHERE id_dc = {$d['id_paket']}");
                                                $rowPrice = mysqli_fetch_assoc($q_price);
                                                $total_price += $rowPrice['tarif_dc'] * $d['jumlah']; // Menghitung total harga berdasarkan jumlah pcs
                                            }
                                        }

                                        // Menampilkan total harga atau status "Menunggu admin"
                                        if (count(array_unique($jenis_paket)) == 1 && in_array('cs', $jenis_paket)) {
                                            echo $total_price > 0 ? 'Rp. ' . number_format($total_price, 0, ',', '.') : "<span class='text-danger'>Menunggu admin</span>"; // Menampilkan harga total atau status "Menunggu admin"
                                        } else {
                                            echo "<span class='text-danger'>Menunggu admin</span>"; // Jika ada lebih dari 1 jenis layanan
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($row['transaksi_tgl_selesai']) ?></td>
                                <td>
                                    <?php
                                    // Periksa apakah status transaksi sudah ada
                                    if (!empty($row['transaksi_status'])) {
                                        $labels = [
                                            'menunggu' => 'danger',
                                            'proses' => 'warning',
                                            'selesai' => 'success',
                                            'diantar' => 'primary'
                                            // Added 'menunggu' status
                                        ];
                                        $label_class = $labels[$row['transaksi_status']] ?? 'danger';
                                        echo "<div class='label label-{$label_class}'>" . strtoupper($row['transaksi_status']) . "</div>";
                                    } else {
                                        echo "<div class='label label-danger'>MENUNGGU ADMIN</div>";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $payment_labels = [
                                        'belum_bayar' => 'danger',
                                        'lunas' => 'success',
                                    ];
                                    $payment_status = $row['status_pembayaran'] ?? 'belum_bayar';
                                    $payment_class = $payment_labels[$payment_status] ?? 'danger';
                                    echo "<span class='label label-{$payment_class}'>" . strtoupper(str_replace('_', ' ', htmlspecialchars($payment_status))) . "</span>";
                                    ?>
                                </td>
                                <td>
                                    <a href="transaksi_invoice.php?id=<?= $row['transaksi_id'] ?>" target="_blank" class="btn btn-sm btn-warning">Invoice</a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="9">
                                    <?php
                                    $id_transaksi = $row['transaksi_id'];
                                    $detail = mysqli_query($koneksi, "SELECT * FROM transaksi_detail WHERE transaksi_id = $id_transaksi");

                                    $grouped = [
                                        'ck' => [],
                                        'cs' => [],
                                        'dc' => []
                                    ];

                                    while ($d = mysqli_fetch_assoc($detail)) {
                                        $grouped[$d['jenis_paket']][] = $d;
                                    }

                                    foreach (['ck', 'cs', 'dc'] as $jenis) {
                                        if (!empty($grouped[$jenis])) {
                                            $label = $jenis == 'ck' ? 'Cuci Komplit' : ($jenis == 'cs' ? 'Cuci Satuan' : 'Dry Clean');
                                            echo "<strong>Detail $label:</strong><ul>";

                                            foreach ($grouped[$jenis] as $det) {
                                                $paket_nama = '-';
                                                if ($jenis == 'ck') {
                                                    $q = mysqli_query($koneksi, "SELECT nama_paket_ck FROM tb_cuci_komplit WHERE id_ck = {$det['id_paket']}");
                                                    $rowPaket = mysqli_fetch_assoc($q);
                                                    $paket_nama = $rowPaket['nama_paket_ck'];
                                                } elseif ($jenis == 'cs') {
                                                    $q = mysqli_query($koneksi, "SELECT nama_cs FROM tb_cuci_satuan WHERE id_cs = {$det['id_paket']}");
                                                    $rowPaket = mysqli_fetch_assoc($q);
                                                    $paket_nama = $rowPaket['nama_cs'];
                                                } else {
                                                    $q = mysqli_query($koneksi, "SELECT nama_paket_dc FROM tb_dry_clean WHERE id_dc = {$det['id_paket']}");
                                                    $rowPaket = mysqli_fetch_assoc($q);
                                                    $paket_nama = $rowPaket['nama_paket_dc'];
                                                }

                                                echo "<li><strong>Paket:</strong> " . htmlspecialchars($paket_nama) . " - " . $det['jumlah'] . " pcs";

                                                // Jika paket adalah Cuci Komplit atau Cuci Satuan, tampilkan pakaian yang diinput
                                                if (in_array($jenis, ['ck', 'dc'])) {
                                                    // Ambil jenis pakaian yang sudah dimasukkan sebelumnya
                                                    $q_pakaian = mysqli_query($koneksi, "SELECT pakaian_jenis, pakaian_jumlah FROM pakaian WHERE transaksi_id = {$id_transaksi} AND jenis_paket = '$jenis' AND id_paket = {$det['id_paket']}");
                                                    if (mysqli_num_rows($q_pakaian) > 0) {
                                                        echo "<ul>";
                                                        while ($p = mysqli_fetch_assoc($q_pakaian)) {
                                                            echo "<li>" . htmlspecialchars($p['pakaian_jenis']) . " - " . $p['pakaian_jumlah'] . " pcs</li>";
                                                        }
                                                        echo "</ul>";
                                                    }
                                                }

                                                echo "</li>";
                                            }
                                            echo "</ul>";
                                        }
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (mysqli_num_rows($result) == 0): ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data transaksi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>