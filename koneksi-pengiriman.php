<?php
	$server_pengiriman = "localhost";//nama server
	$user_pengiriman = "root";//usernya
	$password_pengiriman = "";//password
	$db_pengiriman = "test_pengiriman";//database
    $port_pengiriman = 3310;    // Tambahkan port MySQL yang sesuai
	

    // $server_pengiriman = "anzio-db.id.domainesia.com";//nama server	
    // $user_pengiriman = "mandir36_test_pengiriman_2025";//usernya
    // $password_pengiriman = "test-pengiriman-2025";//password
    // $db_pengiriman = "mandir36_test_pengiriman2025";//database


	// Koneksi dan memilih database di server
	$connect_pengiriman = new mysqli($server_pengiriman, $user_pengiriman, $password_pengiriman, $db_pengiriman, $port_pengiriman);

	if (!$connect_pengiriman) {
		die("Koneksi gagal: " . mysqli_connect_error());
	}
		// echo "Koneksi berhasil";
	    // mysqli_close($connect);

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