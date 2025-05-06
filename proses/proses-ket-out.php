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
// Kode untuk sanitasi input
require_once "../function/sanitasi_input.php";

// Function Encrypt dan Decrypt
require_once "../function/function-enkripsi.php";

$id_user = decrypt($_SESSION['tiket_id'], $key_global);
$datetime = date('Y-m-d H:i:s');

// Simpon
if (isset($_POST['simpan-ket-out'])) {
    // Sanitasi seluruh $_POST
    $sanitasi_post = sanitizeInput($_POST);
    $id_ket_out = $sanitasi_post['id_ket_out'];
    $nama_ket_out = trim($sanitasi_post['nama_ket_out']);

    $sql = "SELECT id_ket_out, ket_out FROM keterangan_out WHERE id_ket_out = '$id_ket_out' OR ket_out = '$nama_ket_out' ";
    $query = mysqli_query($connect, $sql);

    if ($query->num_rows > 0) {
        $_SESSION['info'] = 'Data sudah ada';
        header('Location:../keterangan-out.php');
    } else {
        $simpan_data = "INSERT INTO keterangan_out 
                        (id_ket_out, ket_out, created_by)
                        VALUES
                        ('$id_ket_out', '$nama_ket_out', '$id_user')";
        $query = mysqli_query($connect, $simpan_data);
        $_SESSION['info'] = 'Disimpan';
        header('Location:../keterangan-out.php');
    }

    // Edit
} else if (isset($_POST['edit-ket-out'])) {
    $sanitasi_post = sanitizeInput($_POST);
    $id_ket_out = $sanitasi_post['id_ket_out'];
    $id_ket_out_decrypt = decrypt($id_ket_out, $key_global);
    $nama_ket_out = trim($sanitasi_post['nama_ket_out']);

    $cek_data = "SELECT * FROM keterangan_out WHERE ket_out = '$nama_ket_out' ";
    $query_cek = mysqli_query($connect, $cek_data);

    if ($query_cek->num_rows > 0) {
        $_SESSION['info'] = 'Data sudah ada';
        header('Location:../keterangan-out.php');
    } else {
        $update = "UPDATE keterangan_out SET  
                   ket_out = '$nama_ket_out',
                   updated_date = '$datetime',
                   updated_by = '$id_user'
                   WHERE id_ket_out = '$id_ket_out_decrypt' ";
        $query = mysqli_query($connect, $update);
        $_SESSION['info'] = 'Diupdate';
        header('Location: ../keterangan-out.php');
    }

    // Hapus
} else if (isset($_GET['hapus-ket-out'])) {
    $sanitasi_get = sanitizeInput($_GET);
    $idh = $sanitasi_get['hapus-ket-out'];
    $idh_decrypt = decrypt($idh, $key_global);
    $hapus_data = "DELETE FROM keterangan_out WHERE id_ket_out = '$idh_decrypt'";
    $query = mysqli_query($connect, $hapus_data);

    if ($query) {
        $_SESSION['info'] = 'Dihapus';
        header('Location:../keterangan-out.php');
    } else {
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header('Location:../keterangan-out.php');
    }
}
