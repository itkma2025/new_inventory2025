<?php  
include "koneksi.php";
include 'assets/Qrcode/qrlib.php';
date_default_timezone_set('Asia/Jakarta');
$today = date('d/m/Y, H:i:s');

$sql_qr = $connect->query("SELECT id_link, url_qr, qr_img, created_date FROM qr_link"); 
while ($data = mysqli_fetch_array($sql_qr)){ 
    $id_link = $data['id_link'];
    $url = $data['url_qr'];
    $qr_img = $data['qr_img'];
    $server_saat_ini = $_SERVER['SERVER_NAME'];

    // Mendapatkan komponen-komponen dari URL
    $parsedUrl = parse_url($url);

    // Memisahkan domain
    if (isset($parsedUrl['scheme']) && ($parsedUrl['scheme'] === 'http' || $parsedUrl['scheme'] === 'https')) {
        $domain = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
    } else {
        $domain = $parsedUrl['host'];
    }

    $url_qr = $server_saat_ini. "/" .$parsedUrl['path']. "?" .$parsedUrl['query'];

    echo $url_qr;

    if ($server_saat_ini != $domain) {
        if ($qr_img != $new_nama_qr_img) {
            unlink("gambar/QRcode/$qr_img");

            $size = 300;
            $correctionLevel = 'M';
            $logoPath = 'assets/img/KMA.png';  
            $logoSize = 120;  

            $new_nama_qr_img = preg_replace('/[^\w]+/', '_', substr($qr_img, 0, strrpos($qr_img, '.'))) . ".png";
            $outputFile = "gambar/QRcode/$new_nama_qr_img";

            QRcode::png($url_qr, $outputFile, $correctionLevel, $size, 2);

            $QR = imagecreatefrompng($outputFile);
            $logo = imagecreatefrompng($logoPath);
            $QR_width = imagesx($QR);
            $QR_height = imagesy($QR);
            $logo_width = imagesx($logo);
            $logo_height = imagesy($logo);
            $centerX = ($QR_width - $logo_width) / 2;
            $centerY = ($QR_height - $logo_height) / 2;
            imagecopy($QR, $logo, $centerX, $centerY, 0, 0, $logo_width, $logo_height);
            imagepng($QR, $outputFile);

            imagedestroy($QR);
            imagedestroy($logo);

            $sql_update_qr = $connect->query("UPDATE qr_link SET url_qr = '$url_qr', qr_img = '$new_nama_qr_img',  updated_date = '$today' WHERE id_link = '$id_link'"); 
        }
    }
}
?>
