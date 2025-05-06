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
	if (isset($sanitasi_post["simpan-merk"])) {
		$id_merk = $sanitasi_post['id_merk'];
		$nama_merk = $sanitasi_post['nama_merk'];

		$cek_kat = mysqli_query($connect, "SELECT nama_merk FROM tb_merk WHERE nama_merk = '$nama_merk'");

		if ($cek_kat->num_rows > 0) {
			$_SESSION['info'] = 'Nama merk sudah ada';
			echo "<script>document.location.href='../merk-produk.php'</script>";
		} else {
			mysqli_query($connect, "INSERT INTO tb_merk
						(id_merk, nama_merk, created_by) 
						VALUES 
						('$id_merk', '$nama_merk', '$id_user')");

			$_SESSION['info'] = 'Disimpan';
			echo "<script>document.location.href='../merk-produk.php'</script>";
		}

		//Edit
	} elseif (isset($sanitasi_post["edit-merk"])) {
		$id_merk = decrypt($sanitasi_post['id_merk'], $key_global);
		$nama_merk = $sanitasi_post['nama_merk'];

		// menampilkan data
		$query = "SELECT nama_merk FROM tb_merk WHERE id_merk = '$id_merk'";
		$result = mysqli_query($connect, $query);
		$data_lama = mysqli_fetch_assoc($result);

		// Cek apakah nama merk sama dengan data lama
		if ($data_lama['nama_merk'] == $nama_merk) {
			// Nama tidak berubah, tidak perlu melakukan query pembaruan
			$_SESSION['info'] = 'Tidak Ada Perubahan Data';
			echo "<script>document.location.href='../merk-produk.php'</script>";
		} else {
			// Nama berubah, cek apakah ada nama yang sama di database
			$cek_kat = mysqli_query($connect, "SELECT nama_merk FROM tb_merk WHERE nama_merk = '$nama_merk'");

			if ($cek_kat->num_rows > 0) {
				// Ada nama yang sama di database, tampilkan pesan error
				$_SESSION['info'] = 'Nama merk sudah ada';
				echo "<script>document.location.href='../merk-produk.php'</script>";
			} else {
				// Nama belum ada yang sama, lakukan pembaruan
				$update = mysqli_query($connect, "UPDATE tb_merk SET nama_merk = '$nama_merk' WHERE id_merk = '$id_merk'");

				if ($update) {
					$_SESSION['info'] = 'Diupdate';
					echo "<script>document.location.href='../merk-produk.php'</script>";
				} else {
					// Jika terjadi kesalahan saat pembaruan
					$_SESSION['info'] = 'Gagal melakukan update';
					echo "<script>document.location.href='../merk-produk.php'</script>";
				}
			}
		}

	// Hapus 
	} elseif ($sanitasi_get['hapus-merk']) {
		//tangkap URL dengan $sanitasi_get
		$idh = decrypt($sanitasi_get['hapus-merk'], $key_global);

		// perintah queery sql untuk hapus data
		$sql = "DELETE FROM  tb_merk WHERE  id_merk='$idh'";
		$query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

		if ($query_del) {
			$_SESSION['info'] = 'Dihapus';
			echo "<script>document.location.href='../merk-produk.php'</script>";
		} else {
			$_SESSION['info'] = 'Data Gagal Dihapus';
			echo "<script>document.location.href='../merk-produk.php'</script>";
		}
	}
?>
