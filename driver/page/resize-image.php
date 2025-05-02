<?php  
    function compressAndResizeImage($source, $destination, $targetWidth, $targetHeight, $quality) {
        // Mendapatkan informasi gambar
        $info = getimagesize($source);
        
        // Mengecek tipe gambar
        if ($info['mime'] == 'image/jpeg') {
            // Membaca gambar
            $image = imagecreatefromjpeg($source);
        }
        elseif ($info['mime'] == 'image/png') {
            // Membaca gambar
            $image = imagecreatefrompng($source);
        } else {
            // Tipe gambar tidak didukung
            return false;
        }
        
        // Mendapatkan orientasi gambar
        $orientation = 1; // Default: landscape
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($source);
            if ($exif !== false && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
            }
        }
        
        // Menghitung ukuran gambar yang akan diubah
        $width = imagesx($image);
        $height = imagesy($image);
        
        // Menghitung aspek rasio gambar asli
        $aspectRatio = $width / $height;
        
        // Menghitung aspek rasio target
        $targetAspectRatio = $targetWidth / $targetHeight;
        
        // Menentukan bagaimana gambar akan diubah
        if ($aspectRatio >= $targetAspectRatio) {
            // Gambar asli lebih lebar daripada target, maka lebar akan diubah ke targetWidth
            $newWidth = $targetWidth;
            $newHeight = $targetWidth / $aspectRatio;
        } else {
            // Gambar asli lebih tinggi daripada target, maka tinggi akan diubah ke targetHeight
            $newHeight = $targetHeight;
            $newWidth = $targetHeight * $aspectRatio;
        }
        
        // Membuat gambar baru dengan ukuran yang diubah
        $newImage = imagecreatetruecolor($targetWidth, $targetHeight);
        
        // Mengubah mode blending gambar baru untuk menghindari kotak hitam
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $transparent);
        
        // Mengompres dan menyalin gambar ke gambar baru
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        
        // Memutar gambar sesuai orientasi asli
        switch ($orientation) {
            case 3:
                $newImage = imagerotate($newImage, 180, 0);
                break;
            case 6:
                $newImage = imagerotate($newImage, -90, 0);
                break;
            case 8:
                $newImage = imagerotate($newImage, 90, 0);
                break;
        }
        
        // Menyimpan gambar dengan kualitas tertentu
        if ($info['mime'] == 'image/jpeg') {
            imagejpeg($newImage, $destination, $quality);
        } elseif ($info['mime'] == 'image/png') {
            imagepng($newImage, $destination, round(9 - ($quality / 100) * 9));
        }
        
        // Menghapus gambar dari memori
        imagedestroy($image);
        imagedestroy($newImage);
        
        return true;
    }
?>