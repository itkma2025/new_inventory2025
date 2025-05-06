<?php  
function processFileUpload($source, $destination, $targetWidth, $targetHeight, $quality) {
    // Mendapatkan informasi file
    $info = pathinfo($source);

    // Mengecek tipe file berdasarkan ekstensi
    $extension = strtolower($info['extension']);

    if ($extension === 'pdf') {
        // Jika file adalah PDF, langsung pindahkan tanpa kompresi
        if (move_uploaded_file($source, $destination)) {
            return "File PDF berhasil diunggah tanpa kompresi.";
        } else {
            return "Gagal mengunggah file PDF.";
        }
    } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
        // Jika file adalah gambar, lakukan kompresi dan resize
        return compressAndResizeImage($source, $destination, $targetWidth, $targetHeight, $quality) 
            ? "Gambar berhasil diunggah dan dikompresi."
            : "Gagal mengunggah atau mengompresi gambar.";
    } else {
        // Jika tipe file tidak didukung
        return "Tipe file tidak didukung.";
    }
}

function compressAndResizeImage($source, $destination, $targetWidth, $targetHeight, $quality) {
    // Mendapatkan informasi gambar
    $info = getimagesize($source);
    
    // Mengecek tipe gambar
    if ($info['mime'] == 'image/jpeg') {
        // Membaca gambar
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] == 'image/png') {
        // Membaca gambar
        $image = imagecreatefrompng($source);
    } else {
        // Tipe gambar tidak didukung
        return false;
    }
    
    // Menghitung ukuran gambar yang akan diubah
    $width = imagesx($image);
    $height = imagesy($image);
    $aspectRatio = $width / $height;
    $targetAspectRatio = $targetWidth / $targetHeight;
    
    if ($aspectRatio >= $targetAspectRatio) {
        $newWidth = $targetWidth;
        $newHeight = $targetWidth / $aspectRatio;
    } else {
        $newHeight = $targetHeight;
        $newWidth = $targetHeight * $aspectRatio;
    }
    
    $newImage = imagecreatetruecolor($targetWidth, $targetHeight);
    imagealphablending($newImage, false);
    imagesavealpha($newImage, true);
    $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
    imagefill($newImage, 0, 0, $transparent);
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
    
    if ($info['mime'] == 'image/jpeg') {
        imagejpeg($newImage, $destination, $quality);
    } elseif ($info['mime'] == 'image/png') {
        imagepng($newImage, $destination, round(9 - ($quality / 100) * 9));
    }
    
    imagedestroy($image);
    imagedestroy($newImage);
    
    return true;
}
?>
