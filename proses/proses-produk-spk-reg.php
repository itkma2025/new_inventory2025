<?php
    require_once "../akses.php";
    require_once "../function/uuid.php";
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);
    $nama_user = decrypt($_SESSION['tiket_nama'], $key_global);

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
	$sanitasi_post = sanitizeInput($_POST);
	$sanitasi_get = sanitizeInput($_GET);
    $date_now = date('Y/m/d, H:i:s');

    if (isset($sanitasi_post['simpan-tmp'])) {
        // Mendapatkan data yang dikirimkan melalui form
        $id_tmp = $sanitasi_post['id_tmp']; // Mengambil ID transaksi dari form
        $id_spk_reg = $sanitasi_post['id_spk_reg_tmp'];
        $qty = $sanitasi_post['qty_tmp']; // Mengambil nilai qty yang diperbarui

        // Catat waktu mulai
        $startTime = microtime(true);

        // Mulai transaksi database
        mysqli_begin_transaction($connect);

        try {
            // Persiapkan query dengan Prepared Statement
            $stmt = mysqli_prepare($connect, "UPDATE tmp_produk_spk SET qty = ?, status_tmp = '1' WHERE id_tmp = ?");
            if (!$stmt) {
                throw new Exception("Gagal mempersiapkan statement.");
            }

            // Batasi data yang diproses dalam satu batch
            $chunkSize = 10; // Contoh: 10 data per batch
            $totalData = count($id_tmp);

            for ($i = 0; $i < $totalData; $i += $chunkSize) {
                $endIndex = min($i + $chunkSize, $totalData);
                for ($j = $i; $j < $endIndex; $j++) {
                    // Ambil data dari batch
                    $id = $id_tmp[$j];
                    $spk_reg = $id_spk_reg[$j];
                    $newQtyInt = str_replace(',', '', $qty[$j]); // Hapus tanda ribuan (,)
                    $newQtyInt = intval($newQtyInt); // Konversi ke integer

                    // Binding parameter (integer untuk qty, string untuk id_tmp)
                    mysqli_stmt_bind_param($stmt, 'is', $newQtyInt, $id);

                    // Eksekusi statement
                    if (!mysqli_stmt_execute($stmt)) {
                        throw new Exception("Terjadi kesalahan saat menyimpan data untuk ID: $id");
                    }
                }
            }

            // Tutup statement setelah selesai
            mysqli_stmt_close($stmt);
            
            // Jika tidak ada kesalahan, commit transaksi
            mysqli_commit($connect);

            // Catat waktu akhir
            $endTime = microtime(true);
            
            // Hitung waktu proses
            $processTime = $endTime - $startTime;

            // Simpan waktu proses ke sesi
            $_SESSION['process_time'] = $processTime;

            // Simpan waktu ke dalam file log baru
            $logMessage = "Proses berhasil: Waktu eksekusi untuk SPK ID $id_spk_reg adalah " . number_format($processTime, 6) . " detik.\n";
            file_put_contents("log_proses.txt", $logMessage, FILE_APPEND); // Menyimpan ke dalam file log_proses.txt

            // Redirect ke halaman detail dengan id terenkripsi
            $id_spk_encrypt = encrypt($id_spk_reg[0], $key_spk); // Enkripsi salah satu ID untuk URL
            header("Location: ../detail-produk-spk-reg.php?id=$id_spk_encrypt");
            exit;

        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            mysqli_rollback($connect);
            
            // Catat waktu akhir
            $endTime = microtime(true);
            
            // Hitung waktu proses meskipun gagal
            $processTime = $endTime - $startTime;

            // Simpan waktu proses ke sesi
            $_SESSION['process_time'] = $processTime;

            // Simpan waktu ke dalam file log baru, termasuk pesan error
            $logMessage = "Proses gagal: Waktu eksekusi untuk SPK ID $id_spk_reg adalah " . number_format($processTime, 6) . " detik. Error: " . $e->getMessage() . "\n";
            file_put_contents("log_proses.txt", $logMessage, FILE_APPEND); // Menyimpan ke dalam file log_proses.txt

            // Redirect ke halaman yang sama dengan pesan error
            $id_spk_encrypt = encrypt($id_spk_reg[0], $key_spk); // Enkripsi salah satu ID untuk URL
            header("Location: ../detail-produk-spk-reg.php?id=$id_spk_encrypt&error=" . urlencode($e->getMessage()));
            exit;
        }
    } else if (isset($sanitasi_post['simpan-trx'])) {
        // Mendapatkan data yang dikirimkan melalui form tanpa langsung mengubahnya dengan htmlspecialchars
        $uuid = uuid();
        $id_transaksi = $sanitasi_post['id_transaksi'];
        $id_spk_reg = $sanitasi_post['id_spk_reg'];
        $id_produk = $sanitasi_post['id_produk'];
        $harga = $sanitasi_post['harga'];
        $qty = $sanitasi_post['qty'];
        $created_date = $sanitasi_post['created_date'];
        $created_by = $id_user;
        $day = date('d');
        $month = date('m');
        $year = date('y');

        // Catat waktu mulai
        $startTime = microtime(true);

        try {
            // Mulai Transaksi
            mysqli_begin_transaction($connect);

            // Batch processing
            $chunkSize = 10; // Ukuran batch
            $totalData = count($id_transaksi);

            // Menyimpan log ke file
            $logFile = 'log_proses.txt';
            $logMessage = "Proses dimulai pada: " . date('Y-m-d H:i:s') . "\n";

            // Melakukan perulangan untuk menyimpan setiap data dengan batch
            for ($i = 0; $i < $totalData; $i += $chunkSize) {
                $endIndex = min($i + $chunkSize, $totalData);
                $batchData = [];

                // Persiapkan data untuk batch
                for ($j = $i; $j < $endIndex; $j++) {
                    // Lakukan htmlspecialchars di dalam perulangan untuk setiap elemen
                    $id_spk = $id_spk_reg[$j]; // Mengambil nilai id_spk untuk setiap iterasi
                    $id_spk_encrypt = encrypt($id_spk, $key_spk);
                    $id_trx = $id_transaksi[$j];
                    $spk_reg = $id_spk_reg[$j];
                    $produk = $id_produk[$j];
                    $hrg = str_replace(',', '', $harga[$j]); // Menghapus tanda ribuan (,)
                    $hrg = intval($hrg); // Mengubah string harga menjadi integer
                    $jml = str_replace(',', '', $qty[$j]);
                    $jml = intval($jml);
                    $total_harga = $hrg * $jml;
                    $created = $created_date[$j];
                    $id_temp = "TEMP-" . $year . $month . uuid() . $day;

                    // Delete data temp transaksi
                    $stmt = $connect->prepare("DELETE FROM tmp_produk_spk WHERE id_spk = ?");
                    $stmt->bind_param('s', $id_spk);
                    $delete_temp = $stmt->execute();

                    if (!$delete_temp) {
                        throw new Exception("Delete data temp transaksi: " . $stmt->error);
                    }

                    // Simpan data produk ke dalam table transaksi
                    $batchData[] = [
                        'id_trx' => $id_trx,
                        'spk_reg' => $spk_reg,
                        'produk' => $produk,
                        'hrg' => $hrg,
                        'jml' => $jml,
                        'total_harga' => $total_harga,
                        'created' => $created,
                        'created_by' => $created_by,
                        'id_temp' => $id_temp
                    ];
                }

                // Simpan data ke dalam database dalam satu batch
                $stmt = $connect->prepare("INSERT INTO transaksi_produk_reg 
                                            (id_transaksi, id_spk, id_produk, harga, qty, total_harga, created_date, created_by) 
                                            VALUES 
                                            (?, ?, ?, ?, ?, ?, ?, ?)");

                foreach ($batchData as $data) {
                    $stmt->bind_param('sssiiiss', 
                        $data['id_trx'], 
                        $data['spk_reg'], 
                        $data['produk'], 
                        $data['hrg'], 
                        $data['jml'], 
                        $data['total_harga'], 
                        $data['created'], 
                        $data['created_by']
                    );

                    if (!$stmt->execute()) {
                        throw new Exception("Gagal menyimpan data trx: " . $stmt->error);
                    }

                    // Simpan data tmp KS
                    $stmt_tmp_ks = $connect->prepare("INSERT INTO tmp_kartu_stock (id_tmp, id_transaksi, created_by) VALUES (?, ?, ?)");
                    $stmt_tmp_ks->bind_param('sss', $data['id_temp'], $data['id_trx'], $data['created_by']);
                    if (!$stmt_tmp_ks->execute()) {
                        throw new Exception("Gagal menyimpan data temp: " . $stmt_tmp_ks->error);
                    }

                    // Update status SPK
                    $stmt_update_spk = $connect->prepare("UPDATE spk_reg SET status_spk = 'Dalam Proses' WHERE id_spk_reg = ?");
                    $stmt_update_spk->bind_param('s', $data['spk_reg']);
                    if (!$stmt_update_spk->execute()) {
                        throw new Exception("Gagal Update status SPK: " . $stmt_update_spk->error);
                    }
                }
            }

            // Commit transaksi jika semua berhasil
            $connect->commit();

            // Catat waktu akhir dan log proses
            $endTime = microtime(true);
            $processTime = $endTime - $startTime;
            $logMessage .= "Proses selesai pada: " . date('Y-m-d H:i:s') . "\n";
            $logMessage .= "Total waktu proses: " . number_format($processTime, 4) . " detik\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            $_SESSION['info'] = "Disimpan";
            header("Location:../spk-reg.php?sort=baru");
            exit();

        } catch (Exception $e) {
            // Catat waktu akhir dan durasi saat error terjadi
            $endTime = microtime(true);
            $processTime = $endTime - $startTime;

            // Rollback transaksi jika ada yang gagal di proses
            $connect->rollback();

            // Catat error ke log
            $logMessage = "Error terjadi pada: " . date('Y-m-d H:i:s') . "\n";
            $logMessage .= "Pesan Error: " . $e->getMessage() . "\n";
            $logMessage .= "Durasi proses sebelum error: " . number_format($processTime, 4) . " detik\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND);

            $_SESSION['info'] = "Data Gagal Disimpan";
            echo "Gagal saat proses data: " . $e->getMessage();
            exit();
        }
    } else if (isset($sanitasi_post['edit'])) {
        $id_tmp = $sanitasi_post['id_tmp'];
        $id_spk = $sanitasi_post['id_spk'];
        $qty_edit = $sanitasi_post['qty_edit'];
        $qty =  str_replace(',', '', $qty_edit);
        $qty = intval($qty);
        $id_spk_encrypt = encrypt($id_spk, $key_spk);

        $update = mysqli_query($connect, "UPDATE tmp_produk_spk SET qty = '$qty' WHERE id_tmp = '$id_tmp'");
        if ($update) {
            $_SESSION['info'] = "Disimpan";
            header("Location:../detail-produk-spk-reg.php?id=$id_spk_encrypt");
        } else {
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../detail-produk-spk-reg.php?id=$id_spk_encrypt");
        }
    } else if (isset($sanitasi_get['hapus_tmp'])) {
        $idh = decrypt($sanitasi_get['hapus_tmp'], $key_spk);
        $id_spk_encrypt = $sanitasi_get['id_spk'];

        $delete = mysqli_query($connect, "DELETE FROM tmp_produk_spk WHERE id_tmp = '$idh'");
        if ($delete) {
            $_SESSION['info'] = "Dihapus";
            header("Location:../detail-produk-spk-reg.php?id=$id_spk_encrypt");
        } else {
            $_SESSION['info'] = "Data Gagal Dihapus";
            header("Location:../detail-produk-spk-reg.php?id=$id_spk_encrypt");
        }

        // hapus produk trx
    } else if (isset($_GET['hapus_trx'])) {
        $idh = decrypt($_GET['hapus_trx'], $key_spk);
        $id_spk_encrypt = $_GET['id_spk'];

        try{
            $delete = mysqli_query($connect, "DELETE FROM transaksi_produk_reg WHERE id_transaksi = '$idh'");
            $delete_tmp = mysqli_query($connect, "DELETE FROM tmp_kartu_stock WHERE id_transaksi = '$idh'");
            if (!$delete && !$delete_tmp) {
                throw new Exception("Terjadi kesalahan saat menyimpan data.");
            } else {
                // Jika tidak terjadi kesalahan, commit transaksi
                mysqli_commit($connect);
                $_SESSION['info'] = 'Dihapus';
                header("Location:../spk-reg.php?sort=baru");
            }
        }catch (Exception){
            mysqli_rollback($connect);
            $_SESSION['info'] = 'Data Gagal Dihapus';
            header("Location:../spk-reg.php?sort=baru");
        }

    
        if ($delete) {
            $_SESSION['info'] = "Dihapus";
            header("Location:../detail-produk-spk-reg-dalam-proses.php?id=$id_spk_encrypt");
        } else {
            $_SESSION['info'] = "Data Gagal Dihapus";
            header("Location:../detail-produk-spk-reg-dalam-proses.php?id=$id_spk_encrypt");
        }

        // cancel pesanan
    } else if (isset($_POST['cancel-belum-diproses'])) {
        date_default_timezone_set('Asia/Jakarta');
        $id_spk = decrypt($_POST['id_spk'], $key_spk);
        $alasan = $_POST['alasan'];
        $menu_cancel = 'Belum Diproses';
        $user = $nama_user;
        $time = date('d/m/Y, H:i:s');

        mysqli_begin_transaction($connect);

        try {
            $insert = mysqli_query($connect, "INSERT INTO trx_cancel (id_trx_cancel, id_spk, id_produk, qty) SELECT id_tmp, id_spk, id_produk, qty FROM tmp_produk_spk WHERE id_spk ='$id_spk'");

            $delete = mysqli_query($connect, "DELETE FROM tmp_produk_spk WHERE id_spk = '$id_spk'");

            $update = mysqli_query($connect, "UPDATE spk_reg SET status_spk = 'Cancel Order', note = '$alasan', menu_cancel = '$menu_cancel', user_cancel = '$user', date_cancel = '$time' WHERE id_spk_reg = '$id_spk'");

            if (!$insert && !$delete && !$update) {
                throw new Exception("Terjadi kesalahan saat menyimpan data.");
            }

            // Jika tidak terjadi kesalahan, commit transaksi
            mysqli_commit($connect);
            $_SESSION['info'] = 'Dicancel';
            header("Location:../spk-reg.php?sort=baru");
        } catch (Exception $e) {
            // Jika terjadi kesalahan, rollback transaksi
            mysqli_rollback($connect);
            $_SESSION['info'] = 'Silahkan Ulangi Kembali';
            header("Location:../spk-reg.php?sort=baru");
        }
    } else if (isset($_POST['cancel-dalam-proses'])) {
        date_default_timezone_set('Asia/Jakarta');
        $id_spk = decrypt($_POST['id_spk'], $key_spk);
        $alasan = $_POST['alasan'];
        $menu_cancel = 'Dalam Proses';
        $user = $nama_user;
        $time = date('d/m/Y, H:i:s');

        mysqli_begin_transaction($connect);

        try {
            $insert = mysqli_query($connect, "INSERT INTO trx_cancel (id_trx_cancel, id_spk, id_produk, harga, qty, disc, total_harga) SELECT id_transaksi, id_spk, id_produk, harga, qty, disc, total_harga FROM transaksi_produk_reg WHERE id_spk ='$id_spk'");

            $delete = mysqli_query($connect, "DELETE FROM transaksi_produk_reg WHERE id_spk = '$id_spk'");

            $update = mysqli_query($connect, "UPDATE spk_reg SET status_spk = 'Cancel Order', note = '$alasan', menu_cancel = '$menu_cancel', user_cancel = '$user', date_cancel = '$time' WHERE id_spk_reg = '$id_spk'");

            if (!$insert && !$delete && !$update) {
                throw new Exception("Terjadi kesalahan saat menyimpan data.");
            }
            // Jika tidak terjadi kesalahan, commit transaksi
            mysqli_commit($connect);
            $_SESSION['info'] = 'Dicancel';
            header("Location:../spk-dalam-proses.php?sort=baru");
        } catch (Exception $e) {
            // Jika terjadi kesalahan, rollback transaksi
            mysqli_rollback($connect);
            $_SESSION['info'] = 'Silahkan Ulangi Kembali';
            header("Location:../spk-dalam-proses.php?sort=baru");
        }
    } else if (isset($_POST['cancel-siap-kirim'])) {
        date_default_timezone_set('Asia/Jakarta');
        $id_spk = decrypt($_POST['id_spk'], $key_spk);
        $alasan = $_POST['alasan'];
        $menu_cancel = 'Siap Kirim';
        $user = $nama_user;
        $time = date('d/m/Y, H:i:s');

        mysqli_begin_transaction($connect);

        try {
            $insert = mysqli_query($connect, "INSERT INTO trx_cancel (id_trx_cancel, id_spk, id_produk, harga, qty, disc, total_harga) SELECT id_transaksi, id_spk, id_produk, harga, qty, disc, total_harga FROM transaksi_produk_reg WHERE id_spk ='$id_spk'");

            $delete = mysqli_query($connect, "DELETE FROM transaksi_produk_reg WHERE id_spk = '$id_spk'");

            $update = mysqli_query($connect, "UPDATE spk_reg SET status_spk = 'Cancel Order', note = '$alasan', menu_cancel = '$menu_cancel', user_cancel = '$user', date_cancel = '$time' WHERE id_spk_reg = '$id_spk'");

            if (!$insert && !$delete && !$update) {
                throw new Exception("Terjadi kesalahan saat menyimpan data.");
            }
            // Jika tidak terjadi kesalahan, commit transaksi
            mysqli_commit($connect);
            $_SESSION['info'] = 'Dicancel';
            header("Location:../spk-siap-kirim.php?sort=baru");
        } catch (Exception $e) {
            // Jika terjadi kesalahan, rollback transaksi
            mysqli_rollback($connect);
            $_SESSION['info'] = 'Silahkan Ulangi Kembali';
            header("Location:../spk-dalam-proses.php?sort=baru");
        }
        // ===========================================================================================================================

        // Proses Siap Kirim
    } else if (isset($_POST['siap-kirim'])) {
        $id_spk_reg = decrypt($_POST['id_spk_reg'], $key_spk);
        $status_validasi = decrypt($_POST['status_validasi'], $key_spk);
        $petugas = $_POST['petugas'];
        $id_spk_encrypt = decrypt($id_spk_reg, $key_spk);

        if ($status_validasi == 'enabled') {
            // Ambil data dari tmp_kartu_stock dan kelompokkan berdasarkan id_produk_ks
            $tmp_ks = $connect->query("SELECT 
                id_tmp_ks, 
                id_transaksi,
                id_spk_ks, 
                id_produk_ks, 
                status_barang,
                qty_ks, 
                keterangan_input,
                input_date, 
                input_by 
            FROM tmp_kartu_stock
            WHERE id_spk_ks = '$id_spk_reg'");

            // Kelompokkan data berdasarkan prefix id_produk_ks
            $data_groups = [];
            while ($data_tmp = mysqli_fetch_array($tmp_ks)) {
                $id_produk_ks = $data_tmp['id_produk_ks'];

                // Tentukan kelompok berdasarkan prefix id_produk_ks
                if (strpos($id_produk_ks, 'BR-ECAT') === 0) {
                    $table_name = 'kartu_stock_ecat';
                } elseif (strpos($id_produk_ks, 'BR-REG') === 0) {
                    $table_name = 'kartu_stock_reg';
                } elseif (strpos($id_produk_ks, 'SETMRW') === 0) {
                    $table_name = 'kartu_stock_set_reg';
                } elseif (strpos($id_produk_ks, 'SETECAT') === 0) {
                    $table_name = 'kartu_stock_set_ecat';
                } else {
                    header("Location: ../404.php");
                    exit();
                }

                // Kelompokkan data berdasarkan tabel tujuan
                $data_groups[$table_name][] = $data_tmp;
            }

            try {
                // Mulai transaksi
                $connect->begin_transaction();

                // Proses setiap kelompok data sesuai tabel tujuan
                foreach ($data_groups as $table_name => $data_list) {
                    foreach ($data_list as $data_tmp) {
                        $id_tmp_ks = $data_tmp['id_tmp_ks'];
                        $id_transaksi = $data_tmp['id_transaksi'];
                        $id_produk_ks = $data_tmp['id_produk_ks'];
                        $status_barang = $data_tmp['status_barang'];
                        $id_spk_ks = $data_tmp['id_spk_ks'];
                        $qty_ks = $data_tmp['qty_ks'];
                        $keterangan = $data_tmp['keterangan_input'];
                        $input_date = $data_tmp['input_date'];
                        $input_by = $data_tmp['input_by'];

                        // Menampilkan ID untuk jenis barang keluar
                        $sql_ket_out = " SELECT 
                                        id_ket_out,
                                        ket_out
                                    FROM keterangan_out 
                                    WHERE ket_out = 'Penjualan'";
                        $query_ket_out = $connect->query($sql_ket_out);
                        $data_ket_out = mysqli_fetch_array($query_ket_out);
                        $id_ket_out = $data_ket_out['id_ket_out'];

                        // Query untuk memindahkan data ke tabel tujuan
                        $stmt = $connect->prepare("INSERT INTO $table_name 
                                (id_kartu_stock, id_transaksi, id_produk, status_barang, jenis_barang_keluar, id_spk, qty_out, keterangan, created_date, created_by) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param('sssississs', $id_tmp_ks, $id_transaksi, $id_produk_ks, $status_barang, $id_ket_out, $id_spk_ks, $qty_ks, $keterangan, $input_date, $input_by);
                        $simpan_ks = $stmt->execute();

                        if (!$simpan_ks) {
                            throw new Exception("Gagal menyimpan data ke kartu stok: " . $stmt->error);
                        }

                        // Hapus data dari tmp_kartu_stock setelah dipindahkan
                        $stmt = $connect->prepare("DELETE FROM tmp_kartu_stock WHERE id_tmp_ks = ?");
                        $stmt->bind_param('s', $id_tmp_ks);
                        $delete_tmp = $stmt->execute();

                        if (!$delete_tmp) {
                            throw new Exception("Gagal menghapus data dari tmp_kartu_stock: " . $stmt->error);
                        }
                    }
                }

                // Update SPK setelah data dipindahkan
                $stmt = $connect->prepare("UPDATE spk_reg 
                    SET status_spk = 'Siap Kirim', petugas = ?, notif_date = ? 
                    WHERE id_spk_reg = ?");
                $stmt->bind_param('sss', $petugas, $date_now, $id_spk_reg);
                $update_spk = $stmt->execute();

                if (!$update_spk) {
                    throw new Exception("Gagal memperbarui SPK: " . $stmt->error);
                }

                // Jika semua perintah berhasil, commit transaksi
                $connect->commit();
                $_SESSION['info'] = "Disimpan";
                header("Location:../spk-dalam-proses.php?sort=baru");
                exit();
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                $connect->rollback();
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../spk-dalam-proses.php?sort=baru");
                exit();
            }
        } else {
            $_SESSION['info'] = "Gagal Validasi Data";
            header("Location:../spk-dalam-proses.php?sort=baru");
        }
    }
