<?php
require_once __DIR__ . "/../akses.php";
$id_user = decrypt($_SESSION['tiket_id'], $key_global);

// Penghubung Library
require_once __DIR__ . '/../assets/vendor/autoload.php';

$datetime_now = date('Y-m-d H:i:s');

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
// Function encrypt dan decrypt
require_once __DIR__ . "/../function/function-enkripsi.php";
// Generate UUID
require_once __DIR__ . "/../function/uuid.php";

// Library sanitasi input data
require_once __DIR__ . "/../function/sanitasi_input.php";
$sanitasi_post = sanitizeInput($_POST);
if (isset($sanitasi_post['alert-ubah-pengiriman-bum'])) {
    $id_status_kirim_revisi = $sanitasi_post['id_status_kirim_revisi'];
    $id_komplain = $sanitasi_post['id_komplain'];
    $id_komplain_encrypt = encrypt($id_komplain, $key_spk);
    $id_inv = $sanitasi_post['id_inv'];
    $id_inv_revisi = $sanitasi_post['id_inv_revisi'];
    $id_bukti_terima = $sanitasi_post['id_bukti_terima'];
    $jenis_pengiriman = $sanitasi_post['jenis_pengiriman'];
    $updated_date = date('d/m/Y H:i:s');
    $day = date('d');
	$month = date('m');
	$year = date('Y');
    $id_history = "HIS-" . $month . $year . uuid() . $day;
    $bukti_kirim_rev = $connect->query("SELECT bukti_satu FROM inv_bukti_terima_revisi WHERE id_bukti_terima = '$id_bukti_terima'");
    $data_bukti_kirim =  mysqli_fetch_array($bukti_kirim_rev);
    $cek_data_bukti = mysqli_num_rows($bukti_kirim_rev);
    try {
        // Begin transaction
        mysqli_begin_transaction($connect);
        // Update status kirim
        $stmt = $connect->prepare("UPDATE revisi_status_kirim 
                                        SET 
                                            jenis_pengiriman = '', 
                                            jenis_penerima = '',
                                            dikirim_driver = '', 
                                            dikirim_ekspedisi = '',
                                            no_resi = '',
                                            jenis_ongkir = 0,
                                            ongkir = 0,
                                            free_ongkir = 0,
                                            dikirim_oleh = '',
                                            diambil_oleh = '',
                                            penanggung_jawab = '',
                                            tgl_kirim = '',
                                            status_kirim = 0,
                                            status_review = 0,
                                            alasan = '',
                                            updated_date = ?
                                        WHERE id_status_kirim_revisi = ?");
        $stmt->bind_param('ss', $updated_date, $id_status_kirim_revisi);
        $update_status_kirim = $stmt->execute();


        if (!$update_status_kirim) {
            throw new Exception("Gagal simpan data: " . $stmt->error);
        }

        // Prepare statement before update data invoice
        $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Komplain Dikirim' WHERE id_inv_bum = ?");
        $stmt->bind_param('s', $id_inv);
        $update_inv = $stmt->execute();

        if (!$update_inv) {
            throw new Exception("Gagal hapus data: " . $stmt->error);
        }

        // Proses delete inv diterima revisi
        $stmt = $connect->prepare("DELETE FROM inv_penerima_revisi WHERE id_komplain = ?");
        $stmt->bind_param('s',  $id_komplain);
        $bukti_terima = $stmt->execute();

        if (!$bukti_terima) {
            throw new Exception("Gagal simpan data: " . $stmt->error);
        }

        // Update inv revisi
        $stmt = $connect->prepare("UPDATE inv_revisi SET status_pengiriman = '0' WHERE id_inv_revisi = ?");
        $stmt->bind_param('s', $id_inv_revisi);
        $update_inv_revisi = $stmt->execute();

        if (!$update_inv_revisi) {
            throw new Exception("Gagal update status kirim: " . $stmt->error);
        }

        // Update bukti terima
        $stmt = $connect->prepare("UPDATE inv_bukti_terima_revisi
                                    SET
                                        approval = 0,
                                        jenis_reject = 0
                                    WHERE id_komplain = ?
                                ");
        
        $stmt->bind_param('s', $id_komplain);
        $update_inv_bukti_terima = $stmt->execute();

        if (!$update_inv_bukti_terima) {
            throw new Exception("Gagal update status kirim");
        }


        // insert history bukti kirim
        $stmt = $connect->prepare("INSERT INTO history_inv_bukti_terima_revisi   
                                                (id_history, id_bukti_terima, id_komplain, bukti_satu, bukti_dua, bukti_tiga, lokasi, approval, approval_date, approval_by, jenis_reject, alasan, created_date, created_by)
                                    SELECT ?, id_bukti_terima, id_komplain, bukti_satu, bukti_dua, bukti_tiga, lokasi, approval, approval_date, approval_by, jenis_reject, alasan, created_date, created_by 
                                    FROM inv_bukti_terima_revisi
                                    WHERE id_komplain = ?");
        // Bind parameter dan eksekusi
        $stmt->bind_param("ss", $id_history, $id_komplain);
        $insert_history = $stmt->execute();
        if (!$insert_history) {
            throw new Exception("Gagal update status kirim");
        }                


        // Commit transaksi jika semua berhasil
        $connect->commit();
        $_SESSION['info'] = "Diupdate";
        header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $connect->rollback();
        $error_message = "Gagal saat proses data: " . $e->getMessage();
        echo $error_message;
        $_SESSION['info'] = "Data Gagal Diupdate";
        // header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
        exit();
    }
}
?>