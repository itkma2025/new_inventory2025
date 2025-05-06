<?php  
require_once "../../akses.php";

if(isset($_POST['simpan'])){
    $uuid =  uuid();
    $day = date('d');
    $month = date('m');
    $year = date('y');
    $id_bank_pt = "BANK_PT_" . $year . "" . $month . "" . $uuid . "" . $day ;
    $id_bank = $_POST['id_bank'];
    $no_rekening = $_POST['no_rekening'];
    $atas_nama = htmlspecialchars($_POST['atas_nama']);
    $created_by = $_SESSION['tiket_nama'];

    $sql_check_bank = mysqli_query($connect, "SELECT id_bank, no_rekening, atas_nama FROM bank_pt WHERE id_bank = '$id_bank' AND no_rekening = '$no_rekening' AND atas_nama = '$atas_nama'");
    if($sql_check_bank->num_rows > 0) {
        // Jika data sudah ada, berikan pesan kesalahan
        $_SESSION['info'] = 'Data sudah ada';
        header("Location:../data-bank-pt.php");
    } else {
        // Simpan data ke database
        $simpan_data = mysqli_query($connect, "INSERT INTO bank_pt (id_bank_pt, id_bank, no_rekening, atas_nama, created_by) VALUES ('$id_bank_pt', '$id_bank', '$no_rekening', '$atas_nama', '$created_by')");
        $_SESSION['info'] = 'Disimpan';
        header("Location:../data-bank-pt.php");
    }

}else if(isset($_POST['edit'])){
    $id_bank_pt = $_POST['id_bank_pt'];
    $id_bank = $_POST['id_bank'];
    $no_rekening = $_POST['no_rekening'];
    $atas_nama = $_POST['atas_nama'];

    $sql_check_bank = mysqli_query($connect, "SELECT id_bank, no_rekening, atas_nama FROM bank_pt WHERE id_bank = '$id_bank' AND no_rekening = '$no_rekening' AND atas_nama = '$atas_nama'");
    if($sql_check_bank->num_rows > 0) {
        // Jika data sudah ada, berikan pesan kesalahan
        $_SESSION['info'] = 'Data sudah ada';
        header("Location:../data-bank-pt.php");
    } else {
        // Simpan data ke database
        $simpan_data = mysqli_query($connect, "UPDATE bank_pt 
                                                SET 
                                                    id_bank = '$id_bank', 
                                                    no_rekening = '$no_rekening', 
                                                    atas_nama = '$atas_nama'
                                                WHERE id_bank_pt = '$id_bank_pt'
                                    ");
        $_SESSION['info'] = 'Diupdate';
        header("Location:../data-bank-pt.php");
    }
}else if(isset($_GET['id'])){
    $id_bank_pt = base64_decode($_GET['id']);
    $delete_bank = mysqli_query($connect, "DELETE FROM bank_pt WHERE id_bank_pt = '$id_bank_pt'");
    if($delete_bank){
        $_SESSION['info'] = 'Dihapus';
        unlink($file_destination);
        header("Location: ../data-bank-pt.php");
    }else{
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header("Location: ../data-bank-pt.php");
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
