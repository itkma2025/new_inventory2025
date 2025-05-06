<?php
    require_once "../akses.php";
    require_once "../page/resize-image.php";
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);

    // Penghubung Library 
    require_once '../assets/vendor/autoload.php';

    $datetime = date('Y-m-d H:i:s');

    // Library Debugging
    use Whoops\Run;
    use Whoops\Handler\PrettyPageHandler;
    // Inisialisasi Whoops
    // Atur status aktif/non-aktif Whoops
    $whoops_enabled = false; // Ubah menjadi false untuk menonaktifkan

    if ($whoops_enabled) {
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();
    }
    // Library sanitasi input data
    require_once "../function/sanitasi_input.php";

    // Function Encrypt dan Decrypt
    require_once "../function/function-enkripsi.php";

    // Function UUID 
    require_once "../function/uuid.php";

    $uuid = uuid();
    $img_uuid = img_uuid();
    $year = date('y');
    $day = date('d');
    $month = date('m');
    $id_bukti_terima = "BKTI" . $year . "" . $uuid . "" . $day;

    if (isset($_POST['diambil-oleh'])) {
        $sanitasi_post = sanitizeInput($_POST);
        $id_inv = $sanitasi_post['id_inv'];
        $id_inv_decrypt = decrypt($id_inv, $key_global);
        // Validasi nonce token
        if (!isset($sanitasi_post['nonce_token']) || $sanitasi_post['nonce_token'] !== $_SESSION['nonce_token']) {
            // Jika token tidak cocok atau tidak ada, hentikan proses dan tampilkan error
            $_SESSION['info'] = "Silahkan Ulangi Kembali";
            header("Location:../detail-produk-dikirim.php?jenis=ppn&&id=$id_inv");
        }
        // Nonce sudah divalidasi, sekarang hapus dari session agar tidak bisa digunakan lagi
        unset($_SESSION['nonce_token']);


        $diambil_oleh = $sanitasi_post['diambil_oleh'];
        $diambil_tanggal = $sanitasi_post['diambil_tanggal'];
        // Mendapatkan informasi file bukti terima 1
        $file1_name = $_FILES['fileku1']['name'];
        $file1_tmp = $_FILES['fileku1']['tmp_name'];
        $file1_destination = "../gambar/bukti1/" . $file1_name;
        try {
            $connect->begin_transaction();
            
            //Pindahkan file bukti terima ke lokasi tujuan
            move_uploaded_file($file1_tmp, $file1_destination);

            if ($file1_name != '') {
                // Cek ukuran file
                $file_size = filesize($file1_destination); // Ambil ukuran file dalam byte

                // Tentukan kualitas kompresi berdasarkan ukuran file
                $quality = ($file_size > 2097152) ? 85 : 100; // Jika lebih dari 2MB, kompres 85%, jika tidak, tetap 100%

                // Kompres gambar bukti terima 1 tanpa mengubah ukuran asli
                $new_file1_name = "Bukti_Satu" . $year . "" . $month . "" . $img_uuid . "" . $day . ".jpg";
                $compressed_file1_destination = "../gambar/bukti1/$new_file1_name";

                compressAndResizeImage($file1_destination, $compressed_file1_destination, $quality);

                // Hapus file asli setelah dikompresi
                unlink($file1_destination);
            }

            // Cek Bukti terima 
            $cek_bukti_terima = mysqli_query($connect, "SELECT id_inv, bukti_satu FROM inv_bukti_terima WHERE id_inv = '$id_inv_decrypt'");
            $lokasi = "PT. Karsa Mandiri Alkesindo";
            if ($cek_bukti_terima->num_rows > 0) {
                $cek_data_bukti = mysqli_fetch_array($cek_bukti_terima);
                $stmt = $connect->prepare("UPDATE inv_bukti_terima SET bukti_satu = ?, lokasi = ?, approval = '0', created_date = ?, created_by = ? WHERE id_inv = ?");
                $stmt->bind_param("sssss", $new_file1_name, $lokasi, $datetime, $id_user, $id_inv_decrypt);
                $update_bukti_terima = $stmt->execute();

                if (!$update_bukti_terima) {
                    throw new Exception("Update Bukti Terima Gagal: " . $stmt->error);
                }
            } else if ($cek_bukti_terima->num_rows == 0){
                $stmt = $connect->prepare("INSERT INTO inv_bukti_terima (id_bukti_terima, id_inv, bukti_satu, lokasi, approval, created_date, created_by ) VALUES (?, ?, ?, ?, '0', ?, ?)");
                $stmt->bind_param("ssssss", $id_bukti_terima, $id_inv_decrypt, $new_file1_name, $lokasi, $datetime, $id_user);
                $simpan_bukti_terima = $stmt->execute();

                if (!$simpan_bukti_terima) {
                    throw new Exception("Update Bukti Terima Gagal: " . $stmt->error); 
                }
            } else {
                header("Location:../404.php");
            }
            // Proses update invoice penerima
            $stmt = $connect->prepare("UPDATE inv_penerima SET nama_penerima = ?, tgl_terima = ? WHERE id_inv = ?");
            $stmt->bind_param("sss", $diambil_oleh, $diambil_tanggal, $id_inv_decrypt);
            $update_inv_penerima = $stmt->execute();

            if (!$update_inv_penerima) {
                throw new Exception("Update Bukti Terima Gagal: " . $stmt->error);
            }

            // Proses update invoice penerima
            $stmt = $connect->prepare("UPDATE inv_ppn SET status_transaksi = 'Diterima' WHERE id_inv_ppn = ?");
            $stmt->bind_param("s", $id_inv_decrypt);
            $update_inv_ppn = $stmt->execute();

            if (!$update_inv_ppn) {
                throw new Exception("Update Bukti Terima Gagal: " . $stmt->error);
            }

            // Proses update status kirim
            $stmt = $connect->prepare("UPDATE status_kirim SET jenis_penerima = 'Customer', status_review = '0' WHERE id_inv = ?");
            $stmt->bind_param("s", $id_inv_decrypt);
            $update_status_kirim = $stmt->execute();

            if (!$update_status_kirim) {
                throw new Exception("Update Bukti Terima Gagal: " . $stmt->error);
            }

            $connect->commit();
            $_SESSION['info'] = "Disimpan";
            header("Location: {$_SERVER['HTTP_REFERER']}");
        } catch (Exception $e) {
            $connect->rollback();
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location: {$_SERVER['HTTP_REFERER']}");  
        }
    }
?>

