<?php
session_start(); // Start the session
include "../koneksi.php";
date_default_timezone_set('Asia/Jakarta');

// Function to encrypt data using AES
function encrypt($data, $key) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

// Encryption key (replace with a more secure key)
$key = '1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p';
$jwtToken = $_COOKIE['jwt_token'] ?? '';

if ($jwtToken) {
    // Escape the token to protect against SQL Injection
    $jwtToken = $koneksi2->real_escape_string($jwtToken);
    
    // Get id_token from user_token table
    $query = "SELECT id_token FROM user_token WHERE token = '$jwtToken'";
    $result = $koneksi2->query($query);
    $token_data = $result->fetch_assoc();

    if ($token_data) {
        $id_token = $token_data['id_token'];
        
        // Get id_user_status from user_status table
        $query = "SELECT id_user_status, id_history FROM user_status WHERE id_token = '$id_token'";
        $result = $koneksi2->query($query);
        $status_data = $result->fetch_assoc();

        if ($status_data) {
            $id_user_status = $status_data['id_user_status'];
            $id_history = $status_data['id_history'];
            
            // Delete data from user_token table
            $query = "DELETE FROM user_token WHERE id_token = '$id_token'";
            $result = $koneksi2->query($query);

            if ($result) {
                // Update status_perangkat in user_status table
                $query = "UPDATE user_status 
                          SET status_perangkat = 'Offline', logout_time = NOW(), id_token = NULL, id_history = NULL 
                          WHERE id_user_status = '$id_user_status'";
                $koneksi2->query($query);

                // Update logout_time in user_history table
                $query = "UPDATE user_history 
                          SET logout_time = NOW() 
                          WHERE id_history = '$id_history'";
                $koneksi2->query($query);
            }
        }
    }
}


// Clear session data and destroy the session
session_unset(); 
session_destroy(); 

// Delete the jwt_token cookie
setcookie('jwt_token', '', time() - 3600, '/'); // Set the cookie expiration time to one hour ago

// Mendapatkan protokol (http atau https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

// Mendapatkan path URI
$uri = $_SERVER['REQUEST_URI'];

// Mendapatkan nama host
$host = $_SERVER['HTTP_HOST'];

// Menggabungkan semuanya untuk membentuk URL lengkap
// untuk localhost
// $url = $protocol . "://" . $host . "/test-inventory";

// untuk hosting
$url = $protocol . "://" . $host;
$encrypted_url = encrypt($url, $key);
// Encode the encrypted URL to prevent spaces or other special characters
$encoded_url = urlencode($encrypted_url);

// untuk localhost
// header("Location: https://localhost/staging-sso?url=$encoded_url");

// Untuk hosting
header("Location: https://system-sso.mandirialkesindo.co.id?url=$encoded_url");

exit;
?>
