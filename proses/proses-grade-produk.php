<?php
	require_once "../akses.php";
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


	// Simpan
	if (isset($sanitasi_post["simpan-grade-produk"])) {
		$id_grade = $sanitasi_post['id_grade'];
		$grade = $sanitasi_post['grade'];

		$cek_grade = mysqli_query($connect, "SELECT nama_grade FROM tb_produk_grade WHERE nama_grade = '$grade'");

		if ($cek_grade->num_rows > 0) {
			$_SESSION['info'] = 'Data sudah ada';
			header("Location:../grade-produk.php");
		} else {
			mysqli_query($connect, "INSERT INTO tb_produk_grade
						(id_grade, nama_grade, created_by) 
						VALUES 
						('$id_grade', '$grade', '$id_user')");

			$_SESSION['info'] = 'Disimpan';
			header("Location:../grade-produk.php");
		}

		//Edit
	} elseif (isset($sanitasi_post["edit-grade-produk"])) {
		$id_grade = decrypt($sanitasi_post['id_grade'], $key_global);
		$grade = $sanitasi_post['grade'];

		// cek data sebelum update
		$cek_grade = mysqli_query($connect, "SELECT * FROM tb_produk_grade WHERE nama_grade = '$grade'");

		if ($cek_grade->num_rows > 0) {
			// Ada nama yang sama di database, tampilkan pesan error
			$_SESSION['info'] = 'Data sudah ada';
			header("Location:../grade-produk.php");
		} else {
			// Data belum ada, simpan data
			$update = $connect->query("UPDATE tb_produk_grade 
										SET
											nama_grade = '$grade',
											updated_by = '$id_user'
										WHERE id_grade='$id_grade'"
									);

			$_SESSION['info'] = 'Diupdate';
			header("Location:../grade-produk.php");
		}

		// Hapus 
	} elseif ($sanitasi_get['hapus-grade-produk']) {
		//tangkap URL dengan $sanitasi_get
		$idh = decrypt($sanitasi_get['hapus-grade-produk'], $key_global);

		// perintah queery sql untuk hapus data
		$sql = "DELETE FROM tb_produk_grade WHERE id_grade ='$idh'";
		$query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

		if ($query_del) {
			$_SESSION['info'] = 'Dihapus';
			header("Location:../grade-produk.php");
		} else {
			$_SESSION['info'] = 'Data Gagal Dihapus';
			header("Location:../grade-produk.php");
		}
	}
?>