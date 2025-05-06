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

    if (isset($sanitasi_post['simpan-stock-reg'])) {
        $id_stock_reg = $sanitasi_post['id_stock_reg'];
        $id_produk = $sanitasi_post['id_produk'];
        $id_kat_jual = $sanitasi_post['id_kat_jual'];
        $stock = $sanitasi_post['stock'];
        $register_value = '1';

        $stock = intval(preg_replace("/[^0-9]/", "", $stock));

        $cek_data = "SELECT id_produk_reg FROM stock_produk_reguler WHERE id_produk_reg = '$id_produk'";
        $query = mysqli_query($connect, $cek_data);

        if ($query->num_rows > 0) {
            $_SESSION['info'] = 'Data sudah ada';
            header("Location:../stock-produk-reg.php");
        } else {
            $cek_produk = "SELECT id_produk_reg FROM tb_produk_reguler WHERE id_produk_reg = '$id_produk'";
            $query_cek = mysqli_query($connect, $cek_produk);
            if ($query_cek->num_rows > 0) {
                mysqli_query($connect, "INSERT INTO stock_produk_reguler
                            (id_stock_prod_reg, id_produk_reg, id_kat_penjualan, stock, created_by)
                            values
                            ('$id_stock_reg', '$id_produk', '$id_kat_jual', '$stock', '$id_user')");

                mysqli_query($connect, "UPDATE tb_produk_reguler 
                                                        SET
                                                        register_value = '$register_value'
                                                        WHERE id_produk_reg= '$id_produk'");
                $_SESSION['info'] = 'Disimpan';
                header("Location:../stock-produk-reg.php");
            } else {
                mysqli_query($connect, "INSERT INTO stock_produk_reguler
                            (id_stock_prod_reg, id_produk_reg, id_kat_penjualan, stock, created_by)
                            values
                            ('$id_stock_reg', '$id_produk', '$id_kat_jual', '$stock', '$id_user')");

                $update = mysqli_query($connect, "UPDATE tb_produk_set_marwa
                                                    SET
                                                    register_value = '$register_value'
                                                    WHERE id_set_marwa='$id_produk'");
                $_SESSION['info'] = 'Disimpan';
                header("Location:../stock-produk-reg.php");
            }
        }
    } else if (isset($sanitasi_get['hapus-stock-reg'])) {
        $idh = urldecode(decrypt($sanitasi_get['hapus-stock-reg'], $key_global));
        $idu = urldecode(decrypt($sanitasi_get['id_produk'], $key_global));
        $register_val = 0;
        try {
            $connect->begin_transaction();
            // Cek apakah id_produk_reg ada di tb_produk_reguler
            $cek_id_query = "SELECT id_produk_reg FROM tb_produk_reguler WHERE id_produk_reg = '$idu'";
            $cek_id_result = $connect->query($cek_id_query);
        
            if ($cek_id_result->num_rows > 0) {
                // Update tb_produk_reguler jika id_produk_reg ditemukan
                $update_stmt = $connect->prepare("UPDATE tb_produk_reguler SET register_value = ? WHERE id_produk_reg = ?");
                $update_stmt->bind_param("is", $register_val, $idu);
                $update_stmt->execute();
        
                // Hapus data dari stock_produk_reguler
                $hapus_stmt = $connect->prepare("DELETE FROM stock_produk_reguler WHERE id_stock_prod_reg = ?");
                $hapus_stmt->bind_param("s", $idh);
                $hapus_stmt->execute();
        
                if ($hapus_stmt->affected_rows > 0) {
                    $_SESSION['info'] = 'Dihapus';
                } else {
                    throw new Exception('Data Gagal Dihapus');
                }
            } else {
                // Update tb_produk_set_marwa jika id_produk_reg tidak ditemukan
                $update_stmt = $connect->prepare("UPDATE tb_produk_set_marwa SET register_value = ? WHERE id_set_marwa = ?");
                $update_stmt->bind_param("is", $register_val, $idu);
                $update_stmt->execute();
        
                // Hapus data dari stock_produk_reguler
                $hapus_stmt = $connect->prepare("DELETE FROM stock_produk_reguler WHERE id_stock_prod_reg = ?");
                $hapus_stmt->bind_param("s", $idh);
                $hapus_stmt->execute();
        
                if ($hapus_stmt->affected_rows > 0) {
                    $_SESSION['info'] = 'Dihapus';
                } else {
                    throw new Exception('Data Gagal Dihapus');
                }
            }
        
            // Commit transaction jika tidak ada masalah
            $connect->commit();
        } catch (Exception $e) {
            // Rollback transaction jika terjadi kesalahan
            $connect->rollback();
            $_SESSION['info'] = $e->getMessage();
            header("Location:../stock-produk-reg.php");
            exit();
        }
        
        // Redirect setelah proses selesai
        header("Location:../stock-produk-reg.php");
        exit();
    }
?>
