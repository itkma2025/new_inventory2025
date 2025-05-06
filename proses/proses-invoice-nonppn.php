<?php
    ob_start();
    require_once "../akses.php";
    // require_once __DIR__ . "/../koneksi-pengiriman.php";
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);
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
    $whoops_enabled = false; // Ubah menjadi false untuk menonaktifkan

    if ($whoops_enabled) {
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();
    }
    
    // Library sanitasi input data
    require_once "../function/sanitasi_input.php";

    // Function UUID 
    require_once "../function/uuid.php";

    if (isset($_POST['simpan-inv'])) {
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
        
        // Ambil data dari form dan lakukan sanitasi
        $id_spk = $_POST['id_spk']; // Perbaikan di sini
        $id_spk_escaped = array();
    
        // Escape each element of the $id_spk array
        foreach ($id_spk as $value) {
            $id_spk_escaped[] = $value; // Sanitasi di sini
        }
        // Lakukan sanitasi untuk input lainnya
        $id_inv_nonppn = $sanitasi_post['id_inv_nonppn'];
        $id_inv_nonppn_encrypt = encrypt($id_inv_nonppn, $key_global);
        $id_cb_nonppn = $sanitasi_post['id_cb_nonppn'];
        $no_inv_nonppn = $sanitasi_post['no_inv_nonppn'];
        $tgl_inv = $sanitasi_post['tgl_inv'];
        $cs = $sanitasi_post['cs'];
        $cs_inv = $sanitasi_post['cs_inv'];
        $jenis_inv = $sanitasi_post['jenis_inv'];
        $tgl_tempo = $sanitasi_post['tgl_tempo'];
        $no_po = $sanitasi_post['no_po'];
        $sp_disc = $sanitasi_post['sp_disc'];
        $note_inv = $sanitasi_post['note_inv'];
        $status_inv = 'Belum Dikirim';
        $status_spk = 'Invoice Sudah Diterbitkan';
        $status_cb = $sanitasi_post['status_cb'];
        $selected_cashback = trim($sanitasi_post['selected_cashback']);
        $cb_total_inv = $sanitasi_post['cb_total_inv'];
        $cb_pajak = $sanitasi_post['cb_pajak'];
        $nama_invoice = 'Invoice_Non_PPN';
        $kwitansi = isset($sanitasi_post['kwitansi']) ?  $sanitasi_post['kwitansi'] : ''; // Sanitasi
        $surat_jalan = isset($sanitasi_post['surat_jalan']) ? $sanitasi_post['surat_jalan'] : ''; // Sanitasi
        $created_by = $id_user;

        // Memecah string menjadi array berdasarkan pemisah koma
        $cashbackArray = explode(',', $selected_cashback);
        // Menginisialisasi array untuk menyimpan data yang didekripsi
        $decrypted_cashbacks = [];

        // Melakukan dekripsi untuk setiap elemen di array
        foreach ($cashbackArray as $cashback) {
            // Trim untuk menghapus spasi ekstra sebelum dekripsi
            $cashback = trim($cashback);
            $decrypted_cashbacks[] = decrypt($cashback, $key_global);
        }
        // Menggabungkan hasil dekripsi kembali dengan pemisah koma
        $selected_cashback_decrypt = implode(',', $decrypted_cashbacks);   

        // Proses pembuatan path
        // Convert $no_inv_nonppn to the desired format 
        $no_inv_nonppn_converted = str_replace('/', '_', $no_inv_nonppn);

        // Generate folder name based on invoice details
        $folder_name = $no_inv_nonppn_converted;

        // Encode a portion of the folder name
        $encoded_portion = base64_encode($folder_name);

        // Combine the original $no_inv_nonppn, encoded portion, and underscore
        $encoded_folder_name = $no_inv_nonppn_converted . '_' . $encoded_portion;

        // Set the path for the customer's folder
        $customer_folder_path = "../Customer/" . $cs . "/" . date('Y') . "/" . date('m') . "/" . date('d') . "/" . ucwords(strtolower(str_replace('_', ' ', $nama_invoice))) . "/" . $encoded_folder_name;

        // Create the customer's folder if it doesn't exist
        if (!is_dir($customer_folder_path)) {
            mkdir($customer_folder_path, 0777, true); // Set permission to 0777 to ensure the folder is writable
        }

        try {
            // Mulai transaksi
            $connect->begin_transaction();

            // Simpan data invoice
            $stmt = $connect->prepare("INSERT IGNORE INTO inv_nonppn 
                                        (id_inv_nonppn, no_inv, tgl_inv, cs_inv, tgl_tempo, sp_disc, kwitansi, surat_jalan, note_inv, kategori_inv, ongkir, status_transaksi, nama_invoice, created_by)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssssisss", $id_inv_nonppn, $no_inv_nonppn, $tgl_inv, $cs_inv, $tgl_tempo, $sp_disc, $kwitansi, $surat_jalan, $note_inv, $jenis_inv, $ongkir, $status_inv, $nama_invoice, $created_by);
            $simpan_inv = $stmt->execute();
            if (!$simpan_inv) {
                throw new Exception("Error simpan data invoice: " . $stmt->error);
            }

            // Simpan data cashback
            $stmt = $connect->prepare("INSERT IGNORE INTO cashback_nonppn 
                                        (id_cb_nonppn, id_inv, status_cb, jenis_cb, cb_total_inv, cb_pajak, created_by)
                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisiis", $id_cb_nonppn, $id_inv_nonppn, $status_cb, $selected_cashback_decrypt, $cb_total_inv, $cb_pajak, $created_by);
            $simpan_cb = $stmt->execute();
            if (!$simpan_cb) {
                throw new Exception("Error simpan data invoice: " . $stmt->error);
            }

            // Siapkan prepared statement untuk update SPK
            $stmt_spk = $connect->prepare("UPDATE spk_reg SET id_inv = ?, no_po = ?, status_spk = ? WHERE id_spk_reg = ?");
            if (!$stmt_spk) {
                throw new Exception("Error preparing statement for SPK: " . $connect->error);
            }

            // Update data SPK
            $id_spk_count = count($id_spk_escaped);
            for ($i = 0; $i < $id_spk_count; $i++) {
                $id_spk_array = $id_spk_escaped[$i];
                $id_spk_array_decrypt = decrypt($id_spk_array, $key_global);

                // Bind parameter untuk setiap SPK
                $stmt_spk->bind_param("ssss", $id_inv_nonppn, $no_po, $status_spk, $id_spk_array_decrypt);

                // Eksekusi update SPK
                if (!$stmt_spk->execute()) {
                    throw new Exception("Error updating SPK data: " . $stmt_spk->error);
                }
            }

            // Commit transaksi jika semua berhasil
            $connect->commit();
            $_SESSION['info'] = "Invoice berhasil dibuat";

            // Tutup statement setelah selesai digunakan
            $stmt->close();
            $stmt_spk->close();

            // Redirect ke halaman invoice
            header("Location:../detail-produk-proforma.php?jenis=nonppn&id=$id_inv_nonppn_encrypt");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $connect->rollback();
            $error_message = "Gagal saat proses data: " . $e->getMessage();
            echo $error_message;
            // $_SESSION['info'] = "Data Gagal Disimpan";
            // Redirect ke halaman error
            // header("Location:../spk-siap-kirim.php?sort=baru");
            exit();
        }
    // ======================================================================================
    } else if (isset($_POST['simpan-cek-harga'])) {
        $sanitasi_post = sanitizeInput($_POST);
        $id_trx = $sanitasi_post['id_trx'];
        $nama_produk = $sanitasi_post['nama_produk'];
        $harga_produk = $sanitasi_post['harga_produk'];
        $disc = isset($sanitasi_post['disc']) ? $sanitasi_post['disc'] : [];
        $disc_cb = isset($sanitasi_post['disc_cb']) ? $sanitasi_post['disc_cb'] : [];
        $qty = $sanitasi_post['qty'];
        $update_status_trx = 1;

        try {
            // Mulai transaksi
            $connect->begin_transaction();
        
            // Persiapkan query untuk prepared statement
            $update_query = $connect->prepare("UPDATE transaksi_produk_reg 
                                               SET
                                                   nama_produk_spk = ?, 
                                                   harga = ?, 
                                                   disc = ?, 
                                                   disc_cb = ?, 
                                                   total_harga = ?, 
                                                   total_cb = ?, 
                                                   status_trx = ?
                                               WHERE id_transaksi = ?");
        
            // Loop untuk melakukan update untuk setiap transaksi
            for ($i = 0; $i < count($id_trx); $i++) {
                $id_trx_array = decrypt($id_trx[$i], $key_global);
                $nama_produk_array = $nama_produk[$i];
                $harga =  str_replace('.', '', $harga_produk[$i]);
                $harga = intval($harga);
                $disc_array = isset($disc[$i]) ? $disc[$i] : '0';
                $disc_cb_array = isset($disc_cb[$i]) ? $disc_cb[$i] : '0';
                $qty_array = str_replace('.', '', $qty[$i]);
                $qty_array = intval($qty_array);
                $update_status_trx_array = $update_status_trx;
                
                // Menghitung total harga dan diskon
                $total_harga = $harga * $qty_array;
                $total_harga_produk = $total_harga * (1 - ($disc_array / 100));
                $total_cb =  $total_harga * ($disc_cb_array / 100);
        
                // Bind parameter untuk setiap iterasi
                $update_query->bind_param("sdssddss", $nama_produk_array, $harga, $disc_array, $disc_cb_array, $total_harga_produk, $total_cb, $update_status_trx_array, $id_trx_array);
                
                // Eksekusi query untuk setiap transaksi
                $update_query->execute();
            }
        
            // Commit transaction jika semua query berhasil
            $connect->commit();
            $_SESSION['info'] = "Disimpan";
            header("Location:../invoice-reguler.php?sort=baru");
        
        } catch (Exception $e) {
            // Rollback transaction jika terjadi kesalahan
            $connect->rollback();
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../invoice-reguler.php?sort=baru");
        }
    // ======================================================================================
    } else if (isset($_POST['ubah-kategori'])) {
        $sanitasi_post = sanitizeInput($_POST);
        $id_inv = $_POST['id_inv'];
        $id_inv_decrypt = decrypt($id_inv, $key_global);
        $kat_inv = $_POST['kat_inv'];
        $sp_disc_edit = isset($sanitasi_post['spdisc_edit']) ? $sanitasi_post['spdisc_edit'] : '0';
        $sp_disc = isset($sanitasi_post['spdisc']) ? $sanitasi_post['spdisc'] : '0';

        try {
            // Mulai transaksi
            $connect->begin_transaction();

            if (empty($kat_inv)){
                if (!empty($sp_disc_edit)){
                    $stmt = $connect->prepare("UPDATE inv_nonppn SET sp_disc = ? WHERE id_inv_nonppn = ?");
                    $stmt->bind_param('ss', $sp_disc_edit, $id_inv_decrypt);
                    $update_inv = $stmt->execute();

                    if($update_inv){
                        // Commit transaction jika semua query berhasil
                        $connect->commit();
                        $_SESSION['info'] = "Disimpan";
                        header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
                    } else {
                        $connect->rollback();
                        throw new Exception(); 
                    }
                }
            } else {
                // Proses update invoice
                $stmt = $connect->prepare("UPDATE inv_nonppn SET kategori_inv = ?, sp_disc = ? WHERE id_inv_nonppn = ?");
                $stmt->bind_param('sss', $kat_inv, $sp_disc, $id_inv_decrypt);
                $update_inv = $stmt->execute();

                // Proses update transaksi produk reg
                $stmt = $connect->prepare("UPDATE transaksi_produk_reg AS tpr
                                            JOIN spk_reg AS sr ON (tpr.id_spk = sr.id_spk_reg)
                                            JOIN inv_nonppn AS nonppn ON (sr.id_inv = nonppn.id_inv_nonppn)
                                            SET tpr.disc = '0',
                                                tpr.total_harga = tpr.harga * tpr.qty
                                            WHERE nonppn.id_inv_nonppn = ?");
                $stmt->bind_param('s', $id_inv_decrypt);
                $update_trx = $stmt->execute();
                if($update_inv && $update_trx){
                    // Commit transaction jika semua query berhasil
                    $connect->commit();
                    $_SESSION['info'] = "Disimpan";
                    header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
                } else {
                    $connect->rollback();
                    throw new Exception(); 
                }
            }
            
        } catch (Exception $e) {
            // Rollback transaction jika terjadi kesalaha n
            $connect->rollback();
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
        }
    // ======================================================================================
    } else if (isset($_POST['add-spk'])) {
        $sanitasi_post = sanitizeInput($_POST);
        $id_spk = $sanitasi_post['id_spk'];
        $id_inv = $sanitasi_post['id_inv'];
        $id_inv_decrypt = decrypt($id_inv, $key_global);
        $status_spk = 'Invoice Sudah Diterbitkan';

        for ($i = 0; $i < count($id_spk); $i++) {
            $id_spk_array = $sanitasi_post['id_spk'][$i];
            $id_spk_array_decrypt = decrypt($id_spk_array, $key_global);

            try {
                // Mulai transaksi
                $connect->begin_transaction();

                $stmt = $connect->prepare("UPDATE spk_reg SET id_inv = ?, status_spk = ? WHERE id_spk_reg = ?");
                $stmt->bind_param("sss", $id_inv_decrypt, $status_spk, $id_spk_array_decrypt);
                $update_spk = $stmt->execute();

                $stmt = $connect->prepare("UPDATE transaksi_produk_reg SET status_trx = '0'  WHERE id_spk = ?");
                $stmt->bind_param("s", $id_spk_array_decrypt);
                $update_trx = $stmt->execute();
                
                if($update_spk && $update_trx){
                    // Commit transaction jika semua query berhasil
                    $connect->commit();
                    $_SESSION['info'] = "Disimpan";
                    header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
                } else {
                    $connect->rollback();
                    throw new Exception(); 
                }
            } catch (Exception $e) {
                // Rollback transaction jika terjadi kesalahan
                $connect->rollback();
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
            }
        }
    // ======================================================================================
    } else if (isset($_POST['ubah-dikirim'])) {
        $sanitasi_post = sanitizeInput($_POST);
        // Validasi nonce token
        if (!isset($sanitasi_post['nonce_token']) || $sanitasi_post['nonce_token'] !== $_SESSION['nonce_token']) {
            // Jika token tidak cocok atau tidak ada, hentikan proses dan tampilkan error
            $_SESSION['info'] = "Silahkan Ulangi Kembali";
            header("Location:../invoice-reguler.php?sort=baru");
            exit();
        }
        // // Nonce sudah divalidasi, sekarang hapus dari session agar tidak bisa digunakan lagi
        unset($_SESSION['nonce_token']);

        $id_status = $sanitasi_post['id_status'];
        $id_inv = $sanitasi_post['id_inv'];
        $id_inv_decrypt = decrypt($id_inv, $key_global);
        $jenis_pengiriman = $sanitasi_post['jenis_pengiriman'];
        $tgl = $sanitasi_post['tgl'];
        $jenis_inv = "nonppn";
        $uuid = uuid();
        $img_uuid = img_uuid();
        $year = date('y');
        $day = date('d');
        $month = date('m');
        $id_inv_penerima = "BKTI" . $year . "" . $uuid . "" . $day;
        $id_inv = $sanitasi_post['id_inv'];
        if ($jenis_pengiriman == 'Driver') {
            $pengirim = $_POST['pengirim'];
            try {
                // Mulai transaksi
                $connect->begin_transaction();

                // Update Invoice
                $stmt = $connect->prepare("UPDATE inv_nonppn SET status_transaksi = 'Dikirim' WHERE id_inv_nonppn = ?");
                $stmt->bind_param('s', $id_inv_decrypt);
                $update_inv = $stmt->execute();
                
                // Update status Kirim
                $stmt = $connect->prepare("INSERT INTO status_kirim
                                                    (id_status_kirim, id_inv, jenis_inv, jenis_pengiriman, dikirim_driver, tgl_kirim)
                                                    VALUES 
                                                    (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssss', $id_status, $id_inv_decrypt, $jenis_inv, $jenis_pengiriman, $pengirim, $tgl);
                $update_status_kirim = $stmt->execute();
                

                if ($update_inv && $update_status_kirim) {
                    // Commit transaksi jika berhasil
                    $connect->commit();
                    $_SESSION['info'] = "Disimpan";
                    header("Location:../invoice-reguler.php?sort=baru");
                } else {
                    $connect->rollback();
                    throw new Exception(); 
                }
            } catch (Exception $e) {
                $connect->rollback();
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../invoice-reguler.php?sort=baru");
                
            }
        } else if ($jenis_pengiriman == 'Diambil Langsung') {
            $id_inv_penerima = "PNMR" . $year . "" . $month . "" . $uuid . "" . $day;
            try {
                // Mulai transaksi
                $connect->begin_transaction();

                // Update Invoice
                $stmt = $connect->prepare("UPDATE inv_nonppn SET status_transaksi = 'Dikirim' WHERE id_inv_nonppn = ?");
                $stmt->bind_param('s', $id_inv_decrypt);
                $update_inv = $stmt->execute();

                // Update Status Kirim
                $stmt = $connect->prepare("INSERT INTO status_kirim
                                                    (id_status_kirim, id_inv, jenis_inv, jenis_pengiriman, jenis_penerima, tgl_kirim)
                                                    VALUES 
                                                    (?, ?, ?, ?, 'Customer', ?)");
                $stmt->bind_param('sssss', $id_status, $id_inv_decrypt, $jenis_inv, $jenis_pengiriman, $tgl);
                $update_status_kirim = $stmt->execute();

                // Update Invoice penerima
                $stmt = $connect->prepare("INSERT INTO inv_penerima 
                                                        (id_inv_penerima, id_inv, alamat) 
                                                        VALUES 
                                                        (?, ?, 'PT. Karsa Mandiri Alkesindo')");
                $stmt->bind_param('ss', $id_inv_penerima, $id_inv_decrypt);
                $update_inv_penerima = $stmt->execute();
                
                if ($update_inv && $update_status_kirim && $update_inv_penerima) {
                    // Commit transaksi jika berhasil
                    $connect->commit();
                    $_SESSION['info'] = "Disimpan";
                    header("Location:../invoice-reguler.php?sort=baru");
                } else {
                    $connect->rollback();
                    throw new Exception(); 
                }

            } catch (Exception $e) {
                $connect->rollback();
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../invoice-reguler.php?sort=baru");
                
            }
        } else if ($jenis_pengiriman == 'Ekspedisi') {
            $ekspedisi = $sanitasi_post['ekspedisi'];
            $jenis_penerima = 'Ekspedisi';
            $dikirim = $sanitasi_post['dikirim'];
            $pj = $sanitasi_post['pj'];
            try {
                // Mulai transaksi
                $connect->begin_transaction();

                // Update Invoice
                $stmt = $connect->prepare("UPDATE inv_nonppn SET status_transaksi = 'Dikirim' WHERE id_inv_nonppn = ?");
                $stmt->bind_param('s', $id_inv_decrypt);
                $update_inv = $stmt->execute();

                // Update Status Kirim
                $stmt = $connect->prepare("INSERT INTO status_kirim
                                                    (id_status_kirim, id_inv, jenis_inv, jenis_pengiriman, jenis_penerima, dikirim_ekspedisi, dikirim_oleh, penanggung_jawab, tgl_kirim) 
                                                    VALUES 
                                                    (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssssss',$id_status, $id_inv_decrypt, $jenis_inv, $jenis_pengiriman, $jenis_penerima, $ekspedisi, $dikirim, $pj, $tgl);
                $update_status_kirim = $stmt->execute();

                
                if ($update_inv && $update_status_kirim) {
                    // Commit transaksi jika berhasil
                    $connect->commit();
                    $_SESSION['info'] = "Disimpan";
                    header("Location:../invoice-reguler.php?sort=baru");
                } else {
                    $connect->rollback();
                    throw new Exception(); 
                }
            } catch (Exception $e) {
                $connect->rollback();
                // $error_message = "Gagal saat proses data: " . $e->getMessage();
                // echo $error_message;
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../invoice-reguler.php?sort=baru");
            }
        } else {
            header("Location:../404.php");
        }
    // ======================================================================================
    } else if (isset($_POST['update-ongkir'])) {
        $id_inv = $_POST['id_inv'];
        $id_inv_encode = base64_encode($id_inv);
        $ongkir = str_replace(',', '', $_POST['ongkir']); // Menghapus tanda ribuan (,)
        $ongkir = intval($ongkir); // Mengubah string harga menjadi integer

        $update_data = mysqli_query($connect, "UPDATE inv_nonppn SET ongkir = '$ongkir' WHERE id_inv_nonppn = '$id_inv'");
        header("Location:../cek-produk-inv-nonppn.php?id='$id_inv_encode'");
    // ======================================================================================
    } else if (isset($_POST['ubah-cs-inv'])) {
        $sanitasi_post = sanitizeInput($_POST);
        $id_inv = $sanitasi_post['id_inv'];
        $id_inv_decrypt = decrypt($id_inv, $key_global);
        $cs_inv = $sanitasi_post['cs_inv'];
        $alamat = $sanitasi_post['alamat'];
        $no_po = $sanitasi_post['no_po'];
        $kwitansi = isset($sanitasi_post['kwitansi']) ?  $sanitasi_post['kwitansi'] : ''; // Sanitasi
        $surat_jalan = isset($sanitasi_post['surat_jalan']) ? $sanitasi_post['surat_jalan'] : ''; // Sanitasi
        $status_cb = $sanitasi_post['status_cb'];
        $updated_by = $id_user;
        $updated_date = date('Y-m-d H:i:s');


        try {
            // Mulai transaksi
            $connect->begin_transaction();

            if($status_cb == '0'){
                $stmt = $connect->prepare("UPDATE cashback_nonppn SET status_cb = ?, jenis_cb = '', cb_total_inv = '', cb_pajak = '', updated_date = ?, updated_by = ? WHERE id_inv = ?");
                $stmt->bind_param("ssss", $status_cb, $updated_date, $updated_by, $id_inv_decrypt);
                $update_status_cb = $stmt->execute();
            } else {
                $stmt = $connect->prepare("UPDATE cashback_nonppn SET status_cb = ?, updated_date = ?, updated_by = ? WHERE id_inv = ?");
                $stmt->bind_param("ssss", $status_cb, $updated_date, $updated_by, $id_inv_decrypt);
                $update_status_cb = $stmt->execute();
            }

            $stmt = $connect->prepare("UPDATE inv_nonppn SET cs_inv = ?, alamat_inv = ?, kwitansi = ?, surat_jalan = ?, updated_date = ?, updated_by = ? WHERE id_inv_nonppn = ?");
            $stmt->bind_param("ssiisss", $cs_inv, $alamat, $kwitansi, $surat_jalan, $updated_date, $updated_by, $id_inv_decrypt);
            $update_inv = $stmt->execute();

            $stmt = $connect->prepare("UPDATE spk_reg SET no_po = ?, updated_date = ?, updated_by = ? WHERE id_inv = ?");
            $stmt->bind_param("ssss", $no_po, $updated_date, $updated_by, $id_inv_decrypt);
            $update_spk = $stmt->execute();

            

            if($update_inv && $update_spk && $update_status_cb){
                // Commit transaction jika semua query berhasil
                $connect->commit();
                $_SESSION['info'] = "Disimpan";
                header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
            } else {
                throw new Exception("Error updating SPK data: " . $stmt_spk->error);
            }
        } catch (Exception $e) {
            // Rollback transaction jika terjadi kesalahan
            $connect->rollback();
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
        }
    // ======================================================================================
    } else if (isset($_POST['ubah-jenis-cb'])){
        $sanitasi_post = sanitizeInput($_POST);
        $id_inv = $sanitasi_post['id_inv'];
        $id_inv_decrypt = decrypt($id_inv, $key_global);
        $selected_cashback = trim($sanitasi_post['selected_cashback']);
        $cb_total_inv = $sanitasi_post['cb_total_inv'];
        $cb_pajak = $sanitasi_post['cb_pajak'];
        $updated_date = date('Y-m-d H:i:s');
        $updated_by = $id_user;
        // Memecah string menjadi array berdasarkan pemisah koma
        $cashbackArray = explode(',', $selected_cashback);
        // Menginisialisasi array untuk menyimpan data yang didekripsi
        $decrypted_cashbacks = [];

        // Melakukan dekripsi untuk setiap elemen di array
        foreach ($cashbackArray as $cashback) {
            // Trim untuk menghapus spasi ekstra sebelum dekripsi
            $cashback = trim($cashback);
            $decrypted_cashbacks[] = decrypt($cashback, $key_global);
        }
        // Menggabungkan hasil dekripsi kembali dengan pemisah koma
        $selected_cashback_decrypt = implode(',', $decrypted_cashbacks);   
    
        $stmt = $connect->prepare("UPDATE cashback_nonppn 
                                    SET 
                                        jenis_cb = ?, 
                                        cb_total_inv = ?, 
                                        cb_pajak = ?, 
                                        updated_date = ?,
                                        updated_by = ?
                                    WHERE id_inv = ?");
        $stmt->bind_param("ssssss", $selected_cashback_decrypt, $cb_total_inv, $cb_pajak, $updated_date, $updated_by, $id_inv_decrypt);
        $update_cb = $stmt->execute();

        if($update_cb){
            $_SESSION['info'] = "Diupdate";
            header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
        } else {
            $_SESSION['info'] = "Data Gagal Diupdate";
            header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
        }
    // ======================================================================================
    } else if (isset($_POST['edit-produk-proforma'])){
        $sanitasi_post = sanitizeInput($_POST);
        // // Validasi nonce token
        if (!isset($sanitasi_post['nonce_token']) || $sanitasi_post['nonce_token'] !== $_SESSION['nonce_token']) {
            // Jika token tidak cocok atau tidak ada, hentikan proses dan tampilkan error
            $_SESSION['info'] = "Silahkan Ulangi Kembali";
            header("Location:../invoice-reguler.php?sort=baru");
            exit();
        }
        // Nonce sudah divalidasi, sekarang hapus dari session agar tidak bisa digunakan lagi
        unset($_SESSION['nonce_token']);

        $id_trx = decrypt($sanitasi_post['id_trx'], $key_global);
        $id_inv = $sanitasi_post['id_inv'];
        $nama_produk = $sanitasi_post['nama_produk'];
        $harga = $sanitasi_post['harga_produk'];
        $harga = str_replace('.', '', $harga); // Menghapus tanda ribuan (,)
        $harga = intval($harga); // Mengubah string harga menjadi integer
        $qty = $sanitasi_post['qty'];
        $qty = str_replace('.', '', $qty); // Menghapus tanda ribuan (,)
        $qty = intval($qty); // Mengubah string harga menjadi integer
        $disc = isset($sanitasi_post['disc']) ? $sanitasi_post['disc'] : '0';
        $disc_cb = isset($sanitasi_post['disc_cb']) ? $sanitasi_post['disc_cb'] : '0';
        $total = $harga * $qty;
        $total_disc = $total * $disc / 100;
        $grand_total = $total - $total_disc;
        $total_cb =  $total * ($disc_cb / 100);
        $updated_by = $id_user;

        $stmt = $connect->prepare("UPDATE transaksi_produk_reg SET nama_produk_spk = ?, harga = ?, disc = ?, disc_cb = ?, total_harga = ?, total_cb = ?, updated_date = ?, updated_by = ? WHERE id_transaksi = ?"); 
        $stmt->bind_param("sissiisss", $nama_produk, $harga, $disc, $disc_cb, $grand_total, $total_cb, $datetime_now, $updated_by, $id_trx);
        $update_harga = $stmt->execute();

        if($update_harga){
            $_SESSION['info'] = "Diupdate";
            header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
        } else {
            $_SESSION['info'] = "Data Gagal Diupdate";
            header("Location:../detail-produk-proforma.php?jenis=nonppn&&id=$id_inv");
        }
    }   

    ob_start();
?>