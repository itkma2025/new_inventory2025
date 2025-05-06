<?php
include "../akses.php"; 
require_once "../function/encrypt-decrypt-file.php";
$id_user = decrypt($_SESSION['tiket_id'], $key_global);

if (isset($_POST["simpan-cs"])) {
    // Ambil data dari form
    $id_cs = htmlspecialchars($_POST['id_cs']);
    $jenis_usaha = htmlspecialchars($_POST['jenis_usaha']);
    $nama_cs = htmlspecialchars($_POST['nama_cs']); 
    $nama_cs = ltrim($nama_cs, " \t"); // Menghapus spasi dan tab di awal teks
    $alamat = $connect->real_escape_string($_POST['alamat_cs']);
    $nama_cp = htmlspecialchars($_POST['nama_cp']);
    $telp = htmlspecialchars($_POST['telp_cs']);
    $email = htmlspecialchars($_POST['email']);
    $npwp = htmlspecialchars($_POST['npwp']);

    // Cek jika data sudah ada
    $cek_cs = mysqli_query($connect, "SELECT nama_cs FROM tb_customer WHERE nama_cs = '$nama_cs'");
    $path = "../Customer/".$nama_cs."/NPWP";
    if (!file_exists($path)) {
        // Folder belum ada, buat folder baru
        mkdir($path, 0777, true);
    }

    if ($cek_cs->num_rows > 0) {
        $_SESSION['info'] = 'Data Gagal Disimpan';
        header("Location:../data-customer.php");
    } else {
        try {
            // Mulai transaksi
            $connect->begin_transaction();
            
            // Simpan data customer
            $stmt = $connect->prepare("INSERT INTO tb_customer
                                        (id_cs, jenis_usaha, nama_cs, alamat, nama_cp, no_telp, email, npwp, npwp_img, created_by) 
                                        VALUES 
                                        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Default value for npwp_img 
            $new_file_name = '';
            
            // Jika file diunggah
            if (!empty($_FILES["fileku"]["tmp_name"])) {
                // Mendapatkan informasi file
                $file_tmp = $_FILES["fileku"]["tmp_name"];
                $file_name = $_FILES["fileku"]["name"];
                $new_file_name = 'IMG-' . $nama_cs . '-' . uniqid() . '.jpg';

                // Memeriksa ekstensi file yang diunggah
                $fileInfo = pathinfo($file_name);
                $fileExtension = strtolower($fileInfo['extension']);
                $allowedExtensions = ['png', 'jpeg', 'jpg', 'pdf'];

                if (!in_array($fileExtension, $allowedExtensions)) {
                    throw new Exception('Jenis file tidak didukung. Hanya file dengan ekstensi .png, .jpeg, .jpg, dan .pdf yang diizinkan.');
                }

                // Proses Upload Gambar NPWP
                $file_target = $path . '/' .$new_file_name;
                $encryptedFileContent = encryptFile($file_tmp, $fileKey);
                // Menyimpan file
                if (file_put_contents($file_target, $encryptedFileContent) === false) {
                    throw new Exception('Gagal menyimpan file di server.');
                }
            }

            $stmt->bind_param("ssssssssss", $id_cs, $jenis_usaha, $nama_cs, $alamat, $nama_cp, $telp, $email, $npwp, $new_file_name, $id_user);
            $simpan_cs = $stmt->execute();
        
            if (!$simpan_cs) {
                throw new Exception("Gagal menyimpan data customer: " . $stmt->error);
            }

            // Commit transaksi jika semua berhasil
            $connect->commit();
        
            // Jika semua berhasil
            $_SESSION['info'] = "Disimpan";
            header("Location: ../data-customer.php");
            exit();
        
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $connect->rollback();
            // $error_message = "Gagal saat proses data: " . $e->getMessage();
            // echo $error_message;
            // Tangani kesalahan
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location: ../data-customer.php");
            exit();
        }
    }
//Edit
} else if (isset($_POST["edit-cs"])) {
    $id_cs = htmlspecialchars($_POST['id_cs']);
	$jenis_usaha = htmlspecialchars($_POST['jenis_usaha']);
	$nama_cs = htmlspecialchars($_POST['nama_cs']);
    $nama_cs = ltrim($nama_cs, " \t"); // Menghapus spasi dan tab di awal teks
	$alamat = $connect->real_escape_string($_POST['alamat_cs']);
	$nama_cp = htmlspecialchars($_POST['nama_cp']);
	$telp = htmlspecialchars($_POST['telp_cs']);
	$email = htmlspecialchars($_POST['email']);
	$npwp = htmlspecialchars($_POST['npwp']);
    $ket_img = htmlspecialchars($_POST['ket_img']);
    $img_npwp = htmlspecialchars($_POST['img_npwp']);

	// Tampilkan data untuk pengecekan data sebelum update
    $cek_cs = $connect->query("SELECT id_cs, nama_cs FROM tb_customer WHERE id_cs = '$id_cs'"); 
    $cek_data_cs = mysqli_fetch_array($cek_cs);

    $path = "../Customer/".$nama_cs."/NPWP";
    $unlink_img = $path . "/" . $img_npwp;

    if (!file_exists($path)) {
        // Folder belum ada, buat folder baru
        mkdir($path, 0777, true);
    }

    if ($cek_data_cs['nama_cs'] != $nama_cs) {
        // Nama berubah, cek apakah nama baru sudah ada di database
        $cek_nama = mysqli_query($connect, "SELECT nama_cs FROM tb_customer WHERE nama_cs = '$nama_cs'");

        if ($cek_nama->num_rows > 0) {
            // Nama sudah ada di database
            $_SESSION['info'] = 'Nama customer sudah ada';
            header("Location: ../data-customer.php");
            exit;
        }
    }

    try {
        // Mulai transaksi
        $connect->begin_transaction();
        
        // Mendapatkan informasi file
        $file_tmp = $_FILES["fileku"]["tmp_name"];
        $file_name = $_FILES["fileku"]["name"];
        $new_file_name = 'IMG-' . $nama_cs . '-' . uniqid() . '.jpg';

        // Simpan atau update data customer
        if ($ket_img == "Tidak Diubah" ) {
            // Jika gambar tidak diubah
            $stmt = $connect->prepare("UPDATE tb_customer 
                                        SET jenis_usaha = ?, 
                                        nama_cs = ?, 
                                        alamat = ?, 
                                        nama_cp = ?, 
                                        no_telp = ?, 
                                        email = ?, 
                                        npwp = ?,  
                                        updated_by = ? 
                                        WHERE id_cs = ?");
            $stmt->bind_param("sssssssss", $jenis_usaha, $nama_cs, $alamat, $nama_cp, $telp, $email, $npwp, $id_user, $id_cs);
        } else {
            // Jika gambar diubah
            // Validasi nama file
            if (empty($file_name)) {
                $nama_file = '';
                // Menghapus gambar lama jika ada
                if (file_exists($unlink_img) && !is_dir($unlink_img)) {
                    unlink($unlink_img);
                }                
            } else {
                // Memeriksa ekstensi file yang diunggah
                $nama_file = $new_file_name;
                $fileInfo = pathinfo($file_name);
                $fileExtension = strtolower($fileInfo['extension']);
                $allowedExtensions = ['png', 'jpeg', 'jpg', 'pdf'];
                if (!in_array($fileExtension, $allowedExtensions)) {
                    throw new Exception('Jenis file tidak didukung. Hanya file dengan ekstensi .png, .jpeg, .jpg, dan .pdf yang diizinkan.');
                }

                // Proses Upload Gambar NPWP
                $file_target = $path . '/' . $nama_file;
                $encryptedFileContent = encryptFile($file_tmp, $fileKey);
                // Menyimpan file
                if (file_put_contents($file_target, $encryptedFileContent) === false) {
                    throw new Exception('Gagal menyimpan file di server.');
                }

                // Hapus gambar lama jika ada
                if (file_exists($unlink_img) && !is_dir($unlink_img)) {
                    unlink($unlink_img);
                }
            }
           

           
            $stmt = $connect->prepare("UPDATE tb_customer 
                                        SET jenis_usaha = ?, 
                                        nama_cs = ?, 
                                        alamat = ?, 
                                        nama_cp = ?, 
                                        no_telp = ?, 
                                        email = ?, 
                                        npwp = ?, 
                                        npwp_img = ?, 
                                        updated_by = ? 
                                        WHERE id_cs = ?");
            $stmt->bind_param("ssssssssss", $jenis_usaha, $nama_cs, $alamat, $nama_cp, $telp, $email, $npwp, $nama_file, $id_user, $id_cs);
        }

        $update_cs = $stmt->execute();

        if (!$update_cs) {
            throw new Exception("Gagal menyimpan data customer: " . $stmt->error);
        }

        // Commit transaksi jika semua berhasil
        $connect->commit(); 
        $_SESSION['info'] = "Diupdate";
        header("Location: ../data-customer.php");
        exit();

    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $connect->rollback();
        // $error_message = "Gagal saat proses data: " . $e->getMessage();
        // echo $error_message;
        $_SESSION['info'] = "Data Gagal Diupdate";
        header("Location: ../data-customer.php");
        exit();
    }

	// Hapus
} elseif ($_GET['hapus-cs']) {
	//tangkap URL dengan $_GET
	$idh = $_GET['hapus-cs'];
	$id_cs = base64_decode($idh);

	// perintah queery sql untuk hapus data
	$sql = "DELETE FROM tb_customer WHERE id_cs='$id_cs'";
	$query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));


	if ($query_del) {
		$_SESSION['info'] = 'Dihapus';
		echo "<script>document.location.href='../data-customer.php'</script>";
	} else {
		$_SESSION['info'] = 'Data Gagal Dihapus';
		echo "<script>document.location.href='../data-customer.php'</script>";
	}
}