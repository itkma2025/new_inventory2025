<?php
	$server = "localhost";//nama server
	$user = "root";//usernya
	$password = "";//password
	$db = "db_inventory";//database

	// Koneksi dan memilih database di server
	$connect = mysqli_connect($server,$user,$password,$db);

	if (!$connect) {
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