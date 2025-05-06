<?php
require_once "../akses.php";

// Penghubung Library
require_once '../assets/vendor/autoload.php';
// Library Tangal
use Carbon\Carbon;
$datetime_now = Carbon::now();

// Library Debugging
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
// Inisialisasi Whoops
// Atur status aktif/non-aktif Whoops
$whoops_enabled = false; // Ubah menjadi false untuk menonaktifkan

if ($whoops_enabled) {
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();
}
// Library sanitasi input data
require_once "../function/sanitasi_input.php";

// Function Encrypt dan Decrypt
require_once "../function/function-enkripsi.php";

// Function UUID 
require_once "../function/uuid.php";

// Kode untuk membuat ID
$uuid = uuid();
$img_uuid = img_uuid();
$year = date('y');
$day = date('d');
$month = date('m');
$id_inv_bukti = "BKTI" . $year . "" . $month . "" . $uuid . "" . $day;
$id_inv_penerima = "PNMR" . $year . "" . $month . "" . $uuid . "" . $day;

if (isset($_POST['diterima_ekspedisi'])){
    $sanitasi_post = sanitizeInput($_POST);
    $id_inv = $sanitasi_post['id_inv'];
    $id_inv_decrypt = decrypt($id_inv, $key_global);
    // Validasi nonce token
    if (!isset($sanitasi_post['nonce_token']) || $sanitasi_post['nonce_token'] !== $_SESSION['nonce_token']) {
        // Jika token tidak cocok atau tidak ada, hentikan proses dan tampilkan error
        $_SESSION['info'] = "Silahkan Ulangi Kembali";
        header("Location:../detail-produk-dikirim.php?jenis=nonppn&&id=$id_inv");
    }
    // Nonce sudah divalidasi, sekarang hapus dari session agar tidak bisa digunakan lagi
    unset($_SESSION['nonce_token']);

    $alamat = $sanitasi_post['alamat'];
    $nama_penerima = $sanitasi_post['nama_penerima'];
    $tgl = $sanitasi_post['tgl'];

    try {
        $connect->begin_transaction();
        // Proses simpan data diterima
        $stmt = $connect->prepare("INSERT INTO inv_penerima 
                                    (id_inv_penerima, id_inv, nama_penerima, alamat, tgl_terima) 
                                    VALUES (?, ?, ?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE 
                                    id_inv_penerima = VALUES(id_inv_penerima),
                                    nama_penerima = VALUES(nama_penerima), 
                                    alamat = VALUES(alamat), 
                                    tgl_terima = VALUES(tgl_terima)");

        $stmt->bind_param("sssss", $id_inv_penerima, $id_inv_decrypt, $nama_penerima, $alamat, $tgl);
        $simpan_data = $stmt->execute();



        // Proses update invoice  
        $stmt = $connect->prepare("UPDATE inv_nonppn SET status_transaksi = 'Diterima' WHERE id_inv_nonppn = ?");
        $stmt->bind_param("s", $id_inv_decrypt);
        $update_data = $stmt->execute();



        if ($simpan_data && $update_data) {
            // Commit transaksi
            $connect->commit();
            $_SESSION['info'] = "Disimpan";
            header("Location:../invoice-reguler-dikirim.php?sort=baru");
            exit();
        } else {
            throw new Exception('Gagal menyimpan file di server.');
        }

    } catch (Exception $e){
        $connect->rollback();
        $_SESSION['info'] = "Data Gagal Disimpan";
        echo $e->getMessage();
        // header("Location:../invoice-reguler-dikirim.php?sort=baru");
        exit();
    }
}
?>