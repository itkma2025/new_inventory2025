<?php  
     header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
     header("Pragma: no-cache"); // HTTP 1.0.
     header("Expires: 0"); // Proxies.

    // Hubungkan Koneksi
    require_once "../akses.php";

    // Penghubung Library
    require_once '../assets/vendor/autoload.php';

    // Function Encrypt dan Decrypt
    require_once "../function/function-enkripsi.php";

    // Hubungkan Koneksi
    require_once "../akses.php";

    // Penghubung Library
    require_once '../assets/vendor/autoload.php';

    // Library sanitasi input data
    require_once "../function/sanitasi_input.php";

    $updated_by = base64_decode($_SESSION['tiket_id']);
    $datetime_now = date('Y-m-d H:i:s');
    // Sanitasi seluruh $_POST
    $sanitasi_post = sanitizeInput($_POST);
    if (isset($sanitasi_post['id_spk'])) {
        $id_spk = decrypt($sanitasi_post['id_spk'], $key_global);
        $update = $connect->query("UPDATE transaksi_produk_reg 
                                        SET 
                                            disc_cb = 0, 
                                            updated_date = '$datetime_now',
                                            updated_by = '$updated_by'
                                        WHERE id_spk IN ($id_spk)");
    }
?>