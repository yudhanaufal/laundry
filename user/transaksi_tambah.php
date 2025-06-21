<?php
include 'header.php';
include '../koneksi.php';

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header('Location: index.php');
    exit();
}

$laundry = mysqli_query($koneksi, "SELECT * FROM laundry");
$laundryList = mysqli_fetch_all($laundry, MYSQLI_ASSOC);

$nama = $_SESSION['nama'] ?? "Nama";
$laundry_id = $_GET['laundry_id'] ?? null;

$paket_list = [];
if ($laundry_id) {
    // Ambil data paket
    $data_cs = mysqli_query($koneksi, "SELECT 'cs' as jenis, id_cs as id, nama_cs as nama, tarif_cs as tarif FROM tb_cuci_satuan WHERE laundry_id = '$laundry_id'");
    $data_ck = mysqli_query($koneksi, "SELECT 'ck' as jenis, id_ck as id, nama_paket_ck as nama, tarif_ck as tarif FROM tb_cuci_komplit WHERE laundry_id = '$laundry_id'");
    $data_dc = mysqli_query($koneksi, "SELECT 'dc' as jenis, id_dc as id, nama_paket_dc as nama, tarif_dc as tarif FROM tb_dry_clean WHERE laundry_id = '$laundry_id'");

    $paket_list = array_merge(
        mysqli_fetch_all($data_cs, MYSQLI_ASSOC),
        mysqli_fetch_all($data_ck, MYSQLI_ASSOC),
        mysqli_fetch_all($data_dc, MYSQLI_ASSOC)
    );
}
?>

<div class="container">
    <div class="panel">
        <div class="panel-heading">
            <h4>Transaksi Baru</h4>
        </div>
        <div class="panel-body">
            <form action="proses_transaksi_baru.php" method="POST">
                <div class="form-group">
                    <label>Nama Pelanggan:</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($nama); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Tanggal Masuk:</label>
                    <input type="date" name="transaksi_tgl" class="form-control" value="<?= date('Y-m-d'); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Pilih Laundry:</label>
                    <div class="input-group mb-2">
                        <select name="laundry_id" class="form-control" id="laundrySelect" required>
                            <option value="">-- Pilih Laundry --</option>
                            <?php foreach ($laundryList as $laundry): ?>
                                <option value="<?= $laundry['laundry_id'] ?>" <?= $laundry['laundry_id'] == ($laundry_id ?? 0) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($laundry['nama_laundry']) ?> (<?= htmlspecialchars($laundry['alamat']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Pilih Paket Laundry:</label>
                    <div id="paket-list">
                        <div class="row paket-row align-items-center" style="margin-bottom: 15px;">
                            <div class="col-xs-12 col-md-4" style="margin-bottom: 10px">
                                <select name="paket[]" class="form-control paket-select" required>
                                    <option value="">-- Pilih Paket --</option>
                                    <?php foreach ($paket_list as $paket): ?>
                                        <option value="<?= $paket['jenis'] . '-' . $paket['id'] ?>">
                                            <?= strtoupper($paket['jenis']) ?> - <?= htmlspecialchars($paket['nama']) ?> (Rp <?= number_format($paket['tarif'], 0, ',', '.') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <!-- Input jenis pakaian, default disembunyikan -->
                                <input type="text" name="pakaian_jenis[]" class="form-control pakaian-input mt-2" placeholder="Jenis Pakaian" style="display: none;" required>
                            </div>

                            <div class="col-xs-12 col-md-6" style="margin-bottom: 10px">
                                <!-- Kosong, karena input jenis pakaian sudah ada di kolom sebelumnya -->
                            </div>

                            <div class="col-xs-12 col-md-3 d-flex align-items-center" style="margin-bottom: 10px; gap: 10px;">
                                <input type="number" name="jumlah[]" class="form-control" placeholder="1" min="1" required style="max-width: 80px;">
                                <button type="button" class="btn btn-danger remove-paket" style="padding: 6px 12px;">Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="tambahPaket" class="btn btn-success">Tambah Paket</button>
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Simpan">
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Redirect saat pilih laundry berubah
    document.getElementById('laundrySelect').addEventListener('change', function() {
        const selectedId = this.value;
        if (selectedId) {
            window.location.href = 'transaksi_tambah.php?laundry_id=' + selectedId;
        }
    });

    // Generate opsi paket untuk select baru
    const paketOptions = `<?php foreach ($paket_list as $paket): ?>
        <option value="<?= $paket['jenis'] . '-' . $paket['id'] ?>">
            <?= strtoupper($paket['jenis']) ?> - <?= htmlspecialchars($paket['nama']) ?> (Rp <?= number_format($paket['tarif'], 0, ',', '.') ?>)
        </option>
    <?php endforeach; ?>`;

    // Buat baris paket baru
    function createPaketRow() {
        const div = document.createElement("div");
        div.classList.add("row", "mb-2", "paket-row", "align-items-center");
        div.style.marginBottom = "15px";
        div.innerHTML = `
            <div class="col-xs-12 col-md-4" style="margin-bottom: 10px">
                <select name="paket[]" class="form-control paket-select" required>
                    <option value="">-- Pilih Paket --</option>
                    ${paketOptions}
                </select>
                <input type="text" name="pakaian_jenis[]" class="form-control pakaian-input mt-2" placeholder="Jenis Pakaian" style="display: none;" required>
            </div>

            <div class="col-xs-12 col-md-6" style="margin-bottom: 10px">
                <!-- Kosong -->
            </div>

            <div class="col-xs-12 col-md-1" style="margin-bottom: 10px">
                <input type="number" name="jumlah[]" class="form-control" placeholder="1" min="1" required>
            </div>

            <div class="col-xs-12 col-md-1" style="margin-bottom: 10px">
                <button type="button" class="btn btn-danger remove-paket">Hapus</button>
            </div>
        `;
        return div;
    }

    // Tambah baris paket
    document.getElementById("tambahPaket").addEventListener("click", function() {
        const paketList = document.getElementById("paket-list");
        const newRow = createPaketRow();
        paketList.appendChild(newRow);
    });

    // Hapus baris paket
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("remove-paket")) {
            event.target.closest(".paket-row").remove();
        }
    });

    // Tampilkan atau sembunyikan input jenis pakaian sesuai paket yang dipilih
    document.addEventListener("change", function(event) {
        if (event.target.classList.contains("paket-select")) {
            const value = event.target.value;
            const jenis = value.split("-")[0];
            const pakaianInput = event.target.parentElement.querySelector(".pakaian-input");

            if (jenis === "ck" || jenis === "dc") {
                pakaianInput.style.display = "block";
                pakaianInput.required = true;
            } else if (jenis === "cs") {
                pakaianInput.style.display = "none";
                pakaianInput.required = false;
                pakaianInput.value = "";
            } else {
                pakaianInput.style.display = "none";
                pakaianInput.required = false;
                pakaianInput.value = "";
            }
        }
    });

    // Jalankan fungsi change pada select yang sudah ada agar sesuai kondisi saat load halaman
    document.querySelectorAll('.paket-select').forEach(function(select) {
        const event = new Event('change');
        select.dispatchEvent(event);
    });
</script>

<?php include 'footer.php'; ?>