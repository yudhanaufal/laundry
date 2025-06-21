<?php 
include 'header.php'; 
include '../koneksi.php';

// Pastikan admin atau user sudah login
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location: ../index.php?pesan=belum_login");
    exit();
}

// Ambil id user dari sesi
$user_id = $_SESSION['id'];

?>

<div class="container">
    <div class="panel">
        <div class="panel-heading">
            <h4>Profil Saya</h4>
        </div>
        <div class="panel-body">
            <!-- Tabel Data Profil -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="1%">No</th>
                            <th>Nama</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                            <th width="15%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil data user berdasarkan id dari session
                        $query = "SELECT * FROM user WHERE id = ?";
                        $stmt = $koneksi->prepare($query);
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $no = 1;
                        while ($d = $result->fetch_assoc()) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($d['nama']); ?></td>
                                <td><?php echo htmlspecialchars($d['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars($d['alamat']); ?></td>
                                <td>
                                    <!-- Tombol Edit -->
                                    <a href="profil_edit.php?id=<?php echo $d['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="glyphicon glyphicon-pencil"></i> Edit
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }

                        // Jika tidak ditemukan user
                        if ($no == 1) {
                            echo "<tr><td colspan='5' class='text-center'>Data tidak ditemukan</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
