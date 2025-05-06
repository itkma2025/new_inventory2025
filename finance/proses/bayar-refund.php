<?php  
    require_once "../../akses.php";
    require_once "../page/resize-image.php";
    require_once "../../function/uuid.php";

    if(isset($_POST['simpan-pembayaran'])){
        // Get POST data and sanitize input
        $id_bayar = htmlspecialchars($_POST['id_bayar']);
        $id_bayar_decrypt = decrypt($id_bayar, $key_finance);
        $id_cs = htmlspecialchars($_POST['id_cs']);
        $id_cs_decrypt = decrypt($id_cs, $key_finance);
        $id_inv = htmlspecialchars($_POST['id_inv']);
        $id_inv_decrypt = decrypt($id_inv, $key_finance);
        $id_refund = htmlspecialchars($_POST['id_refund']);
        $id_refund_decrypt = decrypt($id_refund, $key_finance);
        $metode_bayar = htmlspecialchars($_POST['metode_pembayaran']);
        $nominal = str_replace('.', '', $_POST['nominal']);
        $nominal = intval($nominal);
        $tgl_bayar = htmlspecialchars($_POST['tgl_bayar']);
        $keterangan_bayar = htmlspecialchars($_POST['keterangan_bayar']);
        $jenis_inv = htmlspecialchars($_POST['jenis_inv']);
        $sisa_tagihan = htmlspecialchars($_POST['sisa_tagihan']);
        $id_bank_pt = !empty($_POST['id_bank_pt']) ? htmlspecialchars($_POST['id_bank_pt']) : '-';
        $nama_pengirim = !empty($_POST['nama_pengirim']) ? htmlspecialchars($_POST['nama_pengirim']) : '-';
        $rek_pengirim = !empty($_POST['rek_pengirim']) ? htmlspecialchars($_POST['rek_pengirim']) : '-';
        $id_bank_pengirim = !empty($_POST['id_bank_pengirim']) ? htmlspecialchars($_POST['id_bank_pengirim']) : '-';
        $id_bank_select = !empty($_POST['id_bank_select']) ? htmlspecialchars($_POST['id_bank_select']) : '-';
        $created_by = htmlspecialchars($_SESSION['tiket_nama']);

        // Generate additional data
        $date = date('d/m/Y H:i:s');
        $uuid = uuid(); // Replace this with your actual UUID generation method
        $day = date('d');
        $month = date('m');
        $year = date('y');
        $id_bank_cs = "BANK_CS" . $year . "" . $month . "" . $uuid . "" . $day;
        $id_bukti = "BUKTI" . $year . "" . $month . "" . $uuid . "" . $day;
        $nama_invoice = 'Invoice_Non_PPN';
        $status_lunas = ($sisa_tagihan == '0') ? '1' : '2';


        if ($metode_bayar == "cash"){
            //Begin transaction
            mysqli_begin_transaction($connect);
            try{
                $sql_bayar = mysqli_query($connect, "INSERT IGNORE INTO finance_bayar_refund 
                                                (id_bayar, id_refund, id_inv, metode_pembayaran, keterangan_bayar, total_bayar, tgl_bayar, created_by) 
                                                VALUES 
                                                ('$id_bayar_decrypt', '$id_refund_decrypt', '$id_inv_decrypt', '$metode_bayar', '$keterangan_bayar', '$nominal', '$tgl_bayar', '$created_by')");

                $sql_finance_refund = mysqli_query($connect, "UPDATE finance_refund SET status_refund = '$status_lunas' WHERE id_refund = '$id_refund_decrypt'");


                if (!$sql_bayar && !$sql_finance_refund) {
                    throw new Exception("Error updating data");
                } else {
                     // Commit the transaction
                    mysqli_commit($connect);
                    $_SESSION['info'] = "Disimpan";
                    header("location:../list-refund-dana.php?date_range=year");
                    exit();
                }
            } catch (Exception $e) {
                // Rollback the transaction if an error occurs
                mysqli_rollback($connect);
                // $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("location:../list-refund-dana.php?date_range=year"); 
            } 
        } else {
            if($jenis_inv == 'nonppn'){
                $sql_inv = mysqli_query($connect, " SELECT  
                                                        nonppn.id_inv_nonppn, 
                                                        nonppn.no_inv, 
                                                        DAY(STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y')) AS day_inv, 
                                                        -- LPAD : Menambahkan karakter '0' di sebelah kiri angka bulan agar panjangnya mencapai 2 karakter.
                                                        -- Contoh: 1 menjadi '01', 10 tetap '10'
                                                        LPAD(MONTH(STR_TO_DATE(tgl_inv, '%d/%m/%Y')), 2, '0') AS month_inv,
                                                        YEAR(STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y')) AS year_inv,
                                                        refund.id_refund,
                                                        refund.no_refund,
                                                        cs.id_cs,
                                                        cs.nama_cs
                                                    FROM inv_nonppn AS nonppn
                                                    LEFT JOIN finance_refund refund ON (nonppn.id_inv_nonppn = refund.id_inv)
                                                    LEFT JOIN spk_reg spk ON (nonppn.id_inv_nonppn = spk.id_inv)
                                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                    WHERE nonppn.id_inv_nonppn = '$id_inv_decrypt'");
                $data_inv = mysqli_fetch_array($sql_inv);
                $no_inv_nonppn = $data_inv['no_inv'];
                $no_refund = $data_inv['no_refund'];
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
                $bukti_pembayaran = "Bukti_Transfer_Refund";
            
                // Set the path for the customer's folder
                $customer_folder_path = "../../Customer/" . $cs . "/" . $year_inv . "/" . $month_inv . "/" . $day_inv . "/" . ucwords(strtolower(str_replace('_', ' ', $nama_invoice))) . "/" . $encoded_folder_name ."/". $bukti_pembayaran ."/";


                $path_upload = "";
                if (!is_dir($customer_folder_path)) {
                    $path_upload = mkdir($customer_folder_path, 0777, true); // Set permission to 0777 to ensure the folder is writable
                } else {
                    $path_upload = "";
                }
                
                // Mendapatkan informasi file bukti transfer
                $file1_name = $_FILES['fileku1']['name'];
                $file1_tmp = $_FILES['fileku1']['tmp_name'];
                $file1_destination =  $customer_folder_path . $file1_name;
                move_uploaded_file($file1_tmp, $file1_destination);

                if($file1_name != ''){
                    $no_refund_converted = str_replace('/', '_', $no_refund);
                    $name_no_refund = $no_refund_converted;
                    
                    $no = 1;
                    $file_extension = ".jpg";
                    
                    do {
                        // Generate nama file baru dengan nomor yang bertambah
                        $new_file1_name = $name_no_refund . "_" . $no . $file_extension;
                        $compressed_file1_destination = $customer_folder_path . $new_file1_name;

                        echo $new_file1_name;
                    
                        // Cek apakah file dengan nama tersebut sudah ada
                        if (!file_exists($compressed_file1_destination)) {
                            // Jika tidak ada, lakukan kompresi dan ubah ukuran gambar
                            compressAndResizeImage($file1_destination, $compressed_file1_destination, 500, 500, 100);
                            unlink($file1_destination); // Hapus file sumber yang tidak dikompresi
                            break; // Keluar dari loop karena nama file sudah unik
                        }
                    
                        $no++; // Jika nama file sudah ada, tambahkan nomor dan coba lagi
                    } while (true);
                    
                    if($id_bank_pengirim != ''){
                        $bank_pengirim = $id_bank_pengirim;
                    } else {
                        $bank_pengirim = $id_bank_select;
                    }      

                    $cek_data = mysqli_query($connect, "SELECT id_bank, no_rekening FROM bank_cs WHERE id_cs = '$id_cs_decrypt' AND id_bank = '$bank_pengirim' AND no_rekening = '$rek_pengirim'");

                } else {
                    echo "Terjadi kesalahan";
                }

                // Begin transaction
                mysqli_begin_transaction($connect);
                try{
                    // Kondisi simpan data dan update data bank CS
                    $sql_cs_bank = '';
                    if($cek_data->num_rows == 0){
                        $sql_cs_bank = mysqli_query($connect, "INSERT IGNORE INTO bank_cs 
                                                    (id_bank_cs, id_cs, id_bank, no_rekening, atas_nama, created_by) 
                                                    VALUES 
                                                    ('$id_bank_cs', '$id_cs', '$bank_pengirim', '$rek_pengirim', '$nama_pengirim', '$created_by')");
                    } else {
                        $sql_cs_bank = mysqli_query($connect, "UPDATE bank_cs SET id_bank = '$bank_pengirim', no_rekening = '$rek_pengirim', atas_nama = '$nama_pengirim' WHERE id_bank_cs = '$id_bank_pengirim'");
                    }

                    $sql_bayar = mysqli_query($connect, "INSERT IGNORE INTO finance_bayar_refund 
                                                (id_bayar, id_bank_pt, id_refund, id_inv, id_bukti, metode_pembayaran, keterangan_bayar, total_bayar, tgl_bayar, created_by) 
                                                VALUES 
                                                ('$id_bayar_decrypt', '$id_bank_pt', '$id_refund_decrypt', '$id_inv_decrypt', '$id_bukti', '$metode_bayar', '$keterangan_bayar', '$nominal', '$tgl_bayar', '$created_by')");
                    
                    $sql_bukti_tf = mysqli_query($connect, "INSERT IGNORE INTO finance_bukti_tf_refund
                                                        (id_bukti_tf, id_refund, tf_bank, rek_pengirim, tf_an, bukti_tf, path, created_by) 
                                                        VALUES 
                                                        ('$id_bukti', '$id_refund_decrypt', '$bank_pengirim', '$rek_pengirim', '$nama_pengirim', '$new_file1_name', '$customer_folder_path', '$created_by')");
 
                    $sql_finance_refund = mysqli_query($connect, "UPDATE finance_refund SET status_refund = '$status_lunas' WHERE id_refund = '$id_refund_decrypt'");

                    // Pindahkan file bukti transfer ke lokasi tujuan
                    


                    if (!$sql_cs_bank && !$sql_bayar && !$sql_bukti_tf && !$sql_finance_refund) {
                        throw new Exception("Error updating data");
                    } else {
                        // Commit the transaction
                        mysqli_commit($connect);
                        // Redirect to the invoice page
                        $_SESSION['info'] = "Disimpan";
                        header("location:../list-refund-dana.php?date_range=year");
                        exit();
                    }
                } catch (Exception $e) {
                    // Rollback the transaction if an error occurs
                    mysqli_rollback($connect);
                    // Handle the error (e.g., display an error message)
                    $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
                    $_SESSION['info'] = "Data Gagal Disimpan";
                    header("location:../list-refund-dana.php?date_range=year"); 
                } 
            } else if($jenis_inv == 'ppn'){
                $sql_inv = mysqli_query($connect, " SELECT  
                                                        ppn.id_inv_ppn, 
                                                        ppn.no_inv, 
                                                        DAY(STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y')) AS day_inv, 
                                                        -- LPAD : Menambahkan karakter '0' di sebelah kiri angka bulan agar panjangnya mencapai 2 karakter.
                                                        -- Contoh: 1 menjadi '01', 10 tetap '10'
                                                        LPAD(MONTH(STR_TO_DATE(tgl_inv, '%d/%m/%Y')), 2, '0') AS month_inv,
                                                        YEAR(STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y')) AS year_inv,
                                                        refund.id_refund,
                                                        refund.no_refund,
                                                        cs.id_cs,
                                                        cs.nama_cs
                                                    FROM inv_ppn AS ppn
                                                    LEFT JOIN finance_refund refund ON (ppn.id_inv_ppn = refund.id_inv)
                                                    LEFT JOIN spk_reg spk ON (ppn.id_inv_ppn = spk.id_inv)
                                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                    WHERE ppn.id_inv_ppn = '$id_inv_decrypt'");
                $data_inv = mysqli_fetch_array($sql_inv);
                $no_inv_ppn = $data_inv['no_inv'];
                $no_refund = $data_inv['no_refund'];
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
                $bukti_pembayaran = "Bukti_Transfer_Refund";
            
                // Set the path for the customer's folder
                $customer_folder_path = "../../Customer/" . $cs . "/" . $year_inv . "/" . $month_inv . "/" . $day_inv . "/" . ucwords(strtolower(str_replace('_', ' ', $nama_invoice))) . "/" . $encoded_folder_name ."/". $bukti_pembayaran ."/";


                $path_upload = "";
                if (!is_dir($customer_folder_path)) {
                    $path_upload = mkdir($customer_folder_path, 0777, true); // Set permission to 0777 to ensure the folder is writable
                } else {
                    $path_upload = "";
                }
                
                // Mendapatkan informasi file bukti transfer
                $file1_name = $_FILES['fileku1']['name'];
                $file1_tmp = $_FILES['fileku1']['tmp_name'];
                $file1_destination =  $customer_folder_path . $file1_name;
                move_uploaded_file($file1_tmp, $file1_destination);

                if($file1_name != ''){
                    $no_refund_converted = str_replace('/', '_', $no_refund);
                    $name_no_refund = $no_refund_converted;
                    
                    $no = 1;
                    $file_extension = ".jpg";
                    
                    do {
                        // Generate nama file baru dengan nomor yang bertambah
                        $new_file1_name = $name_no_refund . "_" . $no . $file_extension;
                        $compressed_file1_destination = $customer_folder_path . $new_file1_name;

                        echo $new_file1_name;
                    
                        // Cek apakah file dengan nama tersebut sudah ada
                        if (!file_exists($compressed_file1_destination)) {
                            // Jika tidak ada, lakukan kompresi dan ubah ukuran gambar
                            compressAndResizeImage($file1_destination, $compressed_file1_destination, 500, 500, 100);
                            unlink($file1_destination); // Hapus file sumber yang tidak dikompresi
                            break; // Keluar dari loop karena nama file sudah unik
                        }
                    
                        $no++; // Jika nama file sudah ada, tambahkan nomor dan coba lagi
                    } while (true);
                    
                    if($id_bank_pengirim != ''){
                        $bank_pengirim = $id_bank_pengirim;
                    } else {
                        $bank_pengirim = $id_bank_select;
                    }      

                    $cek_data = mysqli_query($connect, "SELECT id_bank, no_rekening FROM bank_cs WHERE id_cs = '$id_cs_decrypt' AND id_bank = '$bank_pengirim' AND no_rekening = '$rek_pengirim'");

                } else {
                    echo "Terjadi kesalahan";
                }

                // Begin transaction
                mysqli_begin_transaction($connect);
                try{
                    // Kondisi simpan data dan update data bank CS
                    $sql_cs_bank = '';
                    if($cek_data->num_rows == 0){
                        $sql_cs_bank = mysqli_query($connect, "INSERT IGNORE INTO bank_cs 
                                                    (id_bank_cs, id_cs, id_bank, no_rekening, atas_nama, created_by) 
                                                    VALUES 
                                                    ('$id_bank_cs', '$id_cs', '$bank_pengirim', '$rek_pengirim', '$nama_pengirim', '$created_by')");
                    } else {
                        $sql_cs_bank = mysqli_query($connect, "UPDATE bank_cs SET id_bank = '$bank_pengirim', no_rekening = '$rek_pengirim', atas_nama = '$nama_pengirim' WHERE id_bank_cs = '$id_bank_pengirim'");
                    }

                    $sql_bayar = mysqli_query($connect, "INSERT IGNORE INTO finance_bayar_refund 
                                                (id_bayar, id_bank_pt, id_refund, id_inv, id_bukti, metode_pembayaran, keterangan_bayar, total_bayar, tgl_bayar, created_by) 
                                                VALUES 
                                                ('$id_bayar_decrypt', '$id_bank_pt', '$id_refund_decrypt', '$id_inv_decrypt', '$id_bukti', '$metode_bayar', '$keterangan_bayar', '$nominal', '$tgl_bayar', '$created_by')");
                    
                    $sql_bukti_tf = mysqli_query($connect, "INSERT IGNORE INTO finance_bukti_tf_refund
                                                        (id_bukti_tf, id_refund, tf_bank, rek_pengirim, tf_an, bukti_tf, path, created_by) 
                                                        VALUES 
                                                        ('$id_bukti', '$id_refund_decrypt', '$bank_pengirim', '$rek_pengirim', '$nama_pengirim', '$new_file1_name', '$customer_folder_path', '$created_by')");
 
                    $sql_finance_refund = mysqli_query($connect, "UPDATE finance_refund SET status_refund = '$status_lunas' WHERE id_refund = '$id_refund_decrypt'");

                    // Pindahkan file bukti transfer ke lokasi tujuan
                    


                    if (!$sql_cs_bank && !$sql_bayar && !$sql_bukti_tf && !$sql_finance_refund) {
                        throw new Exception("Error updating data");
                    } else {
                        // Commit the transaction
                        mysqli_commit($connect);
                        // Redirect to the invoice page
                        $_SESSION['info'] = "Disimpan";
                        header("location:../list-refund-dana.php?date_range=year");
                        exit();
                    }
                } catch (Exception $e) {
                    // Rollback the transaction if an error occurs
                    mysqli_rollback($connect);
                    // Handle the error (e.g., display an error message)
                    $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
                    $_SESSION['info'] = "Data Gagal Disimpan";
                    header("location:../list-refund-dana.php?date_range=year"); 
                } 
            } else if($jenis_inv == 'bum'){
                $sql_inv = mysqli_query($connect, " SELECT  
                                                        bum.id_inv_bum, 
                                                        bum.no_inv, 
                                                        DAY(STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y')) AS day_inv, 
                                                        -- LPAD : Menambahkan karakter '0' di sebelah kiri angka bulan agar panjangnya mencapai 2 karakter.
                                                        -- Contoh: 1 menjadi '01', 10 tetap '10'
                                                        LPAD(MONTH(STR_TO_DATE(tgl_inv, '%d/%m/%Y')), 2, '0') AS month_inv,
                                                        YEAR(STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y')) AS year_inv,
                                                        refund.id_refund,
                                                        refund.no_refund,
                                                        cs.id_cs,
                                                        cs.nama_cs
                                                    FROM inv_bum AS bum
                                                    LEFT JOIN finance_refund refund ON (bum.id_inv_bum = refund.id_inv)
                                                    LEFT JOIN spk_reg spk ON (bum.id_inv_bum = spk.id_inv)
                                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                    WHERE bum.id_inv_bum = '$id_inv_decrypt'");
                $data_inv = mysqli_fetch_array($sql_inv);
                $no_inv_bum = $data_inv['no_inv'];
                $no_refund = $data_inv['no_refund'];
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
                $bukti_pembayaran = "Bukti_Transfer_Refund";
            
                // Set the path for the customer's folder
                $customer_folder_path = "../../Customer/" . $cs . "/" . $year_inv . "/" . $month_inv . "/" . $day_inv . "/" . ucwords(strtolower(str_replace('_', ' ', $nama_invoice))) . "/" . $encoded_folder_name ."/". $bukti_pembayaran ."/";


                $path_upload = "";
                if (!is_dir($customer_folder_path)) {
                    $path_upload = mkdir($customer_folder_path, 0777, true); // Set permission to 0777 to ensure the folder is writable
                } else {
                    $path_upload = "";
                }
                
                // Mendapatkan informasi file bukti transfer
                $file1_name = $_FILES['fileku1']['name'];
                $file1_tmp = $_FILES['fileku1']['tmp_name'];
                $file1_destination =  $customer_folder_path . $file1_name;
                move_uploaded_file($file1_tmp, $file1_destination);

                if($file1_name != ''){
                    $no_refund_converted = str_replace('/', '_', $no_refund);
                    $name_no_refund = $no_refund_converted;
                    
                    $no = 1;
                    $file_extension = ".jpg";
                    
                    do {
                        // Generate nama file baru dengan nomor yang bertambah
                        $new_file1_name = $name_no_refund . "_" . $no . $file_extension;
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
                    
                    if($id_bank_pengirim != ''){
                        $bank_pengirim = $id_bank_pengirim;
                    } else {
                        $bank_pengirim = $id_bank_select;
                    }      

                    $cek_data = mysqli_query($connect, "SELECT id_bank, no_rekening FROM bank_cs WHERE id_cs = '$id_cs_decrypt' AND id_bank = '$bank_pengirim' AND no_rekening = '$rek_pengirim'");

                } else {
                    echo "Terjadi kesalahan";
                }

                // Begin transaction
                mysqli_begin_transaction($connect);
                try{
                    // Kondisi simpan data dan update data bank CS
                    $sql_cs_bank = '';
                    if($cek_data->num_rows == 0){
                        $sql_cs_bank = mysqli_query($connect, "INSERT IGNORE INTO bank_cs 
                                                    (id_bank_cs, id_cs, id_bank, no_rekening, atas_nama, created_by) 
                                                    VALUES 
                                                    ('$id_bank_cs', '$id_cs', '$bank_pengirim', '$rek_pengirim', '$nama_pengirim', '$created_by')");
                    } else {
                        $sql_cs_bank = mysqli_query($connect, "UPDATE bank_cs SET id_bank = '$bank_pengirim', no_rekening = '$rek_pengirim', atas_nama = '$nama_pengirim' WHERE id_bank_cs = '$id_bank_pengirim'");
                    }

                    $sql_bayar = mysqli_query($connect, "INSERT IGNORE INTO finance_bayar_refund 
                                                (id_bayar, id_bank_pt, id_refund, id_inv, id_bukti, metode_pembayaran, keterangan_bayar, total_bayar, tgl_bayar, created_by) 
                                                VALUES 
                                                ('$id_bayar_decrypt', '$id_bank_pt', '$id_refund_decrypt', '$id_inv_decrypt', '$id_bukti', '$metode_bayar', '$keterangan_bayar', '$nominal', '$tgl_bayar', '$created_by')");
                    
                    $sql_bukti_tf = mysqli_query($connect, "INSERT IGNORE INTO finance_bukti_tf_refund
                                                        (id_bukti_tf, id_refund, tf_bank, rek_pengirim, tf_an, bukti_tf, path, created_by) 
                                                        VALUES 
                                                        ('$id_bukti', '$id_refund_decrypt', '$bank_pengirim', '$rek_pengirim', '$nama_pengirim', '$new_file1_name', '$customer_folder_path', '$created_by')");
 
                    $sql_finance_refund = mysqli_query($connect, "UPDATE finance_refund SET status_refund = '$status_lunas' WHERE id_refund = '$id_refund_decrypt'");

                    // Pindahkan file bukti transfer ke lokasi tujuan
                    


                    if (!$sql_cs_bank && !$sql_bayar && !$sql_bukti_tf && !$sql_finance_refund) {
                        throw new Exception("Error updating data");
                    } else {
                        // Commit the transaction
                        mysqli_commit($connect);
                        // Redirect to the invoice page
                        $_SESSION['info'] = "Disimpan";
                        header("location:../list-refund-dana.php?date_range=year");
                        exit();
                    }
                } catch (Exception $e) {
                    // Rollback the transaction if an error occurs
                    mysqli_rollback($connect);
                    // Handle the error (e.g., display an error message)
                    $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
                    $_SESSION['info'] = "Data Gagal Disimpan";
                    header("location:../list-refund-dana.php?date_range=year"); 
                } 
            }
        }
    }

    // Note
    // 0 = Open : Ketika Buat Refund Baru maka statusnya Open
    // 1 = Close: Ketika selesai di transfer 
    // 2 = Sudah Bayar : Belum Lunas
    // 3 = Cancel : Ketika batal refund
?>