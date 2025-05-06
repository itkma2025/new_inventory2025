<?php
session_start();
include "../koneksi.php";
date_default_timezone_set('Asia/Jakarta');
$generatedOTP = generateOTP();

$expirationTime = 123;
$expirationTimestamp = time() + $expirationTime;
// Format waktu kedaluwarsa (opsional, dapat disesuaikan sesuai kebutuhan)
$expirationFormatted = date("Y-m-d H:i:s", $expirationTimestamp);

// Simpan
if (isset($_POST["simpan-user"])) {
	$id_user = $_POST['id_user'];
	$nama_lengkap = $_POST['nama_lengkap'];
	$jenkel = $_POST['jenkel'];
	$email = $_POST['email'];
	$id_role = $_POST['role'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	$created = $_POST['created'];
	$otp = $generatedOTP;
	$uuid = generate_uuid();
	$day = date('d');
	$month = date('m');
	$year = date('y');
	$id_verifikasi = "VERIF" . $year . "" . $month . "" . $uuid . "" . $day;
	$id_verifikasi_encode = base64_encode($id_verifikasi);

	// Hash password
	$password_hash = password_hash($password, PASSWORD_DEFAULT);

	// Begin transaction
	mysqli_begin_transaction($connect);
	try {
		$cek_user = mysqli_query($connect, "SELECT username, email FROM user WHERE username = '$username' OR email = '$email' ");

		if ($cek_user->num_rows > 0) {
			echo "error";
			header("Location: ../registrasi-user.php?gagal");
		} else {
			$insert_user = mysqli_query($connect, "INSERT INTO user 
						  (id_user, nama_user, jenkel, email, id_user_role, username, password, created_date) 
						  VALUES 
						  ('$id_user', '$nama_lengkap', '$jenkel', '$email', '$id_role', '$username', '$password_hash', '$created')");

			$insert_verifikasi = $connect->query("INSERT INTO user_verifikasi (id_verifikasi, id_user, email, otp, expired) VALUES ('$id_verifikasi', '$id_user', '$email', '$otp', '$expirationFormatted')");

			if ($insert_user && $insert_verifikasi) {
				// Commit the transaction
				mysqli_commit($connect);
				header("Location:../send-mail-verifikasi.php?id=$id_verifikasi_encode");
			}
		}
	} catch (Exception $e) {
		// Rollback the transaction if an error occurs
		mysqli_rollback($connect);
		// Handle the error (e.g., display an error message)
		$error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
?>
		<!-- Sweet Alert -->
		<link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
		<script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
		<script>
			document.addEventListener("DOMContentLoaded", function() {
				Swal.fire({
					title: "Error!",
					text: "<?php echo $error_message; ?>",
					icon: "error",
				}).then(function() {
					window.location.href = "../login.php";
				});
			});
		</script>
<?php
	}
} elseif (isset($_POST["acc-user"])) {
	$id_user = htmlspecialchars($_POST['id_user']);
	$tgl_approval = date('d/m/Y H:i:s');
	$approval_by = $_SESSION['tiket_nama'];

	// Update approval user
	$update = $connect->query("	UPDATE user 
									SET 
									approval = '1',  tgl_approval = '$tgl_approval', approval_by = '$approval_by' WHERE id_user = '$id_user'");
	if ($update) {
		$_SESSION['info'] = "Disimpan";
		header("Location:../data-user.php");
	} else {
		$_SESSION['info'] = "Data Gagal Disimpan";
		header("Location:../data-user.php");
	}
} elseif (isset($_POST["tolak-user"])) {
	$id_user = htmlspecialchars($_POST['id_user']);
	// Update approval user
	$update = $connect->query("DELETE FROM user WHERE id_user = '$id_user'");
	if ($update) {
		$_SESSION['info'] = "Ditolak";
		header("Location:../data-user.php");
	} else {
		$_SESSION['info'] = "Data Gagal Disimpan";
		header("Location:../data-user.php");
	}
	//Edit 
} elseif (isset($_POST["edit-user"])) {
	$id_update = $_POST['id_user_role'];
	$hak_akses = $_POST['role'];
	$update = mysqli_query($connect, "UPDATE user_role 
	                SET
	                role ='$hak_akses'
	                WHERE id_user_role='$id_update'");
	if ($update) {
		$_SESSION['info'] = 'Diupdate';
		echo "<script>document.location.href='../data-user-role.php'</script>";
	} else {
		$_SESSION['info'] = 'Data Gagal Diupdate';
		echo "<script>document.location.href='../data-user-role.php'</script>";
	}

	// Hapus
} elseif ($_GET['hapus-user']) {
	//tangkap URL dengan $_GET
	$idh = $_GET['hapus-user'];

	// perintah queery sql untuk hapus data
	$sql = "DELETE FROM user WHERE id_user='$idh'";
	$query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

	if ($query_del) {
		$_SESSION['info'] = 'Dihapus';
		echo "<script>document.location.href='../data-user.php'</script>";
	} else {
		$_SESSION['info'] = 'Data Gagal Dihapus';
		echo "<script>document.location.href='../data-user.php'</script>";
	}
}


function generateOTP()
{
	// Panjang OTP
	$otpLength = 6;

	// Menghasilkan OTP acak dengan panjang yang ditentukan
	$otp = '';
	for ($i = 0; $i < $otpLength; $i++) {
		$otp .= rand(0, 9);
	}

	return $otp;
}

function generate_uuid()
{
	return sprintf(
		'%04x%04x%04x',
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0x0fff) | 0x4000,
		mt_rand(0, 0x3fff) | 0x8000,
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff),
		mt_rand(0, 0xffff)
	);
}

?>