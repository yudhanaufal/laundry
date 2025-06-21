<?php include 'header.php'; ?>

<?php
include '../koneksi.php';
?>

<div class="container">
    <div class="alert alert-info text-center">
        <h4 style="margin-bottom: 0px">SELAMAT DATANG DI <b>UMKM LAUNDRY</b></h4>
    </div>

    <div class="panel">
        <div class="panel-heading">
            <center>
                <b>
                    <h2>Dashboard</h2>
                </b>
            </center>
        </div>
        <div class="panel-body">

            <div class="row">
                <?php
                // Menghitung jumlah pelanggan  
                $pengguna = mysqli_query($koneksi, "SELECT * FROM user WHERE level = 'pengguna'");
                $jumlah_pengguna = mysqli_num_rows($pengguna);

                // Menghitung jumlah admin  
                $admin = mysqli_query($koneksi, "SELECT * FROM user WHERE level = 'admin'");
                $jumlah_admin = mysqli_num_rows($admin);
                ?>

                <!-- Panel Admin -->
                <div class="col-md-6 col-sm-7">
                    <div class="panel panel-danger">
                        <div class="panel-heading text-center">
                            <h1>
                                <i class="glyphicon glyphicon-lock"></i>
                                <span class="pull-right"><?php echo $jumlah_admin; ?></span>
                            </h1>
                            <p>Jumlah Admin</p>
                        </div>
                    </div>
                </div>


                <!-- Panel Pelanggan -->
                <div class="col-md-6 col-sm-8">
                    <div class="panel panel-primary">
                        <div class="panel-heading text-center">
                            <h1>
                                <i class="glyphicon glyphicon-user"></i>
                                <span class="pull-right"><?php echo $jumlah_pengguna; ?></span>
                            </h1>
                            <p>Jumlah Pelanggan</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Panel Data Admin -->
    <div class="panel">
        <div class="panel-heading">
            <h4>Daftar Admin</h4>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="1%">No</th>
                            <th>Nama Laundry</th>
                            <th>Username</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                            <th>Laundry ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil data admin berdasarkan level = 'admin'
                        $query_admin = "SELECT * FROM user WHERE level = 'admin'";
                        $data_admin = mysqli_query($koneksi, $query_admin);

                        if (!$data_admin) {
                            die("Query failed: " . mysqli_error($koneksi));
                        }

                        $no = 1;
                        while ($admin = mysqli_fetch_array($data_admin)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $admin['nama']; ?></td>
                                <td><?php echo $admin['username']; ?></td>
                                <td><?php echo $admin['no_hp']; ?></td>
                                <td><?php echo $admin['alamat']; ?></td>
                                <td><?php echo ($admin['laundry_id']) ? $admin['laundry_id'] : '-'; ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Panel Data Pelanggan -->
    <div class="panel">
        <div class="panel-heading">
            <h4>Daftar Pelanggan</h4>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="1%">No</th>
                            <th>Nama Pelanggan</th>
                            <th>Username</th>
                            <th>No HP</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil data pelanggan berdasarkan level = 'pengguna'
                        $query_pelanggan = "SELECT * FROM user WHERE level = 'pengguna'";
                        $data_pelanggan = mysqli_query($koneksi, $query_pelanggan);

                        if (!$data_pelanggan) {
                            die("Query failed: " . mysqli_error($koneksi));
                        }

                        $no = 1;
                        while ($pelanggan = mysqli_fetch_array($data_pelanggan)) {
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $pelanggan['nama']; ?></td>
                                <td><?php echo $pelanggan['username']; ?></td>
                                <td><?php echo $pelanggan['no_hp']; ?></td>
                                <td><?php echo $pelanggan['alamat']; ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>
</div>
</div>
</div>

<?php include 'footer.php'; ?>