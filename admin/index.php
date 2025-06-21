<?php
include 'header.php';
include '../koneksi.php';

// Cek jika admin sudah login
if (!isset($_SESSION['laundry_id'])) {
    echo "<script>alert('Anda tidak memiliki akses. Silakan login.'); window.location.href='../login.php';</script>";
    exit();
}

$laundry_id = $_SESSION['laundry_id'];

// Ambil Nama Laundry berdasarkan ID
$query_laundry = mysqli_prepare($koneksi, "SELECT nama_laundry FROM laundry WHERE laundry_id = ?");
mysqli_stmt_bind_param($query_laundry, "i", $laundry_id);
mysqli_stmt_execute($query_laundry);
mysqli_stmt_bind_result($query_laundry, $nama_laundry);
mysqli_stmt_fetch($query_laundry);
mysqli_stmt_close($query_laundry);
?>

<div class="container">
    <div class="alert alert-info text-center">
        <h4>SELAMAT DATANG DI <b><?php echo strtoupper($nama_laundry); ?></b></h4>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <h4>Dashboard</h4>
        </div>

        <div class="panel-body">
            <div class="row">
                <!-- Jumlah Pelanggan -->
                <?php
                $query_pelanggan = mysqli_prepare($koneksi, "SELECT COUNT(DISTINCT transaksi_pelanggan) FROM transaksi WHERE laundry_id = ?");
                mysqli_stmt_bind_param($query_pelanggan, "i", $laundry_id);
                mysqli_stmt_execute($query_pelanggan);
                mysqli_stmt_bind_result($query_pelanggan, $jumlah_pelanggan);
                mysqli_stmt_fetch($query_pelanggan);
                mysqli_stmt_close($query_pelanggan);
                ?>

                <div class="col-md-3 col-sm-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading text-center">
                            <h1>
                                <i class="glyphicon glyphicon-user"></i>
                                <span class="pull-right"><?php echo $jumlah_pelanggan; ?></span>
                            </h1>
                            <p>Jumlah Pelanggan</p>
                        </div>
                    </div>
                </div>

                <!-- Status Transaksi -->
                <?php
                $statuses = [
                    ['status' => 'proses', 'label' => 'Cucian Di Proses', 'color' => 'warning', 'icon' => 'glyphicon-retweet'],
                    ['status' => 'selesai', 'label' => 'Cucian Selesai', 'color' => 'success', 'icon' => 'glyphicon-ok-circle'],
                    ['status' => 'diantar', 'label' => 'Cucian Diantar', 'color' => 'primary', 'icon' => 'glyphicon-ok']
                ];

                foreach ($statuses as $stat) {
                    $query_transaksi = mysqli_prepare($koneksi, "SELECT COUNT(transaksi_id) FROM transaksi WHERE laundry_id = ? AND transaksi_status = ?");
                    mysqli_stmt_bind_param($query_transaksi, "is", $laundry_id, $stat['status']);
                    mysqli_stmt_execute($query_transaksi);
                    mysqli_stmt_bind_result($query_transaksi, $count);
                    mysqli_stmt_fetch($query_transaksi);
                    mysqli_stmt_close($query_transaksi);
                ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="panel panel-<?php echo $stat['color']; ?>">
                            <div class="panel-heading text-center">
                                <h1>
                                    <i class="glyphicon <?php echo $stat['icon']; ?>"></i>
                                    <span class="pull-right"><?php echo $count; ?></span>
                                </h1>
                                <p><?php echo $stat['label']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Panel Riwayat Transaksi Terakhir -->
    <div class="panel">
        <div class="panel-heading">
            <h4>Riwayat Transaksi Terakhir</h4>
        </div>
        <div class="panel-body">
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
                            <th>Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query untuk mengambil transaksi yang hanya terkait dengan laundry_id admin
                        $query_riwayat = mysqli_prepare($koneksi, "
                        SELECT transaksi.*, user.nama 
                        FROM transaksi 
                        JOIN user ON transaksi.transaksi_pelanggan = user.id 
                        WHERE transaksi.laundry_id = ? 
                        ORDER BY transaksi.transaksi_id DESC 
                        LIMIT 7
                        ");

                        mysqli_stmt_bind_param($query_riwayat, "i", $laundry_id);
                        mysqli_stmt_execute($query_riwayat);
                        $result = mysqli_stmt_get_result($query_riwayat);

                        $no = 1;
                        while ($d = mysqli_fetch_array($result)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>INVOICE-<?php echo $d['transaksi_id']; ?></td>
                                <td><?php echo $d['transaksi_tgl']; ?></td>
                                <td><?php echo $d['nama']; ?></td>
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
                                <td><?php echo $d['transaksi_tgl_selesai']; ?></td>
                                <td>
                                    <?php
                                    // Menampilkan harga jika ada
                                    if ($d['transaksi_total_harga'] > 0) {
                                        echo "Rp. " . number_format($d['transaksi_total_harga'], 0, ',', '.');
                                    } else {
                                        echo "<span class='text-danger'>Menunggu admin</span>";
                                    }
                                    ?>
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
                                <td>
                                    <?php
                                    if ($d['status_pembayaran'] == 'lunas') {
                                        echo "<span class='label label-success'>Lunas</span>";
                                    } else {
                                        echo "<span class='label label-danger'>Belum Bayar</span>";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php
                        }
                        mysqli_stmt_close($query_riwayat);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>