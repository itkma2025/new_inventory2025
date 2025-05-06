<?php
session_start();
include "koneksi.php";
$id_reset = base64_decode($_GET['id']);
echo $id_reset;
$cek_data_reset = $connect->query("SELECT id_reset, id_user, email, otp FROM reset_password WHERE id_reset = '$id_reset'");
$data = mysqli_fetch_array($cek_data_reset);
$email = $data['email'];
$otp = $data['otp'];
$reset_id = base64_encode($data['id_reset']);

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
    $mail->Body = '<p style="font-family: Arial, sans-serif; color: #333;">Halo,</p>
               <p style="font-family: Arial, sans-serif; color: #333;">Kami menerima permintaan untuk mereset kata sandi akun Anda. Mohon untuk tidak memberikan kode berikut kepada orang lain:</p>
               <h3 style="background-color: #f4f4f4; padding: 10px; font-family: Arial, sans-serif; color: #333;">' . $otp . '</h3>
               <p style="font-family: Arial, sans-serif; color: #333;">Terima kasih!</p>';

    // Isi email versi plain text
    $mail->AltBody = '';

    // Kirim email kita
    $mail->send();
    $_SESSION['alert'] = 'OTP telah dikirim, silahkan cek email anda';
    header("Location:validasi-otp.php?id=$reset_id");

} catch (Exception $th) {
    echo "Gagal Kirim Email: {$mail->ErrorInfo}";
}