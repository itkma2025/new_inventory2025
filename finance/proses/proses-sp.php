<?php
require_once "../../akses.php";

// Simpan
if (isset($_POST["simpan-sp"])) {
	$id_sp = $_POST['id_sp'];
	$nama_sp = $_POST['nama_sp'];
	$alamat = $_POST['alamat_sp'];
	$telp = $_POST['telp_sp'];
	$email = $_POST['email'];
	$created_date = $_POST['created'];
	$created_by = $_SESSION['tiket_id'];

	$cek_sp = mysqli_query($connect, "SELECT nama_sp FROM tb_supplier WHERE nama_sp = '$nama_sp'");

	if ($cek_sp->num_rows > 0) {
		$_SESSION['info'] = 'Nama supplier sudah ada';
		header("Location:../data-supplier.php");
	} else {
		mysqli_query($connect, "INSERT INTO tb_supplier
                      (id_sp, nama_sp, email, alamat, no_telp, created_date, created_by) VALUES ('$id_sp', '$nama_sp', '$email', '$alamat', '$telp', '$created_date', '$created_by')");

		$_SESSION['info'] = 'Disimpan';
		header("Location:../data-supplier.php");
	}

	//Edit
} elseif (isset($_POST["edit-sp"])) {
	$id_sp = $_POST['id_sp'];
	$nama_sp = $_POST['nama_sp'];
	$alamat = $_POST['alamat_sp'];
	$telp = $_POST['telp_sp'];
	$email = $_POST['email'];
	$updated_date = $_POST['updated'];
	$updated_by = $_SESSION['tiket_id'];

	// menampilkan data
	$query = "SELECT * FROM tb_supplier WHERE id_sp = '$id_sp'";
	$result = mysqli_query($connect, $query);
	$data_lama = mysqli_fetch_assoc($result);

	if ($data_lama['nama_sp'] == $nama_sp) {
		// Nama tidak berubah, simpan data langsung
		$update = mysqli_query($connect, "UPDATE tb_supplier 
	                SET
					nama_sp = '$nama_sp',
					email = '$email',
					alamat = '$alamat',
					no_telp = '$telp',
					updated_date = '$updated_date',
					updated_by = '$updated_by'
	                WHERE id_sp='$id_sp'");
		$_SESSION['info'] = 'Disimpan';
		echo "<script>document.location.href='../data-supplier.php'</script>";
	} else {
		// Nama berubah, cek apakah ada nama yang sama di database
		$cek_sp = mysqli_query($connect, "SELECT nama_sp FROM tb_supplier WHERE nama_sp = '$nama_sp'");

		if ($cek_sp->num_rows > 0) {
			// Ada nama yang sama di database, tampilkan pesan error
			$_SESSION['info'] = 'Nama supplier sudah ada';
			echo "<script>document.location.href='../data-supplier.php'</script>";
		} else {
			// Nama belum digunakan, simpan data
			$update = mysqli_query($connect, "UPDATE tb_supplier 
							SET
							nama_sp = '$nama_sp',
							email = '$email',
							alamat = '$alamat',
							no_telp = '$telp',
							updated_date = '$updated_date',
							updated_by = '$updated_by'
							WHERE id_sp='$id_sp'");

			$_SESSION['info'] = 'Diupdate';
			echo "<script>document.location.href='../data-supplier.php'</script>";
		}
	}

	// Hapus
} elseif ($_GET['hapus-sp']) {
	//tangkap URL dengan $_GET
	$idh = $_GET['hapus-sp'];
	$id_sp = base64_decode($idh);

	// perintah queery sql untuk hapus data
	$sql = "DELETE FROM tb_supplier WHERE id_sp='$id_sp'";
	$query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

	if ($query_del) {
		$_SESSION['info'] = 'Dihapus';
		echo "<script>document.location.href='../data-supplier.php'</script>";
	} else {
		$_SESSION['info'] = 'Data Gagal Dihapus';
		echo "<script>document.location.href='../data-supplier.php'</script>";
	}
}
