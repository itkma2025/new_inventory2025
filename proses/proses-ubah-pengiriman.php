<?php
    require_once "../akses.php";

    // Penghubung Library
    require_once '../assets/vendor/autoload.php';

    // Library Debugging
    use Whoops\Run;
    use Whoops\Handler\PrettyPageHandler;

    // Atur status aktif/non-aktif Whoops
    $whoops_enabled = false; // Ubah menjadi false untuk menonaktifkan

    if ($whoops_enabled) {
        // Inisialisasi Whoops
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();
    }

    // Library sanitasi input data
    require_once "../function/sanitasi_input.php";

    // Function Encrypt dan Decrypt 
    require_once "../function/function-enkripsi.php";

    $sanitasi_get = sanitizeInput($_GET);

    $id_status_kirim = decrypt($sanitasi_get['id_status_kirim'], $key_global);
    $id_inv = decrypt($sanitasi_get['id_inv'], $key_global);
    // Kode untuk membuat kondisi update Nonppn, Ppn, dan Bum
    $id_inv_substr = substr($id_inv, 0, 3);
    if ($id_inv_substr == 'NON') {
        try {
            // Mulai transaksi
            $connect->begin_transaction();

            // Cek data invoice penerima
            $cek_inv_penerima = $connect->query("SELECT id_inv FROM inv_penerima WHERE id_inv = '$id_inv'");
        
            if ($cek_inv_penerima->num_rows > 0) {
                // Perintah khusus jika id_inv ditemukan di inv_penerima
                $delete_inv_penerima = $connect->query("DELETE FROM inv_penerima WHERE id_inv = '$id_inv'");
                if (!$delete_inv_penerima) {
                    throw new Exception('Gagal menghapus data inv_penerima.');
                }
            }
        
            // Perintah UPDATE dan DELETE yang sama untuk kedua kasus
            $update_inv = $connect->query("UPDATE inv_nonppn SET status_transaksi = 'Belum Dikirim', ongkir = '0', free_ongkir = '0', ongkir_free = '0' WHERE id_inv_nonppn = '$id_inv'");
            $delete_status_kirim = $connect->query ("DELETE FROM status_kirim WHERE id_status_kirim = '$id_status_kirim'");
        
            if ($update_inv && $delete_status_kirim) {
                // Commit transaksi jika semua berhasil
                $connect->commit(); 
                $_SESSION['info'] = 'Diupdate';
                header("Location:../invoice-reguler-dikirim.php");
                exit;
            } else {
                // Rollback transaksi jika terjadi kesalahan
                $connect->rollback();
                throw new Exception('Gagal mengupdate data.');
            }
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $connect->rollback();
            $_SESSION['info'] = 'Data Gagal Diupdate';
            header("Location:../invoice-reguler-dikirim.php");
            exit;
        }
    } else if ($id_inv_substr == 'PPN') {
        try {
            // Mulai transaksi
            $connect->begin_transaction();

            // Cek data invoice penerima
            $cek_inv_penerima = $connect->query("SELECT id_inv FROM inv_penerima WHERE id_inv = '$id_inv'");
        
            if ($cek_inv_penerima->num_rows > 0) {
                // Perintah khusus jika id_inv ditemukan di inv_penerima
                $delete_inv_penerima = $connect->query("DELETE FROM inv_penerima WHERE id_inv = '$id_inv'");
                if (!$delete_inv_penerima) {
                    throw new Exception('Gagal menghapus data inv_penerima.');
                }
            }
        
            // Perintah UPDATE dan DELETE yang sama untuk kedua kasus
            $update_inv = $connect->query("UPDATE inv_ppn SET status_transaksi = 'Belum Dikirim', ongkir = '0', free_ongkir = '0', ongkir_free = '0' WHERE id_inv_ppn = '$id_inv'");
            $delete_status_kirim = $connect->query ("DELETE FROM status_kirim WHERE id_status_kirim = '$id_status_kirim'");
        
            if ($update_inv && $delete_status_kirim) {
                // Commit transaksi jika semua berhasil
                $connect->commit(); 
                $_SESSION['info'] = 'Diupdate';
                header("Location:../invoice-reguler-dikirim.php");
                exit;
            } else {
                // Rollback transaksi jika terjadi kesalahan
                $connect->rollback();
                throw new Exception('Gagal mengupdate data.');
            }
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $connect->rollback();
            $_SESSION['info'] = 'Data Gagal Diupdate';
            header("Location:../invoice-reguler-dikirim.php");
            exit;
        }
    } else if ($id_inv_substr == "BUM") {
        try {
            // Mulai transaksi
            $connect->begin_transaction();

            // Cek data invoice penerima
            $cek_inv_penerima = $connect->query("SELECT id_inv FROM inv_penerima WHERE id_inv = '$id_inv'");
        
            if ($cek_inv_penerima->num_rows > 0) {
                // Perintah khusus jika id_inv ditemukan di inv_penerima
                $delete_inv_penerima = $connect->query("DELETE FROM inv_penerima WHERE id_inv = '$id_inv'");
                if (!$delete_inv_penerima) {
                    throw new Exception('Gagal menghapus data inv_penerima.');
                }
            }
        
            // Perintah UPDATE dan DELETE yang sama untuk kedua kasus
            $update_inv = $connect->query("UPDATE inv_bum SET status_transaksi = 'Belum Dikirim', ongkir = '0', free_ongkir = '0', ongkir_free = '0' WHERE id_inv_bum = '$id_inv'");
            $delete_status_kirim = $connect->query ("DELETE FROM status_kirim WHERE id_status_kirim = '$id_status_kirim'");
        
            if ($update_inv && $delete_status_kirim) {
                // Commit transaksi jika semua berhasil
                $connect->commit(); 
                $_SESSION['info'] = 'Diupdate';
                header("Location:../invoice-reguler-dikirim.php");
                exit;
            } else {
                // Rollback transaksi jika terjadi kesalahan
                $connect->rollback();
                throw new Exception('Gagal mengupdate data.');
            }
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $connect->rollback();
            $_SESSION['info'] = 'Data Gagal Diupdate';
            header("Location:../invoice-reguler-dikirim.php");
            exit;
        }
    } else {
        header("Location:404.php");
    }
?>