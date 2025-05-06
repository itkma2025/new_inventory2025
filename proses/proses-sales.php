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

    if (isset($sanitasi_post['simpan-sales'])) {
        $id_sales = $sanitasi_post['id_sales'];
        $nama_sales = $sanitasi_post['nama_sales'];
        $telp = $sanitasi_post['telp'];
        $id_user = $sanitasi_post['id_user'];
        $created = $sanitasi_post['created'];

        $cek_data = mysqli_query($connect, "SELECT nama_sales FROM tb_sales WHERE nama_sales = '$nama_sales'");

        if ($cek_data->num_rows < 1) {
            $simpan_data = "INSERT INTO tb_sales
                            (id_sales, id_user, nama_sales, no_telp, created_date) VALUES ('$id_sales', '$id_user', '$nama_sales', '$telp', '$created')";
            $query = mysqli_query($connect, $simpan_data);
            $_SESSION['info'] = 'Disimpan';
            header("Location:../data-sales.php");
        } else {
            $_SESSION['info'] = 'Data sudah ada';
            header("Location:../data-sales.php");
        }
    } else if (isset($sanitasi_post['edit-sales'])) {
        $id_sales = $sanitasi_post['id_sales'];
        $nama_sales = $sanitasi_post['nama_sales'];
        $telp = $sanitasi_post['telp_sales'];

        $cek_data = mysqli_query($connect, "SELECT * FROM tb_sales WHERE id_sales = '$id_sales'");
        $row = mysqli_fetch_array($cek_data);

        if ($row['nama_sales'] == $nama_sales) {
            $edit_data = mysqli_query($connect, "UPDATE tb_sales
            SET
            no_telp = '$telp'
            WHERE id_sales = '$id_sales'");
            $_SESSION['info'] = 'Diupdate';
            header("Location:../data-sales.php");
        } else {
            $cek_data = mysqli_query($connect, "SELECT nama_sales FROM tb_sales WHERE nama_sales = '$nama_sales'");

            if ($cek_data->num_rows > 0) {
                // Ada nama yang sama di database, tampilkan pesan error
                $_SESSION['info'] = 'Data sudah ada';
                header("Location:../data-sales.php");
            } else {
                // Nama belum digunakan, simpan data
                $edit_data = mysqli_query($connect, "UPDATE tb_sales
                SET
                nama_sales = '$nama_sales',
                no_telp = '$telp'
                WHERE id_sales = '$id_sales'");

                $_SESSION['info'] = 'Diupdate';
                header("Location:../data-sales.php");
            }
        }
    } elseif ($sanitasi_get['hapus-sales']) {
        //tangkap URL dengan $sanitasi_get
        $idh = $sanitasi_get['hapus-sales'];
        $id_sales = base64_decode($idh);

        // perintah queery sql untuk hapus data
        $sql = "DELETE FROM tb_sales WHERE id_sales='$id_sales'";
        $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));


        if ($query_del) {
            $_SESSION['info'] = 'Dihapus';
            header("Location:../data-sales.php");
        } else {
            $_SESSION['info'] = 'Data Gagal Dihapus';
            header("Location:../data-sales.php");
        }
    }
