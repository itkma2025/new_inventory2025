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

    if (isset($sanitasi_post['simpan-reg'])) {
        $id_ganti_merk_out = $sanitasi_post['id_ganti_merk_out'];
        $id_prod_awal = $sanitasi_post['id_produk_awal'];
        $merk_awal = $sanitasi_post['merk_awal'];
        $qty_awal = $sanitasi_post['qty_awal'];
        $id_ganti_merk_in = $sanitasi_post['id_ganti_merk_in'];
        $id_prod_akhir = $sanitasi_post['id_produk_akhir'];
        $merk_akhir = $sanitasi_post['merk_akhir'];
        $qty_akhir = $sanitasi_post['qty_akhir'];
        $nama_petugas = $sanitasi_post['nama_petugas'];
        $created = $sanitasi_post['created'];

        $qty_awal = intval(preg_replace("/[^0-9]/", "", $qty_awal));
        $qty_akhir = intval(preg_replace("/[^0-9]/", "", $qty_akhir));

        if ($merk_awal == $merk_akhir) {
            $_SESSION['info'] = 'Merk Tidak Boleh Sama';
            header("Location:../input-ganti-merk-reg.php");
        } else {
            //mulai transaksi
            mysqli_begin_transaction($connect);
            try {
                //simpan data pada tabel pertama
                $sql1 = "INSERT INTO ganti_merk_reg_out (id_ganti_merk_out, id_produk_reg, qty, created_date, created_by) VALUES ('$id_ganti_merk_out', '$id_prod_awal', '$qty_awal', '$created', '$id_user')";
                mysqli_query($connect, $sql1);

                //simpan data pada tabel kedua
                $sql2 = "INSERT INTO ganti_merk_reg_in (id_ganti_merk_in, id_produk_reg, qty, nama_petugas, created_date, created_by) VALUES ('$id_ganti_merk_in', '$id_prod_akhir', '$qty_akhir', '$nama_petugas',  '$created', '$id_user')";
                mysqli_query($connect, $sql2);

                if ($sql1 && $sql2) {
                    //jika semua query berhasil, commit transaksi
                    mysqli_commit($connect);
                    $_SESSION['info'] = 'Disimpan';
                    header("Location:../input-ganti-merk-reg.php");
                } else {
                    //jika terjadi kesalahan pada salah satu query, rollback transaksi
                    mysqli_rollback($connect);
                    // Handle the error (e.g., display an error message)
                    $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
                    $_SESSION['info'] = 'Data Gagal Disimpan';
                    header("Location:../input-ganti-merk-reg.php");
                }
            } catch (Exception $e) {
                //jika terjadi kesalahan pada salah satu query, rollback transaksi
                mysqli_rollback($connect);
                // Handle the error (e.g., display an error message)
                $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
                $_SESSION['info'] = 'Data Gagal Disimpan';
                header("Location:../input-ganti-merk-reg.php");
            }
        }
    } elseif ($sanitasi_get['hapus_id']) {
        //tangkap URL dengan $sanitasi_get
        $idh = decrypt($sanitasi_get['hapus_id'], $key_global);
        // perintah queery sql untuk hapus data
        $sql = "DELETE a, b
                FROM ganti_merk_reg_out AS a
                JOIN ganti_merk_reg_in AS b ON a.id_ganti_merk_out = b.id_ganti_merk_in
                WHERE a.id_ganti_merk_out = '$idh'";

        $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

        if ($query_del) {
            $_SESSION['info'] = 'Dihapus';
            header("Location:../ganti-merk-reg.php");
        } else {
            $_SESSION['info'] = 'Data Gagal Dihapus';
            header("Location:../ganti-merk-reg.php");
        }
    }


?>