<?php
include "../koneksi.php";
session_start();
date_default_timezone_set('Asia/Jakarta');

$day = date('d');
$month = date('m');
$year = date('y');
$uuid = 'RESET' . $year . "" . $month . "" . generate_uuid() . "" . $day;
$encode_uuid = base64_encode($uuid);
$expirationTime = 123;
$expirationTimestamp = time() + $expirationTime;
// Format waktu kedaluwarsa (opsional, dapat disesuaikan sesuai kebutuhan)
$expirationFormatted = date("Y-m-d H:i:s", $expirationTimestamp);

if (isset($_POST['reset'])) {
    $otp = htmlspecialchars($_POST['otp']);
    $cek_data = htmlspecialchars($_POST['cek_data']);

    $cek_data_user = $connect->query("SELECT id_user, email FROM user WHERE username = '$cek_data' OR email = '$cek_data'");
    $data = mysqli_fetch_array($cek_data_user);
    $id_user = $data['id_user'];
    $email = $data['email'];
    if ($data) {
        $cek_approval = $connect->query("SELECT approval FROM user WHERE id_user = '$id_user'");
        $data_approval = mysqli_fetch_array($cek_approval);
        $approval = $data_approval['approval'];
        if ($approval == '1') {
            $cek_data_reset = $connect->query("SELECT id_reset, id_user, email , otp FROM reset_password WHERE id_user = '$id_user'");
            $data_reset = mysqli_fetch_array($cek_data_reset);
            $reset_id = $data_reset['id_reset'];
            $reset_id_encode = base64_encode($reset_id);
            if ($cek_data_reset->num_rows > 0) {
                $update = $connect->query("UPDATE reset_password SET otp = '$otp', expired = '$expirationFormatted' WHERE id_reset = '$reset_id'");
                header("Location:../send-mail.php?id=$reset_id_encode");
            } else {
                $simpan = $connect->query("INSERT INTO reset_password   (id_reset, id_user, email, otp, expired) 
                                                                            VALUES 
                                                                            ('$uuid', '$id_user', '$email', '$otp', '$expirationFormatted')");
                header("Location:../send-mail.php?id=$encode_uuid");
            }
        } else {
            $_SESSION['alert'] = 'Akun anda belum aktif, silahkan tunggu atau hubungi admin';
            header("Location:../lupa-password.php");
        }
    } else {
        $_SESSION['alert'] = 'Username atau Email tidak ditemukan';
        header("Location:../lupa-password.php");
    }
}


// Generatew UUID
function generate_uuid()
{
    return sprintf(
        '%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
//  End Generate UUID 
