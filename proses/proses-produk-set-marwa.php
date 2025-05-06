<?php
require_once "../akses.php";
$key = "KM@2024?SET";
$id_user = decrypt($_SESSION['tiket_id'], $key_global);

if (isset($_POST['simpan-set-marwa'])) {
    $id_set_marwa = htmlspecialchars($_POST['id_set_marwa']);
    $kode_set = htmlspecialchars($_POST['kode_barang']);
    $no_batch = htmlspecialchars($_POST['no_batch']);
    $nama_set_marwa = htmlspecialchars($_POST['nama_set_marwa']);
    $id_lokasi = htmlspecialchars($_POST['id_lokasi']);
    $katproduk = htmlspecialchars($_POST['kategori_produk']);
    $katjual = htmlspecialchars($_POST['kategori_penjualan']);
    $grade = htmlspecialchars($_POST['grade']);
    $merk = htmlspecialchars($_POST['merk']);
    $harga = htmlspecialchars($_POST['harga']);
    $stock = htmlspecialchars($_POST['stock']);
    $deskripsi = $_POST['deskripsi'];

    // Mengubah format menjadi int
    $harga = intval(preg_replace("/[^0-9]/", "", $harga));

    $cek_data = mysqli_query($connect, "SELECT * FROM tb_produk_set_marwa WHERE kode_set_marwa = '$kode_set'");

    if ($cek_data->num_rows > 0) {
        $_SESSION['info'] = 'Data sudah ada';
        header("Location:../data-produk-set-marwa.php");
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
        $direktori_tujuan = "../gambar/upload-produk-set-marwa/";
        $target_file = $direktori_tujuan . $nama_file_baru;
        move_uploaded_file($tmp_file, $target_file);
        mysqli_query($connect, "INSERT INTO tb_produk_set_marwa
                    (id_set_marwa, kode_set_marwa, no_batch, nama_set_marwa, id_lokasi, id_kat_produk, id_kat_penjualan, id_grade, id_merk, harga_set_marwa, gambar, deskripsi, created_by) 
                    VALUES 
                    ('$id_set_marwa', '$kode_set', '$no_batch', '$nama_set_marwa', '$id_lokasi', '$katproduk', '$katjual', '$grade', '$merk', '$harga', '$nama_file_baru', '$deskripsi', '$id_user')");

        $_SESSION['info'] = 'Disimpan';
        header("Location:../data-produk-set-marwa.php");
    }
} elseif (isset($_POST['edit-set-marwa'])) {
    $id_set_marwa = htmlspecialchars($_POST['id_set_marwa']);
    $kode_set = htmlspecialchars($_POST['kode_barang']);
    $no_batch = htmlspecialchars($_POST['no_batch']);
    $nama_set_marwa = htmlspecialchars($_POST['nama_set_marwa']);
    $id_lokasi = htmlspecialchars($_POST['id_lokasi']);
    $katproduk = htmlspecialchars($_POST['kategori_produk']);
    $katjual = htmlspecialchars($_POST['kategori_penjualan']);
    $grade = htmlspecialchars($_POST['grade']);
    $merk = htmlspecialchars($_POST['merk']);
    $harga = htmlspecialchars($_POST['harga']);
    $deskripsi = $_POST['deskripsi'];
    $id_user = htmlspecialchars($_POST['id_user']);
    $harga = intval(preg_replace("/[^0-9]/", "", $harga));

    // Mendapatkan informasi file
    $nama_file = $_FILES["fileku"]["name"];
    $tipe_file = $_FILES["fileku"]["type"];
    $ukuran_file = $_FILES["fileku"]["size"];
    $tmp_file = $_FILES["fileku"]["tmp_name"];

    //cek data sebelum update (Jika gambar tidak di ubah)
    if ($nama_file == '') {
        //data di simpan
        $update = mysqli_query($connect, "UPDATE tb_produk_set_marwa
    								  SET 
                                        kode_set_marwa = '$kode_set',
                                        no_batch = '$no_batch',
                                        nama_set_marwa = '$nama_set_marwa',
                                        id_lokasi = '$id_lokasi',
                                        id_kat_produk = '$katproduk',
                                        id_kat_penjualan = '$katjual',
                                        id_grade = '$grade',
                                        id_merk = '$merk',
                                        harga_set_marwa = '$harga',
                                        deskripsi =  '$deskripsi',
                                        updated_by = '$id_user'
                                      WHERE id_set_marwa = '$id_set_marwa'");
        if ($update) {
            $_SESSION['info'] = 'Diupdate';
            header("Location:../data-produk-set-marwa.php");
        } else {
            $_SESSION['info'] = 'Data Gagal Diupdate';
            header("Location:../data-produk-set-marwa.php");
        }
    } else {
        $sql = "SELECT gambar FROM tb_produk_set_marwa WHERE id_set_marwa = '$id_set_marwa'";
        $result = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($result);
        $gambar = $row['gambar'];
        if ($gambar != '') {
            unlink("../gambar//upload-produk-set-marwa/$gambar");
        }

        // Enkripsi nama file
        $ubah_nama = 'IMG';
        $nama_file_baru = $ubah_nama . uniqid() . '.jpg';

        // Simpan file ke direktori tujuan
        $direktori_tujuan = "../gambar//upload-produk-set-marwa/";
        $target_file = $direktori_tujuan . $nama_file_baru;
        move_uploaded_file($tmp_file, $target_file);

        $update = mysqli_query($connect, "UPDATE tb_produk_set_marwa
                                            SET 
                                                kode_set_marwa = '$kode_set',
                                                no_batch = '$no_batch',
                                                nama_set_marwa = '$nama_set_marwa',
                                                id_lokasi = '$id_lokasi',
                                                id_kat_produk = '$katproduk',
                                                id_kat_penjualan = '$katjual',
                                                id_grade = '$grade';
                                                id_merk = '$merk',
                                                harga_set_marwa = '$harga',
                                                gambar = '$nama_file_baru',
                                                deskripsi =  '$deskripsi',
                                                updated_by = '$id_user'
                                            WHERE id_set_marwa = '$id_set_marwa'");

        if ($update) {
            $_SESSION['info'] = 'Diupdate';
            header("Location:../data-produk-set-marwa.php");
        } else {
            $_SESSION['info'] = 'Data Gagal Diupdate';
            header("Location:../data-produk-set-marwa.php");
        }
    }
} elseif (isset($_GET['hapus-set-marwa'])) {
    //tangkap URL dengan $_GET
    $idh = $_GET['hapus-set-marwa'];
    $decrypt_idh = decrypt($idh, $key);

    $cek_gambar = $connect->query("SELECT tpsm.gambar, qr.qr_img 
                                   FROM tb_produk_set_marwa tpsm
                                   LEFT JOIN qr_link_set_reg qr ON (tpsm.id_set_marwa = qr.id_set_qr)
                                   WHERE id_set_marwa = '$decrypt_idh'");
    $data_gambar = mysqli_fetch_array($cek_gambar);
    $gambar =  $data_gambar['gambar'];
    $qr_img = $data_gambar['qr_img'];

    // perintah queery sql untuk hapus data 
    $sql = "DELETE tpsm, ipsm
            FROM tb_produk_set_marwa tpsm
            LEFT JOIN isi_produk_set_marwa ipsm ON (tpsm.id_set_marwa = ipsm.id_set_marwa)
            WHERE tpsm.id_set_marwa = '$decrypt_idh'";

    $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

    if ($query_del) {
        unlink("../gambar/upload-produk-set-marwa/$gambar");
        unlink("../gambar/QRcode-set-marwa/$qr_img");
        $_SESSION['info'] = 'Dihapus';
        header("Location:../data-produk-set-marwa.php");
    } else {
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header("Location:../data-produk-set-marwa.php");
    }

    // Proses CRUD isi set marwa
} elseif (isset($_POST['simpan-isi-set-marwa'])) {
    $id_isi_set_marwa = $_POST['id_isi_set_marwa'];
    $id_set_marwa = $_POST['id_set_marwa'];
    $encrypt_id = encrypt($id_set_marwa, $key);
    $id_produk = $_POST['id_produk'];
    $qty = $_POST['qty'];
    $qty = intval(preg_replace("/[^0-9]/", "", $qty));
    $created_by = $_SESSION['tiket_nama'];

    $cek_data = mysqli_query($connect, "SELECT id_set_marwa, id_produk FROM isi_produk_set_marwa WHERE id_set_marwa = '$id_set_marwa'");
    $data = mysqli_fetch_array($cek_data);
    $num_rows = mysqli_num_rows($cek_data);

    if ($num_rows > 0) {
        if ($data['id_set_marwa'] == $id_set_marwa && $data['id_produk'] == $id_produk) {
            $_SESSION['info'] = "Data sudah ada";
            header("Location:../detail-isi-set-marwa.php?detail-id=$encrypt_id");
        } else {
            $simpan_data = "INSERT INTO isi_produk_set_marwa 
                            (id_isi_set_marwa, id_set_marwa, id_produk, qty, created_by) 
                            VALUES 
                            ('$id_isi_set_marwa', '$id_set_marwa', '$id_produk', '$qty', '$created_by')";
            $query = mysqli_query($connect, $simpan_data);
            $_SESSION['info'] = 'Disimpan';
            header("Location:../detail-isi-set-marwa.php?detail-id=$encrypt_id");
        }
    } else if ($num_rows == 0) {
        $simpan_data = "INSERT INTO isi_produk_set_marwa 
                    (id_isi_set_marwa, id_set_marwa, id_produk, qty, created_by) 
                    VALUES 
                    ('$id_isi_set_marwa', '$id_set_marwa', '$id_produk', '$qty' , '$created_by')";
        $query = mysqli_query($connect, $simpan_data);
        $_SESSION['info'] = 'Disimpan';
        header("Location:../detail-isi-set-marwa.php?detail-id=$encrypt_id");
    }
} elseif (isset($_POST['edit-isi-set-marwa'])) {
    $id_isi_set_marwa = $_POST['id_isi_set_marwa'];
    $id_set_marwa = $_POST['id_set_marwa'];
    $encrypt_id = encrypt($id_set_marwa, $key);
    $id_produk = $_POST['id_produk'];
    $qty = $_POST['qty'];
 
    $update = "UPDATE isi_produk_set_marwa
               SET 
               id_set_marwa = '$id_set_marwa',
               id_produk = '$id_produk',
               qty = '$qty',
               updated_by = '$id_user'
               WHERE id_isi_set_marwa = '$id_isi_set_marwa'";
    $query_update = mysqli_query($connect, $update);
    if ($query_update) {
        $_SESSION['info'] = 'Diupdate';
        header("Location:../detail-isi-set-marwa.php?detail-id=$encrypt_id");
    } else {
        $_SESSION['info'] = 'Data Gagal Diupdate';
        header("Location:../detail-isi-set-marwa.php?detail-id=$encrypt_id");
    }
} elseif (isset($_GET['hapus-isi-set'])) {
    //tangkap URL dengan $_GET
    $idh = $_GET['hapus-isi-set'];
    $decrypt_idh = decrypt($idh, $key);
    $kode = $_GET['kode'];
    // perintah queery sql untuk hapus data
    $sql = "DELETE FROM isi_produk_set_marwa WHERE id_isi_set_marwa='$decrypt_idh'";
    $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

    if ($query_del) {
        $_SESSION['info'] = 'Dihapus';
        header("Location:../detail-isi-set-marwa.php?detail-id=$kode");
    } else {
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header("Location:../detail-isi-set-marwa.php?detail-id=$kode");
    }
}
