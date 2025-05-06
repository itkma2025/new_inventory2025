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

$id_user = decrypt($_SESSION['tiket_id'], $key_global);
$datetime = date('Y-m-d H:i:s');

// Simpon
if (isset($_POST['simpan-ket-in'])) {
    // Sanitasi seluruh $_POST
    $sanitasi_post = sanitizeInput($_POST);
    $id_ket_in = $sanitasi_post['id_ket_in'];
    $nama_ket_in = trim($sanitasi_post['nama_ket_in']);

    $sql = "SELECT id_ket_in, ket_in FROM keterangan_in WHERE id_ket_in = '$id_ket_in' OR ket_in = '$nama_ket_in' ";
    $query = mysqli_query($connect, $sql);

    if ($query->num_rows > 0) {
        $_SESSION['info'] = 'Data sudah ada';
        header('Location:../keterangan-in.php');
    } else {
        $simpan_data = "INSERT INTO keterangan_in 
                        (id_ket_in, ket_in, created_by)
                        VALUES
                        ('$id_ket_in', '$nama_ket_in', '$id_user')";
        $query = mysqli_query($connect, $simpan_data);
        $_SESSION['info'] = 'Disimpan';
        header('Location:../keterangan-in.php');
    }

    // Edit
} else if (isset($_POST['edit-ket-in'])) {
    $sanitasi_post = sanitizeInput($_POST);
    $id_ket_in = $sanitasi_post['id_ket_in'];
    $id_ket_in_decrypt = decrypt($id_ket_in, $key_global);
    $nama_ket_in = trim($sanitasi_post['nama_ket_in']);

    $cek_data = "SELECT * FROM keterangan_in WHERE ket_in = '$nama_ket_in' ";
    $query_cek = mysqli_query($connect, $cek_data);

    if ($query_cek->num_rows > 0) {
        $_SESSION['info'] = 'Data sudah ada';
        header('Location:../keterangan-in.php');
    } else {
        $update = "UPDATE keterangan_in SET  
                   ket_in = '$nama_ket_in',
                   updated_date = '$datetime',
                   updated_by = '$id_user'
                   WHERE id_ket_in = '$id_ket_in_decrypt' ";
        $query = mysqli_query($connect, $update);
        $_SESSION['info'] = 'Diupdate';
        header('Location: ../keterangan-in.php');
    }

    // Hapus
} else if (isset($_GET['hapus-ket-in'])) {
    $sanitasi_get = sanitizeInput($_GET);
    $idh = $sanitasi_get['hapus-ket-in'];
    $idh_decrypt = decrypt($idh, $key_global);
    $hapus_data = "DELETE FROM keterangan_in WHERE id_ket_in = '$idh_decrypt'";
    $query = mysqli_query($connect, $hapus_data);

    if ($query) {
        $_SESSION['info'] = 'Dihapus';
        header('Location:../keterangan-in.php');
    } else {
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header('Location:../keterangan-in.php');
    }
}
