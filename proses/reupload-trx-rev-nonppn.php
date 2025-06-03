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

if (isset($sanitasi_post['reupload'])){
    $id_status_kirim_revisi = $sanitasi_post['id_status_kirim_revisi'];
    $id_komplain = $sanitasi_post['id_komplain'];
    $id_komplain_encrypt = encrypt($id_komplain, $key_spk);
    $id_inv = $sanitasi_post['id_inv'];
    $id_inv_revisi = $sanitasi_post['id_inv_revisi'];
    $id_bukti_terima = $sanitasi_post['id_bukti_terima'];
    $uuid = uuid();
    $img_uuid = img_uuid();
    $year = date('y');
    $day = date('d');
    $month = date('m');
    $id_inv_bukti = "BKTI-REV" . $year . "" . $month . "" . $uuid . "" . $day;
    $bukti_sebelumnya = $sanitasi_post['bukti_sebelumnya'];

    // Proses upload file
    $allowedExtensions = ['png', 'jpeg', 'jpg', 'pdf']; // Ekstensi file yang diizinkan
    $file_name = $_FILES['fileku']['name'];
    $file_tmp = $_FILES['fileku']['tmp_name'];
    $new_file_name = "Bukti_Satu" . $year . $month . $img_uuid . $day . ".jpg";
    $path = "../gambar-revisi/bukti1/" . $new_file_name;
    $path_unlink = "../gambar-revisi/bukti1/" . $bukti_sebelumnya;

    // Memeriksa ekstensi file yang diunggah
    $fileInfo = pathinfo($file_name);
    $fileExtension = strtolower($fileInfo['extension']);

    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception('Jenis file tidak didukung. Hanya file dengan ekstensi .png, .jpeg, .jpg, dan .pdf yang diizinkan.');
    }

    // Proses Upload
    if (!move_uploaded_file($file_tmp, $path)) {
        throw new Exception('Gagal mengupload file.');
    }

    // Mulai Transaksi
    $connect->begin_transaction();
    try {
        // Proses update status kirim revisi
        $stmt = $connect->prepare("UPDATE revisi_status_kirim 
                                        SET
                                            status_kirim = 0,
                                            status_review = 0,
                                            updated_date = ?
                                        WHERE id_status_kirim_revisi = ?");
        $stmt->bind_param('ss', $datetime_now, $id_status_kirim_revisi);
        $update_status_kirim = $stmt->execute();


        if (!$update_status_kirim) {
            throw new Exception("Gagal update data: " . $stmt->error);
        }

        
        // Proses update bukti terima
        $stmt = $connect->prepare("UPDATE inv_bukti_terima_revisi 
                                    SET 
                                        bukti_satu = ?,
                                        lokasi = 'PT. Karsa Mandiri Alkesindo',
                                        created_date = ?,
                                        created_by = ?
                                        WHERE id_bukti_terima = ?");
        $stmt->bind_param('ssss', $new_file_name, $datetime_now, $id_user, $id_bukti_terima);
        $bukti_terima = $stmt->execute();
       
        if (!$bukti_terima) {
            throw new Exception("Gagal simpan data: " . $stmt->error);
        }

        // Commit transaksi jika semua berhasil
        $connect->commit();
        $_SESSION['info'] = "Disimpan";
        header("Location:../detail-komplain-revisi-nonppn.php?id=$id_komplain_encrypt");
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $connect->rollback();
        echo $error_message = "Gagal saat proses data: " . $e->getMessage();
        // $_SESSION['info'] = "Data Gagal Disimpan";
        // header("Location:../detail-komplain-revisi-nonppn.php?id=$id_komplain_encrypt");
        exit();
    }
} else {
    header("Location:../404.php");
}
?>