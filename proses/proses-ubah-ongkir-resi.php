<?php
    require_once "../akses.php";
    require_once "../page/resize-image.php";
    // Penghubung Library
    require_once '../assets/vendor/autoload.php'; 

    $datetime_now = date('Y-m-d H:i:s');

    $id_user = decrypt($_SESSION['tiket_id'], $key_global);

    // Library Debugging
    use Whoops\Run;
    use Whoops\Handler\PrettyPageHandler;

    // Atur status aktif/non-aktif Whoops
    $whoops_enabled = false; // Ubah menjadi false untuk menonaktifkan

    if ($whoops_enabled) {
        // Inisialisasi Whoops
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();
    }
    // Library sanitasi input data
    require_once "../function/sanitasi_input.php";

    // Function Encrypt dan Decrypt
    require_once "../function/function-enkripsi.php";

    // Function Encrypt dan Decrypt
    require_once "../function/uuid.php";

    if (isset($_POST['ubah-ongkir-nonppn']) || isset($_POST['ubah-ongkir-ppn']) || isset($_POST['ubah-ongkir-bum'])) {
        $sanitasi_post = sanitizeInput($_POST);
    
        // Validasi nonce token
        if (!isset($sanitasi_post['nonce_token']) || $sanitasi_post['nonce_token'] !== $_SESSION['nonce_token']) {
            $_SESSION['info'] = "Silahkan Ulangi Kembali";
            header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
        }
        unset($_SESSION['nonce_token']);
    
        // Tentukan jenis tabel berdasarkan tombol yang ditekan
        if (isset($_POST['ubah-ongkir-nonppn'])) {
            $tipe_inv = 'nonppn';
        } elseif (isset($_POST['ubah-ongkir-ppn'])) {
            $tipe_inv = 'ppn';
        } else {
            $tipe_inv = 'bum';
        }
        $nama_tabel = "inv_$tipe_inv";
    
        // Ambil data umum
        $id_inv = $sanitasi_post['id_inv'];
        $id_inv_decrypt = decrypt($id_inv, $key_global);
        $ekspedisi = $sanitasi_post['ekspedisi'] ?? $sanitasi_post['ekspedisi_reupload'];
        $no_resi = $sanitasi_post['edit_resi'];
        $jenis_ongkir = $sanitasi_post['jenis_ongkir_edit'] ?? $sanitasi_post['jenis_ongkir_reupload'];
        $ongkir = intval(str_replace('.', '', $sanitasi_post['edit_ongkir']));
        $free_ongkir = $sanitasi_post['free_ongkir'] ?? '0';
    
        $uuid = uuid();
        $img_uuid = img_uuid();
        $year = date('y');
        $day = date('d');
        $month = date('m');
        $id_bukti_terima = "BKTI{$year}{$uuid}{$day}";
    
        // File
        $file1_name = basename($_FILES['fileku1']['name']);
        $file1_tmp = $_FILES['fileku1']['tmp_name'];
        $file1_destination = "../gambar/bukti1/" . $file1_name;
    
        $ongkir_cb = $free_ongkir === '1' ? $ongkir : '0';
        $ongkir_non_cb = $free_ongkir === '0' ? $ongkir : '0';
    
        if (!in_array($free_ongkir, ['0', '1'])) {
            header("Location: ../404.php");
            exit();
        }
    
        try {
            $connect->begin_transaction();
    
            // Pindahkan dan kompres gambar
            move_uploaded_file($file1_tmp, $file1_destination);
            $new_file1_name = '';
            if ($file1_name != '') {
                $new_file1_name = "Bukti_Satu{$year}{$month}{$img_uuid}{$day}.jpg";
                $compressed_file1_destination = "../gambar/bukti1/$new_file1_name";
                compressAndResizeImage($file1_destination, $compressed_file1_destination, 500, 500, 100);
                unlink($file1_destination);
            }
    
            // Cek dan update/insert bukti terima
            $cek_bukti_terima = mysqli_query($connect, "SELECT id_inv FROM inv_bukti_terima WHERE id_inv = '$id_inv_decrypt'");
            $lokasi = "PT. Karsa Mandiri Alkesindo";
    
            if ($cek_bukti_terima->num_rows > 0) {
                $stmt = $connect->prepare("UPDATE inv_bukti_terima SET bukti_satu = ?, lokasi = ?, approval = '0', created_date = ?, created_by = ? WHERE id_inv = ?");
                $stmt->bind_param("sssss", $new_file1_name, $lokasi, $datetime_now, $id_user, $id_inv_decrypt);
            } else {
                $stmt = $connect->prepare("INSERT INTO inv_bukti_terima (id_bukti_terima, id_inv, bukti_satu, lokasi, created_date, created_by) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $id_bukti_terima, $id_inv_decrypt, $new_file1_name, $lokasi, $datetime_now, $id_user);
            }
            if (!$stmt->execute()) {
                throw new Exception("Simpan/Update Bukti Terima Gagal: " . $stmt->error);
            }
    
            // Update status_kirim
            $stmt = $connect->prepare("UPDATE status_kirim SET dikirim_ekspedisi = ?, no_resi = ?, jenis_ongkir = ?, status_review = '0' WHERE id_inv = ?");
            $stmt->bind_param("ssss", $ekspedisi, $no_resi, $jenis_ongkir, $id_inv_decrypt);
            if (!$stmt->execute()) {
                throw new Exception("Update Status Kirim Gagal: " . $stmt->error);
            }
    
            // Update ongkir berdasarkan jenis invoice
            $kolom_id = $tipe_inv === 'nonppn' ? 'id_inv_nonppn' : ($tipe_inv === 'ppn' ? 'id_inv_ppn' : 'id_inv_bum');
            $stmt = $connect->prepare("UPDATE $nama_tabel SET ongkir = ?, ongkir_free = ?, free_ongkir = ? WHERE $kolom_id = ?");
            $stmt->bind_param("ssss", $ongkir_non_cb, $ongkir_cb, $free_ongkir, $id_inv_decrypt);
            if (!$stmt->execute()) {
                throw new Exception("Update Ongkir Gagal: " . $stmt->error);
            }
    
            $connect->commit();
            $_SESSION['info'] = "Disimpan";
        } catch (Exception $e) {
            $connect->rollback();
            $_SESSION['info'] = "Data Gagal Disimpan";
        }
    
        header("Location: {$_SERVER['HTTP_REFERER']}");
    }
    
?>