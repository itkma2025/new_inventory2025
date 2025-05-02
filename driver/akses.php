<?php
    require_once "../function/error_log.php";
    // Periksa apakah sesi sudah dimulai
    if (!isset($_SESSION)) {
        session_start();
    }
    // Periksa apakah pengguna telah login
    if (empty($_SESSION['tiket_user'])) {
        // Redirect ke halaman logout.php
        header("location: logout.php");
    } else {
        include "koneksi.php";
        // Atur zona waktu
        date_default_timezone_set('Asia/Jakarta');
        $id_user = base64_decode($_SESSION['tiket_id']);
        $ip = $_SESSION['ip'];
        $id_status = $_SESSION['id_status'];
        $device = $_SESSION['jenis_perangkat'];
        $token = $_SESSION['token'];

        //Ambil status pengguna dari database
        $cek_expired_token = $connect->query("SELECT 
                                                    us.id_user_status,
                                                    us.id_user,
                                                    us.id_token,
                                                    us.login_time,
                                                    us.jenis_perangkat,
                                                    us.status_perangkat,
                                                    STR_TO_DATE(ut.expired_token_time, '%d/%m/%Y %H:%i:%s') AS expired
                                                FROM user_status AS us 
                                                LEFT JOIN user_token ut ON us.id_token = ut.id_token
                                                WHERE us.id_user = '$id_user' AND us.jenis_perangkat = '$device'
                                            ");
        $data = mysqli_fetch_array($cek_expired_token);
       // Memeriksa jika $data adalah array dan elemen yang dibutuhkan ada
        $id_status = isset($data['id_user_status']) ? $data['id_user_status'] : null;
        $id_token_old = isset($data['id_token']) ? $data['id_token'] : null;
        $expired_token = isset($data['expired']) ? $data['expired'] : null;
        $jenis_perangkat = isset($data['jenis_perangkat']) ? $data['jenis_perangkat'] : null;
        $status_perangkat = isset($data['status_perangkat']) ? $data['status_perangkat'] : null;
        $login_time = isset($data['login_time']) ? $data['login_time'] : null;
        $logout_time = date('d/m/Y H:i:s');

        // Proses Delete Token

        // Waktu saat ini
        $currentTime = date('Y-m-d H:i:s');

        // echo $expired_token. "<br>";
        // echo $currentTime. "<br>";
        // echo $jenis_perangkat. "<br>";
        // echo $status_perangkat. "<br>";

        // Periksa status pengguna
        if ($status_perangkat == "Online") {
            // Periksa apakah sesi telah berakhir (30 menit tidak ada aktivitas)
            $session_time = 1800; // 30 menit
            $current_time = time();

            if (isset($_SESSION['last_activity']) && ($current_time - $_SESSION['last_activity']) > $session_time) {
                // Jika sesi telah berakhir, hancurkan sesi dan redirect ke logout.php
                header("location: logout.php");
            } else {
                // Perbarui waktu aktivitas terakhir setiap kali ada aktivitas
                $_SESSION['last_activity'] = $current_time;
            }
            // Bandingkan waktu saat ini dengan waktu kedaluarsa
            if ($currentTime > $expired_token) {
                // Jika waktu saat ini telah melebihi waktu kedaluarsa, update data history
                $connect->begin_transaction();
                try {

                    $update_status = $connect->query("UPDATE user_status SET id_token = '' , logout_time = '$currentTime', status_perangkat = 'Offline' WHERE id_token = '$id_token_old'");

                    $delete_token = $connect->query("DELETE FROM user_token WHERE id_token = '$id_token_old'");

                    $update_history = mysqli_query($connect, "UPDATE user_history SET logout_time = '$logout_time' WHERE id_user = '$id_user' AND login_time = '$login_time'");

                    if ($update_status && $delete_token && $update_history) {
                        // Commit transaksi
                        $connect->commit();
                        header("location: logout.php");
                    } else {
                        // Rollback transaksi jika salah satu operasi gagal
                        $connect->rollback();
                    }
                } catch (Exception $e) {
                    // Rollback transaksi jika salah satu operasi gagal
                    $connect->rollback();
                }
            } else {
                $cek_status = $connect->query("SELECT 
                                                    us.id_user,
                                                    ut.token
                                                FROM user_status us
                                                LEFT JOIN user_token ut ON us.id_token = ut.id_token
                                                WHERE id_user = '$id_user' AND jenis_perangkat = '$device' AND status_perangkat = 'Online'
                                            ");
                $data_status = mysqli_fetch_array($cek_status);

                if ($token != $data_status['token']) {
                    $_SESSION['alert'] = 'User sedang aktif, jika itu bukan anda silahkan hubungin IT';
                    header("Location: login.php");
                } else {
                    // Periksa apakah sesi telah berakhir (30 menit tidak ada aktivitas)
                    $session_time = 1800; // 30 menit
                    $current_time = time();

                    if (isset($_SESSION['last_activity']) && ($current_time - $_SESSION['last_activity']) > $session_time) {
                        // Jika sesi telah berakhir, hancurkan sesi dan redirect ke logout.php
                        header("location: logout.php");
                    } else {
                        // Perbarui waktu aktivitas terakhir setiap kali ada aktivitas
                        $_SESSION['last_activity'] = $current_time;
                    }
                }
            }
        } else {
            header("location: logout.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="assets/img/logo-kma.png" rel="icon">
</head>

<body>
    <script>
        window.onload = function() {
            // Cek apakah elemen dengan ID 'countdown' ada di halaman
            var countdownElement = document.getElementById("countdown");
            if (countdownElement) {
                // Set the date we're counting down to (30 minutes from now)
                var countDownDate = new Date().getTime() + (30 * 60 * 1000);

                // Update the count down every 1 second
                var x = setInterval(function() {

                    // Get today's date and time
                    var now = new Date().getTime();

                    // Find the distance between now and the count down date
                    var distance = countDownDate - now;

                    // Time calculations for minutes and seconds
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Pad the numbers with leading zeros if they are less than 10
                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    // Output the result in the element with id="countdown"
                    countdownElement.innerHTML = minutes + ":" + seconds;

                    // If the count down is over, write some text and redirect
                    if (distance < 0) {
                        clearInterval(x);
                        window.location.href = 'logout.php';
                    }
                }, 1000);
            } else {
                console.log("Element with id 'countdown' not found. Countdown will not start.");
            }
        };
    </script>
</body>

</html>