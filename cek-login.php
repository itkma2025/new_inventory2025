<?php
session_start(); // memulai sebuah sesi
session_regenerate_id(true); // Mengganti ID sesi setelah login

$id_status = 'STATUS' . generate_uuid();
$id_token = 'TOKEN' . generate_uuid();
$id_history = 'HIS' . generate_uuid();
$encrypt_id_status = base64_encode($id_status);
$encrypt_id_token = base64_encode($id_token);

// Menampilkan IP, Jenis Perangkat, Lokasi
$ip_address = $_SERVER['REMOTE_ADDR'];
$os = $_SERVER['HTTP_USER_AGENT'];

$device = "Desktop";
if (preg_match("/(iPhone|iPod|iPad|Android|BlackBerry|Windows Phone)/i", $os)) {
    $device = "Mobile";
}

// Menampilkan Lokasi
$url = 'http://ip-api.com/json/' . $ip_address;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);

$location = json_decode($result);
$locationString = json_encode($location);
$locationString = '';
if (isset($location->city) && isset($location->country)) {
    $locationString .= $location->city . ',' . $location->country . PHP_EOL;
}

// ============================================================================
include "koneksi.php";

if (isset($_POST['login'])) {
    // Ambil data dari formulir login
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);

    // Query untuk mencari data user dari database
    $query = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($connect, $query);

    // Periksa apakah username ditemukan
    if (mysqli_num_rows($result) == 1) {
        // Ambil data password dari database
        $row = mysqli_fetch_assoc($result);
        $password_hash = $row['password'];

        // Verifikasi password
        if (password_verify($password, $password_hash)) {
            if($row['approval'] == 1){
                // Password benar, simpan data user ke session dan arahkan ke halaman dashboard
                //ambil data dari nama kolom operator
                $_SESSION['tiket_id'] = base64_encode($row['id_user']);
                $_SESSION['tiket_user'] = $row['username'];
                $_SESSION['tiket_nama'] = $row['nama_user'];
                $_SESSION['tiket_role'] = $row['id_user_role'];
                $_SESSION['ip'] = $ip_address;
                $_SESSION['os'] = $os;
                $_SESSION['jenis_perangkat'] = $device;
                $_SESSION['lokasi'] = $locationString;
                $_SESSION['id_status'] = $encrypt_id_status;
                $_SESSION['id_token'] = $encrypt_id_token;
                $id_role =  $_SESSION['tiket_role'];

                // Update User Login Session
                $id_status_encrypt =  $_SESSION['id_status'];
                $id_user = base64_decode($_SESSION['tiket_id']);
                $ip = $_SESSION['ip'];
                $device = $_SESSION['jenis_perangkat'];
                $lokasi = $_SESSION['lokasi'];

                // Create token
                $token = $os . $username; 
                $encrypt_token = hash('sha256', $token);

                // Simpan token kedalam session
                $_SESSION['token'] = $encrypt_token;

                
                $online = 'Online';

                date_default_timezone_set('Asia/Jakarta');
                $today = new DateTime();
                $todayFormat = $today->format('d/m/Y H:i:s');
                // Tambahkan interval waktu 8 jam
                $tokenExpired = clone $today;
                $tokenExpired->add(new DateInterval('PT4H'));

                // Format waktu kedaluwarsa sesuai kebutuhan Anda
                $tokenExpiredFormat = $tokenExpired->format('d/m/Y H:i:s');

                // Cek device 
                $cek_status = $connect->query("SELECT 
                                                    us.id_user,
                                                    us.login_time,
                                                    ut.token
                                                FROM user_status us
                                                LEFT JOIN user_token ut ON us.id_token = ut.id_token
                                                WHERE id_user = '$id_user' AND jenis_perangkat = '$device'
                                            ");
                $data_status = mysqli_fetch_array($cek_status);
                $total_data_status = mysqli_num_rows($cek_status);

                if($total_data_status == 0){
                    $connect->begin_transaction();
                    try{
                        // Data belum ada, lakukan INSERT
                        // Simpan Status
                        $simpan_status = mysqli_query($connect, "INSERT INTO user_status (id_user_status, id_user, id_token, login_time, jenis_perangkat, status_perangkat) VALUES ('$id_status', '$id_user', '$id_token', '$todayFormat', '$device', '$online')");
                        
                        $simpan_token = $connect->query("INSERT INTO user_token (id_token, token, expired_token_time)VALUES('$id_token', '$encrypt_token', '$tokenExpiredFormat')");

                        $simpan_history_baru = $connect->query("INSERT INTO user_history (id_history, id_user_status, id_user, login_time, logout_time, ip_login, os, jenis_perangkat, lokasi) VALUES ('$id_history', '$id_status', '$id_user', '$todayFormat', '', '$ip', '$os', '$device', '$lokasi')");

                        if ($simpan_status && $simpan_token && $simpan_history_baru) {
                            // Commit transaksi
                            $connect->commit();
                            $sql_role = " SELECT u.id_user_role, d.id_user_role, d.role 
                                                FROM user AS u 
                                                JOIN user_role AS d ON (u.id_user_role = d.id_user_role)
                                                WHERE u.id_user_role = '$id_role'";
                            $query_role = mysqli_query($connect, $sql_role) or die(mysqli_error($connect));
                            $data_role = mysqli_fetch_array($query_role);
                            $role = $data_role['role']; 
                            
                            if($role == 'Finance'){
                                header("Location: finance/dashboard.php");
                            } else if ($role == 'Driver'){
                                header("Location: driver/dashboard.php");
                            } else if ($role == 'Admin Gudang'){
                                header("Location: dashboard.php");
                            } else if ($role == 'Operator Gudang'){
                                header("Location: scan-qr.php");
                            } else {
                                header("Location: dashboard.php");
                            }
                        } else {
                            // Rollback transaksi jika salah satu operasi gagal
                            $connect->rollback();
                            $_SESSION['alert'] = 'Terjadi kesalahan server, silahkan ulangi kembali';
                            header("Location: login.php");
                        }
                        
                    }catch (Exception $e){
                        // Rollback transaksi jika salah satu operasi gagal
                        $connect->rollback();
                        $_SESSION['alert'] = 'Terjadi kesalahan server, silahkan ulangi kembali';
                        header("Location: login.php");
                    }
                } else {
                    $cek_status_perangkat = $connect->query("SELECT id_user, status_perangkat FROM user_status WHERE id_user = '$id_user' AND jenis_perangkat = '$device'"); 
                    $data_status_perangkat = mysqli_fetch_array($cek_status_perangkat);
                    $status_perangkat =  $data_status_perangkat['status_perangkat'];
                    
                    if($status_perangkat == "Offline"){
                        $connect->begin_transaction();
                        try{
                            // Update id_token, login time, logout time dan status perangkat
                            $update_status = $connect->query("UPDATE user_status 
                                                                    SET 
                                                                        id_user_status = '$id_status',
                                                                        id_token = '$id_token',
                                                                        login_time = '$todayFormat',
                                                                        logout_time = '',
                                                                        jenis_perangkat = '$device',
                                                                        status_perangkat = '$online'
                                                                    WHERE id_user = '$id_user' AND jenis_perangkat = '$device'
                                                                "); 

                            $simpan_token = $connect->query("INSERT INTO user_token (id_token, token, expired_token_time)VALUES('$id_token', '$encrypt_token', '$tokenExpiredFormat')");

                            $simpan_history_baru = $connect->query("INSERT INTO user_history (id_history, id_user_status, id_user, login_time, logout_time, ip_login, os, jenis_perangkat, lokasi) VALUES ('$id_history', '$id_status', '$id_user', '$todayFormat', '', '$ip', '$os', '$device', '$lokasi')");

                            if ($update_status && $simpan_token && $simpan_history_baru) {
                                // Commit transaksi
                                $connect->commit();
                                $sql_role = " SELECT u.id_user_role, d.id_user_role, d.role 
                                                FROM user AS u 
                                                JOIN user_role AS d ON (u.id_user_role = d.id_user_role)
                                                WHERE u.id_user_role = '$id_role'";
                                $query_role = mysqli_query($connect, $sql_role) or die(mysqli_error($connect));
                                $data_role = mysqli_fetch_array($query_role);
                                $role = $data_role['role']; 
                                
                                if($role == 'Finance'){
                                    header("Location: finance/dashboard.php");
                                } else if ($role == 'Driver'){
                                    header("Location: driver/dashboard.php");
                                } else if ($role == 'Admin Gudang'){
                                    header("Location: dashboard.php");
                                } else if ($role == 'Operator Gudang'){
                                        header("Location: scan-qr.php");
                                } else {
                                    header("Location: dashboard.php");
                                }
                            } else {
                                // Rollback transaksi jika salah satu operasi gagal
                                $connect->rollback();
                                $_SESSION['alert'] = 'Terjadi kesalahan server, silahkan ulangi kembali';
                                header("Location: login.php");
                            }
                        }catch (Exception $e){
                            // Rollback transaksi jika salah satu operasi gagal
                            $connect->rollback();
                            $_SESSION['alert'] = 'Terjadi kesalahan server, silahkan ulangi kembali';
                            header("Location: login.php");
                        }
                    } else {
                        $cek_expired_token = $connect->query("SELECT 
                                                                    us.id_user,
                                                                    us.id_token,
                                                                    ut.token,
                                                                    STR_TO_DATE(ut.expired_token_time, '%d/%m/%Y %H:%i:%s') AS expired
                                                                FROM user_status AS us 
                                                                LEFT JOIN user_token ut ON us.id_token = ut.id_token
                                                                WHERE id_user = '$id_user' AND jenis_perangkat = '$device'
                                                            "); 
                        $data_token = mysqli_fetch_array($cek_expired_token);
                        $expired = $data_token['expired'];
                        $id_token_old = $data_token['id_token'];
                        $token_old = $data_token['token'];
                        $currentTime = date('Y-m-d H:i:s');

                        if($token_old == $encrypt_token){
                            if($currentTime > $expired){
                                $connect->begin_transaction();
                                try{
                                    // Data sudah ada, lakukan UPDATE
                                    $update_status = $connect->query("UPDATE user_status 
                                                                        SET 
                                                                            id_user_status = '$id_status',
                                                                            login_time = '$todayFormat',
                                                                            logout_time = '',
                                                                            jenis_perangkat = '$device',
                                                                            status_perangkat = '$online'
                                                                        WHERE id_user = '$id_user' AND jenis_perangkat = '$device'
                                                                    "); 

                                    $update_token = $connect->query("UPDATE user_token 
                                                                        SET
                                                                            token = '$encrypt_token', 
                                                                            expired_token_time = '$tokenExpiredFormat'
                                                                        WHERE id_token = '$id_token_old'
                                                                    ");
                                    
                                    $simpan_history_baru = $connect->query("INSERT INTO user_history (id_history, id_user_status, id_user, login_time, logout_time, ip_login, os, jenis_perangkat, lokasi) VALUES ('$id_history', '$id_status', '$id_user', '$todayFormat', '', '$ip', '$os', '$device', '$lokasi')");

                                    $update_history_sebelumnya = $connect->query("UPDATE user_history SET logout_time = '$todayFormat' WHERE id_user = '$id_user' AND login_time = '$login_time'");

                                    if ($update_status && $update_token && $simpan_history_baru && $update_history_sebelumnya) {
                                        // Commit transaksi
                                        $connect->commit();
                                        $sql_role = " SELECT u.id_user_role, d.id_user_role, d.role FROM user AS u 
                                                        JOIN user_role AS d ON (u.id_user_role = d.id_user_role)
                                                        WHERE u.id_user_role = '$id_role'";
                                        $query_role = mysqli_query($connect, $sql_role) or die(mysqli_error($connect));
                                        $data_role = mysqli_fetch_array($query_role);
                                        $role = $data_role['role']; 
                                    
                                        if($role == 'Finance'){
                                            header("Location: finance/dashboard.php");
                                        } else if ($role == 'Driver'){
                                            header("Location: driver/dashboard.php");
                                        } else if ($role == 'Admin Gudang'){
                                            header("Location: dashboard.php");
                                        } else if ($role == 'Operator Gudang'){
                                            header("Location: scan-qr.php");
                                        } else {
                                            header("Location: dashboard.php");
                                        }
                                    } else {
                                        // Rollback transaksi jika salah satu operasi gagal
                                        $connect->rollback();
                                        $_SESSION['alert'] = 'Terjadi kesalahan server, silahkan ulangi kembali';
                                        header("Location: login.php");
                                    } 
                                }catch (Exception $e){
                                    // Rollback transaksi jika salah satu operasi gagal
                                    $connect->rollback();
                                    $_SESSION['alert'] = 'Terjadi kesalahan server, silahkan ulangi kembali';
                                    header("Location: login.php");
                                }
                            } else {
                                $sql_role = " SELECT u.id_user_role, d.id_user_role, d.role 
                                                FROM user AS u 
                                                JOIN user_role AS d ON (u.id_user_role = d.id_user_role)
                                                WHERE u.id_user_role = '$id_role'";
                                $query_role = mysqli_query($connect, $sql_role) or die(mysqli_error($connect));
                                $data_role = mysqli_fetch_array($query_role);
                                $role = $data_role['role']; 
                                
                                if($role == 'Finance'){
                                    header("Location: finance/dashboard.php");
                                } else if ($role == 'Driver'){
                                    header("Location: driver/dashboard.php");
                                } else if ($role == 'Admin Gudang'){
                                    header("Location: dashboard.php");
                                } else if ($role == 'Operator Gudang'){
                                    header("Location: scan-qr.php");
                                } else {
                                    header("Location: dashboard.php");
                                } 
                            }
                        } else {
                            if($currentTime > $expired){
                                $connect->begin_transaction();
                                try{
                                    // Data sudah ada, lakukan UPDATE
                                    $update_status = $connect->query("UPDATE user_status 
                                                                        SET 
                                                                            id_user_status = '$id_status',
                                                                            login_time = '$todayFormat',
                                                                            logout_time = '',
                                                                            jenis_perangkat = '$device',
                                                                            status_perangkat = '$online'
                                                                        WHERE id_user = '$id_user' AND jenis_perangkat = '$device'
                                                                    "); 

                                    $update_token = $connect->query("UPDATE user_token 
                                                                        SET
                                                                            token = '$encrypt_token', 
                                                                            expired_token_time = '$tokenExpiredFormat'
                                                                        WHERE id_token = '$id_token_old'
                                                                    ");
                                    
                                    $simpan_history_baru = $connect->query("INSERT INTO user_history (id_history, id_user_status, id_user, login_time, logout_time, ip_login, os, jenis_perangkat, lokasi) VALUES ('$id_history', '$id_status', '$id_user', '$todayFormat', '', '$ip', '$os', '$device', '$lokasi')");

                                    $update_history_sebelumnya = $connect->query("UPDATE user_history SET logout_time = '$todayFormat' WHERE id_user = '$id_user' AND login_time = '$login_time'");

                                    if ($update_status && $update_token && $simpan_history_baru && $update_history_sebelumnya) {
                                        // Commit transaksi
                                        $connect->commit();
                                        $sql_role = " SELECT u.id_user_role, d.id_user_role, d.role FROM user AS u 
                                                        JOIN user_role AS d ON (u.id_user_role = d.id_user_role)
                                                        WHERE u.id_user_role = '$id_role'";
                                        $query_role = mysqli_query($connect, $sql_role) or die(mysqli_error($connect));
                                        $data_role = mysqli_fetch_array($query_role);
                                        $role = $data_role['role']; 
                                    
                                        if($role == 'Finance'){
                                            header("Location: finance/dashboard.php");
                                        } else if ($role == 'Driver'){
                                            header("Location: driver/dashboard.php");
                                        } else if ($role == 'Admin Gudang'){
                                            header("Location: dashboard.php");
                                        } else if ($role == 'Operator Gudang'){
                                            header("Location: scan-qr.php");
                                        } else {
                                            header("Location: dashboard.php");
                                        }
                                    } else {
                                        // Rollback transaksi jika salah satu operasi gagal
                                        $connect->rollback();
                                        $_SESSION['alert'] = 'Terjadi kesalahan server, silahkan ulangi kembali';
                                        header("Location: login.php");
                                    } 
                                }catch (Exception $e){
                                    // Rollback transaksi jika salah satu operasi gagal
                                    $connect->rollback();
                                    $_SESSION['alert'] = 'Terjadi kesalahan server, silahkan ulangi kembali';
                                    header("Location: login.php");
                                }
                            } else {
                                $_SESSION['alert'] = 'User sedang aktif, jika itu bukan anda silahkan hubungin IT';
                                header("Location: login.php");
                            }
                        }
                    }
                }
            } else {
                // Akun belum di approval
                $_SESSION['alert'] = 'Akun belum disetujui oleh admin';
                header("Location: login.php");
            }
           
        } else {
            // Password salah, kembali ke halaman login
            header("Location: login.php?gagal");
        }
    } else {
        // Username tidak ditemukan, kembali ke halaman login
        header("Location: login.php?belum_terdaftar");
    }
}

// Generate UUID
function generate_uuid()
{
    return sprintf(
        '%04x%04x%04x%04x',
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
// End Generate UUID 
?>