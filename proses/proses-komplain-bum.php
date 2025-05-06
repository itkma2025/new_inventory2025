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
    require_once __DIR__ . "/../function/uuid.php";
    // Library sanitasi input data
    require_once __DIR__ . "/../function/sanitasi_input.php";
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
        $id_komplain = "KMPLN-" . $year . $month . $uuid . $day;
        $id_kondisi = "KNDSI-" . $year . $month . $uuid . $day;
        
        // Kode untuk membuat no komplain
        $sql = $connect->query("
                                SELECT max(no_komplain) as maxID, 
                                    STR_TO_DATE(tgl_komplain, '%d/%m/%Y') AS tgl 
                                FROM inv_komplain 
                                WHERE YEAR(STR_TO_DATE(tgl_komplain, '%d/%m/%Y')) = '$year_komplain'
                                ");
        $data = $sql->fetch_assoc();
        $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $kode = $data['maxID'];
        $ket1 = "/CC/KMA/";
        $bln = $array_bln[date('n')];
        $ket2 = "/";
        $ket3 = date("Y");
        $urutkan = (int)substr($kode, 0, 3);
        $urutkan++;
        $no_komplain = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;

        echo "<pre>";
        print_r($sanitasi_post);
        echo "</pre>";
        
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

            $sql_inv_bum = $connect->query("SELECT no_inv FROM inv_bum WHERE id_inv_bum = '$id_inv_decrypt'");
            $data_inv = $sql_inv_bum->fetch_assoc();
            $no_inv = $data_inv['no_inv'];

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