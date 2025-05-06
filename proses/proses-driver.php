<?php
include('../akses.php');
if (isset($_POST['simpan-driver'])) {
    $id_driver = $_POST['id_driver'];
    $id_user = $_POST['id_user'];
    $nama = $_POST['nama_driver'];
    $no_telp = $_POST['no_telp'];
    $created = $_POST['created'];

    $cek_data = mysqli_query($connect, "SELECT nama_pengirim FROM tb_driver WHERE nama_pengirim = '$nama'");

    if ($cek_data->num_rows < 1) {
        $simpan_data = "INSERT INTO tb_driver (id_driver, id_user, nama_pengirim, no_telp, created_date) VALUES ('$id_driver', '$id_user', '$nama', '$no_telp', '$created')";
        $query_simpan = mysqli_query($connect, $simpan_data);
        $_SESSION['info'] = 'Disimpan';
        header("Location: ../driver.php");
    } else {
        $_SESSION['info'] = 'Data sudah ada';
        header("Location: ../driver.php");
    }

    //Edit
} elseif (isset($_POST["edit-driver"])) {
    $id_driver = $_POST['id_driver'];
    $nama_driver = $_POST['nama_driver'];
    $telp = $_POST['no_telp'];
    $updated = date('d/m/Y H:i:s');

    // menampilkan data
    $query = "SELECT * FROM tb_driver WHERE id_driver = '$id_driver'";
    $result = mysqli_query($connect, $query);
    $data_lama = mysqli_fetch_assoc($result);

    if ($data_lama['nama_pengirim'] == $nama_driver) {
        // Nama tidak berubah, simpan data langsung
        $update = mysqli_query($connect, "UPDATE tb_driver 
                    SET
    				nama_pengirim = '$nama_driver',
    				no_telp = '$telp',
    				updated_date = '$updated'
                    WHERE id_driver='$id_driver'");
        $_SESSION['info'] = 'Disimpan';
        echo "<script>document.location.href='../driver.php'</script>";
    } else {
        // Nama berubah, cek apakah ada nama yang sama di database
        $cek_driver = mysqli_query($connect, "SELECT nama_pengirim FROM tb_driver WHERE nama_pengirim = '$nama_driver'");

        if ($cek_driver->num_rows > 0) {
            // Ada nama yang sama di database, tampilkan pesan error
            $_SESSION['info'] = 'Data sudah ada';
            echo "<script>document.location.href='../driver.php'</script>";
        } else {
            // Nama belum digunakan, simpan data
            $update = mysqli_query($connect, "UPDATE tb_driver 
    					SET
    					nama_pengirim = '$nama_driver',
    					no_telp = '$telp',
    					updated_date = '$updated'
    					WHERE id_driver='$id_driver'");

            $_SESSION['info'] = 'Diupdate';
            echo "<script>document.location.href='../driver.php'</script>";
        }
    }

    // Hapus
} elseif ($_GET['hapus-driver']) {
    //tangkap URL dengan $_GET
    $idh = $_GET['hapus-driver'];
    $id_driver = base64_decode($idh);

    // perintah queery sql untuk hapus data
    $sql = "DELETE FROM tb_driver WHERE id_driver='$id_driver'";
    $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));


    if ($query_del) {
        $_SESSION['info'] = 'Dihapus';
        echo "<script>document.location.href='../driver.php'</script>";
    } else {
        $_SESSION['info'] = 'Data Gagal Dihapus';
        echo "<script>document.location.href='../driver.php'</script>";
    }
}
