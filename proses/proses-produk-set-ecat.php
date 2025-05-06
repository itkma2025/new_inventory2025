<?php
require_once "../akses.php";
$key = "KM@2024?SET";
$id_user = decrypt($_SESSION['tiket_id'], $key_global);

if (isset($_POST['simpan-set-ecat'])) {
    $id_set_ecat = htmlspecialchars($_POST['id_set_ecat']);
    $kode_set = htmlspecialchars($_POST['kode_barang']);
    $no_batch = htmlspecialchars($_POST['no_batch']);
    $nama_set_ecat = htmlspecialchars($_POST['nama_set_ecat']);
    $id_lokasi = htmlspecialchars($_POST['id_lokasi']);
    $katproduk = htmlspecialchars($_POST['kategori_produk']);
    $katjual = htmlspecialchars($_POST['kategori_penjualan']);
    $grade = htmlspecialchars($_POST['grade']);
    $merk = htmlspecialchars($_POST['merk']);
    $harga = htmlspecialchars($_POST['harga']);
    $deskripsi = $_POST['deskripsi'];

    // Mengubah format menjadi int
    $harga = intval(preg_replace("/[^0-9]/", "", $harga));

    $cek_data = mysqli_query($connect, "SELECT * FROM tb_produk_set_ecat WHERE kode_set_ecat = '$kode_set'");

    if ($cek_data->num_rows > 0) {
        $_SESSION['info'] = 'Data sudah ada';
        header("Location:../data-produk-set-ecat.php");
    } else {
        // Mendapatkan informasi file
        $nama_file = $_FILES["fileku"]["name"];
        $tipe_file = $_FILES["fileku"]["type"];
        $ukuran_file = $_FILES["fileku"]["size"];
        $tmp_file = $_FILES["fileku"]["tmp_name"];

        // Enkripsi nama file
        $ubah_nama = 'IMG';
        $nama_file_baru = $ubah_nama . uniqid() . '.jpg';

        // Simpan file ke direktori tujuan
        $direktori_tujuan = "../gambar/upload-produk-set-ecat/";
        $target_file = $direktori_tujuan . $nama_file_baru;
        move_uploaded_file($tmp_file, $target_file);
        mysqli_query($connect, "INSERT INTO tb_produk_set_ecat
                    (id_set_ecat, kode_set_ecat, no_batch, nama_set_ecat, id_lokasi, id_kat_produk, id_kat_penjualan, id_grade, id_merk, harga_set_ecat, gambar, deskripsi, created_by) 
                    VALUES 
                    ('$id_set_ecat', '$kode_set', '$no_batch', '$nama_set_ecat', '$id_lokasi', '$katproduk', '$katjual', '$id_grade', '$merk', '$harga', '$nama_file_baru', '$deskripsi', '$id_user')");

        $_SESSION['info'] = 'Disimpan';
        header("Location:../data-produk-set-ecat.php");
    }
} elseif (isset($_POST['edit-set-ecat'])) {
    $id_set_ecat = htmlspecialchars($_POST['id_set_ecat']);
    $kode_set = htmlspecialchars($_POST['kode_barang']);
    $no_batch = htmlspecialchars($_POST['no_batch']);
    $nama_set_ecat = htmlspecialchars($_POST['nama_set_ecat']);
    $id_lokasi = htmlspecialchars($_POST['id_lokasi']);
    $katproduk = htmlspecialchars($_POST['kategori_produk']);
    $katjual = htmlspecialchars($_POST['kategori_penjualan']);
    $grade = htmlspecialchars($_POST['grade']);
    $merk = htmlspecialchars($_POST['merk']);
    $harga = htmlspecialchars($_POST['harga']);
    $harga = intval(preg_replace("/[^0-9]/", "", $harga));
    $deskripsi = $_POST['deskripsi'];

    // Mendapatkan informasi file
    $nama_file = $_FILES["fileku"]["name"];
    $tipe_file = $_FILES["fileku"]["type"];
    $ukuran_file = $_FILES["fileku"]["size"];
    $tmp_file = $_FILES["fileku"]["tmp_name"];

    //cek data sebelum update (Jika gambar tidak di ubah)
    if ($nama_file == '') {
        //data di simpan
        $update = mysqli_query($connect, "UPDATE tb_produk_set_ecat
    								  SET 
                                        kode_set_ecat = '$kode_set',
                                        no_batch = '$no_batch',
                                        nama_set_ecat = '$nama_set_ecat',
                                        id_lokasi = '$id_lokasi',
                                        id_kat_produk = '$katproduk',
                                        id_kat_penjualan = '$katjual',
                                        id_grade = '$id_grade',
                                        id_merk = '$merk',
                                        harga_set_ecat = '$harga',
                                        deskripsi = '$deskripsi',
                                        updated_by = '$id_user'
                                      WHERE id_set_ecat = '$id_set_ecat'");
        if ($update) {
            $_SESSION['info'] = 'Diupdate';
            header("Location:../data-produk-set-ecat.php");
        } else {
            $_SESSION['info'] = 'Data Gagal Diupdate';
            header("Location:../data-produk-set-ecat.php");
        }
    } else {
        $sql = "SELECT gambar FROM tb_produk_set_ecat WHERE id_set_ecat = '$id_set_ecat'";
        $result = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($result);
        $gambar = $row['gambar'];
        if ($gambar != '') {
            unlink("../gambar//upload-produk-set-ecat/$gambar");
        }

        // Enkripsi nama file
        $ubah_nama = 'IMG';
        $nama_file_baru = $ubah_nama . uniqid() . '.jpg';

        // Simpan file ke direktori tujuan
        $direktori_tujuan = "../gambar//upload-produk-set-ecat/";
        $target_file = $direktori_tujuan . $nama_file_baru;
        move_uploaded_file($tmp_file, $target_file);

        $update = mysqli_query($connect, "UPDATE tb_produk_set_ecat
                                            SET 
                                                kode_set_ecat = '$kode_set',
                                                no_batch = '$no_batch',
                                                nama_set_ecat = '$nama_set_ecat',
                                                id_lokasi = '$id_lokasi',
                                                id_kat_produk = '$katproduk',
                                                id_kat_penjualan = '$katjual',
                                                id_grade = '$id_grade',
                                                id_merk = '$merk',
                                                harga_set_ecat = '$harga',
                                                gambar = '$nama_file_baru',
                                                deskripsi =  '$deskripsi',
                                                updated_by = '$id_user'
                                            WHERE id_set_ecat = '$id_set_ecat'");

        if ($update) {
            $_SESSION['info'] = 'Diupdate';
            header("Location:../data-produk-set-ecat.php");
        } else {
            $_SESSION['info'] = 'Data Gagal Diupdate';
            header("Location:../data-produk-set-ecat.php");
        }
    }
} elseif (isset($_GET['hapus-set-ecat'])) {
    //tangkap URL dengan $_GET
    $idh = $_GET['hapus-set-ecat'];
    $decrypt_idh = decrypt($idh, $key);

    $cek_gambar = $connect->query("SELECT tpsm.gambar, qr.qr_img 
                                   FROM tb_produk_set_ecat tpsm
                                   LEFT JOIN qr_link_set_reg qr ON (tpsm.id_set_ecat = qr.id_set_qr)
                                   WHERE id_set_ecat = '$decrypt_idh'");
    $data_gambar = mysqli_fetch_array($cek_gambar);
    $gambar =  $data_gambar['gambar'];
    $qr_img = $data_gambar['qr_img'];

    // perintah queery sql untuk hapus data 
    $sql = "DELETE tpsm, ipsm
            FROM tb_produk_set_ecat tpsm
            LEFT JOIN isi_produk_set_ecat ipsm ON (tpsm.id_set_ecat = ipsm.id_set_ecat)
            WHERE tpsm.id_set_ecat = '$decrypt_idh'";

    $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

    if ($query_del) {
        unlink("../gambar/upload-produk-set-ecat/$gambar");
        unlink("../gambar/QRcode-set-ecat/$qr_img");
        $_SESSION['info'] = 'Dihapus';
        header("Location:../data-produk-set-ecat.php");
    } else {
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header("Location:../data-produk-set-ecat.php");
    }

    // Proses CRUD isi set ecat
} elseif (isset($_POST['simpan-isi-set-ecat'])) {
    $id_isi_set_ecat = $_POST['id_isi_set_ecat'];
    $id_set_ecat = $_POST['id_set_ecat'];
    $encrypt_id = encrypt($id_set_ecat, $key);
    $id_produk = $_POST['id_produk'];
    $qty = $_POST['qty'];
    $qty = intval(preg_replace("/[^0-9]/", "", $qty));

    $cek_data = mysqli_query($connect, "SELECT id_set_ecat, id_produk FROM isi_produk_set_ecat WHERE id_set_ecat = '$id_set_ecat'");
    $data = mysqli_fetch_array($cek_data);
    $num_rows = mysqli_num_rows($cek_data);

    if ($num_rows > 0) {
        if ($data['id_set_ecat'] == $id_set_ecat && $data['id_produk'] == $id_produk) {
            $_SESSION['info'] = "Data sudah ada";
            header("Location:../detail-isi-set-ecat.php?detail-id=$encrypt_id");
        } else {
            $simpan_data = "INSERT INTO isi_produk_set_ecat 
                            (id_isi_set_ecat, id_set_ecat, id_produk, qty, created_by) 
                            VALUES 
                            ('$id_isi_set_ecat', '$id_set_ecat', '$id_produk', '$qty', '$id_user')";
            $query = mysqli_query($connect, $simpan_data);
            $_SESSION['info'] = 'Disimpan';
            header("Location:../detail-isi-set-ecat.php?detail-id=$encrypt_id");
        }
    } else if ($num_rows == 0) {
        $simpan_data = "INSERT INTO isi_produk_set_ecat 
                    (id_isi_set_ecat, id_set_ecat, id_produk, qty, created_by) 
                    VALUES 
                    ('$id_isi_set_ecat', '$id_set_ecat', '$id_produk', '$qty' , '$id_user')";
        $query = mysqli_query($connect, $simpan_data);
        $_SESSION['info'] = 'Disimpan';
        header("Location:../detail-isi-set-ecat.php?detail-id=$encrypt_id");
    }
} elseif (isset($_POST['edit-isi-set-ecat'])) {
    $id_isi_set_ecat = $_POST['id_isi_set_ecat'];
    $id_set_ecat = $_POST['id_set_ecat'];
    $encrypt_id = encrypt($id_set_ecat, $key);
    $id_produk = $_POST['id_produk'];
    $qty = $_POST['qty'];

    $update = "UPDATE isi_produk_set_ecat
               SET 
               id_set_ecat = '$id_set_ecat',
               id_produk = '$id_produk',
               qty = '$qty',
               updated_by = '$id_user'
               WHERE id_isi_set_ecat = '$id_isi_set_ecat'";
    $query_update = mysqli_query($connect, $update);
    if ($query_update) {
        $_SESSION['info'] = 'Diupdate';
        header("Location:../detail-isi-set-ecat.php?detail-id=$encrypt_id");
    } else {
        $_SESSION['info'] = 'Data Gagal Diupdate';
        header("Location:../detail-isi-set-ecat.php?detail-id=$encrypt_id");
    }
} elseif (isset($_GET['hapus-isi-set'])) {
    //tangkap URL dengan $_GET
    $idh = $_GET['hapus-isi-set'];
    $decrypt_idh = decrypt($idh, $key);
    $kode = $_GET['kode'];
    // perintah queery sql untuk hapus data
    $sql = "DELETE FROM isi_produk_set_ecat WHERE id_isi_set_ecat='$decrypt_idh'";
    $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

    if ($query_del) {
        $_SESSION['info'] = 'Dihapus';
        header("Location:../detail-isi-set-ecat.php?detail-id=$kode");
    } else {
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header("Location:../detail-isi-set-ecat.php?detail-id=$kode");
    }
}
