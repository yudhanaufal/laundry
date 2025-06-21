<?php
include 'header.php';
include '../koneksi.php';

if (!isset($_SESSION['laundry_id'])) {
    echo "<script>alert('Anda tidak memiliki akses. Silakan login.'); window.location.href='../login.php';</script>";
    exit();
}

$laundry_id = $_SESSION['laundry_id'];
?>

<div class="container">
    <div class="panel">
        <div class="panel-heading">
            <h4>Data Transaksi Laundry</h4>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Invoice</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Berat (Kg) / Pcs</th>
                            <th>Total Harga</th>
                            <th>Selesai</th>
                            <th>Status</th>
                            <th>Status Pembayaran</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT t.*, u.nama FROM transaksi t JOIN user u ON t.transaksi_pelanggan = u.id WHERE t.laundry_id = ? ORDER BY t.transaksi_id DESC";
                        $stmt = mysqli_prepare($koneksi, $query);
                        mysqli_stmt_bind_param($stmt, "i", $laundry_id);
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);

                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id_transaksi = $row['transaksi_id'];
                            $total_price = 0;
                            $berat_total = 0;
                            $pcs = 0;

                            $detail = mysqli_query($koneksi, "SELECT * FROM transaksi_detail WHERE transaksi_id = $id_transaksi");

                            $grouped = [
                                'ck' => [],
                                'cs' => [],
                                'dc' => []
                            ];

                            while ($d = mysqli_fetch_assoc($detail)) {
                                $grouped[$d['jenis_paket']][] = $d;

                                if ($d['jenis_paket'] == 'cs') {
                                    $pcs += $d['jumlah'];
                                } else {
                                    $berat_total += $row['transaksi_berat'];
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
                                    // Menampilkan berat atau jumlah pcs untuk Cuci Satuan dan layanan lainnya
                                    if ($row['transaksi_berat'] > 0) {
                                        // Jika ada berat (Kg) untuk layanan lainnya (selain Cuci Satuan)
                                        echo $row['transaksi_berat'] . ' Kg'; // Menampilkan berat (Kg) untuk layanan selain Cuci Satuan
                                    } else {
                                        // Hitung jumlah pcs untuk Cuci Satuan
                                        $id_transaksi = $row['transaksi_id'];
                                        $detail = mysqli_query($koneksi, "SELECT * FROM transaksi_detail WHERE transaksi_id = $id_transaksi");

                                        $pcs = 0; // Menyimpan jumlah pcs untuk Cuci Satuan
                                        $berat_total = 0; // Menyimpan total berat untuk Cuci Komplit/Dry Clean
                                        $jenis_paket = []; // Menyimpan jenis paket yang ada dalam transaksi

                                        // Loop untuk menghitung jumlah pcs dan berat total
                                        while ($d = mysqli_fetch_assoc($detail)) {
                                            $jenis_paket[] = $d['jenis_paket']; // Menambahkan jenis paket ke array
                                            if ($d['jenis_paket'] == 'cs') { // Jika jenis paket adalah Cuci Satuan
                                                $pcs += $d['jumlah']; // Tambah jumlah pcs
                                            } else {
                                                $berat_total += $row['transaksi_berat']; // Tambah berat untuk Cuci Komplit/Dry Clean
                                            }
                                        }

                                        // Jika ada lebih dari satu jenis layanan (misalnya Cuci Satuan dan Cuci Komplit)
                                        if (count(array_unique($jenis_paket)) > 1) {
                                            // Menampilkan pcs dan berat jika ada lebih dari satu jenis layanan
                                            echo $berat_total . ' Kg, ' . $pcs . ' pcs'; // Menampilkan jumlah pcs dan berat
                                        } elseif (in_array('cs', $jenis_paket)) {
                                            // Jika hanya ada Cuci Satuan, tampilkan pcs
                                            echo $pcs . ' pcs'; // Menampilkan jumlah pcs untuk Cuci Satuan
                                        } else {
                                            // Jika hanya ada layanan lain (Cuci Komplit/Dry Clean), tampilkan berat
                                            echo "<span class='text-danger'>Menunggu admin</span>"; // Menunggu admin jika data tidak lengkap
                                        }
                                    }
                                    ?>

                                </td>
                                <td>
                                    <?php
                                    $status = true;
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
                                            $status = false;
                                            echo "<span class='text-danger'>Menunggu admin</span>"; // Jika ada lebih dari 1 jenis layanan
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($row['transaksi_tgl_selesai']) ?></td>
                                <td>
                                    <?php
                                    // Cek apakah admin atau superadmin
                                    if ($_SESSION['level'] == 'admin' || $_SESSION['level'] == 'superadmin') {
                                    ?>
                                        <select class="form-control status-dropdown badge-select" data-id="<?= $row['transaksi_id'] ?>">
                                            <option value="menunggu" <?= $row['transaksi_status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                            <option value="proses" <?= $row['transaksi_status'] == 'proses' ? 'selected' : '' ?>>Proses</option>
                                            <option value="selesai" <?= $row['transaksi_status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                            <option value="diantar" <?= $row['transaksi_status'] == 'diantar' ? 'selected' : '' ?>>Diantar</option>
                                        </select>
                                    <?php
                                    } else {
                                        // Kalau user biasa hanya lihat badge
                                        if (!empty($row['transaksi_status'])) {
                                            $badges = [
                                                'menunggu' => 'danger',
                                                'proses' => 'warning',
                                                'selesai' => 'success',
                                                'diantar' => 'primary'
                                            ];
                                            $badge_class = $badges[$row['transaksi_status']] ?? 'danger';
                                            echo "<span class='badge bg-{$badge_class}'>" . strtoupper($row['transaksi_status']) . "</span>";
                                        } else {
                                            echo "<span class='badge bg-danger'>MENUNGGU ADMIN</span>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($_SESSION['level'] == 'admin' || $_SESSION['level'] == 'superadmin') : ?>
                                        <select class="form-control pembayaran-dropdown" data-id="<?= $row['transaksi_id'] ?>">
                                            <option value="belum_bayar" <?= $row['status_pembayaran'] == 'belum_bayar' ? 'selected' : '' ?>>BELUM BAYAR</option>
                                            <option value="lunas" <?= $row['status_pembayaran'] == 'lunas' ? 'selected' : '' ?>>LUNAS</option>
                                        </select>
                                    <?php else : ?>
                                        <?php if ($row['status_pembayaran'] == 'lunas') : ?>
                                            <div class="label label-success">LUNAS</div>
                                        <?php else : ?>
                                            <div class="label label-danger">BELUM BAYAR</div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['transaksi_berat'] > 0 || $status) : ?>
                                        <a href="transaksi_invoice.php?id=<?= $row['transaksi_id'] ?>" target="_blank" class="btn btn-sm btn-warning">Invoice</a>
                                    <?php endif ?>
                                    <a href="transaksi_edit.php?id=<?= $row['transaksi_id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                </td>
                            </tr>

                            <!-- Detail Transaksi -->
                            <tr>
                                <td colspan="9">
                                    <?php
                                    foreach (['ck', 'cs', 'dc'] as $jenis) {
                                        if (!empty($grouped[$jenis])) {
                                            $label = $jenis == 'ck' ? 'Cuci Komplit' : ($jenis == 'cs' ? 'Cuci Satuan' : 'Dry Clean');
                                            echo "<strong>Detail $label:</strong><ul>";

                                            foreach ($grouped[$jenis] as $det) {
                                                $paket_nama = '-';
                                                if ($jenis == 'ck') {
                                                    $q = mysqli_query($koneksi, "SELECT nama_paket_ck FROM tb_cuci_komplit WHERE id_ck = {$det['id_paket']}");
                                                    $rowPaket = mysqli_fetch_assoc($q);
                                                    $paket_nama = $rowPaket['nama_paket_ck'] ?? '-';
                                                } elseif ($jenis == 'cs') {
                                                    $q = mysqli_query($koneksi, "SELECT nama_cs FROM tb_cuci_satuan WHERE id_cs = {$det['id_paket']}");
                                                    $rowPaket = mysqli_fetch_assoc($q);
                                                    $paket_nama = $rowPaket['nama_cs'] ?? '-';
                                                } else {
                                                    $q = mysqli_query($koneksi, "SELECT nama_paket_dc FROM tb_dry_clean WHERE id_dc = {$det['id_paket']}");
                                                    $rowPaket = mysqli_fetch_assoc($q);
                                                    $paket_nama = $rowPaket['nama_paket_dc'] ?? '-';
                                                }

                                                echo "<li><strong>Paket:</strong> " . htmlspecialchars($paket_nama) . " - " . $det['jumlah'] . " pcs";

                                                if (in_array($jenis, ['ck', 'dc'])) {
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
                                <td colspan="9" class="text-center">Tidak ada data transaksi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateBadgeColor(selectElement) {
        var value = selectElement.value;

        selectElement.classList.remove('badge-lunas', 'badge-proses', 'badge-selesai', 'badge-diantar');

        if (value === 'lunas') {
            selectElement.classList.add('badge-lunas');
        } else if (value === 'proses') {
            selectElement.classList.add('badge-proses');
        } else if (value === 'selesai') {
            selectElement.classList.add('badge-selesai');
        } else if (value === 'diantar') {
            selectElement.classList.add('badge-diantar');
        }
    }

    $(document).ready(function() {
        // Set warna badge saat load halaman
        $('.badge-select').each(function() {
            updateBadgeColor(this);
        });

        // Ketika user mengubah status
        $('.badge-select').change(function() {
            var transaksiId = $(this).data('id');
            var newStatus = $(this).val();

            var selectElement = this;

            // Konfirmasi dulu sebelum update
            if (confirm("Yakin ingin mengubah status menjadi " + newStatus.toUpperCase() + "?")) {
                $.ajax({
                    url: 'transaksi_update_status.php',
                    method: 'POST',
                    data: {
                        id: transaksiId,
                        status: newStatus
                    },
                    success: function(response) {
                        if (response.trim() == "success") {
                            alert('Status berhasil diupdate!');
                            updateBadgeColor(selectElement);
                        } else {
                            alert('Gagal update status: ' + response);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat menghubungi server.');
                    }
                });
            } else {
                // Kalau batal, reset ke value sebelumnya (optional)
                window.location.reload();
            }
        });
    });
    // Status pembayaran
    $('.pembayaran-dropdown').change(function() {
        var transaksiId = $(this).data('id');
        var newStatus = $(this).val();

        if (confirm("Yakin ingin mengubah status pembayaran menjadi " + newStatus.toUpperCase() + "?")) {
            $.ajax({
                url: 'transaksi_update_pembayaran.php',
                method: 'POST',
                data: {
                    id: transaksiId,
                    status_pembayaran: newStatus
                },
                success: function(response) {
                    if (response.trim() === 'success') {
                        alert('Status pembayaran berhasil diupdate!');
                    } else {
                        alert('Gagal update status pembayaran: ' + response);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghubungi server.');
                }
            });
        } else {
            window.location.reload();
        }
    });
</script>

<?php include 'footer.php'; ?>