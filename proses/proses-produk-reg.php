<?php
require_once "../akses.php";
require_once '../assets/Qrcode/qrlib.php';
$id_user = decrypt($_SESSION['tiket_id'], $key_global);

// Simpan
if (isset($_POST["simpan-produk-reg"])) {
	$token_csrf = $_POST['csrf_token'];
	$id_produk = $_POST['id_produk'];
	$kode = $_POST['kode_produk'];
	$nama = htmlspecialchars($_POST['nama_produk']);
	$kode_katalog = htmlspecialchars($_POST['kode_katalog']);
	$no_batch = htmlspecialchars($_POST['no_batch']);
	$satuan = $_POST['satuan'];
	$merk = $_POST['merk'];
	$harga = $_POST['harga'];
	$lokasi = htmlspecialchars($_POST['id_lokasi']);
	$kat_produk = $_POST['kategori_produk'];
	$kat_penjualan = htmlspecialchars($_POST['kategori_penjualan']);
	$grade = htmlspecialchars($_POST['grade']);
	$jenis_produk = $_POST['jenis_produk'];
	$deskripsi = $_POST['deskripsi'];
	// Cek token
	$exp_token = $_SESSION['token_exp'];
	$date_now = date('Y-m-d H:i:s');
	if ($token_csrf != "") {
		if ($jenis_produk == 'reg') {
			if ($date_now > $exp_token) {
				$_SESSION['info'] = 'Token expired';
				header("Location:../tambah-data-produk.php");
			} else {
				$cek_data = mysqli_query($connect, "SELECT * FROM tb_produk_reguler WHERE kode_produk = '$kode' AND nama_produk = '$nama' AND id_merk = '$merk' AND kode_katalog = '$kode_katalog'");

				if ($cek_data->num_rows > 0) {
					$_SESSION['info'] = 'Data sudah ada';
					header("Location:../data-produk-reg.php");
				} else {
					// Convert budget to integer
					$harga = intval(preg_replace("/[^0-9]/", "", $harga));

					// Mendapatkan informasi file
					$nama_file = $_FILES["fileku"]["name"];
					$tipe_file = $_FILES["fileku"]["type"];
					$ukuran_file = $_FILES["fileku"]["size"];
					$tmp_file = $_FILES["fileku"]["tmp_name"];

					// Enkripsi nama file
					$ubah_nama = 'IMG';
					$nama_file_baru = $ubah_nama . uniqid() . '.jpg';

					// Simpan file ke direktori tujuan
					$direktori_tujuan = "../gambar/upload-produk-reg/";
					$target_file = $direktori_tujuan . $nama_file_baru;
					move_uploaded_file($tmp_file, $target_file);

					$sql = "INSERT INTO tb_produk_reguler
						(id_produk_reg, id_merk, id_kat_produk, id_kat_penjualan, id_grade, id_lokasi, kode_produk, nama_produk, no_batch, kode_katalog, satuan, harga_produk, gambar, deskripsi, created_by)
						VALUES
						('$id_produk', '$merk', '$kat_produk', '$kat_penjualan', '$grade', '$lokasi', '$kode', '$nama', '$no_batch', '$kode_katalog', '$satuan', '$harga', '$nama_file_baru', '$deskripsi', '$id_user')";
					$query = mysqli_query($connect, $sql) or die(mysqli_error($connect, $sql));

					$_SESSION['info'] = 'Disimpan';
					header("Location:../data-produk-reg.php");
				}
			}
		} else if ($jenis_produk == 'ecat') {
			if ($date_now > $exp_token) {
				$_SESSION['info'] = 'Token expired';
				header("Location:../tambah-data-produk.php");
			} else {
				$cek_data = mysqli_query($connect, "SELECT * FROM tb_produk_ecat WHERE kode_produk = '$kode' AND nama_produk = '$nama' AND id_merk = '$merk' AND kode_katalog = '$kode_katalog'");

				if ($cek_data->num_rows > 0) {
					$_SESSION['info'] = 'Data sudah ada';
					header("Location:../data-produk-reg.php");
				} else {
					// Convert budget to integer
					$harga = intval(preg_replace("/[^0-9]/", "", $harga));

					// Mendapatkan informasi file
					$nama_file = $_FILES["fileku"]["name"];
					$tipe_file = $_FILES["fileku"]["type"];
					$ukuran_file = $_FILES["fileku"]["size"];
					$tmp_file = $_FILES["fileku"]["tmp_name"];

					// Enkripsi nama file
					$ubah_nama = 'IMG';
					$nama_file_baru = $ubah_nama . uniqid() . '.jpg';

					// Simpan file ke direktori tujuan
					$direktori_tujuan = "../gambar/upload-produk-ecat/";
					$target_file = $direktori_tujuan . $nama_file_baru;
					move_uploaded_file($tmp_file, $target_file);
					$sql = "INSERT INTO tb_produk_ecat
						(id_produk_ecat, id_merk, id_kat_produk, id_kat_penjualan, id_grade, id_lokasi, kode_produk, nama_produk, no_batch, kode_katalog, satuan, harga_produk, gambar, deskripsi, created_by)
						VALUES
						('$id_produk', '$merk', '$kat_produk', '$kat_penjualan', '$grade', '$lokasi', '$kode', '$nama', '$no_batch', '$kode_katalog', '$satuan', '$harga', '$nama_file_baru', '$deskripsi', '$id_user')";
					$query = mysqli_query($connect, $sql) or die(mysqli_error($connect, $sql));

					$_SESSION['info'] = 'Disimpan';
					header("Location:../data-produk-ecat.php");
				}
			}
		}
	} else {
		$_SESSION['info'] = 'Token not found';
		echo "<script>document.location.href='../tambah-data-produk.php'</script>";
	}

	//Edit
} elseif (isset($_POST["edit-produk-reg"])) {
	$id_produk = htmlspecialchars($_POST['id_produk']);
	$kode = htmlspecialchars($_POST['kode_produk']);
	$no_batch = htmlspecialchars($_POST['no_batch']);
	$nama = htmlspecialchars($_POST['nama_produk']);
	$kode_katalog = htmlspecialchars($_POST['kode_katalog']);
	$satuan = $_POST['satuan'];
	$merk = $_POST['merk'];
	$harga = $_POST['harga'];
	$lokasi = htmlspecialchars($_POST['id_lokasi']);
	$kat_produk = htmlspecialchars($_POST['id_kat_produk']);
	$kat_penjualan = htmlspecialchars($_POST['kategori_penjualan']);
	$grade = htmlspecialchars($_POST['grade']);
	$deskripsi = $_POST['deskripsi'];
	// Convert budget to integer
	$harga = intval(preg_replace("/[^0-9]/", "", $harga));

	// Mendapatkan informasi file
	$nama_file = $_FILES["fileku"]["name"];
	$tipe_file = $_FILES["fileku"]["type"];
	$ukuran_file = $_FILES["fileku"]["size"];
	$tmp_file = $_FILES["fileku"]["tmp_name"];

	try {
		// Mulai transaksi
		$connect->begin_transaction();

		 // Default nilai gambar
		 $gambar_query = '';

		 // Cek jika ada file gambar yang diunggah
		 if (!empty($_FILES["fileku"]["name"])) {
			 // Query untuk mendapatkan gambar lama
			 $result = $connect->query("SELECT gambar FROM tb_produk_reguler WHERE id_produk_reg = '$id_produk'");
			 if (!$result) {
				 throw new Exception("Gagal mendapatkan data gambar lama: " . $connect->error);
			 }
			 $row = $result->fetch_assoc();
	 
			 // Hapus file lama jika ada
			 if (!empty($row['gambar'])) {
				 $old_file_path = "../gambar/upload-produk-reg/{$row['gambar']}";
				 if (file_exists($old_file_path)) {
					 if (!unlink($old_file_path)) {
						 throw new Exception("Gagal menghapus file lama: $old_file_path");
					 }
				 }
			 }
	 
			 // Enkripsi nama file baru
			 $ubah_nama = 'IMG';
			 $nama_file_baru = $ubah_nama . uniqid() . '.jpg';
	 
			 // Simpan file ke direktori tujuan
			 $direktori_tujuan = "../gambar/upload-produk-reg/";
			 $target_file = $direktori_tujuan . $nama_file_baru;
			 if (!move_uploaded_file($_FILES["fileku"]["tmp_name"], $target_file)) {
				 throw new Exception("Gagal mengunggah file ke direktori tujuan: $target_file");
			 }
	 
			 // Tambahkan kolom gambar ke query update
			 $gambar_query = ", gambar = '$nama_file_baru'";
		 }
	 
		 // Query update
		 $update_query = "UPDATE tb_produk_reguler 
							SET 
								id_merk = '$merk', 
								id_kat_produk = '$kat_produk', 
								id_kat_penjualan = '$kat_penjualan', 
								id_grade = '$grade', 
								id_lokasi = '$lokasi', 
								kode_produk = '$kode', 
								no_batch = '$no_batch', 
								nama_produk = '$nama', 
								kode_katalog = '$kode_katalog', 
								satuan = '$satuan', 
								harga_produk = '$harga', 
								deskripsi = '$deskripsi', 
								updated_by = '$id_user' 
								$gambar_query 
							WHERE id_produk_reg = '$id_produk'
						";
	 
		 if (!$connect->query($update_query)) {
			 throw new Exception("Gagal memperbarui data produk: " . $connect->error);
		 }
	 
		 // Commit transaksi jika semua berhasil
		 $connect->commit();
		 $_SESSION['info'] = 'Diupdate';
		 header("Location:../data-produk-reg.php");
	} catch (Exception $e) {
		// Rollback transaksi jika terjadi kesalahan
		$connect->rollback();
		$_SESSION['info'] = 'Data Gagal Diupdate';
		header("Location:../data-produk-reg.php");
		// Tampilkan pesan error untuk debugging
		// echo "Terjadi kesalahan: " . $e->getMessage();
	}
	//Edit Ecat
} elseif (isset($_POST["edit-produk-ecat"])) {
	$id_produk = htmlspecialchars($_POST['id_produk']);
	$kode = htmlspecialchars($_POST['kode_produk']);
	$no_batch = htmlspecialchars($_POST['no_batch']);
	$nama = htmlspecialchars($_POST['nama_produk']);
	$kode_katalog = htmlspecialchars($_POST['kode_katalog']);
	$satuan = $_POST['satuan'];
	$merk = $_POST['merk'];
	$harga = $_POST['harga'];
	$lokasi = htmlspecialchars($_POST['id_lokasi']);
	$kat_produk = htmlspecialchars($_POST['id_kat_produk']);
	$kat_penjualan = htmlspecialchars($_POST['kategori_penjualan']);
	$grade = htmlspecialchars($_POST['grade']);
	$deskripsi = $_POST['deskripsi'];
	// Convert budget to integer
	$harga = intval(preg_replace("/[^0-9]/", "", $harga));

	// Mendapatkan informasi file
	$nama_file = $_FILES["fileku"]["name"];
	$tipe_file = $_FILES["fileku"]["type"];
	$ukuran_file = $_FILES["fileku"]["size"];
	$tmp_file = $_FILES["fileku"]["tmp_name"];

	try {
		// Mulai transaksi
		$connect->begin_transaction();

		 // Default nilai gambar
		 $gambar_query = '';

		 // Cek jika ada file gambar yang diunggah
		 if (!empty($_FILES["fileku"]["name"])) {
			 // Query untuk mendapatkan gambar lama
			 $result = $connect->query("SELECT gambar FROM tb_produk_ecat WHERE id_produk_ecat = '$id_produk'");
			 if (!$result) {
				 throw new Exception("Gagal mendapatkan data gambar lama: " . $connect->error);
			 }
			 $row = $result->fetch_assoc();
	 
			 // Hapus file lama jika ada
			 if (!empty($row['gambar'])) {
				 $old_file_path = "../gambar/upload-produk-reg/{$row['gambar']}";
				 if (file_exists($old_file_path)) {
					 if (!unlink($old_file_path)) {
						 throw new Exception("Gagal menghapus file lama: $old_file_path");
					 }
				 }
			 }
	 
			 // Enkripsi nama file baru
			 $ubah_nama = 'IMG';
			 $nama_file_baru = $ubah_nama . uniqid() . '.jpg';
	 
			 // Simpan file ke direktori tujuan
			 $direktori_tujuan = "../gambar/upload-produk-ecat/";
			 $target_file = $direktori_tujuan . $nama_file_baru;
			 if (!move_uploaded_file($_FILES["fileku"]["tmp_name"], $target_file)) {
				 throw new Exception("Gagal mengunggah file ke direktori tujuan: $target_file");
			 }
	 
			 // Tambahkan kolom gambar ke query update
			 $gambar_query = ", gambar = '$nama_file_baru'";
		 }
	 
		 // Query update
		 $update_query = "UPDATE tb_produk_ecat 
							SET 
								id_merk = '$merk', 
								id_kat_produk = '$kat_produk', 
								id_kat_penjualan = '$kat_penjualan', 
								id_grade = '$grade', 
								id_lokasi = '$lokasi', 
								kode_produk = '$kode', 
								no_batch = '$no_batch', 
								nama_produk = '$nama', 
								kode_katalog = '$kode_katalog', 
								satuan = '$satuan', 
								harga_produk = '$harga', 
								deskripsi = '$deskripsi', 
								updated_by = '$id_user' 
								$gambar_query 
							WHERE id_produk_ecat = '$id_produk'
						";
	 
		 if (!$connect->query($update_query)) {
			 throw new Exception("Gagal memperbarui data produk: " . $connect->error);
		 }
	 
		 // Commit transaksi jika semua berhasil
		 $connect->commit();
		 $_SESSION['info'] = 'Diupdate';
		 header("Location:../data-produk-ecat.php");
	} catch (Exception $e) {
		// Rollback transaksi jika terjadi kesalahan
		$connect->rollback();
		$_SESSION['info'] = 'Data Gagal Diupdate';
		header("Location:../data-produk-ecat.php");
		// Tampilkan pesan error untuk debugging
		// echo "Terjadi kesalahan: " . $e->getMessage();
	}
// Hapus 
} elseif (isset($_POST['hapus-produk-reg'])) {
	$idh = decrypt($_POST['id_produk'], $key_global);

	// Mengambil nama gambar yang terkait
	$sql = "SELECT 
					pr.id_produk_reg, pr.gambar, qr.id_produk_qr, qr.qr_img 
				FROM tb_produk_reguler AS pr
				LEFT JOIN qr_link qr ON (pr.id_produk_reg = qr.id_produk_qr)
				WHERE id_produk_reg = '$idh'";

	// Membuat prepared statement
	$query = mysqli_query($connect, $sql);
	$row = mysqli_fetch_array($query);
	$gambar = $row['gambar'];
	$gambar_qr = $row['qr_img'];

	try {
		// Memulai transaksi
		mysqli_begin_transaction($connect);

		// Menghapus data dari tabel tb_produk_reguler
		$sql_delete_produk = "DELETE FROM tb_produk_reguler WHERE id_produk_reg = '$idh'";
		$stmt_delete_produk = mysqli_query($connect, $sql_delete_produk);

		$sql_delete_produk = "DELETE FROM stock_produk_reguler WHERE id_produk_reg = '$idh'";
		$stmt_delete_stock = mysqli_query($connect, $sql_delete_produk);


		// Menghapus data dari tabel qr_link
		$sql_delete_qr = "DELETE FROM qr_link WHERE id_produk_qr = '$idh'";
		$stmt_delete_qr = mysqli_query($connect, $sql_delete_qr);

		// Menjalankan penghapusan
		if ($stmt_delete_produk && $stmt_delete_qr && $stmt_delete_stock) {
			// Hapus gambar terkait
			unlink("../gambar/upload-produk-reg/$gambar");
			unlink("../gambar/QRcode/$gambar_qr");

			// Commit transaksi
			mysqli_commit($connect);

			$_SESSION['info'] = 'Dihapus';
			header("Location:../data-produk-reg.php");
		} else {
			// Rollback transaksi jika ada kesalahan
			mysqli_rollback($connect);

			$_SESSION['info'] = 'Data Gagal Dihapus';
			header("Location:../data-produk-reg.php");
		}
	} catch (Exception $e) {
		// Tangani pengecualian di sini, misalnya:
		$_SESSION['info'] = 'Terjadi kesalahan: ' . $e->getMessage();
		header("Location:../data-produk-reg.php");
	}
	// Hapus Ecat
} elseif (isset($_POST['hapus-produk-ecat'])) {
	$idh = decrypt($_POST['id_produk'], $key_global);

	// Mengambil nama gambar yang terkait
	$sql = "SELECT 
					pr.id_produk_ecat, pr.gambar, qr.id_produk_qr, qr.qr_img 
				FROM tb_produk_ecat AS pr
				LEFT JOIN qr_link_ecat qr ON (pr.id_produk_ecat = qr.id_produk_qr)
				WHERE id_produk_ecat = '$idh'";

	// Membuat prepared statement
	$query = mysqli_query($connect, $sql);
	$row = mysqli_fetch_array($query);
	$gambar = $row['gambar'];
	$gambar_qr = $row['qr_img'];

	try {
		// Memulai transaksi
		mysqli_begin_transaction($connect);

		// Menghapus data dari tabel tb_produk_ecat
		$sql_delete_produk = "DELETE FROM tb_produk_ecat WHERE id_produk_ecat = '$idh'";
		$stmt_delete_produk = mysqli_query($connect, $sql_delete_produk);

		// Menghapus data dari tabel tb_produk_ecat
		$sql_delete_produk = "DELETE FROM stock_produk_ecat WHERE id_produk_ecat = '$idh'";
		$stmt_delete_stock = mysqli_query($connect, $sql_delete_produk);


		// Menghapus data dari tabel qr_link
		$sql_delete_qr = "DELETE FROM qr_link_ecat WHERE id_produk_qr = '$idh'";
		$stmt_delete_qr = mysqli_query($connect, $sql_delete_qr);

		// Menjalankan penghapusan
		if ($stmt_delete_produk && $stmt_delete_qr && $stmt_delete_stock) {
			// Hapus gambar terkait
			unlink("../gambar/upload-produk-ecat/$gambar");
			unlink("../gambar/QRcode-ecat/$gambar_qr");

			// Commit transaksi
			mysqli_commit($connect);

			$_SESSION['info'] = 'Dihapus';
			header("Location:../data-produk-ecat.php");
		} else {
			// Rollback transaksi jika ada kesalahan
			mysqli_rollback($connect);

			$_SESSION['info'] = 'Data Gagal Dihapus';
			header("Location:../data-produk-ecat.php");
		}
	} catch (Exception $e) {
		// Tangani pengecualian di sini, misalnya:
		$_SESSION['info'] = 'Terjadi kesalahan: ' . $e->getMessage();
		echo "<script>document.location.href='../data-produk-ecat.php'</script>";
	}
}
