<?php  
    function compressAndResizeImage($source, $destination, $quality) {
        $info = getimagesize($source);
        
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        } else {
            return false;
        }
    
        // Resize agar lebih kecil (misalnya 80% dari ukuran asli)
        $width = imagesx($image);
        $height = imagesy($image);
        $newWidth = intval($width * 0.8);
        $newHeight = intval($height * 0.8);
        
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
        // Simpan gambar dengan pengaturan kompresi lebih baik
        if ($info['mime'] == 'image/jpeg') {
            imagejpeg($newImage, $destination, min($quality, 90)); // Jangan lebih dari 90%
        } elseif ($info['mime'] == 'image/png') {
            $pngQuality = ($quality >= 90) ? 6 : 9;
            imagepng($newImage, $destination, $pngQuality);
        }
    
        imagedestroy($image);
        imagedestroy($newImage);
    
        return true;
    }
    
    
?>