<?php
include 'header.php';
include '../koneksi.php';

// Ambil ID user yang sedang login
$user_id = $_SESSION['id']; // Sesuaikan dengan variabel sesi yang menyimpan ID user

// Ambil Nama Laundry berdasarkan ID yang terkait dengan user_id
$query_laundry = mysqli_prepare($koneksi, "SELECT nama_laundry FROM laundry WHERE laundry_id = ?");
mysqli_stmt_bind_param($query_laundry, "i", $_SESSION['laundry_id']);
mysqli_stmt_execute($query_laundry);
mysqli_stmt_bind_result($query_laundry, $nama_laundry);
mysqli_stmt_fetch($query_laundry);
mysqli_stmt_close($query_laundry);
?>

<div class="container">
    <div class="alert alert-info text-center">
        <h4 style="margin-bottom: 0px">SELAMAT DATANG DI <b><?php echo strtoupper($nama_laundry); ?></b></h4>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <h4>Dashboard</h4>
        </div>
        <div class="panel-body">

            <div class="row">
                <!-- Jumlah Transaksi -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h1>
                                <i class="glyphicon glyphicon-user"></i>
                                <span class="pull-right">
                                    <?php
                                    // Menghitung jumlah riwayat transaksi milik user yang sedang login
                                    $transaksi_query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE transaksi_pelanggan = $user_id");
                                    echo $transaksi_query ? mysqli_num_rows($transaksi_query) : "0";
                                    ?>
                                </span>
                            </h1>
                            Riwayat Transaksi
                        </div>
                    </div>
                </div>

                <!-- Jumlah Cucian Di Proses -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h1>
                                <i class="glyphicon glyphicon-retweet"></i>
                                <span class="pull-right">
                                    <?php
                                    // Jumlah cucian dalam proses
                                    $proses_query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE transaksi_pelanggan = $user_id AND transaksi_status='proses'");
                                    echo $proses_query ? mysqli_num_rows($proses_query) : "0";
                                    ?>
                                </span>
                            </h1>
                            Jumlah Cucian Di Proses
                        </div>
                    </div>
                </div>

                <!-- Jumlah Cucian Selesai -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h1>
                                <i class="glyphicon glyphicon-info-sign"></i>
                                <span class="pull-right">
                                    <?php
                                    // Jumlah cucian siap ambil
                                    $siap_ambil_query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE transaksi_pelanggan = $user_id AND transaksi_status='selesai'");
                                    echo $siap_ambil_query ? mysqli_num_rows($siap_ambil_query) : "0";
                                    ?>
                                </span>
                            </h1>
                            Jumlah Cucian Selesai
                        </div>
                    </div>
                </div>

                <!-- Jumlah Cucian Diantar -->
                <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <h1>
                                <i class="glyphicon glyphicon-ok-circle"></i>
                                <span class="pull-right">
                                    <?php
                                    // Jumlah cucian diantar
                                    $selesai_query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE transaksi_pelanggan = $user_id AND transaksi_status='diantar'");
                                    echo $selesai_query ? mysqli_num_rows($selesai_query) : "0";
                                    ?>
                                </span>
                            </h1>
                            Jumlah Cucian Diantar
                        </div>
                    </div>
                </div>
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
                    <tr>
                        <th width="1%">No</th>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Berat (Kg) / Pcs</th>
                        <th>Tgl. Selesai</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Status Pembayaran</th>
                    </tr>

                    <?php
                    // Query untuk mengambil transaksi yang hanya terkait dengan user yang sedang login
                    $data_query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE transaksi_pelanggan = '$user_id' ORDER BY transaksi_id DESC");

                    if ($data_query) {
                        $no = 1;
                        while ($d = mysqli_fetch_array($data_query)) {
                    ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>INVOICE-<?php echo $d['transaksi_id']; ?></td>
                                <td><?php echo $d['transaksi_tgl']; ?></td>
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
                                        echo "<span class='label label-success'>LUNAS</span>";
                                    } else {
                                        echo "<span class='label label-danger'>BELUM BAYAR</span>";
                                    }
                                    ?>
                                </td>

                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='7'>Tidak ada data transaksi.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>