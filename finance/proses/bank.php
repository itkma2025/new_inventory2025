<?php  
require_once "../../akses.php";

if(isset($_POST['simpan'])){
    $uuid =  uuid();
    $day = date('d');
    $month = date('m');
    $year = date('y');
    $id_bank = "BANK" . $year . "" . $month . "" . $uuid . "" . $day ;
    $nama_bank = htmlspecialchars($_POST['nama_bank']);
    $created_by = $_SESSION['tiket_nama'];

    $file_name = $_FILES['fileku']['name'];
    $file_tmp = $_FILES['fileku']['tmp_name'];
    $file_destination = "../logo-bank/" . $file_name;

    $sql_check_bank = mysqli_query($connect, "SELECT nama_bank FROM bank WHERE nama_bank = '$nama_bank'");
    if(mysqli_num_rows($sql_check_bank) > 0) {
        // Jika data sudah ada, berikan pesan kesalahan
        $_SESSION['info'] = 'Data sudah ada';
        header("Location:../data-bank.php");
        exit; // Keluar dari skrip
    } else {
        if (file_exists($file_destination)) {
            // Simpan data ke database
            $simpan_data = mysqli_query($connect, "INSERT INTO bank (id_bank, nama_bank, logo, created_by) VALUES ('$id_bank', '$nama_bank', '$file_name', '$created_by')");
            $_SESSION['info'] = 'Disimpan';
            header("Location:../data-bank.php");
            exit; // Keluar dari skrip untuk menghindari eksekusi lebih lanjut.
        } else {
            // Jika file belum ada, maka upload gambar
            move_uploaded_file($file_tmp, $file_destination);
            // Simpan data ke database
            $simpan_data = mysqli_query($connect, "INSERT INTO bank (id_bank, nama_bank, logo, created_by) VALUES ('$id_bank', '$nama_bank', '$file_name', '$created_by')");
            $_SESSION['info'] = 'Disimpan';
            header("Location:../data-bank.php");
        }
    }
}else if(isset($_GET['id'])){
    $id_bank = base64_decode($_GET['id']);
    $file_name = base64_decode($_GET['logo']);
    $file_destination = "../logo-bank/" . $file_name;
    $delete_bank = mysqli_query($connect, "DELETE FROM bank WHERE id_bank = '$id_bank'");
    if($delete_bank){
        $_SESSION['info'] = 'Dihapus';
        unlink($file_destination);
        header("Location: ../data-bank.php");
    }else{
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header("Location: ../data-bank.php");
    }

}

function uuid() {
    $data = openssl_random_pseudo_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s', str_split(bin2hex($data), 4));
}
?>
