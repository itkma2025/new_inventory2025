<?php  
    include "../akses.php";
    // Penghubung Library
    require_once '../assets/vendor/autoload.php';
    // Library Tangal
    use Carbon\Carbon;
    $datetime_now = Carbon::now();

    // Library Debugging
    use Whoops\Run;
    use Whoops\Handler\PrettyPageHandler;
    // Inisialisasi Whoops
    // Atur status aktif/non-aktif Whoops
    $whoops_enabled = true; // Ubah menjadi false untuk menonaktifkan

    if ($whoops_enabled) {
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();
    }
    // Library sanitasi input data
    require_once "../function/sanitasi_input.php";

    // Function Encrypt dan Decrypt
    require_once "../function/function-enkripsi.php";

    if(isset($_POST['ubah-driver'])){ 
        $sanitasi_post = sanitizeInput($_POST);

        // Validasi nonce token
        if (!isset($sanitasi_post['nonce_token']) || $sanitasi_post['nonce_token'] !== $_SESSION['nonce_token']) {
            // Jika token tidak cocok atau tidak ada, hentikan proses dan tampilkan error
            $_SESSION['info'] = "Silahkan Ulangi Kembali";
            header("Location:../spk-siap-kirim.php?sort=baru");
            exit();
        }
        // // Nonce sudah divalidasi, sekarang hapus dari session agar tidak bisa digunakan lagi
        unset($_SESSION['nonce_token']);

        $id_inv = $sanitasi_post['id_inv'];
        $id_inv_decrypt = decrypt($id_inv, $key_global);
        $jenis_inv = $sanitasi_post['jenis_inv'];
        $pengirim = $sanitasi_post['pengirim'];
        $id_inv_substr = $id_inv;
     
        $stmt = $connect->prepare("UPDATE status_kirim SET dikirim_driver = ? WHERE id_inv = ?");
        $stmt->bind_param("ss", $pengirim, $id_inv_decrypt);
        $update_status_kirim = $stmt->execute();
        
        if ($update_status_kirim){
            $_SESSION['info'] = "Disimpan";
            header("Location:../detail-produk-dikirim.php?jenis=$jenis_inv&&id=$id_inv");
        } else {
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../detail-produk-dikirim.php?jenis=$jenis_inv&&id=$id_inv");
        }
    } else {
        header("Location:../404.php");
    }
?>