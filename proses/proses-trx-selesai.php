<?php  
require_once "../akses.php";
require_once "../page/resize-image.php";
// Penghubung Library
require_once '../assets/vendor/autoload.php';
// Library Tangal
use Carbon\Carbon;
$datetime_now = Carbon::now();

// Library Debugging
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;

// Atur status aktif/non-aktif Whoops
$whoops_enabled = false; // Ubah menjadi false untuk menonaktifkan

if ($whoops_enabled) {
    // Inisialisasi Whoops
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();
}
// Library sanitasi input data
require_once "../function/sanitasi_input.php";

// Function Encrypt dan Decrypt
require_once "../function/function-enkripsi.php";

// Function Encrypt dan Decrypt
require_once "../function/uuid.php";

$year = date('y');
$day = date('d');
$month = date('m');
$uuid = uuid();
$id_finance = "FINANCE" . $year . "". $month . "" . $uuid . "" . $day;
$sanitasi_get = sanitizeInput($_GET);
$id_inv = $sanitasi_get['id_inv'];
$id_inv_decrypt = decrypt($id_inv, $key_global);
$jenis_inv = $sanitasi_get['jenis_inv'];
$total_inv = $sanitasi_get['total_inv'];
$nonce_token = $sanitasi_get['nonce_token'];
$update_inv = '';

// Validasi nonce token
if (!isset($sanitasi_get['nonce_token']) || $sanitasi_get['nonce_token'] !== $_SESSION['nonce_token']) {
    // Jika token tidak cocok atau tidak ada, hentikan proses dan tampilkan error
    $_SESSION['info'] = "Silahkan Ulangi Kembali";
    header("Location:../spk-siap-kirim.php?sort=baru");
    exit();
}
// Nonce sudah divalidasi, sekarang hapus dari session agar tidak bisa digunakan lagi
unset($_SESSION['nonce_token']);

try {
    // Mulai Transaksi
    $connect->begin_transaction();
    if ($jenis_inv == 'nonppn'){
        $stmt = $connect->prepare("UPDATE inv_nonppn SET status_transaksi = 'Transaksi Selesai' WHERE id_inv_nonppn = ?");
        $stmt->bind_param("s", $id_inv_decrypt);
        $update_inv = $stmt->execute();
    } else if ($jenis_inv == 'ppn') {
        $stmt = $connect->prepare("UPDATE inv_ppn SET status_transaksi = 'Transaksi Selesai' WHERE id_inv_ppn = ?");
        $stmt->bind_param("s", $id_inv_decrypt);
        $update_inv = $stmt->execute();
    } else if ($jenis_inv == 'bum'){
        $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Transaksi Selesai' WHERE id_inv_bum = ?");
        $stmt->bind_param("s", $id_inv_decrypt);
        $update_inv = $stmt->execute();
    }

    // Insert data ke dalam table finance
    $stmt = $connect->prepare("INSERT INTO finance
                                            (id_finance, id_inv, jenis_inv, total_inv) 
                                            VALUES 
                                            (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $id_finance, $id_inv_decrypt, $jenis_inv, $total_inv);
    $simpan_finance = $stmt->execute();

    // Insert data ke dalam table history produk
    $stmt = $connect->prepare("INSERT IGNORE INTO 
                                            history_produk_terjual (id_trx_history, id_inv, id_produk, qty)
                                        SELECT
                                            tpr.id_transaksi,
                                            spk.id_inv,
                                            tpr.id_produk,
                                            tpr.qty
                                        FROM spk_reg AS spk
                                        LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk 
                                        WHERE spk.id_inv = ?");
    $stmt->bind_param('s', $id_inv_decrypt);
    $simpan_history_produk = $stmt->execute();

    if ($update_inv && $simpan_finance && $simpan_history_produk) {
        // Commit transaksi
        $connect->commit();
        $_SESSION['info'] = 'Disimpan';
        header("Location:../invoice-reguler-diterima.php?sort=baru");
    } else {
        // Rollback transaksi
        $connect->rollback();
        throw new Exception(); 
    }
} catch (Exception $e){
    // Rollback transaksi
    $connect->rollback();
    $_SESSION['info'] = 'Data Gagal Disimpan';
    header("Location:../invoice-reguler-diterima.php?sort=baru");
}
?>