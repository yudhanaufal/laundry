<?php  
session_start(); // Pastikan session diaktifkan  
include 'header.php';   

// Cek apakah pengguna adalah super admin  
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {  
    header("location:pemilik.php?pesan=gagal&detail=Akses ditolak. Anda tidak memiliki izin untuk menambah pemilik.");  
    exit;  
}  
?>  
<div class="container">  
    <br/>  
    <br/>  
    <br/>  
    <div class="col-md-5 col-md-offset-3">  
        <div class="panel">  
            <div class="panel-heading">  
                <h4>Tambah Pemilik Baru</h4>  
            </div>  
            <div class="panel-body">  
                <form method="post" action="pemilik_aksi.php">  
                    <div class="form-group">  
                        <label>Nama</label>  
                        <input type="text" class="form-control" name="nama" placeholder="Masukkan nama ..." required>  
                    </div>  
                    <div class="form-group">  
                        <label>HP</label>  
                        <input type="tel" class="form-control" name="hp" placeholder="Masukkan no.hp ..." required>  
                    </div>  
                    <br/>  
                    <input type="submit" class="btn btn-primary" value="Simpan">  
                </form>  
            </div>  
        </div>  
    </div>  
</div>  
<?php include 'footer.php'; ?>