<?php 
    // Enkripsi file
    function encryptFile($filePath, $encryptionKey) {
        // Membaca konten file
        $fileContent = file_get_contents($filePath);
    
        // IV untuk enkripsi
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    
        // Mengenkripsi konten file
        $encryptedContent = openssl_encrypt($fileContent, 'aes-256-cbc', $encryptionKey, 0, $iv);
        if ($encryptedContent === false) {
            return false;
        }
    
        // Menggabungkan IV dan konten terenkripsi
        return $iv . $encryptedContent;
    }    

    // Dekripsi file
    function decryptFile($encryptedFilePath, $encryptionKey) {
        // Membaca konten file yang dienkripsi
        $encryptedFileContent = file_get_contents($encryptedFilePath);
        if ($encryptedFileContent === false) {
            return false;
        }
    
        // Mendapatkan panjang IV
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    
        // Memisahkan IV dan konten terenkripsi
        $iv = substr($encryptedFileContent, 0, $ivLength);
        $encryptedContent = substr($encryptedFileContent, $ivLength);
    
        // Mendekripsi konten file
        $decryptedContent = openssl_decrypt($encryptedContent, 'aes-256-cbc', $encryptionKey, 0, $iv);
        if ($decryptedContent === false) {
            return false;
        }
    
        return $decryptedContent;
    }

    // Get content type
    function getContentType($fileExtension) {
        switch ($fileExtension) {
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'pdf':
                return 'application/pdf';
            default:
                return false;
        }
    }

    // Kunci enkripsi
    $fileKey = 'K@rs@2024?'; // Kunci enkripsi harus 16, 24, atau 32 bytes

    // Cara penggunaan decrypt
    // $encryptedFileName = 'hasil-upload/'. $_GET['file'];

    // $decryptedContent = decryptFile($encryptedFileName, $fileKey);
    // if ($decryptedContent === false) {
    //     die('Gagal mendekripsi file');
    // }

    // // Menentukan jenis konten berdasarkan ekstensi file
    // $fileInfo = pathinfo($encryptedFileName);
    // $fileExtension = strtolower($fileInfo['extension']);
    // $contentType = getContentType($fileExtension);

    // if ($contentType === false) {
    //     die('Jenis file tidak didukung');
    // }

    // // Mengirim header yang sesuai untuk menampilkan file di browser
    // header('Content-Type: ' . $contentType);
    // echo $decryptedContent;
?>