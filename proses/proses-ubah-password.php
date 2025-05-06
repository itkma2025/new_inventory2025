<?php
include "../akses.php";
date_default_timezone_set('Asia/Jakarta');

if (isset($_POST['ubah-password'])) {
    $id_reset = htmlspecialchars($_POST['id_reset']);
    $password = htmlspecialchars($_POST['password']);

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $cek_data_reset = $connect->query("SELECT id_reset, id_user, email FROM reset_password WHERE id_reset = '$id_reset'");
    $data = mysqli_fetch_array($cek_data_reset);
    $id_user = $data['id_user'];
    echo $id_user;
    if ($data) {
        $reset_password = $connect->query("UPDATE user SET password = '$password_hash' WHERE id_user = '$id_user'");
        if ($reset_password) {
            $_SESSION['alert'] = 'Password berhasil diubah';
            header("Location:../login.php");
        }
    } else {
        $_SESSION['alert'] = 'Password gagal diubah, Silahkan Ulangi Kembali';
        header("Location:../login.php");
    }
}
