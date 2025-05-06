<?php  
    require_once __DIR__ . "/../../akses.php";
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);

    // Penghubung Library
    require_once __DIR__ . '/../../assets/vendor/autoload.php';
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
    require_once __DIR__ . "/../../function/uuid.php";
    // Library sanitasi input data
    require_once __DIR__ . "/../../function/sanitasi_input.php";
    $sanitasi_post = sanitizeInput($_POST);

    if(isset($sanitasi_post['komplain'])){
        $id_inv = $sanitasi_post['id_inv'];
        $id_inv_decrypt = decrypt($id_inv, $key_global);
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

            $sql_inv_bum = $connect->query("SELECT bum.no_inv, kmpl.id_komplain, total_komplain
                                                FROM inv_bum AS bum 
                                                LEFT JOIN inv_komplain kmpl ON (bum.id_inv_bum = kmpl.id_inv)
                                                WHERE bum.id_inv_bum = '$id_inv_decrypt'");
            $data_inv = $sql_inv_bum->fetch_assoc();
            $no_inv = $data_inv['no_inv'];
            $id_komplain = $data_inv['id_komplain'];
            $total_komplain = $data_inv['total_komplain'];

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

             // Proses update invoice
             $stmt = $connect->prepare("UPDATE inv_revisi SET status_trx_selesai = '0' WHERE id_inv = ?");
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

            // Proses update data di table invoice komplain
            $stmt = $connect->prepare("UPDATE inv_komplain 
                                        SET id_inv = ?, 
                                            tgl_komplain = ?, 
                                            status_komplain = '0',
                                            total_komplain = ?, 
                                            updated_by = ?
                                        WHERE id_inv = ?");

            $stmt->bind_param('ssiss', $id_inv_decrypt, $tgl, $total_komplain, $id_user, $id_inv_decrypt);
            $update_inv_komplain = $stmt->execute();

            // Penanganan jika proses gagal
            if (!$update_inv_komplain) {
                throw new Exception($stmt->error);
            }


            // Proses update data di tabel komplain_kondisi
            $stmt = $connect->prepare("UPDATE komplain_kondisi 
                                        SET kat_komplain = ?, 
                                            kondisi_pesanan = ?, 
                                            status_retur = ?, 
                                            status_refund = ?, 
                                            catatan = ?
                                        WHERE id_komplain = ?");

            $stmt->bind_param('siiiss', $kat_komplain, $kondisi_pesanan, $retur, $refund, $catatan, $id_komplain);
            $update_komplain_kondisi = $stmt->execute();

            // Penanganan jika proses gagal
            if (!$update_komplain_kondisi) {
                throw new Exception($stmt->error);
            }

            // Jika semua proses sukses, lakukan commit
            $connect->commit();

            // Simpan log sukses
            $success_message = date('Y-m-d H:i:s') . " - Proses Simpan Data Untuk No.Inv: " . $no_inv . " Berhasil  \n";
            file_put_contents($log_success, $success_message, FILE_APPEND);
            // Proses redirect
            $_SESSION['info'] = "Disimpan";
            header("Location:../invoice-reguler-selesai.php");
        } catch (Exception $e) {
            // Jika terjadi error, rollback perubahan
            $connect->rollback();
            // Simpan log error
            $error_message = date('Y-m-d H:i:s') . " - ERROR: " . $e->getMessage() . "\n";
            file_put_contents($log_error, $error_message, FILE_APPEND);
            // Proses redirect
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../invoice-reguler-selesai.php");
        }
    }
?>