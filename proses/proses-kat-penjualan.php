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
if (isset($sanitasi_post["simpan-kat-penjualan"])) {
	$id_kat_penjualan = $sanitasi_post['id_kat_penjualan'];
	$nama_kategori = $sanitasi_post['nama_kategori'];
	$min_stock = $sanitasi_post['min_stock'];
	$max_stock = $sanitasi_post['max_stock'];
	$min_stock_ready = $sanitasi_post['min_stock_ready'];
	$max_stock_ready = $sanitasi_post['max_stock_ready'];

	$min_stock = intval(preg_replace("/[^0-9]/", "", $min_stock));
	$max_stock = intval(preg_replace("/[^0-9]/", "", $max_stock));

	$cek_kat = mysqli_query($connect, "SELECT nama_kategori FROM tb_kat_penjualan WHERE nama_kategori = '$nama_kategori'");

	if ($cek_kat->num_rows > 0) {
		$_SESSION['info'] = 'Data Gagal Disimpan';
		header("Location:../kategori-penjualan.php");
	} else {
		// Persiapkan query
		$stmt = $connect->prepare("INSERT INTO tb_kat_penjualan 
			(id_kat_penjualan, nama_kategori, min_stock, max_stock, min_stock_ready, max_stock_ready, created_by) 
			VALUES (?, ?, ?, ?, ?, ?, ?)");
		
		// Periksa apakah prepare berhasil
		if ($stmt) {
			// Bind parameter ke statement
			$stmt->bind_param(
				"ssiiiis", // Jenis data: s = string, i = integer
				$id_kat_penjualan, 
				$nama_kategori, 
				$min_stock, 
				$max_stock, 
				$min_stock_ready, 
				$max_stock_ready, 
				$id_user
			);
	
			// Eksekusi statement
			if ($stmt->execute()) {
				$_SESSION['info'] = 'Disimpan';
			} else {
				$_SESSION['info'] = 'Data Gagal Disimpan';
			}
	
			// Tutup statement
			$stmt->close();
		} else {
			$_SESSION['info'] = 'Data Gagal Disimpan';
		}
	
		// Redirect ke halaman kategori-penjualan.php
		header("Location:../kategori-penjualan.php");
	}
	
	//Edit
} elseif (isset($sanitasi_post["edit-kat-penjualan"])) {
	$id_kat_penjualan = decrypt($sanitasi_post['id_kat_penjualan'], $key_global);
	$nama_kategori = $sanitasi_post['nama_kategori'];
	$min_stock = $sanitasi_post['min_stock'];
	$max_stock = $sanitasi_post['max_stock'];
	$min_stock_ready = $sanitasi_post['min_stock_ready'];
	$max_stock_ready = $sanitasi_post['max_stock_ready'];

	$min_stock = intval(preg_replace("/[^0-9]/", "", $min_stock));
	$max_stock = intval(preg_replace("/[^0-9]/", "", $max_stock));
	// menampilkan data
	$query = "SELECT * FROM tb_kat_penjualan WHERE id_kat_penjualan = '$id_kat_penjualan'";
	$result = mysqli_query($connect, $query);
	$data_lama = mysqli_fetch_assoc($result);

	// Cek apakah nama kategori berubah
	if ($data_lama['nama_kategori'] != $nama_kategori) {
		// Nama berubah, cek apakah ada nama yang sama di database
		$sql = "SELECT COUNT(*) AS jumlah FROM tb_kat_penjualan WHERE nama_kategori = '$nama_kategori'";
		$result = mysqli_query($connect, $sql);
		$row = mysqli_fetch_assoc($result);

		if ($row['jumlah'] > 0) {
			// Ada nama yang sama di database, tampilkan pesan error
			$_SESSION['info'] = 'Nama kategori sudah ada';
			header("Location:../kategori-penjualan.php");
			exit;
		}
	}

	// Simpan data (baik nama kategori tidak berubah atau nama kategori valid dan belum ada di database)
	$stmt = $connect->prepare("UPDATE tb_kat_penjualan 
		SET 
			nama_kategori = ?, 
			min_stock = ?, 
			max_stock = ?, 
			min_stock_ready = ?, 
			max_stock_ready = ?, 
			updated_by = ? 
		WHERE id_kat_penjualan = ?");
	$stmt->bind_param("siiiiss", $nama_kategori, $min_stock, $max_stock, $min_stock_ready, $max_stock_ready, $id_user, $id_kat_penjualan);

	// Eksekusi query dan beri notifikasi
	if ($stmt->execute()) {
		$_SESSION['info'] = 'Disimpan';
	} else {
		$_SESSION['info'] = 'Data Gagal Disimpan';
	}

	$stmt->close();
	header("Location:../kategori-penjualan.php");

	// Hapus 
} elseif ($sanitasi_get['hapus-kat-penjualan']) {
	//tangkap URL dengan $sanitasi_get
	$idh = decrypt($sanitasi_get['hapus-kat-penjualan'], $key_global);

	// perintah queery sql untuk hapus data
	$sql = "DELETE FROM tb_kat_penjualan WHERE id_kat_penjualan='$idh'";
	$query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

	if ($query_del) {
		$_SESSION['info'] = 'Dihapus';
		header("Location:../kategori-penjualan.php");
	} else {
		$_SESSION['info'] = 'Data Gagal Dihapus';
		header("Location:../kategori-penjualan.php");
	}
}
