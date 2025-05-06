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

	if (isset($sanitasi_post["simpan-cs"])) {
		$id_cs = $sanitasi_post['id_cs'];
		$nama_cs = $sanitasi_post['nama_cs'];
		$alamat = $connect->real_escape_string($sanitasi_post['alamat_cs']);
		$telp = $sanitasi_post['telp_cs'];
		$email = $sanitasi_post['email'];
	
		$cek_cs = mysqli_query($connect, "SELECT nama_cs FROM tb_customer_sph WHERE nama_cs = '$nama_cs'");
	
		if ($cek_cs->num_rows > 0) {
		$_SESSION['info'] = 'Data Gagal Disimpan';
		header("Location:../data-customer-sph.php");
		} else {
			// Membuat folder baru
			$simpan_cs = mysqli_query($connect, "INSERT INTO tb_customer_sph
								(id_cs, nama_cs, alamat, no_telp, email, created_by) 
								VALUES 
								('$id_cs', '$nama_cs', '$alamat', '$telp', '$email', '$id_user')");
			
			$_SESSION['info'] = 'Disimpan';
			echo "<script>document.location.href='../data-customer-sph.php'</script>";
		}

		//Edit
	} elseif (isset($sanitasi_post["edit-cs"])) {
		$id_cs = decrypt($sanitasi_post['id_cs'], $key_global);
		$nama_cs = $sanitasi_post['nama_cs'];
		$alamat = $connect->real_escape_string($sanitasi_post['alamat_cs']);
		$telp = $sanitasi_post['telp_cs'];
		$email = $sanitasi_post['email'];

		// menampilkan data
		$query = "SELECT * FROM tb_customer_sph WHERE id_cs = '$id_cs'";
		$result = mysqli_query($connect, $query);
		$data_lama = mysqli_fetch_assoc($result);

		if ($data_lama['nama_cs'] == $nama_cs) {
			// Nama tidak berubah, simpan data langsung
			$update = mysqli_query($connect, "UPDATE tb_customer_sph
						SET
						nama_cs = '$nama_cs',
						alamat = '$alamat',
						no_telp = '$telp',
						email = '$email',
						updated_by = '$id_user'
						WHERE id_cs='$id_cs'");
			$_SESSION['info'] = 'Disimpan';
			// echo "<script>document.location.href='../data-customer-sph.php'</script>";
		} else {
			// Nama berubah, cek apakah ada nama yang sama di database
			$cek_cs = mysqli_query($connect, "SELECT nama_cs FROM tb_customer_sph WHERE nama_cs = '$nama_cs'");

			if ($cek_cs->num_rows > 0) {
				// Ada nama yang sama di database, tampilkan pesan error
				$_SESSION['info'] = 'Nama customer sudah ada';
				echo "<script>document.location.href='../data-customer_sph.php'</script>";
			} else {
				// Nama belum digunakan, simpan data
				$update = mysqli_query($connect, "UPDATE tb_customer_sph 
								SET
								nama_cs = '$nama_cs',
								alamat = '$alamat',
								no_telp = '$telp',
								email = '$email',
								updated_by = '$id_user'
								WHERE id_cs='$id_cs'");

				$_SESSION['info'] = 'Diupdate';
				echo "<script>document.location.href='../data-customer-sph.php'</script>";
			}
		}

		// Hapus
	} elseif ($sanitasi_get['hapus-cs']) {
		//tangkap URL dengan $sanitasi_get
		$idh = $sanitasi_get['hapus-cs'];
		$id_cs = decrypt($idh, $key_global);
		
		// perintah queery sql untuk hapus data
		$sql = "DELETE FROM tb_customer_sph WHERE id_cs='$id_cs'";
		$query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));


		if ($query_del) {
			$_SESSION['info'] = 'Dihapus';
			echo "<script>document.location.href='../data-customer-sph.php'</script>";
		} else {
			$_SESSION['info'] = 'Data Gagal Dihapus';
			echo "<script>document.location.href='../data-customer-sph.php'</script>";
		}
	}