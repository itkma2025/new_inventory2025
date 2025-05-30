<?php  
    require_once __DIR__ . "/../akses.php";
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);

    // Penghubung Library
    require_once __DIR__ . '/../assets/vendor/autoload.php';
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
    // Function UUID
    require_once __DIR__ . "/../function/uuid.php";
    // Function No Revisi
    require_once __DIR__ . "/../function/create-no-revisi.php";
    // Library sanitasi input data
    require_once __DIR__ . "/../function/sanitasi_input.php";
    $sanitasi_post = sanitizeInput($_POST);

    if(isset($sanitasi_post['komplain'])){
        $id_inv = $sanitasi_post['id_inv'];
        $id_inv_decrypt = decrypt($id_inv, $key_global);
        $no_inv = $sanitasi_post['no_inv'];
        $cs_inv = $sanitasi_post['cs_inv'];
        $alamat = $sanitasi_post['alamat'];
        $total_inv = $sanitasi_post['total_inv'];
        $tgl = $sanitasi_post['tgl'];
        $kat_komplain = $sanitasi_post['kat_komplain'];
        $kondisi_pesanan = $sanitasi_post['kondisi_pesanan'];
        $retur = $sanitasi_post['retur'];
        $refund = isset($sanitasi_post['refund']) ? $sanitasi_post['refund'] : 0;
        $catatan = $sanitasi_post['catatan'];
        $uuid = uuid();
        $year = date('y');
        $year_komplain = date('Y');
        $day = date('d');
        $month = date('m');
        $id_komplain = "KMPLN-" . $year . $month . $uuid . $day;
        $id_kondisi = "KNDSI-" . $year . $month . $uuid . $day;
        $id_inv_rev = "INVREV-" . $year . "" . $month . "" . $uuid . "" . $day;

        // Cek apakah invoice revisi ada
        $cek_inv = $connect->query("SELECT no_inv_revisi FROM inv_revisi WHERE id_inv = '$id_inv_decrypt' ORDER BY created_date DESC LIMIT 1");
        $data_cek_inv = $cek_inv->fetch_assoc();
        $no_inv_revisi = $data_cek_inv['no_inv_revisi'] ?? null; // Jika tidak ada, set ke null
        $no_inv_select = ''; // Inisialisasi variabel untuk no_inv yang akan digunakan
        $updated_no_inv = ''; // Inisialisasi variabel untuk no_inv yang telah diupdate
        if ($no_inv_revisi) {
            // Jika ada, gunakan no_inv_revisi
            $no_inv_select = $no_inv_revisi;
            $updated_no_inv = incrementRevision($original);
        } else {
            // Jika tidak ada, gunakan no_inv biasa
            $no_inv_select = $no_inv;
            // Cari posisi '/' pertama di luar function
            $pecahkan = strpos($no_inv, '/');

            // Panggil fungsi dengan parameter pecahkan dan posisi '/'
            $updated_no_inv = tambahRevisi($no_inv, $pecahkan);
        }

        // Ambil jumlah data komplain untuk tahun tertentu
        $sql = $connect->query("SELECT COUNT(*) as total FROM inv_komplain WHERE YEAR(STR_TO_DATE(tgl_komplain, '%d/%m/%Y')) = '$year_komplain'");
        $data = $sql->fetch_assoc();
        $total = $data['total'] + 1; // Increment

        $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $bln = $array_bln[date('n')];
        $ket1 = "/CC/KMA/";
        $ket2 = "/";
        $ket3 = date("Y");
        // Buat nomor komplain
        $no_komplain = sprintf("%03d", $total) . $ket1 . $bln . $ket2 . $ket3;
        
        $connect->begin_transaction();
        try{
            // Membuat log activity
            // Pastikan folder log ada
            $log_folder = __DIR__ . '/log';
            if (!file_exists($log_folder)) {
                mkdir($log_folder, 0777, true); // Buat folder jika belum ada
            }

            // Nama file log
            $log_success = $log_folder . '/log_komplain_success.txt';
            $log_error   = $log_folder . '/log_komplain_error.txt';

            // Proses update invoice
            $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Komplain' WHERE id_inv_bum = ?");
            $stmt->bind_param('s', $id_inv_decrypt);
            $update_inv = $stmt->execute();

            // Penanganan jika id inv tidak ada (Khusus Update Data)
            if ($stmt->affected_rows === 0) {
                throw new Exception("ID $id_inv_decrypt tidak ditemukan atau tidak ada perubahan dalam database.");
            }
            // Penanganan jika proses gagal
            if (!$update_inv) {
                throw new Exception($stmt->error);
            }

            // Proses insert data ke table invoice koomplain
            $stmt = $connect->prepare(" INSERT INTO inv_komplain 
                                            (id_komplain, id_inv, no_komplain, tgl_komplain, created_by) 
                                        VALUES 
                                            (?, ?, ?, ?, ?)
                                    ");
            $stmt->bind_param('sssss', $id_komplain, $id_inv_decrypt, $no_komplain, $tgl, $id_user);
            $insert_inv_komplain = $stmt->execute();

            // Penanganan jika proses gagal
            if (!$insert_inv_komplain) {
                throw new Exception($stmt->error);
            }

            $stmt = $connect->prepare("INSERT INTO komplain_kondisi 
                                            (id_kondisi, id_komplain, kat_komplain, kondisi_pesanan, status_retur, status_refund, catatan) 
                                        VALUES 
                                            (?, ?, ?, ?, ?, ?, ?)
                                    ");
            $stmt->bind_param('ssiiiis', $id_kondisi, $id_komplain, $kat_komplain, $kondisi_pesanan, $retur, $refund, $catatan);
            $insert_komplain_kondisi = $stmt->execute();

            // Penanganan jika proses gagal
            if (!$insert_komplain_kondisi) {
                throw new Exception($stmt->error);
            }
            
            $stmt = $connect->prepare(" INSERT IGNORE INTO 
                                            tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp, created_date)
                                        SELECT
                                            tpr.id_transaksi,
                                            spk.id_inv,
                                            tpr.id_produk,
                                            tpr.nama_produk_spk,
                                            tpr.harga,
                                            tpr.qty,
                                            tpr.disc,
                                            tpr.total_harga,
                                            1 as status_tmp,
                                            tpr.created_date
                                        FROM spk_reg AS spk
                                        LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk 
                                        WHERE spk.id_inv = ?");
            $stmt->bind_param('s', $id_inv_decrypt);
            $insert_tmp_produk = $stmt->execute();

            // Penanganan jika proses gagal
            if (!$insert_tmp_produk) {
                throw new Exception($stmt->error);
            }

            // Simpan Inv Revisi
            $stmt =  $connect->prepare("INSERT INTO inv_revisi (id_inv_revisi, id_inv, no_inv_revisi, tgl_inv_revisi, pelanggan_revisi, alamat_revisi, total_inv, status_pengiriman, status_trx_komplain, status_trx_selesai) VALUES (?, ?, ?, ?, ?, ?, ?,  0, 0, 0)");
            $stmt->bind_param('sssssss', $id_inv_rev, $id_inv_decrypt, $updated_no_inv, $tgl, $cs_inv, $alamat, $total_inv);
            $simpan_inv_rev = $stmt->execute();

            if (!$simpan_inv_rev) {
                throw new Exception("Gagal simpan data: " . $stmt->error);
            }
            
            // Jika semua proses sukses, lakukan commit
            $connect->commit();

            // Simpan log sukses
            $success_message = date('Y-m-d H:i:s') . " - Proses Simpan Data Untuk No.Inv: " . $no_inv . " Berhasil  \n";
            file_put_contents($log_success, $success_message, FILE_APPEND);
            // Proses redirect
            $_SESSION['info'] = "Disimpan";
            header("Location:../invoice-reguler-diterima.php");
        } catch (Exception $e) {
            // Jika terjadi error, rollback perubahan
            $connect->rollback();
            // Simpan log error
            $error_message = date('Y-m-d H:i:s') . " - ERROR: " . $e->getMessage() . "\n";
            file_put_contents($log_error, $error_message, FILE_APPEND);
            // Proses redirect
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../invoice-reguler-diterima.php");
        }
    }
?>