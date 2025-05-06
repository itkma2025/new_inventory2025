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


    if (isset($_POST['simpan'])) {
        $id_orderby = $sanitasi_post['id_orderby'];
        $order_by = $sanitasi_post['order_by'];
        $id_user = $sanitasi_post['id_user'];
        $created = $sanitasi_post['created'];

        $cek_data = mysqli_query($connect, "SELECT order_by FROM tb_orderby WHERE order_by = '$order_by'");

        if ($cek_data->num_rows < 1) {
            $simpan_data = "INSERT INTO tb_orderby
                            (id_orderby, id_user, order_by, created_date) VALUES ('$id_orderby', '$id_user', '$order_by', '$created')";
            $query = mysqli_query($connect, $simpan_data);
            $_SESSION['info'] = 'Disimpan';
            header("Location:../data-orderby.php");
        } else {
            $_SESSION['info'] = 'Data sudah ada';
            header("Location:../data-orderby.php");
        }
    } elseif ($sanitasi_get['hapus']) {
        //tangkap URL dengan $sanitasi_get
        $idh = $sanitasi_get['hapus'];
        $id_orderby = base64_decode($idh);

        // perintah queery sql untuk hapus data
        $sql = "DELETE FROM tb_orderby WHERE id_orderby='$id_orderby'";
        $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));


        if ($query_del) {
            $_SESSION['info'] = 'Dihapus';
            header("Location:../data-orderby.php");
        } else {
            $_SESSION['info'] = 'Data Gagal Dihapus';
            header("Location:../data-orderby.php");
        }
    }
