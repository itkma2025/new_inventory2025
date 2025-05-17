<?php
    require_once __DIR__ . "/../akses.php";
    // Penghubung Library
	require_once '../assets/vendor/autoload.php';
    require_once "../function/uuid.php";
    require_once "../function/function-enkripsi.php";
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
    if (isset($sanitasi_post['diterima_ekspedisi'])) {
        $connect->begin_transaction();
        $year = date('y');
        $day = date('d');
        $month = date('m');
        $id_inv_penerima = "PNMR" . $year . "" . $month . "" . uuid() . "" . $day;
        $id_inv = $sanitasi_post['id_inv'];
        $jenis_inv = $sanitasi_post['jenis_inv'];
        $id_komplain = decrypt($sanitasi_post['id_komplain'], $key_spk);
        $alamat = $sanitasi_post['alamat'];
        $nama_penerima = $sanitasi_post['nama_penerima'];
        $tgl = $sanitasi_post['tgl'];

        try {
            $query_diterima = mysqli_query($connect, "INSERT INTO inv_penerima_revisi (id_inv_penerima_revisi, id_komplain, nama_penerima, alamat, tgl_terima) VALUES ('$id_inv_penerima', '$id_komplain', '$nama_penerima', '$alamat', '$tgl')");

            $query_update_inv = '';
            if ($jenis_inv == 'nonppn') {
                $query_update_inv = mysqli_query($connect, "UPDATE inv_nonppn SET status_transaksi = 'Komplain Diterima' WHERE id_inv_nonppn = '$id_inv'");
            } else if ($jenis_inv == 'ppn') {
                $query_update_inv = mysqli_query($connect, "UPDATE inv_ppn SET status_transaksi = 'Komplain Diterima' WHERE id_inv_ppn = '$id_inv'");
            } else if ($jenis_inv == 'bum') {
                $query_update_inv = mysqli_query($connect, "UPDATE inv_bum SET status_transaksi = 'Komplain Diterima' WHERE id_inv_bum = '$id_inv'");
            }

            $query_update_inv_komplain = mysqli_query($connect, "UPDATE inv_komplain SET status_komplain = '0' WHERE id_komplain = '$id_komplain'");

            $query_update_inv_revisi = mysqli_query($connect, "UPDATE inv_revisi SET status_pengiriman = '1',  status_trx_komplain = '0', status_trx_selesai = '0' WHERE id_inv = '$id_inv'");

            $query_update_revisi_status_kirim = mysqli_query($connect, "UPDATE revisi_status_kirim SET status_kirim = '1'  WHERE id_komplain = '$id_komplain'");

            if ($query_diterima && $query_update_inv && $query_update_inv_komplain && $query_update_inv_revisi && $query_update_revisi_status_kirim) {
                // Commit transaksi
                $connect->commit();
                $_SESSION['info'] = "Disimpan";
			    header("Location: {$_SERVER['HTTP_REFERER']}");
            }
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi exception
            $connect->rollback();
            // $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
            $_SESSION['info'] = "Data Gagal Disimpan";
			header("Location: {$_SERVER['HTTP_REFERER']}");
        }
    }
?>