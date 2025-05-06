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
        $id = $sanitasi_post['id_br'];
        $id_produk = $sanitasi_post['id_produk'];
        $qty = $sanitasi_post['qty'];
        $ket = $sanitasi_post['keterangan'];
        $created_date = $sanitasi_post['created'];
        $qty = intval(preg_replace("/[^0-9]/", "", $qty));
        $cek_data = mysqli_query($connect, "SELECT id_isi_br_out_reg FROM isi_br_out_reg WHERE id_isi_br_out_reg = '$id'");
        if ($cek_data->num_rows > 0) {
            $_SESSION['info'] = 'Data Sudah Ada';
            header("Location:../barang-keluar-reg.php");
        } else {
            $simpan = mysqli_query($connect, "INSERT INTO isi_br_out_reg
                                                (id_isi_br_out_reg, id_produk_reg, qty, id_ket_out, created_date, created_by)
                                                VALUES
                                                ('$id', '$id_produk', '$qty', '$ket', '$created_date', '$id_user')");
            $_SESSION['info'] = 'Disimpan';
            header("Location:../barang-keluar-reg.php");
        }
    } else if (isset($sanitasi_post['edit'])) {
        $id = decrypt($sanitasi_post['id_br'], $key_global);
        $id_produk = $sanitasi_post['id_produk'];
        $qty = $sanitasi_post['qty'];
        $ket = $sanitasi_post['keterangan'];
        $qty = intval(preg_replace("/[^0-9]/", "", $qty));

        $cek_data = mysqli_query($connect, "SELECT id_isi_br_out_reg, id_produk_reg, qty, id_ket_out FROM isi_br_out_reg WHERE id_isi_br_out_reg = '$id'");
        $data = mysqli_fetch_array($cek_data);

        if ($data['id_produk_reg'] == $id_produk && $data['qty'] == $qty && $data['id_ket_out'] == $ket) {
            $_SESSION['info'] = "Tidak Ada Perubahan Data";
            header("Location:../barang-keluar-reg.php");
        } else {
            $update = mysqli_query($connect, "UPDATE isi_br_out_reg
                                            SET 
                                            id_produk_reg = '$id_produk',
                                            qty = '$qty',
                                            id_ket_out = '$ket',
                                            updated_by = '$id_user'
                                            WHERE id_isi_br_out_reg  = '$id'");
            $_SESSION['info'] = "Diupdate";
            header("Location:../barang-keluar-reg.php");
        }
    } else if (isset($sanitasi_get['hapus'])) { 
        $idh = decrypt($sanitasi_get['hapus'], $key_global);

        $hapus_data = mysqli_query($connect, "DELETE FROM isi_br_out_reg WHERE id_isi_br_out_reg = '$idh'");

        if ($hapus_data) {
            $_SESSION['info'] = 'Dihapus';
            header("Location:../barang-keluar-reg.php");
        }
    }
?>
