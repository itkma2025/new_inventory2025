<?php
ob_start();
session_start();
require_once "../../akses.php";
require_once "../../koneksi-ecat.php";
require_once "../../function/function-enkripsi.php";
require_once "../../function/uuid.php";
$key = "Driver2024?";

if (isset($_POST['diterima'])) {
    $location = htmlspecialchars(decrypt($_POST['location'], $key));
    $date_now = date('d_m_Y');
    $datetime = date('Y-m-d H:i:s');
    $id_inv = htmlspecialchars($_POST['id_inv']);
    $id_inv_decrypt = decrypt($id_inv, $key);
    $tgl = htmlspecialchars($_POST['tgl']);
    $image = $_POST['image'];
    $nama_driver =  decrypt($_SESSION['tiket_nama'], $key_global);
    $nama_driver = str_replace(' ', '_', $nama_driver);
    // Decode base64 image
    $base64 = str_replace('data:image/png;base64,', '', $image);
    $base64 = str_replace(' ', '+', $base64);
    $data = base64_decode($base64);
    $day = date('d');
    $month = date('m');
    $year = date('Y');
    $fileName = $month . $year . uuid() .  $day;
    $fileNameEncrypt = encrypt($fileName, $key);
    $newFileName = 'IMG_' . $date_now . $fileNameEncrypt . '.png';
    $id_user =  decrypt($_SESSION['tiket_id'], $key_global);

    // Memulai transaksi
    mysqli_begin_transaction($connect_ecat);

    try {
        // Update Bukti Terima
        $bukti_terima = mysqli_query($connect_ecat, "UPDATE inv_bukti_terima 
                                                    SET bukti_terima = '$newFileName', lokasi = '$location', approval = '0', created_by = '$id_user'
                                                    WHERE id_inv_ecat = '$id_inv_decrypt'");

        // Query untuk update jenis_penerima di status_kirim
        $query_update_status = mysqli_query($connect_ecat, "UPDATE status_kirim SET status_review = '0'  WHERE id_inv_ecat = '$id_inv_decrypt'");

        // Proses penyimpanan gambar
        // Create ../../gambar/bukti_kirim/ folder if it doesn't exist
        $baseDir = '../../gambar/bukti_kirim/ecat/';
        if (!file_exists($baseDir)) {
            mkdir($baseDir, 0755, true); // Create base directory with permissions 755
        }

        // Create nama_driver folder inside ../../gambar/bukti_kirim/ if it doesn't exist
        $driverFolder = $baseDir . $nama_driver;
        if (!file_exists($driverFolder)) {
            mkdir($driverFolder, 0755, true); // Create nama_driver folder with permissions 755
        }

        // Generate file path
        $filePath = $driverFolder . '/' . $newFileName;

        // Simpan gambar
        $gambar_disimpan = file_put_contents($filePath, $data);

        if (!$bukti_terima && $query_update_status && !$gambar_disimpan) {
            throw new Exception("Gagal Proses");
        }
        // Jika semua query dan proses penyimpanan gambar berhasil, commit transaksi
        mysqli_commit($connect_ecat);
        $_SESSION['info'] = "Disimpan";
        header("Location:../list-invoice.php", true, 303);
    } catch (Exception $e) {
        // Jika terjadi pengecualian (exception), rollback transaksi
        mysqli_rollback($connect_ecat);
        echo $e->getMessage();
        $_SESSION['info'] = "Data Gagal Disimpan";
        // header("Location:../list-invoice.php", true, 303);
        exit;
    } finally {
        // Selalu akhiri koneksi
        mysqli_close($connect_ecat);
    }
   
} else {
    header("Location: ../list-invoice.php");
}
ob_end_flush();
