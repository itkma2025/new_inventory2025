<?php  
    include "../koneksi.php";
    session_start();
    date_default_timezone_set('Asia/Jakarta');

    $expirationTime = 120;
    $expirationTimestamp = time() + $expirationTime;
    // Format waktu kedaluwarsa (opsional, dapat disesuaikan sesuai kebutuhan)
    $expirationFormatted = date("Y-m-d H:i:s", $expirationTimestamp);

    $otp = htmlspecialchars(base64_decode($_GET['otp']));
    $id_verifikasi = htmlspecialchars(base64_decode($_GET['id_verifikasi']));


    echo $otp;
    echo $id_verifikasi;
    $cek_data_verifikasi = $connect->query("SELECT id_verifikasi, id_user, email, otp FROM user_verifikasi WHERE id_verifikasi = '$id_verifikasi'");
    $data = mysqli_fetch_array($cek_data_verifikasi);
    $id_verifikasi_encode = base64_encode($data['id_verifikasi']);
    if ($data) {
        $update = $connect->query("UPDATE user_verifikasi SET otp = '$otp', expired = '$expirationFormatted' WHERE id_verifikasi = '$id_verifikasi'");
        header("Location:../send-mail-verifikasi.php?id=$id_verifikasi_encode");
    } else {
        $_SESSION['alert'] = 'Username atau Email tidak ditemukan';
        // header("Location:lupa-password.php");
    }
       
    
?>
