<?php
include "../akses.php";
if (isset($_POST['ubah-detail-rev-nonppn'])) {
    $id_komplain = htmlspecialchars($_POST['id_komplain']);
    $no_inv_rev = htmlspecialchars($_POST['no_inv_rev']);
    $cs_inv = htmlspecialchars($_POST['cs_inv']);
    $alamat = htmlspecialchars($_POST['alamat']);

    $update_detail_rev = $connect->query("UPDATE inv_revisi SET pelanggan_revisi = '$cs_inv', alamat_revisi = '$alamat' WHERE no_inv_revisi = '$no_inv_rev'");

    if ($update_detail_rev) {
        $_SESSION['info'] = "Diupdate";
        header("Location:../detail-komplain-revisi-nonppn.php?id=$id_komplain");
    } else {
        $_SESSION['info'] = "Data Gagal Diupdate";
        header("Location:../detail-komplain-revisi-nonppn.php?id=$id_komplain");
    }
} else if (isset($_POST['ubah-detail-rev-ppn'])) {
    $id_komplain = htmlspecialchars($_POST['id_komplain']);
    $no_inv_rev = htmlspecialchars($_POST['no_inv_rev']);
    $cs_inv = htmlspecialchars($_POST['cs_inv']);
    $alamat = htmlspecialchars($_POST['alamat']);

    $update_detail_rev = $connect->query("UPDATE inv_revisi SET pelanggan_revisi = '$cs_inv', alamat_revisi = '$alamat' WHERE no_inv_revisi = '$no_inv_rev'");

    if ($update_detail_rev) {
        $_SESSION['info'] = "Diupdate";
        header("Location:../detail-komplain-revisi-ppn.php?id=$id_komplain");
    } else {
        $_SESSION['info'] = "Data Gagal Diupdate";
        header("Location:../detail-komplain-revisi-ppn.php?id=$id_komplain");
    }
} else if (isset($_POST['ubah-detail-rev-bum'])) {
    $id_komplain = htmlspecialchars($_POST['id_komplain']);
    $no_inv_rev = htmlspecialchars($_POST['no_inv_rev']);
    $cs_inv = htmlspecialchars($_POST['cs_inv']);
    $alamat = htmlspecialchars($_POST['alamat']);

    $update_detail_rev = $connect->query("UPDATE inv_revisi SET pelanggan_revisi = '$cs_inv', alamat_revisi = '$alamat' WHERE no_inv_revisi = '$no_inv_rev'");

    if ($update_detail_rev) {
        $_SESSION['info'] = "Diupdate";
        header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain");
    } else {
        $_SESSION['info'] = "Data Gagal Diupdate";
        header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain");
    }
}
