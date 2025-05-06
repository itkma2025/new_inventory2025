<?php
include "../akses.php";

if (isset($_POST['simpan'])) {
    $id_ekspedisi = $_POST['id_ekspedisi'];
    $nama_ekspedisi = $_POST['nama_ekspedisi'];
    $kategori = $_POST['kategori'];

    $cek_data = mysqli_query($connect, "SELECT nama_ekspedisi, kategori FROM ekspedisi WHERE nama_ekspedisi = '$nama_ekspedisi' AND kategori = '$kategori'");

    if ($cek_data->num_rows < 1) {
        $simpan_data = "INSERT INTO ekspedisi
                        (id_ekspedisi, nama_ekspedisi, kategori) VALUES ('$id_ekspedisi', '$nama_ekspedisi', '$kategori')";
        $query = mysqli_query($connect, $simpan_data);
        $_SESSION['info'] = 'Disimpan';
        header("Location:../data-ekspedisi.php");
    } else {
        $_SESSION['info'] = 'Data sudah ada';
        header("Location:../data-ekspedisi.php");
    }
} elseif ($_GET['hapus']) {
    //tangkap URL dengan $_GET
    $idh = $_GET['hapus'];
    $id_ekspedisi = base64_decode($idh);

    // perintah queery sql untuk hapus data
    $sql = "DELETE FROM ekspedisi WHERE id_ekspedisi='$id_ekspedisi'";
    $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));


    if ($query_del) {
        $_SESSION['info'] = 'Dihapus';
        header("Location:../data-ekspedisi.php");
    } else {
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header("Location:../data-ekspedisi.php");
    }
}
