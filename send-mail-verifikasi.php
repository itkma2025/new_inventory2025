<?php
session_start();
include "koneksi.php";
$id_verifikasi = base64_decode($_GET['id']);
$cek_data_verifikasi = $connect->query("SELECT id_verifikasi, id_user, email, otp FROM user_verifikasi WHERE id_verifikasi = '$id_verifikasi'");
$data = mysqli_fetch_array($cek_data_verifikasi);
$email = $data['email'];
$otp = $data['otp'];
$otp_encode = base64_encode($otp);
$verifikasi_id = base64_encode($data['id_verifikasi']);
$url = "https://$_SERVER[HTTP_HOST]/proses/proses-verifikasi-user.php?id=$verifikasi_id&&otp=$otp_encode";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Set penggunaan SMTP
    $mail->isSMTP();

    // Isi dengan informasi host SMTP Google
    $mail->Host = 'anzio.id.domainesia.com';
    
    // Aktifkan autentikasi SMTP
    $mail->SMTPAuth = true;
    
    // Isi dengan alamat email Google Anda sebagai username
    $mail->Username = 'it_support@mandirialkesindo.co.id';
    
    // Isi dengan password aplikasi yang dihasilkan dari akun Google Anda
    $mail->Password = 'it_support2024?';
    
    // Port SMTP untuk Google (gunakan 587 untuk STARTTLS)
    $mail->Port = 587;
    
    // Jenis enkripsi (gunakan ENCRYPTION_STARTTLS untuk STARTTLS)
    $mail->SMTPSecure = 'tls';

    // Atur pengirim email
    $mail->setFrom('it_support@mandirialkesindo.co.id', 'IT_Support');
    // Atur penerima email
    $mail->addAddress($email);
    // Atur reply to
    $mail->addReplyTo('it_support@mandirialkesindo.co.id');
    // Atur cc
    // $mail->addCC('it_support@mandirialkesindo.co.id');
    // // Atur bcc
    // $mail->addBCC('it_support@mandirialkesindo.co.id');

    // Isi email
    $mail->isHTML();
    // Atur subjek
    $mail->Subject = 'Notifikasi Keamanan';
    // Atur body
    $mail->Body = ' <p style="font-family: Arial, sans-serif; color: #333;">Silahkan klik link berikut untuk verifikasi:</p>
                    <p style="font-family: Arial, sans-serif; color: #333;"><a href="' . $url . '">' . $url . '</a></p>
                    <p style="font-family: Arial, sans-serif; color: #333;">Terima kasih!</p>';

    // Atur versi text untuk email
    $mail->AltBody = '';

    // Kirim email kita
    $mail->send();
    $_SESSION['alert'] = 'Verifikasi telah dikirim, silahkan cek email anda';
    header("Location:verifikasi-otp-user.php?id=$verifikasi_id");   

} catch (Exception $th) {
    echo "PHPMailer Error: {$mail->ErrorInfo}";
}