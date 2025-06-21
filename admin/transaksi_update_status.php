<?php
include '../koneksi.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id'] ?? 0);
    $status = mysqli_real_escape_string($koneksi, $_POST['status'] ?? '');

    if ($id > 0 && in_array($status, ['lunas', 'proses', 'selesai', 'diantar'])) {
        $update = mysqli_query($koneksi, "UPDATE transaksi SET transaksi_status = '$status' WHERE transaksi_id = $id");

        if ($update) {
            if ($status == 'selesai') {
                // Ambil data pelanggan + transaksi
                $query = mysqli_query($koneksi, "
                    SELECT u.no_hp, u.nama, t.transaksi_id, t.transaksi_tgl_selesai, t.transaksi_total_harga, t.laundry_id
                    FROM transaksi t
                    JOIN user u ON t.transaksi_pelanggan = u.id
                    WHERE t.transaksi_id = $id
                ");
                $data = mysqli_fetch_assoc($query);
                if ($data) {
                    $no_hp = $data['no_hp'];
                    $nama = $data['nama'];
                    $invoice = $data['transaksi_id'];
                    $tgl_selesai = $data['transaksi_tgl_selesai'];
                    $total_harga = $data['transaksi_total_harga'];

                    // Ambil nama laundry berdasarkan laundry_id
                    $laundry_query = mysqli_query($koneksi, "SELECT nama_laundry FROM laundry WHERE laundry_id = {$data['laundry_id']}");
                    if (!$laundry_query) {
                        die("Query gagal: " . mysqli_error($koneksi));
                    }

                    $laundry = mysqli_fetch_assoc($laundry_query);
                    if ($laundry) {
                        $nama_laundry = $laundry['nama_laundry'];
                    } else {
                        $nama_laundry = 'Laundry Kami'; // Default jika tidak ditemukan
                    }

                    // Susun detail cucian
                    $detail_cucian = '';
                    $detail_query = mysqli_query($koneksi, "SELECT * FROM transaksi_detail WHERE transaksi_id = $invoice");
                    while ($d = mysqli_fetch_assoc($detail_query)) {
                        $paket_nama = '-';
                        if ($d['jenis_paket'] == 'ck') {
                            $q = mysqli_query($koneksi, "SELECT nama_paket_ck FROM tb_cuci_komplit WHERE id_ck = {$d['id_paket']}");
                            $row = mysqli_fetch_assoc($q);
                            $paket_nama = $row['nama_paket_ck'];
                            $detail_cucian .= "🧺 *$paket_nama* - {$d['jumlah']} Kg\n";
                        } elseif ($d['jenis_paket'] == 'dc') {
                            $q = mysqli_query($koneksi, "SELECT nama_paket_dc FROM tb_dry_clean WHERE id_dc = {$d['id_paket']}");
                            $row = mysqli_fetch_assoc($q);
                            $paket_nama = $row['nama_paket_dc'];
                            $detail_cucian .= "🧥 *$paket_nama* - {$d['jumlah']} pcs\n";
                        } elseif ($d['jenis_paket'] == 'cs') {
                            $q = mysqli_query($koneksi, "SELECT nama_cs FROM tb_cuci_satuan WHERE id_cs = {$d['id_paket']}");
                            $row = mysqli_fetch_assoc($q);
                            $paket_nama = $row['nama_cs'];
                            $detail_cucian .= "🧴 *$paket_nama* - {$d['jumlah']} pcs\n";
                        }

                        // Tambahkan jenis pakaian
                        $q_pakaian = mysqli_query($koneksi, "
                            SELECT pakaian_jenis, pakaian_jumlah 
                            FROM pakaian 
                            WHERE transaksi_id = $invoice 
                            AND jenis_paket = '{$d['jenis_paket']}' 
                            AND id_paket = {$d['id_paket']}
                        ");
                        while ($p = mysqli_fetch_assoc($q_pakaian)) {
                            $detail_cucian .= "   - {$p['pakaian_jenis']}: {$p['pakaian_jumlah']} pcs\n";
                        }
                    }

                    // Kirim WA ke pelanggan
                    kirim_wa($no_hp, $nama, $invoice, $tgl_selesai, $total_harga, $detail_cucian, $nama_laundry);
                }
            }
            echo "success";
        } else {
            echo "query error: " . mysqli_error($koneksi);
        }
    } else {
        echo "validation error";
    }
} else {
    echo "method error";
}

// Function untuk kirim WhatsApp menggunakan cURL
function kirim_wa($no_hp, $nama, $invoice, $tgl_selesai, $total_harga, $detail_cucian, $nama_laundry)
{
    $url = 'https://tegal.wablas.com/api/v2/send-message'; // Endpoint Wablas API
    $token = 'Gw5t465BTxnIX2H9mBLtSqwVOyWbTWc9YigqqboIrKxrFl32Bdz8GbP'; // Ganti dengan token Wablas kamu
    $secret_key = 'bfDvLc3v'; // Secret Key Wablas

    // Gabungkan token dan secret key menggunakan titik (.) sebagai pemisah
    $auth_header = $token . '.' . $secret_key;

    // Pesan WhatsApp yang akan dikirim
    $pesan = "
*Halo $nama! 👋🏻*

Laundry kamu sudah *SELESAI* yaa. 🧺✨
Berikut detailnya:

🧾 *Invoice*: INVOICE-$invoice
📅 *Tanggal Selesai*: $tgl_selesai
💵 *Total Harga*: Rp " . number_format($total_harga, 0, ',', '.') . "

🧹 *Detail Cucian:*
$detail_cucian

Terima kasih sudah mempercayakan cucianmu di *$nama_laundry*. 🙏🏻
Kami siap melayani kapanpun kamu butuh! 💬

*Silakan ambil pesanan kamu yaa, atau hubungi admin kalau ingin diantar!* 🚚

- $nama_laundry -
";

    // Data yang akan dikirimkan ke API Wablas
    $data = [
        'phone' => $no_hp,  // Nomor HP pelanggan
        'message' => $pesan, // Pesan yang akan dikirimkan
        'secret' => false,   // Pengaturan secret
    ];

    // Perbaiki bagian berikutnya, supaya parameter yang dikirim adalah array dalam message
    $payload = json_encode([
        'data' => [
            [
                'phone' => $no_hp,
                'message' => $pesan, // Pesan
            ]
        ]
    ]);

    // cURL setup
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Authorization: $auth_header",
        "Content-Type: application/json",
    ]);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($curl, CURLOPT_URL, "https://tegal.wablas.com/api/v2/send-message");
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    // Eksekusi cURL
    $result = curl_exec($curl);
    curl_close($curl);

    // Debugging: Menampilkan hasil respons
    echo "<pre>";
    print_r($result);
}
