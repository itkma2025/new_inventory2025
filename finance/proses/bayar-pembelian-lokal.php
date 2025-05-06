<?php  
    require_once "../../akses.php";
    include "../page/resize-image.php";
    $key = "payment2024";

    if(isset($_POST['simpan-pembayaran'])){
        $id_bayar = htmlspecialchars($_POST['id_bayar']);
        $id_sp = htmlspecialchars($_POST['id_sp']);
        $id_inv = htmlspecialchars($_POST['id_inv']);
        $jenis_inv = htmlspecialchars($_POST['jenis_inv']);
        $id_pembayaran = htmlspecialchars($_POST['id_pembayaran']);
        $id_pembayaran_encrypt = encrypt($id_pembayaran, $key);
        $metode_pembayaran = htmlspecialchars($_POST['metode_pembayaran']);
        $keterangan_bayar = htmlspecialchars($_POST['keterangan_bayar']);
        $tgl_bayar = htmlspecialchars($_POST['tgl_bayar']);
        $nominal = str_replace('.', '', $_POST['nominal']); // Menghapus tanda ribuan (,)
        $nominal = intval($nominal); // Mengubah string harga menjadi integer
        $sisa_tagihan = htmlspecialchars($_POST['sisa_tagihan']);
        $nama_user = $_SESSION['tiket_nama'];

        if ($metode_pembayaran == 'cash') {
            mysqli_begin_transaction($connect);
            try {
                $status_lunas = ($sisa_tagihan == 0) ? "1" : "0";
                $query = [
                            "INSERT INTO finance_bayar_pembelian 
                            (id_bayar, id_pembayaran, id_inv_pembelian, metode_pembayaran, keterangan_bayar, total_bayar, tgl_bayar, created_by)
                            VALUES
                            ('$id_bayar', '$id_pembayaran', '$id_inv', '$metode_pembayaran', '$keterangan_bayar', '$nominal', '$tgl_bayar', '$nama_user')",
                
                            "UPDATE inv_pembelian_lokal SET status_bayar = '1', status_lunas = '$status_lunas' WHERE id_inv_pembelian = '$id_inv'"
                        ];
        
                foreach ($query as $query_all) {
                    $proses_query = $connect->query($query_all);
                    if (!$proses_query) {
                        $error_message = mysqli_error($connect);
                        throw new Exception("Query error: $error_message");
                    }
                }
                // Semua proses berhasil, commit transaksi
                $connect->commit();
                $_SESSION['info'] = "Disimpan";
                header("Location:../detail-payment.php?id=$id_pembayaran_encrypt");
            } catch (Exception $e) {
                // Rollback transaksi jika ada kesalahan
                $connect->rollback();
                $_SESSION['info'] = "Data Gagal Disimpan: " . $e->getMessage();
                header("Location:../detail-payment.php?id=$id_pembayaran_encrypt");
                echo "Error: " . $e->getMessage(); // Tampilkan pesan error
            }
        } else {
            $id_bukti = htmlspecialchars($_POST['id_bukti']);
            $id_bank_sp = htmlspecialchars($_POST['id_bank_sp']);
            $nama_pengirim = htmlspecialchars($_POST['nama_pengirim']);
            $rek_pengirim = htmlspecialchars($_POST['rek_pengirim']);
            $id_bank_pengirim = htmlspecialchars($_POST['id_bank_pengirim']);
            $id_bank_select = htmlspecialchars($_POST['id_bank_select']);
            $id_bank_pt = htmlspecialchars($_POST['id_bank_pt']);

            $sql_pembelian = $connect->query("SELECT 
                                                    DAY(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) AS day_inv, 
                                                    LPAD(MONTH(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')), 2, '0') AS month_inv,
                                                    YEAR(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) AS year_inv,
                                                    ipl.no_trx,
                                                    ipl.jenis_trx,
                                                    sp.nama_sp
                                                FROM inv_pembelian_lokal AS ipl
                                                LEFT JOIN tb_supplier sp ON ipl.id_sp = sp.id_sp
                                                WHERE id_inv_pembelian  = '$id_inv'
                                            ");
            $data_pembelian = mysqli_fetch_array($sql_pembelian);
            $nama_sp = $data_pembelian['nama_sp'];
            $year = $data_pembelian['year_inv'];
            $month = $data_pembelian['month_inv'];
            $day = $data_pembelian['day_inv'];
            $jenis_trx = $data_pembelian['jenis_trx'];
            $no_trx = $data_pembelian['no_trx'];
            
            // Kondisi jenis invoice
            $jenis_inv = "";
            if($jenis_trx == "PPN"){
                $jenis_inv = "Invoice_Ppn";
            } else {
                $jenis_inv = "Invoice_Non_Ppn";
            }

            // Kondisi tf bank
            $id_bank_tf = "";
            if (isset($_POST['id_bank_pengirim'])) {
                $id_bank_tf = $id_bank_pengirim;
               

            } else if (isset($id_bank_select)) {
                $id_bank_tf = $id_bank_select;
            }
            
            // Konversikan no transaksi ke format yang diinginkan
            $no_trx_converted = str_replace('/', '_', $no_trx);
            
            // Enkripsi no_transaksi
            $no_trx_encrypt = encrypt($no_trx_converted, $key);

            // Gabungkan no transaksi asli, bagian yang dienkripsi, dan garis bawah
            $encrypt_folder = $no_trx_converted . '_' . $no_trx_encrypt;

            // untuk Membuat Folder Bukti Pembayaran
            $bukti_pembayaran = "Bukti_Transfer";

            $folder_path = "../../Supplier";
            $path = $nama_sp . "/" . $year . "/" . $month . "/" . $day . "/" . ucwords(strtolower(str_replace('_', ' ', $jenis_inv))) . "/" . $encrypt_folder ."/". $bukti_pembayaran . "/" ;

            // Set the path for the customer's folder
            $supplier_folder_path = $folder_path . "/" . $path;

            // Kondisi pembuatan folder
            $create_folder = "";
            if(!is_dir($supplier_folder_path)){
                $create_folder = mkdir($supplier_folder_path, 0777, true);
            }
                
            // Mendapatkan informasi file bukti transfer
            $file1_name = $_FILES['fileku1']['name'];
            $file1_tmp = $_FILES['fileku1']['tmp_name'];

            // Membuat nama baru untuk file berdasarkan tanggal, waktu, dan UUID
            $new_file1_name = "bukti_transfer_" . $year . $month . uniqid() . $day . ".jpg";

            // Menentukan lokasi tujuan untuk menyimpan file
            $file1_destination = $supplier_folder_path . $new_file1_name;


    
            // Begin transaction
            mysqli_begin_transaction($connect);
            try{
                // Upload bukti TF
                $upload_bukti = move_uploaded_file($file1_tmp, $file1_destination);

                // Simpan data bank supplier
                $cek_data = $connect->query("SELECT id_bank_sp, id_sp, id_bank, no_rekening FROM bank_sp WHERE id_sp = '$id_sp' AND id_bank = '$id_bank_tf' AND no_rekening = '$rek_pengirim'");

                $data_bank_sp =  mysqli_fetch_array($cek_data);
                $cek_id_bank_sp = $data_bank_sp['id_bank_sp'];

                $bank_sp_id = "";
                $bank_sp = "";
                if($cek_data->num_rows == 0){
                    $bank_sp_id = $id_bank_sp;
                    $bank_sp = $connect->query("INSERT INTO bank_sp
                                                    (id_bank_sp, id_sp, id_bank, no_rekening, atas_nama, created_by)
                                                    VALUES
                                                    ('$id_bank_sp', '$id_sp', '$id_bank_tf', '$rek_pengirim', '$nama_pengirim', '$nama_user')
                                            ");
                } else {
                    $bank_sp_id = $cek_id_bank_sp;
                    $bank_sp = "Data Ditemukan";
                }
                // Simpan pembayaran
                $finance_bayar = $connect->query( "INSERT INTO finance_bayar_pembelian 
                                                    (id_bayar, id_bank_pt, id_pembayaran, id_inv_pembelian, id_bukti, metode_pembayaran, keterangan_bayar, total_bayar, tgl_bayar, created_by)
                                                    VALUES
                                                    ('$id_bayar', '$id_bank_pt', '$id_pembayaran', '$id_inv', '$id_bukti', '$metode_pembayaran', '$keterangan_bayar', '$nominal', '$tgl_bayar', '$nama_user')"
                                                );
                // Simpan data bukti TF
                $bukti_tf = $connect->query("INSERT INTO finance_bukti_tf_pembelian 
                                                (id_bukti_tf, tf_bank, id_bank_sp, bukti_tf, path_tf, created_by)
                                                VALUES
                                                ('$id_bukti', '$id_bank_pt', '$bank_sp_id', '$new_file1_name', '$path', '$nama_user')
                                            ");

                if($upload_bukti && $bank_sp && $finance_bayar && $bukti_tf){
                    // Commit transaksi
                    mysqli_commit($connect);
                    $_SESSION['info'] = 'Disimpan';
                    header("Location:../detail-payment.php?id=$id_pembayaran_encrypt");
                } else {
                    // Rollback transaksi jika ada kesalahan
                    $error_message = mysqli_error($connect);
                    throw new Exception("Query error: $error_message");
                }

            }catch(Exception $e){
                // Rollback transaksi jika ada kesalahan
                $connect->rollback();
                $_SESSION['info'] = "Data Gagal Disimpan: " . $e->getMessage();
                // header("Location:../detail-payment.php?id=$id_pembayaran_encrypt");
                echo "Error: " . $e->getMessage(); // Tampilkan pesan error
            }
        } 
    }

?>