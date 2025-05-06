<?php
include("../akses.php");
require_once "../function/function-enkripsi.php";
if (isset($_POST['ubah-status'])) {
    $uuid = uuid();
    $day = date('d');
    $month = date('m');
    $year = date('y');
    $id_status_kirim_revisi = "SKREV-" . $year . "" . $month . "" . $uuid . "" . $day;
    $id_trx_rev = "TRXREV-" . $year . "" . $month . "" . $uuid . "" . $day;
    $id_inv_rev = "INVREV-" . $year . "" . $month . "" . $uuid . "" . $day;
    $status_kirim = htmlspecialchars($_POST['status_kirim']);
    $id_komplain = htmlspecialchars($_POST['id_komplain']);
    $id_komplain_encrypt = encrypt($id_komplain, $key_spk);
    $id_inv = htmlspecialchars($_POST['id_inv']);
    $no_inv = htmlspecialchars($_POST['no_inv']);
    $revisi_invoice = reviseInvoice($no_inv);
    $tgl = htmlspecialchars($_POST['tgl']);
    $cs_inv = htmlspecialchars($_POST['cs_inv']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $total_inv = htmlspecialchars($_POST['total_inv']);
    if ($status_kirim == 'dikirim') {
        $jenis_pengiriman = $_POST['jenis_pengiriman'];
        if ($jenis_pengiriman == 'Driver') {
            $pengirim = $_POST['pengirim'];
            try {
                // Begin transaction
                mysqli_begin_transaction($connect);
                // Simpan status kirim
                $stmt = $connect->prepare("INSERT INTO revisi_status_kirim (id_status_kirim_revisi, id_komplain, jenis_pengiriman, dikirim_driver, tgl_kirim) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssss', $id_status_kirim_revisi, $id_komplain, $jenis_pengiriman, $pengirim, $tgl);
                $simpan_status_kirim = $stmt->execute();

                if (!$simpan_status_kirim) {
                    throw new Exception("Gagal simpan data: " . $stmt->error);
                }

                // Simpan Inv Revisi
                $stmt =  $connect->prepare("INSERT INTO inv_revisi (id_inv_revisi, id_inv, no_inv_revisi, tgl_inv_revisi, pelanggan_revisi, alamat_revisi, total_inv, status_pengiriman, status_trx_komplain, status_trx_selesai) VALUES (?, ?, ?, ?, ?, ?, ?,  0, 0, 0)");
                $stmt->bind_param('sssssss', $id_inv_rev, $id_inv, $revisi_invoice, $tgl, $cs_inv, $alamat, $total_inv);
                $simpan_inv_rev = $stmt->execute();

                if (!$simpan_inv_rev) {
                    throw new Exception("Gagal simpan data: " . $stmt->error);
                }

                // Prepare statement before update data invoice
                $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Komplain Dikirim' WHERE id_inv_bum = ?");
                $stmt->bind_param('s', $id_inv);
                $update_inv = $stmt->execute();

                if (!$update_inv) {
                    throw new Exception("Gagal hapus data: " . $stmt->error);
                }

                // Commit transaksi jika semua berhasil
                $connect->commit();
                $_SESSION['info'] = "Disimpan";
                header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
                exit();
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                $connect->rollback();
                $error_message = "Gagal saat proses data: " . $e->getMessage();
                echo $error_message;
                // $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
                exit();
            }
        } else if ($jenis_pengiriman == 'Ekspedisi') {
            $ekspedisi = htmlspecialchars($_POST['ekspedisi']);
            $jenis_pengiriman = htmlspecialchars($_POST['jenis_pengiriman']);
            $jenis_penerima = 'Ekspedisi';
            $resi = htmlspecialchars($_POST['resi']);
            $jenis_ongkir = htmlspecialchars($_POST['jenis_ongkir']);
            $ongkir = str_replace('.', '', $_POST['ongkir']); // Menghapus tanda ribuan (,)
            $ongkir = intval($ongkir); // Mengubah string harga menjadi integer
            $free_ongkir = htmlspecialchars($_POST['free_ongkir']);
            $dikirim = htmlspecialchars($_POST['dikirim']);
            $pj = htmlspecialchars($_POST['pj']);
            $uuid = generate_uuid();
            $img_uuid = img_uuid();
            $year = date('y');
            $day = date('d');
            $month = date('m');
            $id_inv_bukti = "BKTI-REV" . $year . "" . $month . "" . $uuid . "" . $day;

            // Proses upload file
            $allowedExtensions = ['png', 'jpeg', 'jpg', 'pdf']; // Ekstensi file yang diizinkan
            $file_name = $_FILES['fileku']['name'];
            $file_tmp = $_FILES['fileku']['tmp_name'];
            $new_file_name = "Bukti_Satu" . $year . $month . $img_uuid . $day . ".jpg";
            $path = "../gambar-revisi/bukti1/" . $new_file_name;

            // Memeriksa ekstensi file yang diunggah
            $fileInfo = pathinfo($file_name);
            $fileExtension = strtolower($fileInfo['extension']);

            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception('Jenis file tidak didukung. Hanya file dengan ekstensi .png, .jpeg, .jpg, dan .pdf yang diizinkan.');
            }

            // Proses Upload
            if (!move_uploaded_file($file_tmp, $path)) {
                throw new Exception('Gagal mengupload file.');
            }

            try {
                mysqli_begin_transaction($connect);
                // Proses simpan status kirim revisi
                $stmt = $connect->prepare("INSERT INTO revisi_status_kirim 
                                                                (id_status_kirim_revisi, id_komplain, jenis_pengiriman, jenis_penerima, dikirim_ekspedisi, no_resi, jenis_ongkir, ongkir, free_ongkir, dikirim_oleh, penanggung_jawab, tgl_kirim) 
                                                        VALUES  (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssssssssss', $id_status_kirim_revisi, $id_komplain, $jenis_pengiriman, $jenis_penerima, $ekspedisi, $resi, $jenis_ongkir, $ongkir, $free_ongkir, $dikirim, $pj, $tgl);
                $simpan_status_kirim = $stmt->execute();

                if (!$simpan_status_kirim) {
                    throw new Exception("Gagal simpan data: " . $stmt->error);
                }

                // Proses simpan inv revisi
                $stmt = $connect->prepare("INSERT INTO inv_revisi 
                                                                (id_inv_revisi, id_inv, no_inv_revisi, tgl_inv_revisi, pelanggan_revisi, alamat_revisi, total_inv, status_pengiriman, status_trx_komplain, status_trx_selesai) 
                                                        VALUES  (?, ?, ?, ?, ?, ?, ?, 0, 0, 0)");
                $stmt->bind_param('sssssss', $id_inv_rev, $id_inv, $revisi_invoice, $tgl, $cs_inv, $alamat, $total_inv);
                $simpan_inv_revisi = $stmt->execute();

                if (!$simpan_inv_revisi) {
                    throw new Exception("Gagal simpan data: " . $stmt->error);
                }

                // Proses simpan bukti terima
                $stmt = $connect->prepare("INSERT INTO inv_bukti_terima_revisi 
                                                                (id_bukti_terima, id_komplain, bukti_satu) 
                                                        VALUES  (?, ?, ?)");
                $stmt->bind_param('sss', $id_inv_bukti, $id_komplain, $new_file_name);
                $bukti_terima = $stmt->execute();

                if (!$bukti_terima) {
                    throw new Exception("Gagal simpan data: " . $stmt->error);
                }

                // Prepare statement before update data invoice
                $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Komplain Dikirim' WHERE id_inv_bum = ?");
                $stmt->bind_param('s', $id_inv);
                $update_inv = $stmt->execute();

                if (!$update_inv) {
                    throw new Exception("Gagal hapus data: " . $stmt->error);
                }


                // Commit transaksi jika semua berhasil
                $connect->commit();
                $_SESSION['info'] = "Disimpan";
                header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
                exit();
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                $connect->rollback();
                // $error_message = "Gagal saat proses data: " . $e->getMessage();
                // echo $error_message;
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
                exit();
            }
        } else if ($jenis_pengiriman == 'Diambil Langsung') {
            $diambil_oleh = $_POST['diambil_oleh'];
            $diambil_oleh = ltrim($diambil_oleh, " \t"); // Menghapus spasi dan tab di awal teks
            $uuid = generate_uuid();
            $img_uuid = img_uuid();
            $year = date('y');
            $day = date('d');
            $month = date('m');
            $id_inv_bukti = "BKTI-REV" . $year . "" . $month . "" . $uuid . "" . $day;
            $id_inv_penerima_revisi = "PNMR-REV" . $year . "" . $month . "" . $uuid . "" . $day;
            // Proses upload file
            $allowedExtensions = ['png', 'jpeg', 'jpg', 'pdf']; // Ekstensi file yang diizinkan
            $file_name = $_FILES['fileku']['name'];
            $file_tmp = $_FILES['fileku']['tmp_name'];
            $new_file_name = "Bukti_Satu" . $year . $month . $img_uuid . $day . ".jpg";
            $path = "../gambar-revisi/bukti1/" . $new_file_name;

            // Memeriksa ekstensi file yang diunggah
            $fileInfo = pathinfo($file_name);
            $fileExtension = strtolower($fileInfo['extension']);

            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception('Jenis file tidak didukung. Hanya file dengan ekstensi .png, .jpeg, .jpg, dan .pdf yang diizinkan.');
            }

            // Proses Upload
            if (!move_uploaded_file($file_tmp, $path)) {
                throw new Exception('Gagal mengupload file.');
            }

            try {
                // Begin transaction
                mysqli_begin_transaction($connect);

                $stmt = $connect->prepare("INSERT INTO revisi_status_kirim 
                                                                (id_status_kirim_revisi, id_komplain, jenis_pengiriman, diambil_oleh, tgl_kirim) 
                                                        VALUES  (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssss', $id_status_kirim_revisi, $id_komplain, $jenis_pengiriman, $diambil_oleh, $tgl);
                $simpan_status_kirim = $stmt->execute();

                if (!$simpan_status_kirim) {
                    throw new Exception("Gagal simpan data: " . $stmt->error);
                }

                $stmt = $connect->prepare("INSERT INTO inv_revisi 
                                                                (id_inv_revisi, id_inv, no_inv_revisi, tgl_inv_revisi, pelanggan_revisi, alamat_revisi, total_inv, status_pengiriman, status_trx_komplain, status_trx_selesai) VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1, 0)");
                $stmt->bind_param('sssssss', $id_inv_rev, $id_inv, $revisi_invoice, $tgl, $cs_inv, $alamat, $total_inv);
                $simpan_inv_revisi = $stmt->execute();

                if (!$simpan_inv_revisi) {
                    throw new Exception("Gagal simpan data: " . $stmt->error);
                }


                $stmt = $connect->prepare("INSERT INTO inv_bukti_terima_revisi 
                                                                (id_bukti_terima, id_komplain, bukti_satu) 
                                                        VALUES  (?, ?, ?)");
                $stmt->bind_param('sss', $id_inv_bukti, $id_komplain, $new_file_name);
                $bukti_terima = $stmt->execute();

                if (!$simpan_inv_revisi) {
                    throw new Exception("Gagal simpan data: " . $stmt->error);
                }

                $stmt = $connect->prepare("INSERT INTO inv_penerima_revisi 
                                                                (id_inv_penerima_revisi, id_komplain, nama_penerima, alamat, tgl_terima) 
                                                        VALUES (?, ?, ?, 'PT. Karsa Mandiri Alkesindo', ?)");
                $stmt->bind_param("ssss", $id_inv_penerima_revisi, $id_komplain, $diambil_oleh, $tgl);
                $query_diterima = $stmt->execute();

                if (!$query_diterima) {
                    throw new Exception("Gagal simpan data: " . $stmt->error);
                }

                // Prepare statement before update data invoice
                $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Komplain Diambil' WHERE id_inv_bum = ?");
                $stmt->bind_param('s', $id_inv);
                $update_inv = $stmt->execute();

                if (!$update_inv) {
                    throw new Exception("Gagal hapus data: " . $stmt->error);
                }

                // Commit transaksi jika semua berhasil
                $connect->commit();
                $_SESSION['info'] = "Disimpan";
                header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
                exit();
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                $connect->rollback();
                $error_message = "Gagal saat proses data: " . $e->getMessage();
                echo $error_message;
                $_SESSION['info'] = "Data Gagal Disimpan";
                // header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
                exit();
            }
        } else {
            header("Location:../404.php");
        }
    } else if ($status_kirim == 'selesai') {
        $year = date('y');
        $day = date('d');
        $month = date('m');
        $uuid = uuid();
        $id_komplain = $_POST['id_komplain'];
        $id_komplain_encrypt = encrypt($id_komplain, $key_spk);
        $id_finance = "FINANCE" . $year . $month . $uuid . $day;
        $id_inv = $_POST['id_inv'];
        $jenis_inv = $_POST['jenis_inv'];
        $total_inv = $_POST['total_inv'];

        try {
            // Mulai transaksi
            $connect->begin_transaction();
            // Pengecekan data di tabel finance
            $cek_stmt = $connect->prepare("SELECT id_inv FROM finance WHERE id_inv = ?");
            $cek_stmt->bind_param('s', $id_inv);
            $cek_stmt->execute();
            $cek_stmt->store_result();
            $total_cek_data = $cek_stmt->num_rows;
            $cek_stmt->close();

            // Update status transaksi di tabel inv_bum
            $update_inv_stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Komplain Selesai' WHERE id_inv_bum = ?");
            $update_inv_stmt->bind_param('s', $id_inv);
            $update_inv_stmt->execute();
            if (!$update_inv_stmt) {
                throw new Exception("Gagal update inv_bum: " . $update_inv_stmt->error);
            }
            $update_inv_stmt->close();

            // Proses simpan atau update finance
            if ($total_cek_data == 0) {
                $finance_stmt = $connect->prepare("INSERT INTO finance(id_finance, id_inv, total_inv, jenis_inv) VALUES (?, ?, ?, ?)");
                $finance_stmt->bind_param('ssss', $id_finance, $id_inv, $total_inv, $jenis_inv);
            } else {
                $finance_stmt = $connect->prepare("UPDATE finance SET total_inv = ? WHERE id_inv = ?");
                $finance_stmt->bind_param('ss', $total_inv, $id_inv);
            }
            $finance_stmt->execute();
            if (!$finance_stmt) {
                throw new Exception("Gagal simpan/update finance: " . $finance_stmt->error);
            }
            $finance_stmt->close();

            // Pengecekan data di tabel inv_revisi
            $cek_revisi_stmt = $connect->prepare("SELECT COUNT(*) FROM inv_revisi WHERE id_inv = ?");
            $cek_revisi_stmt->bind_param('s', $id_inv);
            $cek_revisi_stmt->execute();
            $cek_revisi_stmt->bind_result($count);
            $cek_revisi_stmt->fetch();
            $cek_revisi_stmt->close();

            if ($count > 0) {
                // Jika data ada, lakukan update
                $update_revisi_stmt = $connect->prepare("UPDATE inv_revisi SET status_trx_selesai = '1' WHERE id_inv = ?");
                $update_revisi_stmt->bind_param('s', $id_inv);
                $update_revisi_stmt->execute();
                if (!$update_revisi_stmt) {
                    throw new Exception("Gagal update inv_revisi: " . $update_revisi_stmt->error);
                }
                $update_revisi_stmt->close();
            } else {
                // Jika data tidak ada, lakukan insert
                $insert_revisi_stmt = $connect->prepare("INSERT INTO inv_revisi 
                                                                    (id_inv_revisi, id_inv, no_inv_revisi, tgl_inv_revisi, pelanggan_revisi, alamat_revisi, total_inv, status_pengiriman, status_trx_komplain, status_trx_selesai) 
                                                                VALUES  (?, ?, ?, ?, ?, ?, ?, 0, 0, 0)");
                $insert_revisi_stmt->bind_param('sssssss', $id_inv_rev, $id_inv, $revisi_invoice, $tgl, $cs_inv, $alamat, $total_inv);
                $insert_revisi_stmt->execute();
                if (!$insert_revisi_stmt) {
                    throw new Exception("Gagal simpan inv_revisi: " . $insert_revisi_stmt->error);
                }
                $insert_revisi_stmt->close();
            }

            // Update status komplain di tabel inv_komplain
            $update_komplain_stmt = $connect->prepare("UPDATE inv_komplain SET status_komplain = '1' WHERE id_komplain = ?");
            $update_komplain_stmt->bind_param('s', $id_komplain);
            $update_komplain_stmt->execute();
            if (!$update_komplain_stmt) {
                throw new Exception("Gagal update inv_komplain: " . $update_komplain_stmt->error);
            }
            $update_komplain_stmt->close();

            // Insert data ke tabel history_produk_terjual
            $insert_history_stmt = $connect->prepare("INSERT IGNORE INTO history_produk_terjual (id_trx_history, id_inv, id_produk, qty)
                                                                SELECT tpr.id_tmp, tpr.id_inv, tpr.id_produk, tpr.qty
                                                                FROM tmp_produk_komplain AS tpr
                                                                WHERE tpr.id_inv = ?");
            $insert_history_stmt->bind_param('s', $id_inv);
            $insert_history_stmt->execute();
            if (!$insert_history_stmt) {
                throw new Exception("Gagal insert history_produk_terjual: " . $insert_history_stmt->error);
            }
            $insert_history_stmt->close();

            // Commit transaksi jika semua berhasil
            $connect->commit();
            $_SESSION['info'] = "Disimpan";
            header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $connect->rollback();
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
            exit();
        }
    } else if ($status_kirim == 'cancel') {
        try {
            // Mulai Transaksi
            $connect->begin_transaction();

            // Prepare statement before insert data
            $stmt = $connect->prepare("INSERT INTO trx_prod_cancel_komplain 
                                                (id_trx, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, created_produk)
                                                SELECT id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, created_date
                                                FROM tmp_produk_komplain
                                                WHERE id_inv = ?
                                            ");
            $stmt->bind_param('s', $id_inv);
            $insert = $stmt->execute();

            if (!$insert) {
                throw new Exception("Gagal menyimpan data: " . $stmt->error);
            }

            // Prepare statement before delete data
            $stmt = $connect->prepare("DELETE FROM tmp_produk_komplain  WHERE id_inv = ?");
            $stmt->bind_param('s', $id_inv);
            $delete = $stmt->execute();

            if (!$delete) {
                throw new Exception("Gagal hapus data: " . $stmt->error);
            }

            // Prepare statement before update data invoice
            $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Cancel Order' WHERE id_inv_bum = ?");
            $stmt->bind_param('s', $id_inv);
            $update_inv = $stmt->execute();

            if (!$update_inv) {
                throw new Exception("Gagal hapus data: " . $stmt->error);
            }

            // Prepare statement before update data komplain
            $status_komplain = 1;
            $stmt = $connect->prepare("UPDATE inv_komplain SET status_komplain = ? WHERE id_komplain = ? ");
            $stmt->bind_param('is', $status_komplain, $id_komplain);
            $update = $stmt->execute();

            if (!$update_inv) {
                throw new Exception("Gagal hapus data: " . $stmt->error);
            }

            // Commit transaksi jika semua berhasil
            $connect->commit();
            $_SESSION['info'] = "Disimpan";
            header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $connect->rollback();
            $error_message = "Gagal saat proses data: " . $e->getMessage();
            echo $error_message;
            // $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
            exit();
        }
    } else {
        header("Location:../404.php");
    }
} else if (isset($_POST['ubah-pengiriman'])) {
    $id_status_kirim_revisi = htmlspecialchars($_POST['id_status_kirim_revisi']);
    $id_komplain = htmlspecialchars($_POST['id_komplain']);
    $id_komplain_encrypt = encrypt($id_komplain, $key_spk);
    $id_inv = htmlspecialchars($_POST['id_inv']);
    $id_inv_revisi = htmlspecialchars($_POST['id_inv_revisi']);
    $id_bukti_terima = htmlspecialchars($_POST['id_bukti_terima']);
    $jenis_pengiriman = htmlspecialchars($_POST['jenis_pengiriman']);
    $alasan_ubah = htmlspecialchars($_POST['alasan_ubah']);
    $tgl = htmlspecialchars($_POST['tgl']);
    $updated_date = date('d/m/Y H:i:s');
    $bukti_kirim_rev = $connect->query("SELECT bukti_satu FROM inv_bukti_terima WHERE id_bukti_terima = '$id_bukti_terima'");
    $data_bukti_kirim =  mysqli_fetch_array($bukti_kirim_rev);
    $cek_data_bukti = mysqli_num_rows($bukti_kirim_rev);
    if ($jenis_pengiriman == 'Driver') {
        $pengirim = $_POST['pengirim'];
        $bukti_sebelumnya = $data_bukti_kirim['bukti_satu'];
        $path_unlink = "../gambar-revisi/bukti1/" . $bukti_sebelumnya;
        try {
            // Begin transaction
            mysqli_begin_transaction($connect);
            // Update status kirim
            $stmt = $connect->prepare("UPDATE revisi_status_kirim 
                                            SET 
                                                jenis_pengiriman = ?, 
                                                jenis_penerima = '',
                                                dikirim_driver = ?, 
                                                dikirim_ekspedisi = '',
                                                no_resi = '',
                                                jenis_ongkir = 0,
                                                ongkir = 0,
                                                free_ongkir = 0,
                                                dikirim_oleh = '',
                                                diambil_oleh = '',
                                                penanggung_jawab = '',
                                                tgl_kirim = ?,
                                                status_kirim = 0,
                                                alasan = ?,
                                                updated_date = ?
                                            WHERE id_status_kirim_revisi = ?");
            $stmt->bind_param('ssssss', $jenis_pengiriman, $pengirim, $tgl, $alasan_ubah, $updated_date, $id_status_kirim_revisi);
            $update_status_kirim = $stmt->execute();


            if (!$update_status_kirim) {
                throw new Exception("Gagal simpan data: " . $stmt->error);
            }

            // Prepare statement before update data invoice
            $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Komplain Dikirim' WHERE id_inv_bum = ?");
            $stmt->bind_param('s', $id_inv);
            $update_inv = $stmt->execute();

            if (!$update_inv) {
                throw new Exception("Gagal hapus data: " . $stmt->error);
            }

            // Proses update bukti terima
            $stmt = $connect->prepare("DELETE FROM inv_bukti_terima_revisi WHERE id_bukti_terima = ?");
            $stmt->bind_param('s',  $id_bukti_terima);
            $bukti_terima = $stmt->execute();


            if (!$bukti_terima) {
                throw new Exception("Gagal simpan data: " . $stmt->error);
            }

            // Commit transaksi jika semua berhasil
            $connect->commit();
            unlink($path_unlink);
            $_SESSION['info'] = "Diupdate";
            header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $connect->rollback();
            $error_message = "Gagal saat proses data: " . $e->getMessage();
            echo $error_message;
            $_SESSION['info'] = "Data Gagal Diupdate";
            header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
            exit();
        }
    } else if ($jenis_pengiriman == 'Ekspedisi') {
        $ekspedisi = htmlspecialchars($_POST['ekspedisi']);
        $jenis_pengiriman = htmlspecialchars($_POST['jenis_pengiriman']);
        $jenis_penerima = 'Ekspedisi';
        $resi = htmlspecialchars($_POST['resi']);
        $jenis_ongkir = htmlspecialchars($_POST['jenis_ongkir']);
        $ongkir = str_replace('.', '', $_POST['ongkir']); // Menghapus tanda ribuan (,)
        $ongkir = intval($ongkir); // Mengubah string harga menjadi integer
        $free_ongkir = htmlspecialchars($_POST['free_ongkir']);
        $dikirim = htmlspecialchars($_POST['dikirim']);
        $pj = htmlspecialchars($_POST['pj']);
        $uuid = generate_uuid();
        $img_uuid = img_uuid();
        $year = date('y');
        $day = date('d');
        $month = date('m');
        $id_inv_bukti = "BKTI-REV" . $year . "" . $month . "" . $uuid . "" . $day;
        $bukti_sebelumnya = htmlspecialchars($_POST['bukti_sebelumnya']);

        // Proses upload file
        $allowedExtensions = ['png', 'jpeg', 'jpg', 'pdf']; // Ekstensi file yang diizinkan
        $file_name = $_FILES['fileku']['name'];
        $file_tmp = $_FILES['fileku']['tmp_name'];
        $new_file_name = "Bukti_Satu" . $year . $month . $img_uuid . $day . ".jpg";
        $path = "../gambar-revisi/bukti1/" . $new_file_name;
        $path_unlink = "../gambar-revisi/bukti1/" . $bukti_sebelumnya;

        // Memeriksa ekstensi file yang diunggah
        $fileInfo = pathinfo($file_name);
        $fileExtension = strtolower($fileInfo['extension']);

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Jenis file tidak didukung. Hanya file dengan ekstensi .png, .jpeg, .jpg, dan .pdf yang diizinkan.');
        }

        // Proses Upload
        if (!move_uploaded_file($file_tmp, $path)) {
            throw new Exception('Gagal mengupload file.');
        }

        try {
            mysqli_begin_transaction($connect);
            // Proses update status kirim revisi
            $stmt = $connect->prepare("UPDATE revisi_status_kirim 
                                            SET
                                                jenis_pengiriman = ?, 
                                                jenis_penerima = ?, 
                                                dikirim_driver = '',
                                                dikirim_ekspedisi = ?, 
                                                no_resi = ?, 
                                                jenis_ongkir = ?, 
                                                ongkir = ?, 
                                                free_ongkir = ?, 
                                                dikirim_oleh = ?, 
                                                diambil_oleh = '',
                                                penanggung_jawab = ?, 
                                                tgl_kirim = ?,
                                                alasan = ?,
                                                updated_date = ?
                                            WHERE id_status_kirim_revisi = ?");
            $stmt->bind_param('ssssiiissssss', $jenis_pengiriman, $jenis_penerima, $ekspedisi, $resi, $jenis_ongkir, $ongkir, $free_ongkir, $dikirim, $pj, $tgl, $alasan_ubah, $updated_date, $id_status_kirim_revisi);
            $update_status_kirim = $stmt->execute();


            if (!$update_status_kirim) {
                throw new Exception("Gagal update data: " . $stmt->error);
            }

            // Proses update inv revisi
            $stmt = $connect->prepare("UPDATE inv_revisi 
                                                SET status_pengiriman = 0, 
                                                    status_trx_komplain = 0, 
                                                    status_trx_selesai = 0 
                                                WHERE id_inv_revisi = ?");
            $stmt->bind_param('s', $status_pengiriman);
            $update_inv_revisi = $stmt->execute();


            if (!$update_inv_revisi) {
                throw new Exception("Gagal simpan data: " . $stmt->error);
            }

            if ($cek_data_bukti != 0) {
                // Proses update bukti terima
                $stmt = $connect->prepare("UPDATE inv_bukti_terima_revisi 
                                                SET bukti_satu = ?,  
                                                WHERE id_bukti_terima = ? ");
                $stmt->bind_param('ss', $new_file_name, $id_bukti_terima);
                $bukti_terima = $stmt->execute();
            } else {
                // Proses simpan bukti terima
                $stmt = $connect->prepare("INSERT INTO inv_bukti_terima_revisi 
                                            (id_bukti_terima, id_komplain, bukti_satu) 
                                    VALUES  (?, ?, ?)");
                $stmt->bind_param('sss', $id_inv_bukti, $id_komplain, $new_file_name);
                $bukti_terima = $stmt->execute();
            }

            if (!$bukti_terima) {
                throw new Exception("Gagal simpan data: " . $stmt->error);
            }

            // Proses update status transaksi
            $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Komplain Dikirim' WHERE id_inv_bum = ?");
            $stmt->bind_param('s', $id_inv);
            $update_inv = $stmt->execute();

            if (!$update_inv) {
                throw new Exception("Gagal hapus data: " . $stmt->error);
            }


            // Commit transaksi jika semua berhasil
            $connect->commit();
            unlink($path_unlink);
            $_SESSION['info'] = "Disimpan";
            header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $connect->rollback();
            // $error_message = "Gagal saat proses data: " . $e->getMessage();
            // echo $error_message;
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
            exit();
        }
    } else if ($jenis_pengiriman == 'Diambil Langsung') {
        $diambil_oleh = $_POST['diambil_oleh'];
        $diambil_oleh = ltrim($diambil_oleh, " \t"); // Menghapus spasi dan tab di awal teks
        $uuid = generate_uuid();
        $img_uuid = img_uuid();
        $year = date('y');
        $day = date('d');
        $month = date('m');
        $id_inv_bukti = "BKTI-REV" . $year . "" . $month . "" . $uuid . "" . $day;
        $id_inv_penerima_revisi = "PNMR-REV" . $year . "" . $month . "" . $uuid . "" . $day;
        // Proses upload file
        $allowedExtensions = ['png', 'jpeg', 'jpg', 'pdf']; // Ekstensi file yang diizinkan
        $file_name = $_FILES['fileku']['name'];
        $file_tmp = $_FILES['fileku']['tmp_name'];
        $new_file_name = "Bukti_Satu" . $year . $month . $img_uuid . $day . ".jpg";
        $path = "../gambar-revisi/bukti1/" . $new_file_name;

        // Memeriksa ekstensi file yang diunggah
        $fileInfo = pathinfo($file_name);
        $fileExtension = strtolower($fileInfo['extension']);

        if (!in_array($fileExtension, $allowedExtensions)) {
            throw new Exception('Jenis file tidak didukung. Hanya file dengan ekstensi .png, .jpeg, .jpg, dan .pdf yang diizinkan.');
        }

        // Proses Upload
        if (!move_uploaded_file($file_tmp, $path)) {
            throw new Exception('Gagal mengupload file.');
        }

        try {
            // Begin transaction
            mysqli_begin_transaction($connect);

            // Proses update status kirim
            $stmt = $connect->prepare("UPDATE revisi_status_kirim 
                                            SET 
                                                jenis_pengiriman = ?, 
                                                jenis_penerima = '',
                                                dikirim_driver = '', 
                                                dikirim_ekspedisi = '',
                                                no_resi = '',
                                                jenis_ongkir = 0,
                                                ongkir = 0,
                                                free_ongkir = 0,
                                                dikirim_oleh = '',
                                                diambil_oleh = ?,
                                                penanggung_jawab = '',
                                                tgl_kirim = ?,
                                                status_kirim = 0,
                                                alasan = ?,
                                                updated_date = ?
                                            WHERE id_status_kirim_revisi = ?");
            $stmt->bind_param('ssssss', $jenis_pengiriman, $diambil_oleh, $tgl, $alasan_ubah, $updated_date, $id_status_kirim_revisi);
            $update_status_kirim = $stmt->execute();


            if (!$update_status_kirim) {
                throw new Exception("Gagal simpan data: " . $stmt->error);
            }

            // Proses update inv revisi
            $stmt = $connect->prepare("UPDATE inv_revisi 
                                                SET status_pengiriman = 1, 
                                                    status_trx_komplain = 1, 
                                                    status_trx_selesai = 0 
                                                WHERE id_inv_revisi = ?");
            $stmt->bind_param('s', $status_pengiriman);
            $update_inv_revisi = $stmt->execute();


            if (!$update_inv_revisi) {
                throw new Exception("Gagal simpan data: " . $stmt->error);
            }

            // Proses Bukti Terima
            if ($cek_data_bukti != 0) {
                // Proses update bukti terima
                $stmt = $connect->prepare("UPDATE inv_bukti_terima_revisi 
                                                SET bukti_satu = ? 
                                                WHERE id_bukti_terima = ? ");
                $stmt->bind_param('ss', $new_file_name, $id_bukti_terima);
                $bukti_terima = $stmt->execute();
            } else {
                // Proses simpan bukti terima
                $stmt = $connect->prepare("INSERT INTO inv_bukti_terima_revisi 
                                            (id_bukti_terima, id_komplain, bukti_satu) 
                                    VALUES  (?, ?, ?)");
                $stmt->bind_param('sss', $id_inv_bukti, $id_komplain, $new_file_name);
                $bukti_terima = $stmt->execute();
            }

            if (!$bukti_terima) {
                throw new Exception("Gagal simpan data: " . $stmt->error);
            }

            // Proses update inv penerima revisi
            $stmt = $connect->prepare("UPDATE inv_penerima_revisi 
                                                SET id_komplain = ?, 
                                                    nama_penerima = ?, 
                                                    tgl_terima = ? 
                                                WHERE id_inv_penerima_revisi = ?");
            $stmt->bind_param("ssss", $id_komplain, $diambil_oleh, $tgl, $id_inv_penerima_revisi);
            $query_diterima = $stmt->execute();

            if (!$query_diterima) {
                throw new Exception("Gagal update data: " . $stmt->error);
            }


            // Prepare statement before update data invoice
            $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Komplain Diambil' WHERE id_inv_bum = ?");
            $stmt->bind_param('s', $id_inv);
            $update_inv = $stmt->execute();

            if (!$update_inv) {
                throw new Exception("Gagal hapus data: " . $stmt->error);
            }

            // Commit transaksi jika semua berhasil
            $connect->commit();
            $_SESSION['info'] = "Disimpan";
            header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
            exit();
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $connect->rollback();
            $error_message = "Gagal saat proses data: " . $e->getMessage();
            echo $error_message;
            $_SESSION['info'] = "Data Gagal Disimpan";
            // header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
            exit();
        }
    } else {
        header("Location:../404.php");
    }
} else if (isset($_POST['ubah-ongkir'])) {
    $id_status_kirim_revisi = htmlspecialchars(decrypt($_POST['id_status_kirim_revisi'], $key_spk));
    $id_komplain = htmlspecialchars(decrypt($_POST['id_komplain'], $key_spk));
    $id_komplain_encrypt = htmlspecialchars($_POST['id_komplain']);
    $no_resi = htmlspecialchars($_POST['no_resi']);
    $ongkir = str_replace('.', '', $_POST['ongkir']); // Menghapus tanda ribuan (,)
    $ongkir = intval($ongkir); // Mengubah string harga menjadi integer

    $jenis_ongkir = "";
    if ($ongkir == 0) {
        $jenis_ongkir = 1;
    } else {
        $jenis_ongkir = 0;
    }
    $stmt = $connect->prepare("UPDATE revisi_status_kirim 
                                        SET
                                            jenis_ongkir = ?,
                                            no_resi = ?,
                                            ongkir = ?
                                        WHERE id_status_kirim_revisi = ?");
    $stmt->bind_param('isis', $jenis_ongkir, $no_resi, $ongkir, $id_status_kirim_revisi);
    $update_data = $stmt->execute();
    if ($update_data) {
        $_SESSION['info'] = "Disimpan";
        header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encrypt");
        exit();
    }
} else {
    header("Location:../404.php");
}

function uuid()
{
    $data = openssl_random_pseudo_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s', str_split(bin2hex($data), 4));
}

function reviseInvoice($invoice)
{
    // Mencocokkan pola nomor invoice
    if (preg_match('/^(\d+)(\/Rev(\d+))?\/(\w+)\/(\w+)\/(\d+)$/', $invoice, $matches)) {
        $prefix = $matches[1];
        $revision = isset($matches[3]) ? intval($matches[3]) + 1 : 1;
        $part1 = $matches[4];
        $part2 = $matches[5];
        $year = $matches[6];

        $revisedInvoice = "$prefix/Rev$revision/$part1/$part2/$year";
        return $revisedInvoice;
    }
    // Jika pola tidak cocok, tambahkan revisi pertama
    return preg_replace('/(\d+)\/(\w+)\/(\w+)\/(\d+)/', '$1/Rev1/$2/$3/$4', $invoice);
}
// Kode untuk menampilkan hasil kode
// $no_invoice = "004/Rev5/KM/X/2023";
// $revised_invoice = reviseInvoice($no_invoice);

// echo "Nomor Invoice Asli: $no_invoice<br>";
// echo "Nomor Invoice Revisi: $revised_invoice";

function img_uuid()
{
    $data = openssl_random_pseudo_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s', str_split(bin2hex($data), 4));
}

// generate UUID
function generate_uuid()
{
    return sprintf(
        '%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
