<?php
	$server = "localhost";//nama server
	$user = "root";//usernya
	$password = "";//password
	$db = "test_inventory";//database
    $port = 3310;    // Tambahkan port MySQL yang sesuai
	
	
	
	// Koneksi dan memilih database di server
	$connect = new mysqli($server, $user, $password, $db, $port);

	if (!$connect) {
		die("Koneksi gagal: " . mysqli_connect_error());
	}
	// echo "Koneksi berhasil";
	// mysqli_close($connect);

	$host2 = "localhost";//nama server
	$username2 = "root";//usernya
	$password2 = "";//password
	$database2 = "test_user";//database
    $port2 = 3310;    // Tambahkan port MySQL yang sesuai
	
	
    
    $koneksi2 = new mysqli($host2, $username2, $password2, $database2, $port);

    // Cek koneksi ke database kedua
    if (!$koneksi2) {
        die("Koneksi ke database kedua gagal: " . mysqli_connect_error());
    }
	
	// Membuat cache untuk script ini dengan OpCache
    if (function_exists('opcache_invalidate')) {
        opcache_invalidate(__FILE__);
    }
    
    // Melakukan reset cache OpCache setiap 10 detik
    if (function_exists('opcache_reset')) {
        if (time() % 10 == 0) {
            opcache_reset();
        }
    }
?>