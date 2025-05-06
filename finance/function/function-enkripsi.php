<?php
	// fungsi untuk melakukan enkripsi data dengan AES
	function encrypt($plaintext, $key) {
	    // menghasilkan vektor inisialisasi yang acak
	    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
	    $iv = openssl_random_pseudo_bytes($ivlen);

	    // melakukan enkripsi data dengan AES-128-CBC
	    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);

	    // menggabungkan vektor inisialisasi dan hasil enkripsi ke dalam bentuk yang dapat disimpan
	    $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
	    $ciphertext = base64_encode( $iv.$hmac.$ciphertext_raw );
	    return $ciphertext;
	}

	// fungsi untuk melakukan dekripsi data dengan AES
	function decrypt($ciphertext, $key) {
	    // mendekode data yang telah dienkripsi ke dalam bentuk vektor inisialisasi, HMAC, dan hasil enkripsi
	    $c = base64_decode($ciphertext);
	    $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
	    $iv = substr($c, 0, $ivlen);
	    $hmac = substr($c, $ivlen, $sha2len=32);
	    $ciphertext_raw = substr($c, $ivlen+$sha2len);
	    
	    // memeriksa keaslian data menggunakan HMAC
	    $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
	    if (!hash_equals($hmac, $calcmac)) {
	        return null;
	    }

	    // melakukan dekripsi data dengan AES-128-CBC
	    $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
	    return $plaintext;
	}

	// Contoh penggunaan
	// include "../function/function-enkripsi.php";

	// enkripsi
	// $key = "IT@Support";
    // $enkripsi_atas_nama = encrypt($atas_nama, $key);

	// Dekripsi
	// $atas_nama = $data_bank['atas_nama'];
    // $dekripsi_atas_nama = decrypt($atas_nama, $key);
?>