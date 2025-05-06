<?php  
    require_once "../../akses.php";
    require_once "../page/resize-image.php";
    require_once "../../function/uuid.php";
    
    // Penghubung Library
    require_once '../../assets/vendor/autoload.php';
    
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
    // Sanitasi input
    require_once "../../function/sanitasi_input.php";
    
    $date_now = date('d-m-Y H:i:s');
    $day = date('d');
    $month = date('m');
    $year = date('y');
    $uuid = uuid();

    if(isset($_POST['simpan-pembayaran'])){
        $sanitasi_post = sanitizeInput($_POST);
        $id_bayar = decrypt($sanitasi_post['id_bayar'], $key_finance);
        $id_cs = decrypt($sanitasi_post['id_cs'], $key_finance);
        $id_inv = decrypt($sanitasi_post['id_inv'], $key_finance);
        $id_bill = $sanitasi_post['id_bill'];
        $id_bill_decrypt = decrypt($id_bill, $key_finance);
        $id_finance = decrypt($sanitasi_post['id_finance'], $key_finance);
        $jenis_inv = $sanitasi_post['jenis_inv'];
        $id_bank_cs = "BANK_CS" . $year . "" . $month . "" . $uuid . "" . $day ;
        $id_bukti = "BUKTI" . $year . "" . $month . "" . $uuid . "" . $day ;
        $metode_bayar = $sanitasi_post['metode_pembayaran'];
        $tgl_bayar = $sanitasi_post['tgl_bayar_cb'];
        $keterangan_bayar = isset($sanitasi_post['keterangan_bayar_cb']) && $sanitasi_post['keterangan_bayar_cb'] !== '' ? $sanitasi_post['keterangan_bayar_cb'] : '-';
        $total_bayar = str_replace('.', '', $sanitasi_post['nominal_cb']); // Menghapus tanda ribuan (,)
        $total_bayar = intval($total_bayar); // Mengubah string harga menjadi integer
        $nama_invoice = 'Invoice_Non_PPN';
        $sisa_tagihan = $sanitasi_post['sisa_bayar_cb'];
        $nama_pengirim = $sanitasi_post['nama_pengirim_cb'];
        $rek_pengirim = $sanitasi_post['rek_pengirim_cb'];
        $id_bank_pengirim = $sanitasi_post['id_bank_pengirim_cb'];
        $id_bank_select = $sanitasi_post['id_bank_select_cb'];
        $created_by = $_SESSION['tiket_nama'];

        // Validasi nonce token
        // if (!isset($sanitasi_post['nonce_token']) || $sanitasi_post['nonce_token'] !== $_SESSION['nonce_token']) {
        //     // Jika token tidak cocok atau tidak ada, hentikan proses dan tampilkan error
        //     $_SESSION['info'] = "Silahkan Ulangi Kembali";
        //     header("Location:../detail-bill.php?id=$id_bill");
        //     exit();
        // }

        // Nonce sudah divalidasi, sekarang hapus dari session agar tidak bisa digunakan lagi
        // unset($_SESSION['nonce_token']);
        if ($metode_bayar == 'cash'){
            try{
                // Mulai transaksi
                $connect->begin_transaction();

                $status_lunas = "";
                if($sisa_tagihan == 0 ){
                    $status_lunas = "1";
                } else {
                    $status_lunas = "0";
                }
                // Proses Simpan Pembayaran
                $stmt = $connect->prepare("INSERT IGNORE INTO finance_bayar_cb 
                                                                (id_bayar_cb, id_bank_pt, id_tagihan, id_finance, id_bukti, metode_pembayaran, keterangan_bayar, total_bayar, tgl_bayar, created_by) 
                                                                VALUES 
                                                                (?, '-', ?, ?, '-', ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssiss", $id_bayar, $id_bill_decrypt, $id_finance, $metode_bayar, $keterangan_bayar, $total_bayar, $tgl_bayar, $created_by);
                $simpan_bayar = $stmt->execute();

                // Proses update finance
                $stmt = $connect->prepare("UPDATE finance SET status_pembayaran_cb = 1, status_lunas_cb = ?  WHERE id_finance = ?");
                $stmt->bind_param("is", $status_lunas, $id_finance);
                $update_finance = $stmt->execute();

                if($simpan_bayar && $update_finance){
                    // Commit Transaksi
                    $connect->commit();
                    $connect->close();
                    $_SESSION['info'] = 'Disimpan';
                    header("Location:../detail-bill.php?id=$id_bill");
                    exit();
                } else {
                    throw new Exception("Error updating data");
                }
            } catch (Exception $e) {
                // Rollback Transaksi
                $connect->rollback();
                echo 'Caught exception: ',  $e->getMessage(), "\n";
                $connect->close();
                $_SESSION['info'] = 'Data Gagal Disimpan';
                // header("Location:../detail-bill.php?id=$id_bill");
                exit();
            } 
        } else if ($metode_bayar == 'transfer') {
            $id_bank_pt = $sanitasi_post['id_bank_pt_cb'];  
            echo "<pre>";
            print_r($sanitasi_post);
            echo "</pre>";
            if($jenis_inv == 'nonppn'){
                $sql_inv = mysqli_query($connect, " SELECT  nonppn.id_inv_nonppn, 
                                                            nonppn.no_inv, 
                                                            DAY(STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y')) AS day_inv, 
                                                            LPAD(MONTH(STR_TO_DATE(tgl_inv, '%d/%m/%Y')), 2, '0') AS month_inv,
                                                            YEAR(STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y')) AS year_inv,
            
                                                            fnc.id_inv,
                                                            fnc.id_tagihan,
            
                                                            bill.id_tagihan,
                                                            bill.no_tagihan,
            
                                                            cs.id_cs,
                                                            cs.nama_cs,
            
                                                            spk.id_inv,
                                                            spk.id_customer
                                                    FROM inv_nonppn AS nonppn
                                                    LEFT JOIN finance fnc ON (nonppn.id_inv_nonppn = fnc.id_inv)
                                                    LEFT JOIN finance_tagihan bill ON (fnc.id_tagihan = bill.id_tagihan)
                                                    LEFT JOIN spk_reg spk ON (nonppn.id_inv_nonppn = spk.id_inv)
                                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                    WHERE nonppn.id_inv_nonppn = '$id_inv'");
                $data_inv = mysqli_fetch_array($sql_inv);
            
                $no_inv_nonppn = $data_inv['no_inv'];
                $no_tagihan = $data_inv['no_tagihan'];
                $day_inv = $data_inv['day_inv'];
                $month_inv =  $data_inv['month_inv'];
                $year_inv =  $data_inv['year_inv'];
                $cs = $data_inv['nama_cs'];
            
            
                $nama_invoice = 'Invoice_Non_Ppn';
            
                // Convert $no_inv_nonppn to the desired format
                $no_inv_nonppn_converted = str_replace('/', '_', $no_inv_nonppn);
            
                // Generate folder name based on invoice details
                $folder_name = $no_inv_nonppn_converted;
            
                // Encode a portion of the folder name
                $encoded_portion = base64_encode($folder_name);
            
                // Combine the original $no_inv_nonppn, encoded portion, and underscore
                $encoded_folder_name = $no_inv_nonppn_converted . '_' . $encoded_portion;
            
                // untuk Membuat Folder Bukti Pembayaran
                $bukti_pembayaran = "Bukti_Transfer";
            
                // Set the path for the customer's folder
                $customer_folder_path = "../../Customer/" . $cs . "/" . $year_inv . "/" . $month_inv . "/" . $day_inv . "/" . ucwords(strtolower(str_replace('_', ' ', $nama_invoice))) . "/" . $encoded_folder_name ."/". $bukti_pembayaran ."/";

                // Create the customer's folder if it doesn't exist
                if (!is_dir($customer_folder_path)) {
                    mkdir($customer_folder_path, 0777, true); // Set permission to 0777 to ensure the folder is writable
                }

                // Mendapatkan informasi file bukti transfer
                $file1_name = $_FILES['fileku1']['name'];
                $file1_tmp = $_FILES['fileku1']['tmp_name'];
                $file1_destination =  $customer_folder_path . $file1_name;

                // Memeriksa ekstensi file yang diunggah
                $fileInfo = pathinfo($file1_name);
                $fileExtension = strtolower($fileInfo['extension']);
                $allowedExtensions = ['png', 'jpeg', 'jpg'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $_SESSION['info'] = 'Jenis file tidak didukung';
                    header("Location: ../detail-bill.php?id=$id_bill");
                    exit; // Menghentikan eksekusi setelah redirect
                }
        
                // Pindahkan file bukti transfer ke lokasi tujuan
                move_uploaded_file($file1_tmp, $file1_destination);

                $no_tagihan_converted = str_replace('/', '_', $no_tagihan);
                $name_no_tagihan = $no_tagihan_converted;
                
                $no = 1;
                $file_extension = ".jpg";
                
                // Proses Compress Image
                do {
                    // Generate nama file baru dengan nomor yang bertambah
                    $new_file1_name = $name_no_tagihan . "_" . $no . $file_extension;
                    $compressed_file1_destination = $customer_folder_path . $new_file1_name;
                
                    // Cek apakah file dengan nama tersebut sudah ada
                    if (!file_exists($compressed_file1_destination)) {
                        // Jika tidak ada, lakukan kompresi dan ubah ukuran gambar
                        compressAndResizeImage($file1_destination, $compressed_file1_destination, 500, 500, 100);
                        unlink($file1_destination); // Hapus file sumber yang tidak dikompresi
                        break; // Keluar dari loop karena nama file sudah unik
                    }
                
                    $no++; // Jika nama file sudah ada, tambahkan nomor dan coba lagi
                } while (true);
            } else if ($jenis_inv == 'ppn') {
                $sql_inv = mysqli_query($connect, " SELECT  ppn.id_inv_ppn, 
                                                            ppn.no_inv, 
                                                            DAY(STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y')) AS day_inv, 
                                                            LPAD(MONTH(STR_TO_DATE(tgl_inv, '%d/%m/%Y')), 2, '0') AS month_inv,
                                                            YEAR(STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y')) AS year_inv,
            
                                                            fnc.id_inv,
                                                            fnc.id_tagihan,
            
                                                            bill.id_tagihan,
                                                            bill.no_tagihan,
            
                                                            cs.id_cs,
                                                            cs.nama_cs,
            
                                                            spk.id_inv,
                                                            spk.id_customer
                                                    FROM inv_ppn AS ppn
                                                    LEFT JOIN finance fnc ON (ppn.id_inv_ppn = fnc.id_inv)
                                                    LEFT JOIN finance_tagihan bill ON (fnc.id_tagihan = bill.id_tagihan)
                                                    LEFT JOIN spk_reg spk ON (ppn.id_inv_ppn = spk.id_inv)
                                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                    WHERE ppn.id_inv_ppn = '$id_inv'");
                $data_inv = mysqli_fetch_array($sql_inv);
            
                $no_inv_ppn = $data_inv['no_inv'];
                $no_tagihan = $data_inv['no_tagihan'];
                $day_inv = $data_inv['day_inv'];
                $month_inv =  $data_inv['month_inv'];
                $year_inv =  $data_inv['year_inv'];
                $cs = $data_inv['nama_cs'];
            
            
                $nama_invoice = 'Invoice_Ppn';
            
                // Convert $no_inv_ppn to the desired format
                $no_inv_ppn_converted = str_replace('/', '_', $no_inv_ppn);
            
                // Generate folder name based on invoice details
                $folder_name = $no_inv_ppn_converted;
            
                // Encode a portion of the folder name
                $encoded_portion = base64_encode($folder_name);
            
                // Combine the original $no_inv_ppn, encoded portion, and underscore
                $encoded_folder_name = $no_inv_ppn_converted . '_' . $encoded_portion;
            
                // untuk Membuat Folder Bukti Pembayaran
                $bukti_pembayaran = "Bukti_Transfer";
            
                // Set the path for the customer's folder
                $customer_folder_path = "../../Customer/" . $cs . "/" . $year_inv . "/" . $month_inv . "/" . $day_inv . "/" . ucwords(strtolower(str_replace('_', ' ', $nama_invoice))) . "/" . $encoded_folder_name ."/". $bukti_pembayaran ."/";

                // Create the customer's folder if it doesn't exist
                if (!is_dir($customer_folder_path)) {
                    mkdir($customer_folder_path, 0777, true); // Set permission to 0777 to ensure the folder is writable
                }

                // Mendapatkan informasi file bukti transfer
                $file1_name = $_FILES['fileku1']['name'];
                $file1_tmp = $_FILES['fileku1']['tmp_name'];
                $file1_destination =  $customer_folder_path . $file1_name;

                // Memeriksa ekstensi file yang diunggah
                $fileInfo = pathinfo($file1_name);
                $fileExtension = strtolower($fileInfo['extension']);
                $allowedExtensions = ['png', 'jpeg', 'jpg'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $_SESSION['info'] = 'Jenis file tidak didukung';
                    header("Location: ../detail-bill.php?id=$id_bill");
                    exit; // Menghentikan eksekusi setelah redirect
                }

                // Pindahkan file bukti transfer ke lokasi tujuan
                move_uploaded_file($file1_tmp, $file1_destination);

                $no_tagihan_converted = str_replace('/', '_', $no_tagihan);
                $name_no_tagihan = $no_tagihan_converted;
                
                $no = 1;
                $file_extension = ".jpg";
                
                do {
                    // Generate nama file baru dengan nomor yang bertambah
                    $new_file1_name = $name_no_tagihan . "_" . $no . $file_extension;
                    $compressed_file1_destination = $customer_folder_path . $new_file1_name;
                
                    // Cek apakah file dengan nama tersebut sudah ada
                    if (!file_exists($compressed_file1_destination)) {
                        // Jika tidak ada, lakukan kompresi dan ubah ukuran gambar
                        compressAndResizeImage($file1_destination, $compressed_file1_destination, 500, 500, 100);
                        unlink($file1_destination); // Hapus file sumber yang tidak dikompresi
                        break; // Keluar dari loop karena nama file sudah unik
                    }
                
                    $no++; // Jika nama file sudah ada, tambahkan nomor dan coba lagi
                } while (true);
            } else if ($jenis_inv == 'bum') {
                $sql_inv = mysqli_query($connect, " SELECT  bum.id_inv_bum, 
                                                            bum.no_inv, 
                                                            DAY(STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y')) AS day_inv, 
                                                            LPAD(MONTH(STR_TO_DATE(tgl_inv, '%d/%m/%Y')), 2, '0') AS month_inv,
                                                            YEAR(STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y')) AS year_inv,
            
                                                            fnc.id_inv,
                                                            fnc.id_tagihan,
            
                                                            bill.id_tagihan,
                                                            bill.no_tagihan,
            
                                                            cs.id_cs,
                                                            cs.nama_cs,
            
                                                            spk.id_inv,
                                                            spk.id_customer
                                                    FROM inv_bum AS bum
                                                    LEFT JOIN finance fnc ON (bum.id_inv_bum = fnc.id_inv)
                                                    LEFT JOIN finance_tagihan bill ON (fnc.id_tagihan = bill.id_tagihan)
                                                    LEFT JOIN spk_reg spk ON (bum.id_inv_bum = spk.id_inv)
                                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                    WHERE bum.id_inv_bum = '$id_inv'");
                $data_inv = mysqli_fetch_array($sql_inv);
            
                $no_inv_bum = $data_inv['no_inv'];
                $no_tagihan = $data_inv['no_tagihan'];
                $day_inv = $data_inv['day_inv'];
                $month_inv =  $data_inv['month_inv'];
                $year_inv =  $data_inv['year_inv'];
                $cs = $data_inv['nama_cs'];
            
            
                $nama_invoice = 'Invoice_Bum';
            
                // Convert $no_inv_bum to the desired format
                $no_inv_bum_converted = str_replace('/', '_', $no_inv_bum);
            
                // Generate folder name based on invoice details
                $folder_name = $no_inv_bum_converted;
            
                // Encode a portion of the folder name
                $encoded_portion = base64_encode($folder_name);
            
                // Combine the original $no_inv_bum, encoded portion, and underscore
                $encoded_folder_name = $no_inv_bum_converted . '_' . $encoded_portion;
            
                // untuk Membuat Folder Bukti Pembayaran
                $bukti_pembayaran = "Bukti_Transfer";
            
                // Set the path for the customer's folder
                $customer_folder_path = "../../Customer/" . $cs . "/" . $year_inv . "/" . $month_inv . "/" . $day_inv . "/" . ucwords(strtolower(str_replace('_', ' ', $nama_invoice))) . "/" . $encoded_folder_name ."/". $bukti_pembayaran ."/";

                // Create the customer's folder if it doesn't exist
                if (!is_dir($customer_folder_path)) {
                    mkdir($customer_folder_path, 0777, true); // Set permission to 0777 to ensure the folder is writable
                }

                // Mendapatkan informasi file bukti transfer
                $file1_name = $_FILES['fileku1']['name'];
                $file1_tmp = $_FILES['fileku1']['tmp_name'];
                $file1_destination =  $customer_folder_path . $file1_name;

                // Memeriksa ekstensi file yang diunggah
                $fileInfo = pathinfo($file1_name);
                $fileExtension = strtolower($fileInfo['extension']);
                $allowedExtensions = ['png', 'jpeg', 'jpg'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $_SESSION['info'] = 'Jenis file tidak didukung';
                    header("Location: ../detail-bill.php?id=$id_bill");
                    exit; // Menghentikan eksekusi setelah redirect
                }
        
                // Pindahkan file bukti transfer ke lokasi tujuan
                move_uploaded_file($file1_tmp, $file1_destination);

                $no_tagihan_converted = str_replace('/', '_', $no_tagihan);
                $name_no_tagihan = $no_tagihan_converted;
                
                $no = 1;
                $file_extension = ".jpg";
                
                do {
                    // Generate nama file baru dengan nomor yang bertambah
                    $new_file1_name = $name_no_tagihan . "_" . $no . $file_extension;
                    $compressed_file1_destination = $customer_folder_path . $new_file1_name;
                
                    // Cek apakah file dengan nama tersebut sudah ada
                    if (!file_exists($compressed_file1_destination)) {
                        // Jika tidak ada, lakukan kompresi dan ubah ukuran gambar
                        compressAndResizeImage($file1_destination, $compressed_file1_destination, 500, 500, 100);
                        unlink($file1_destination); // Hapus file sumber yang tidak dikompresi
                        break; // Keluar dari loop karena nama file sudah unik
                    }
                    $no++; // Jika nama file sudah ada, tambahkan nomor dan coba lagi
                } while (true);
            } else { 
                header("Loacation:../../404.php");
            }

            // Kondisi untuk id bank, dimana data bank di input manual atau di pilih dengan select option
            if($id_bank_pengirim != ''){
                $bank_pengirim = $id_bank_pengirim;
            } else {
                $bank_pengirim = $id_bank_select;
            }   
            // Kode untuk cek data bank cs
            $cek_data = mysqli_query($connect, "SELECT id_bank, no_rekening FROM bank_cs WHERE id_cs = '$id_cs' AND id_bank = '$bank_pengirim' AND no_rekening = '$rek_pengirim'");
            try{
                // Mulai transaksi
                $connect->begin_transaction();
                $update_finance = "";
                if($sisa_tagihan == 0 ){
                    $stmt = $connect->prepare("UPDATE finance SET status_pembayaran_cb = 1, status_lunas_cb = 1  WHERE id_finance = ?");
                    $stmt->bind_param("s", $id_finance);
                    $update_finance = $stmt->execute();
                } else {
                    $stmt = $connect->prepare("UPDATE finance SET status_pembayaran = 1, status_lunas = 0  WHERE id_finance = ?");
                    $stmt->bind_param("s", $id_finance);
                    $update_finance = $stmt->execute();
                }

                // Kondisi untuk input data cs bank jika belum ada jalankan insert jika sudah maka jalankan update
                $proses_cs_bank = '';
                if($cek_data->num_rows == 0){
                    $stmt = $connect->prepare("INSERT IGNORE INTO bank_cs 
                                                (id_bank_cs, id_cs, id_bank, no_rekening, atas_nama, created_by) 
                                                VALUES 
                                                (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $id_bank_cs, $id_cs, $bank_pengirim, $rek_pengirim, $nama_pengirim, $created_by);
                    $proses_cs_bank = $stmt->execute();
                } else {
                    $stmt = $connect->prepare("UPDATE bank_cs SET id_bank = ?, no_rekening = ?, atas_nama = ? WHERE id_bank_cs = ?");
                    $stmt->bind_param("ssss", $bank_pengirim, $rek_pengirim, $nama_pengirim, $id_bank_pengirim);
                    $proses_cs_bank = $stmt->execute();
                }

                // Proses Simpan Pembayaran
                $stmt = $connect->prepare("INSERT IGNORE INTO finance_bayar_cb 
                            (id_bayar_cb, id_bank_pt, id_tagihan, id_finance, id_bukti, metode_pembayaran, keterangan_bayar, total_bayar, tgl_bayar, created_by) 
                            VALUES 
                            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssiss", $id_bayar, $id_bank_pt, $id_bill_decrypt, $id_finance, $id_bukti, $metode_bayar, $keterangan_bayar, $total_bayar, $tgl_bayar, $created_by);
                $simpan_bayar = $stmt->execute();

                // Proses simpan bukti pembayaran
                $stmt = $connect->prepare("INSERT IGNORE INTO finance_bukti_tf_cb
                                            (id_bukti_tf, id_finance, tf_bank, rek_penerima, tf_an, bukti_tf, path, created_by) 
                                            VALUES 
                                            (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $id_bukti, $id_finance, $bank_pengirim, $rek_pengirim, $nama_pengirim, $new_file1_name, $customer_folder_path, $created_by);
                $simpan_bukti_bayar = $stmt->execute();
                    
                if ($update_finance && $proses_cs_bank && $simpan_bayar && $simpan_bukti_bayar) {
                    // Commit transaksi  
                    $connect->commit();
                    $connect->close();
                    // Redirect to the invoice page
                    $_SESSION['info'] = "Disimpan";
                    header("Location:../detail-bill.php?id=$id_bill");
                    exit();
                } else {
                    throw new Exception("Error updating data");
                }
            } catch (Exception $e) {
            // Rollback Transaksi
                $connect->rollback();
                // Unlink gambar
                unlink($compressed_file1_destination); 
                // echo 'Caught exception: ',  $e->getMessage(), "\n";
                $connect->close();
                $_SESSION['info'] = 'Data Gagal Disimpan';
                header("Location:../detail-bill.php?id=$id_bill");
                exit();
            } 
        }
    }
?>