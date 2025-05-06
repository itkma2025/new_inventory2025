<?php
session_start(); // Start the session
include "koneksi.php";
date_default_timezone_set('Asia/Jakarta');

function deriveKey($key, $salt) {
    return hash_pbkdf2('sha256', $key, $salt, 100000, 32, true); // 32 bytes untuk AES-256
}

function encrypt($data, $key) {
    $method = "AES-256-CBC";
    $salt = random_bytes(16); // Salt untuk KDF
    $key = deriveKey($key, $salt);
    $iv = random_bytes(openssl_cipher_iv_length($method));

    $timestamp = time(); // Timestamp untuk mencegah replay attack
    $nonce = bin2hex(random_bytes(8)); // Nonce unik untuk setiap enkripsi

    $payload = json_encode([
        'data' => $data,
        'timestamp' => $timestamp,
        'nonce' => $nonce
    ]);

    $ciphertext = openssl_encrypt($payload, $method, $key, OPENSSL_RAW_DATA, $iv);
    if ($ciphertext === false) {
        error_log("[ENCRYPTION ERROR] Gagal mengenkripsi data.");
        return false;
    }

    // HMAC untuk autentikasi data
    $hmac = hash_hmac('sha256', $iv . $ciphertext, $key, true);

    // Gabungkan salt, IV, HMAC, dan ciphertext
    $encryptedData = $salt . $iv . $hmac . $ciphertext;

    return base64_encode($encryptedData);
}

// Generate UUID function
function generate_uuid() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Encryption key (replace with a more secure key)
$key = '7x4b8c0d5q6f2g1h9i0l1p6l8m4b5o3v';
$jwtToken = $_COOKIE['jwt_token_test'] ?? '';

if ($jwtToken) {
    $jwtToken = $koneksi2->real_escape_string($jwtToken);
    
    // Mulai transaksi
    $koneksi2->begin_transaction();

    try {
        // Get id_token from user_token table
        $query = "SELECT id_token FROM user_token WHERE token = '$jwtToken'";
        $result = $koneksi2->query($query);
        $token_data = $result->fetch_assoc();

        if ($token_data) {
            $id_token = $token_data['id_token'];
            
            // Get user_status data by id_token
            $query = "SELECT us.*, u.id_user
                        FROM user_status us
                        LEFT JOIN user_akses ua ON us.id_user_akses = ua.id_user_akses
                        LEFT JOIN user u ON ua.id_user = u.id_user
                        WHERE us.id_token = '$id_token' AND us.status_klik_active > 0";
            $result = $koneksi2->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id_history = generate_uuid();
                    $id_user_akses = $row['id_user_akses'];
                    $id_user = $row['id_user'];
                    $login_time = $row['login_time'];
                    $ip_login = $row['ip_login'];
                    $jenis_os = $row['jenis_os'];
                    $jenis_perangkat = $row['jenis_perangkat'];
                    $lokasi_user = $row['lokasi_user'];

                    // Insert ke user_history
                    $insert_query = "INSERT INTO user_history (id_history, id_user_akses, id_user, login_time, logout_time, ip_login, jenis_os, jenis_perangkat, lokasi_user) 
                                        VALUES ('$id_history', '$id_user_akses', '$id_user', '$login_time', NOW(), '$ip_login', '$jenis_os', '$jenis_perangkat', '$lokasi_user')";
                    if (!$koneksi2->query($insert_query)) {
                        throw new Exception("Insert into user_history failed");
                    }
                }

                // Delete dari user_token dan user_status jika semua berhasil
                $delete_user_token = "DELETE FROM user_token WHERE id_token = '$id_token'";
                $delete_user_status = "DELETE FROM user_status WHERE id_token = '$id_token'";
                
                if (!$koneksi2->query($delete_user_token) || !$koneksi2->query($delete_user_status)) {
                    throw new Exception("Delete from user_token or user_status failed");
                }
            }
        }

        // Commit transaksi
        $koneksi2->commit();
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $koneksi2->rollback();

        // Tampilkan alert menggunakan SweetAlert
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Proses Gagal',
                text: 'Terjadi kesalahan saat logout: {$e->getMessage()}'
            }).then(() => {
                window.location.href = 'index.php'; 
            });
        </script>";
        exit;
    }
}


// Clear session data and destroy the session
session_unset(); 
session_destroy(); 

setcookie('jwt_token_test', '', [
    'expires' => time() - 3600,
    'path' => '/',
    'domain' => 'localhosts',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// setcookie('logintoken', '', [
//     'expires' => time() - 3600,
//     'path' => '/',
//     'domain' => 'test-sso.mandirialkesindo.co.id',
//     'secure' => true,
//     'httponly' => true,
//     'samesite' => 'Lax'
// ]);

// Redirect to login page with encrypted URL
$url = 'https://localhost/test-inventory';
$encrypted_url = encrypt($url, $key);
// Encode the encrypted URL to prevent spaces or other special characters
$encoded_url = urlencode($encrypted_url);
header("Location: https://localhost/test-sso?url=$encoded_url");
exit;
?>