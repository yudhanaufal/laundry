<?php include 'header.php'; ?>
<div class="container">
    <div class="col-md-5 col-md-offset-4">
        <?php 
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == "gagal"){
                echo "<div class='alert alert-danger'>Registrasi gagal! Silakan coba lagi.</div>";
            }
        }
        ?>          

        <div class="panel">
            <div class="panel-heading">
                <h4>Tambah Pelanggan Baru</h4>
            </div>
            <div class="panel-body">
                <form method="post" action="pelanggan_aksi.php">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Masukkan username .." required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Masukkan Password .." required>
                    </div>
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="nama" placeholder="Masukkan nama .." required>
                    </div>
                    <div class="form-group">
                        <label>HP</label>
                        <input type="number" class="form-control" name="no_hp" placeholder="Masukkan no.hp .." required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <input type="text" class="form-control" name="alamat" placeholder="Masukkan alamat .." required>
                    </div>
                    <br/>
                    <input type="submit" class="btn btn-primary" value="Simpan">
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
