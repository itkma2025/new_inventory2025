<?php  
    require_once "../akses.php";
	require_once "../function/function-enkripsi.php";
	require_once "../function/get-type-params.php";
	require_once "../function/uuid.php";
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
	$sanitasi_post = sanitizeInput($_POST);
	$sanitasi_get = sanitizeInput($_GET);
	$day = date('d');
	$month = date('m');
	$year = date('Y');
	$id_history = "HIS-" . $month . $year . uuid() . $day;

    $action = $sanitasi_get['action'];

	if (!isset($sanitasi_get['token']) || $sanitasi_get['token'] !== $_SESSION['csrf']) {
        // Jika token tidak cocok atau tidak ada, hentikan proses dan tampilkan error
        $_SESSION['info'] = "Silahkan Ulangi Kembali";
        header("Location:{$_SERVER['HTTP_REFERER']}");
    }
    // Nonce sudah divalidasi, sekarang hapus dari session agar tidak bisa digunakan lagi
    unset($_SESSION['csrf']);

    if($action === 'approval'){
		$id = urldecode($sanitasi_get['id']);
		$id_inv = decrypt($id, $key_global);
		$status_review = 1;
		$approval = 2;
		// Mulai Transaksi
		$connect->begin_transaction();
		try {
			// Update status kirim
			$stmt = $connect->prepare("UPDATE status_kirim
														SET
															status_review = ?,
															review_date = ?	
														WHERE id_inv = ?
													");
			
			$stmt->bind_param('iss', $status_review, $datetime_now, $id_inv);
			$update_status_kirim = $stmt->execute();

			if (!$update_status_kirim) {
				throw new Exception("Gagal update status kirim");
			}

			// Update bukti terima
			$stmt = $connect->prepare("UPDATE inv_bukti_terima
										SET
											approval = ?,
											approval_date = ?,
											approval_by = ?,
											alasan = '-'
										WHERE id_inv = ?
									");
			
			$stmt->bind_param('isss', $approval, $datetime_now, $id_user, $id_inv);
			$update_inv_bukti_terima = $stmt->execute();

			if (!$update_inv_bukti_terima) {
				throw new Exception("Gagal update status kirim");
			}

			// insert history bukti kirim
			$stmt = $connect->prepare("INSERT INTO history_inv_bukti_terima   
													(id_history, id_bukti_terima, id_inv, bukti_satu, bukti_dua, bukti_tiga, lokasi, approval, approval_date, approval_by, alasan, created_date, created_by)
										SELECT ?, id_bukti_terima, id_inv, bukti_satu, bukti_dua, bukti_tiga, lokasi, approval, approval_date, approval_by, alasan, created_date, created_by 
										FROM inv_bukti_terima
										WHERE id_inv = ?");
			// Bind parameter dan eksekusi
			$stmt->bind_param("ss", $id_history, $id_inv);
			$insert_history = $stmt->execute();
			if (!$insert_history) {
				throw new Exception("Gagal update status kirim");
			}

			// Jika semua berhasil, commit perubahan
			$connect->commit();
			$_SESSION['info'] = "Disimpan";
			header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
		} catch (exception $e){
			$connect->rollback();
			$_SESSION['info'] = "Data Gagal Disimpan";
			header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
		}
    } else if ($action === 'reject') {
		$id = urldecode($sanitasi_get['id']);
		$id_inv = decrypt($id, $key_global);
		$jenis = isset($sanitasi_get['jenis']) ? $sanitasi_get['jenis'] : null;
		if (!in_array($jenis, ['1', '2'], true)) {
			// Tidak valid → redirect atau tolak proses
			die("Akses ditolak. Parameter tidak valid.");
		}

		$alasan = $sanitasi_get['alasan'];
		$status_review = 1;
		$approval = 1;
		$sql_status_kirim = $connect->query("SELECT jenis_penerima FROM status_kirim WHERE id_inv = '$id_inv'");
		$data_status_kirim = $sql_status_kirim->fetch_assoc();
		$jenis_penerima = $data_status_kirim['jenis_penerima'];

		$update_jenis_penerima = '';
		if($jenis_penerima == 'Ekspedisi'){
			$update_jenis_penerima = $jenis_penerima;
		}

		// Mulai Transaksi
		$connect->begin_transaction();
		try {
			// Blok kode jika ada kondisi dan tanpa double query
			// tentukan field yang selalu di update
			$query_status_kirim = "UPDATE status_kirim SET status_review = ?, review_date = ?";

			// Parameter awal
			$params = [
				$status_review, $datetime_now
			];

			// Kondisi jenis reject
			if($jenis == '2'){
				$query_status_kirim .= ", jenis_penerima = ?";
				$params[] = $update_jenis_penerima;
			}

			// Tambahkan kondisi where
			$query_status_kirim .= " WHERE id_inv = ?";
			$params[] = $id_inv;

			// Dapatkan tipe berdasarkan isi params
			$types = getParamTypes($params);

			// Prepare dan bind
			$stmt = $connect->prepare($query_status_kirim);
			$stmt->bind_param($types, ...$params);
			$update_status_kirim = $stmt->execute();

			if (!$update_status_kirim) {
				throw new Exception("Gagal update status kirim");
			}



			// Update bukti terima
			$stmt = $connect->prepare("UPDATE inv_bukti_terima
										SET
											approval = ?,
											approval_date = ?,
											approval_by = ?,
											jenis_reject = ?,
											alasan = ? 	
										WHERE id_inv = ?
									");
			
			$stmt->bind_param('ississ', $approval, $datetime_now, $id_user, $jenis, $alasan, $id_inv);
			$update_inv_bukti_terima = $stmt->execute();

			if (!$update_inv_bukti_terima) {
				throw new Exception("Gagal update status kirim");
			}

			// insert history bukti kirim
			$stmt = $connect->prepare("INSERT INTO history_inv_bukti_terima   
													(id_history, id_bukti_terima, id_inv, bukti_satu, bukti_dua, bukti_tiga, lokasi, approval, approval_date, approval_by, jenis_reject, alasan, created_date, created_by)
										SELECT ?, id_bukti_terima, id_inv, bukti_satu, bukti_dua, bukti_tiga, lokasi, approval, approval_date, approval_by, jenis_reject, alasan, created_date, created_by 
										FROM inv_bukti_terima
										WHERE id_inv = ?");
			// Bind parameter dan eksekusi
			$stmt->bind_param("ss", $id_history, $id_inv);
			$insert_history = $stmt->execute();
			if (!$insert_history) {
				throw new Exception("Gagal update status kirim");
			}

			// Jika semua berhasil, commit perubahan
			$connect->commit();
			$_SESSION['info'] = "Disimpan";
			header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
		} catch (exception $e){
			// Jika ada kesalahan, rollback perubahan
			$connect->rollback();
			$_SESSION['info'] = "Data Gagal Disimpan";
			// echo $e->getMessage();
			header("Location: {$_SERVER['HTTP_REFERER']}");
            exit();
		}
    } else {
        header("Location:../404.php");
    }
?>