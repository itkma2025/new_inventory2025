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
	if (isset($sanitasi_post["simpan-lokasi-produk"])) {
		$id_lokasi_produk = $sanitasi_post['id_lokasi_produk'];
		$lokasi = $sanitasi_post['lokasi'];
		$no_lantai = $sanitasi_post['no_lantai'];
		$area = $sanitasi_post['area'];
		$no_rak = $sanitasi_post['no_rak'];

		$cek_lok = mysqli_query($connect, "SELECT * FROM tb_lokasi_produk WHERE nama_lokasi = '$lokasi' AND no_lantai = '$no_lantai' AND nama_area = '$area' AND no_rak = '$no_rak'");

		if ($cek_lok->num_rows > 0) {
			$_SESSION['info'] = 'Data sudah ada';
			header("Location:../lokasi-produk.php");
		} else {
			mysqli_query($connect, "INSERT INTO tb_lokasi_produk
						(id_lokasi, nama_lokasi, no_lantai, nama_area, no_rak, created_by) 
						VALUES 
						('$id_lokasi_produk', '$lokasi', '$no_lantai', '$area', '$no_rak', '$id_user')");

			$_SESSION['info'] = 'Disimpan';
			header("Location:../lokasi-produk.php");
		}

		//Edit
	} elseif (isset($sanitasi_post["edit-lokasi-produk"])) {
		$id_lokasi_produk = decrypt($sanitasi_post['id_lokasi_produk'], $key_global);
		$lokasi = $sanitasi_post['lokasi'];
		$no_lantai = $sanitasi_post['no_lantai'];
		$area = $sanitasi_post['area'];
		$no_rak = $sanitasi_post['no_rak'];

		// cek data sebelum update
		$cek_lok = mysqli_query($connect, "SELECT * FROM tb_lokasi_produk WHERE nama_lokasi = '$lokasi' AND no_lantai = '$no_lantai' AND nama_area = '$area' AND no_rak = '$no_rak'");

		if ($cek_lok->num_rows > 0) {
			// Ada nama yang sama di database, tampilkan pesan error
			$_SESSION['info'] = 'Data sudah ada';
			header("Location:../lokasi-produk.php");
		} else {
			// Data belum ada, simpan data
			$update = mysqli_query($connect, "UPDATE tb_lokasi_produk 
					SET
					nama_lokasi = '$lokasi',
					no_lantai = '$no_lantai',
					nama_area = '$area',
					no_rak = '$no_rak',
					updated_by = '$id_user'
					WHERE id_lokasi='$id_lokasi_produk'");

			$_SESSION['info'] = 'Diupdate';
			header("Location:../lokasi-produk.php");
		}

		// Hapus 
	} else if (isset($sanitasi_post["hapus-lokasi-produk"])) {
		//tangkap URL dengan $_GET
		$idh = decrypt($sanitasi_post['id_lokasi_produk'], $key_global);

		// perintah queery sql untuk hapus data
		$sql = "DELETE FROM tb_lokasi_produk WHERE id_lokasi ='$idh'";
		$query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

		if ($query_del) {
			$_SESSION['info'] = 'Dihapus';
			header("Location:../lokasi-produk.php");
		} else {
			$_SESSION['info'] = 'Data Gagal Dihapus';
			header("Location:../lokasi-produk.php");
		}
	}
