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
        $id_spk = $sanitasi_post['id_spk_reg'];
        $no_spk = $sanitasi_post['no_spk'];
        $tgl_spk = $sanitasi_post['tgl_spk'];
        $no_po = $sanitasi_post['no_po'];
        $tgl_pesan = $sanitasi_post['tgl_pesan'];
        $order_by = $sanitasi_post['order_by'];
        $sales = $sanitasi_post['sales'];
        $id_cs = $sanitasi_post['id_cs'];
        $note = $sanitasi_post['note'];
        $id_status = 'Belum Diproses';
        $id_spk_encrypt = encrypt($id_spk, $key_spk);

        $cek_data = mysqli_query($connect, "SELECT no_spk FROM spk_reg WHERE no_spk = '$no_spk'");

        if ($cek_data->num_rows > 0) {
            $_SESSION['info'] = 'No SPK sudah ada';
            header("Location:../form-create-spk-reg.php");
        } else {
            // Siapkan prepared statement
            $stmt = $connect->prepare("INSERT INTO spk_reg 
            (id_spk_reg, no_spk, tgl_spk, no_po, tgl_pesanan, id_orderby, id_sales, id_customer, note, id_user, status_spk, created_date) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Bind parameter dengan tipe data yang sesuai
            $stmt->bind_param('ssssssssssss', $id_spk, $no_spk, $tgl_spk, $no_po, $tgl_pesan, $order_by, $sales, $id_cs, $note, $id_user, $id_status, $tgl_spk);

            // Eksekusi query
            $simpan = $stmt->execute();

            // Cek apakah query berhasil
            if ($simpan) {
                $_SESSION['info'] = 'No SPK berhasil dibuat';
                header("Location:../detail-produk-spk-reg.php?id=$id_spk_encrypt");
                exit();
            } else {
                $_SESSION['info'] = 'Data Gagal Disimpan';
                header("Location:../detail-produk-spk-reg.php?id=$id_spk_encrypt");
                exit();
            }
            // Tutup statement
            exit();
        }

        // Edit 
    } else if (isset($sanitasi_post['edit'])) {
        $id_spk = $sanitasi_post['id_spk_reg'];
        $no_po = $sanitasi_post['no_po'];
        $tgl_pesan = $sanitasi_post['tgl_pesan'];
        $order_by = $sanitasi_post['order_by'];
        $sales = $sanitasi_post['sales'];
        $note = $sanitasi_post['note'];
        $id_status = 'Belum Diproses';
        $id_spk_encrypt = encrypt($id_spk, $key_spk);
        $tgl_updated = $sanitasi_post['updated'];

        $update = "UPDATE spk_reg 
                SET
                no_po = '$no_po',
                tgl_pesanan = '$tgl_pesan',
                id_orderby = '$order_by',
                id_sales = '$sales',
                note = '$note',
                user_updated = '$id_user',
                updated_date = '$tgl_updated'
                WHERE id_spk_reg = '$id_spk'";
        $query = mysqli_query($connect, $update);
        header("Location:../detail-produk-spk-reg-dalam-proses.php?id=$id_spk_encrypt");
    }
