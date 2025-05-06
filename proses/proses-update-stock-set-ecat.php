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

    if (isset($sanitasi_post['simpan'])) { 
        $id_tr_set = $sanitasi_post['id_tr_set'];
        $id_tr_set_isi = $sanitasi_post['id_tr_set_isi'];
        $id_ket_in = decrypt($sanitasi_post['id_ket_in'], $key_global);
        $id_set = decrypt($sanitasi_post['id_set'], $key_global);
        $id_set_isi = $sanitasi_post['id_set_isi'];
        $qty_set = $sanitasi_post['qty_set'];
        $qty = $sanitasi_post['qty'];
        $id_produk = $sanitasi_post['id_produk'];
        $created = $sanitasi_post['created'];
        $total_data = count($id_set_isi);

        // Memulai transaksi
        mysqli_begin_transaction($connect);

        try {
            $sql_tr_set = "INSERT INTO tr_set_ecat
                            (id_tr_set_ecat, id_set_ecat, id_ket_in, qty, created_date, created_by)
                            VALUES
                            ('$id_tr_set', '$id_set', '$id_ket_in', '$qty_set', '$created', '$id_user')";
            $query1 = mysqli_query($connect, $sql_tr_set);

            if (!$query1) {
                throw new Exception("Terjadi kesalahan saat menyimpan data.");
            }

            for ($i = 0; $i < $total_data; $i++) {
                $new_id_tr_set_isi = $id_tr_set_isi[$i];
                $new_id_set_isi = $id_set_isi[$i];
                $new_id_produk = $id_produk[$i];
                $new_qty = $qty[$i];
                $new_id_tr_set = $id_tr_set;

                $sql_tr_set_isi = "INSERT INTO tr_set_ecat_isi
                        (id_tr_set_ecat_isi, id_tr_set_ecat, id_set_ecat, id_produk_ecat, qty, created_date, created_by)
                        VALUES
                        ('$new_id_tr_set_isi', '$new_id_tr_set', '$new_id_set_isi', '$new_id_produk', '$new_qty', '$created', '$id_user')";
                $query2 = mysqli_query($connect, $sql_tr_set_isi);

                if (!$query2) {
                    throw new Exception("Terjadi kesalahan saat menyimpan data.");
                }
            }

            // Melakukan commit jika tidak ada kesalahan
            mysqli_commit($connect);
            $_SESSION['info'] = 'Disimpan';
            header("Location:../barang-masuk-set-ecat.php?date_range=year");
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            mysqli_rollback($connect);
            $_SESSION['info'] = 'Silahkan Ulangi Kembali';
            header("Location:../barang-masuk-set-ecat.php?date_range=year");
        }
    } else if (isset($sanitasi_get['hapus'])) {
        $id_tr_set_ecat = decrypt($sanitasi_get['hapus'], $key_global);

        // Memulai transaksi
        mysqli_begin_transaction($connect);

        try {
            // Hapus data dari tabel tr_set_ecat_isi
            $sql_delete_tr_set_isi = "DELETE FROM tr_set_ecat_isi WHERE id_tr_set_ecat = '$id_tr_set_ecat'";
            $query_delete_tr_set_isi = mysqli_query($connect, $sql_delete_tr_set_isi);

            if (!$query_delete_tr_set_isi) {
                throw new Exception("Terjadi kesalahan saat menghapus data.");
            }

            // Hapus data dari tabel tr_set_ecat
            $sql_delete_tr_set = "DELETE FROM tr_set_ecat WHERE id_tr_set_ecat = '$id_tr_set_ecat'";
            $query_delete_tr_set = mysqli_query($connect, $sql_delete_tr_set);

            if (!$query_delete_tr_set) {
                throw new Exception("Terjadi kesalahan saat menghapus data.");
            }

            // Melakukan commit jika tidak ada kesalahan
            mysqli_commit($connect);
            $_SESSION['info'] = 'Dihapus';
            header("Location:../barang-masuk-set-ecat.php?date_range=year");
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            mysqli_rollback($connect);
            $_SESSION['info'] = 'Data Gagal Dihapus';
            header("Location:../barang-masuk-set-ecat.php?date_range=year");
        }
    }
