<?php
include "../akses.php";

// Simpan
if (isset($_POST["simpan-role"])) {
	$id_user_role = $_POST['id_user_role'];
	$role = $_POST['role'];
	$created = $_POST['created'];

	$cek_role = mysqli_query($connect, "SELECT role FROM user_role WHERE role = '$role'");

	if ($cek_role->num_rows > 0) {
		$_SESSION['info'] = 'Data Gagal Disimpan';
		echo "<script>document.location.href='../data-user-role.php'</script>";
	} else {
		mysqli_query($connect, "INSERT INTO user_role 
                      (id_user_role, role, created_date) VALUES ('$id_user_role', '$role', '$created')");

		$_SESSION['info'] = 'Disimpan';
		echo "<script>document.location.href='../data-user-role.php'</script>";
	}

	//Edit
} elseif (isset($_POST["edit-role"])) {
	$id_user_role = $_POST['id_user_role'];
	$role = $_POST['role'];

	// menampilkan data
	$query = "SELECT * FROM user_role WHERE id_user_role = '$id_user_role'";
	$result = mysqli_query($connect, $query);
	$data_lama = mysqli_fetch_assoc($result);

	if ($data_lama['role'] == $role) {
		// Nama tidak berubah, simpan data langsung
		$update = mysqli_query($connect, "UPDATE user_role 
	                SET
	                role ='$role'
	                WHERE id_user_role='$id_user_role'");
		$_SESSION['info'] = 'Disimpan';
		echo "<script>document.location.href='../data-user-role.php'</script>";
	} else {
		// Nama berubah, cek apakah ada nama yang sama di database
		$cek_role = mysqli_query($connect, "SELECT role FROM user_role WHERE role = '$role'");

		if ($cek_role->num_rows > 0) {
			// Ada nama yang sama di database, tampilkan pesan error
			$_SESSION['info'] = 'Nama role sudah ada';
			echo "<script>document.location.href='../data-user-role.php'</script>";
		} else {
			// Nama belum digunakan, simpan data
			$update = mysqli_query($connect, "UPDATE user_role 
						SET
						role ='$role'
						WHERE id_user_role='$id_user_role'");

			$_SESSION['info'] = 'Diupdate';
			echo "<script>document.location.href='../data-user-role.php'</script>";
		}
	}

	// Hapus
} elseif ($_GET['hapus-role']) {
	//tangkap URL dengan $_GET
	$idh = $_GET['hapus-role'];

	// perintah queery sql untuk hapus data
	$sql = "DELETE FROM user_role WHERE id_user_role='$idh'";
	$query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

	if ($query_del) {
		$_SESSION['info'] = 'Dihapus';
		echo "<script>document.location.href='../data-user-role.php'</script>";
	} else {
		$_SESSION['info'] = 'Data Gagal Dihapus';
		echo "<script>document.location.href='../data-user-role.php'</script>";
	}
}
