<?php
require_once "../akses.php";
// Penghubung Library
require_once '../assets/vendor/autoload.php';
// Library Debugging
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
// Inisialisasi Whoops
$whoops = new Run;
$whoops->pushHandler(new PrettyPageHandler);
$whoops->register();

// Library sanitasi input data
require_once "../function/sanitasi_input.php";

// Function Encrypt dan Decrypt
require_once "../function/function-enkripsi.php";

$id_user = base64_decode($_SESSION['tiket_id']);
$datetime = date('Y-m-d H:i:s');

// Simpon
if (isset($_POST['simpan-ket-cb'])) {
    // Sanitasi seluruh $_POST
    $sanitasi_post = sanitizeInput($_POST);
    $id_ket_cb = $sanitasi_post['id_ket_cb'];
    $nama_ket_cb = trim($sanitasi_post['nama_ket_cb']);

    $sql = "SELECT id_ket_cashback, ket_cashback FROM keterangan_cashback WHERE id_ket_cashback = '$id_ket_cb' OR ket_cashback = '$nama_ket_cb' ";
    $query = mysqli_query($connect, $sql);

    if ($query->num_rows > 0) {
        $_SESSION['info'] = 'Data sudah ada';
        header('Location:../keterangan-cashback.php');
    } else {
        $simpan_data = "INSERT INTO keterangan_cashback
                        (id_ket_cashback, ket_cashback, created_by)
                        VALUES
                        ('$id_ket_cb', '$nama_ket_cb', '$id_user')";
        $query = mysqli_query($connect, $simpan_data);
        $_SESSION['info'] = 'Disimpan';
        header('Location:../keterangan-cashback.php');
    }

// Edit
} else if (isset($_POST['edit-ket-cb'])) {
    $sanitasi_post = sanitizeInput($_POST);
    $id_ket_cb = $sanitasi_post['id_ket_cb'];
    $id_ket_cb_decrypt = decrypt($id_ket_cb, $key_global);
    $nama_ket_cb = trim($sanitasi_post['nama_ket_cb']);

    $cek_data = "SELECT * FROM keterangan_cashback WHERE ket_cashback = '$nama_ket_cb' ";
    $query_cek = mysqli_query($connect, $cek_data);

    if ($query_cek->num_rows > 0) {
        $_SESSION['info'] = 'Data sudah ada';
        header('Location:../keterangan-cashback.php');
    } else {
        $update = "UPDATE keterangan_cashback SET  
                   ket_cashback = '$nama_ket_cb',
                   updated_date = '$datetime',
                   updated_by = '$id_user'
                   WHERE id_ket_cashback = '$id_ket_cb_decrypt' ";
        $query = mysqli_query($connect, $update);
        $_SESSION['info'] = 'Diupdate';
        header('Location: ../keterangan-cashback.php');
    }

    // Hapus
} else if (isset($_GET['hapus-ket-cb'])) {
    $sanitasi_get = sanitizeInput($_GET);
    $idh = $sanitasi_get['hapus-ket-cb'];
    $idh_decrypt = decrypt($idh, $key_global);
    $hapus_data = "DELETE FROM keterangan_cashback WHERE id_ket_cashback = '$idh_decrypt'";
    $query = mysqli_query($connect, $hapus_data);

    if ($query) {
        $_SESSION['info'] = 'Dihapus';
        header('Location:../keterangan-cashback.php');
    } else {
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header('Location:../keterangan-cashback.php');
    }
}
