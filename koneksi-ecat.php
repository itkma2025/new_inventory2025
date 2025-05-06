<?php
	$server_ecat = "localhost";//nama server
	$user_ecat = "root";//usernya
	$password_ecat = "";//password
	$db_ecat = "test_ecat";//database
    $port_ecat = 3310;    // Tambahkan port MySQL yang sesuai
	

    // $server_ecat = "anzio-db.id.domainesia.com";//nama server	
    // $user_ecat = "mandir36_test_ecat_2025";//usernya
    // $password_ecat = "test-ecat-2025";//password
    // $db_ecat = "mandir36_test_ecat2025";//database


	// Koneksi dan memilih database di server
	$connect_ecat = new mysqli($server_ecat, $user_ecat, $password_ecat, $db_ecat, $port_ecat);

	if (!$connect_ecat) {
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