<?php
	
	// Fungsi untuk mengenkripsi plaintext
	function encrypt($plaintext, $key) {
		// Hasilkan kunci MD5
		$key_md5 = md5($key);
		$combined_key = $key_md5 . "k@rs@2024php";

		// Proses enkripsi XOR
		$result = '';
		for ($i = 0; $i < strlen($plaintext); $i++) {
			$result .= chr(ord($plaintext[$i]) ^ ord($combined_key[$i % strlen($combined_key)]));
		}

		// Encode hasil XOR ke Base64 dan hilangkan spasi
		return str_replace(' ', '', base64_encode($result));
	}

	// Fungsi untuk mendekripsi ciphertext
	function decrypt($ciphertext, $key) {
		// Hasilkan kunci MD5
		$key_md5 = md5($key);
		$combined_key = $key_md5 . "k@rs@2024php";

		// Decode ciphertext dari Base64
		$decoded_ciphertext = base64_decode($ciphertext);
		if ($decoded_ciphertext === false) {
			return "Error: Invalid ciphertext format";
		}

		// Proses dekripsi XOR
		$result = '';
		for ($i = 0; $i < strlen($decoded_ciphertext); $i++) {
			$result .= chr(ord($decoded_ciphertext[$i]) ^ ord($combined_key[$i % strlen($combined_key)]));
		}

		return $result;
	}
	
	

	$key_finance = "Fin@nce2024KM@";
	$key_spk = "Spk2024KM@";
	$key_gudang = "Gud@ng2024KM@";
	$key_global = "K@rs@2o24"

	//Contoh penggunaan
	// $plaintext = "Ini adalah teks yang akan dienkripsi";
	// $key = "123"; // Gunakan teks sebagai kunci

	// // Enkripsi teks
	// $ciphertext = encrypt($plaintext, $key);
	// echo "Ciphertext: " . $ciphertext . "<br>";

	// // Dekripsi teks
	// $decrypted_text = decrypt($ciphertext, $key);
	// echo "Plaintext setelah didekripsi: " . $decrypted_text . "<br>";

?>