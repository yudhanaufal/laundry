<?php 
include 'header.php'; 
include '../koneksi.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['level'])) {
    echo "<script>alert('Akses ditolak!'); window.location='../login.php';</script>";
    exit;
}

// Pastikan hanya superadmin yang bisa mengakses halaman ini
if ($_SESSION['level'] != 'superadmin') {
    echo "<script>alert('Akses ditolak!'); window.location='../index.php';</script>";
    exit;
}
?>  

<div class="container mt-5">  
    <div class="panel">  
        <div class="panel-heading">  
            <h4>Daftar Admin yang Menunggu Persetujuan</h4>  
        </div>  
        <div class="panel-body">  

            <!-- Tabel Data Admin yang Menunggu Persetujuan -->
            <div class="table-responsive">  
                <table class="table table-bordered table-striped">  
                    <thead>  
                        <tr>  
                            <th width="5%">No</th>  
                            <th>Nama Laundry</th>  
                            <th>No HP</th>  
                            <th>Alamat</th>  
                            <th>Bukti Pendaftaran</th>  
                            <th>Status</th>  
                            <th>Aksi</th>  
                        </tr>  
                    </thead>  
                    <tbody>  
                        <?php  
                        // Query untuk mengambil data admin yang menunggu persetujuan
                        // Tidak perlu melakukan LEFT JOIN dengan laundry karena tidak ada laundry_id pada admin_pending
                        $query = "SELECT * FROM admin_pending WHERE status = 'pending'";  

                        $data = mysqli_query($koneksi, $query);  
                        $no = 1;  

                        // Pastikan ada data yang ditemukan
                        if (mysqli_num_rows($data) == 0) {
                            echo "<tr><td colspan='7' class='text-center'>Belum ada admin yang menunggu persetujuan.</td></tr>";
                        } else {
                            while ($d = mysqli_fetch_array($data)) {  
                        ?>  
                            <tr>  
                                <td><?php echo $no++; ?></td>  
                                <td><?php echo htmlspecialchars($d['nama_laundry']); ?></td>  
                                <td><?php echo htmlspecialchars($d['no_hp']); ?></td>  
                                <td><?php echo htmlspecialchars($d['alamat']); ?></td>  
                                <td>
                                    <?php if (!empty($d['bukti_laundry'])) { ?>
                                        <a href="../uploads/<?php echo htmlspecialchars($d['bukti_laundry']); ?>" target="_blank" class="btn btn-sm btn-info">Lihat Bukti</a>
                                    <?php } else { ?>
                                        <span>Belum ada bukti</span>
                                    <?php } ?>
                                </td>  
                                <td><?php echo htmlspecialchars($d['status']); ?></td>  
                                <td>
                                    <!-- Tombol Approve dan Reject -->
                                    <a href="approve_admin.php?id=<?php echo $d['admin_id']; ?>&status=approved" class="btn btn-success btn-sm">Approve</a>
                                    <a href="approve_admin.php?id=<?php echo $d['admin_id']; ?>&status=rejected" class="btn btn-danger btn-sm">Reject</a>
                                </td>  
                            </tr>  
                        <?php  
                            }
                        }  
                        ?>  
                    </tbody>  
                </table>  
            </div>  
        </div>  
    </div>  
</div>  

<?php include 'footer.php'; ?>
