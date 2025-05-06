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


    if (isset($_POST['simpan-inv-import'])) {
        $id_inv_br_import = $sanitasi_post['id_inv_br_import'];
        $no_inv = $sanitasi_post['no_inv'];
        $tgl_inv = $sanitasi_post['tgl_inv'];
        $id_sp = $sanitasi_post['id_sp'];
        $no_order = $sanitasi_post['no_order'];
        $tgl_order = $sanitasi_post['tgl_order'];
        $no_awb = $sanitasi_post['no_awb'];
        $tgl_kirim = $sanitasi_post['tgl_kirim'];
        $ship = $sanitasi_post['ship'];
        $tgl_est = $sanitasi_post['tgl_est'];
        $laut = 'Laut';
        $udara = 'Udara';
        $sampai = 'Sampai Express';
        
        $shipping_by = "";
        if($ship == 10){
            $shipping_by = "Udara";
        } else if ($ship == 30){
            $shipping_by = "Laut";
        } else if ($ship == 45){
            $shipping_by = "Sampai Express";
        } else {
            header("Location:../404.php");
        }

        $simpan_data = "INSERT INTO inv_br_import
                        (id_inv_br_import, id_supplier, no_inv, tgl_inv, no_order, tgl_order, shipping_by, no_awb, tgl_kirim, tgl_est, created_by)
                        VALUES
                        ('$id_inv_br_import', '$id_sp', '$no_inv', '$tgl_inv', '$no_order', '$tgl_order', '$shipping_by', '$no_awb', '$tgl_kirim', '$tgl_est', '$id_user')";
        $query = mysqli_query($connect, $simpan_data);
        if($query){
            $_SESSION['info'] = 'Disimpan';
            header("Location:../barang-masuk-reg-import.php");
        }
    } else if (isset($sanitasi_post['simpan-isi-br-import'])) {
        $id_isi_inv_br_import = $sanitasi_post['id_isi_inv_br_import']; 
        $id_inv_import = decrypt($sanitasi_post['id_inv_import'], $key_global);
        $encode = encrypt($id_inv_import, $key_global);
        $id_produk = $sanitasi_post['id_produk'];
        $harga = $sanitasi_post['harga'];
        $qty = $sanitasi_post['qty'];
        $qty = intval(preg_replace("/[^0-9]/", "", $qty));

        $cek_data = mysqli_query($connect, "SELECT id_produk_reg, id_inv_br_import FROM isi_inv_br_import WHERE id_inv_br_import = '$id_inv_import' AND id_produk_reg = '$id_produk'");
        if ($cek_data->num_rows > 0) {
            $_SESSION['info'] = 'Data sudah ada';
            header("Location:../tampil-br-import.php?id=$encode");
            exit;
        } else {
            $sql = "INSERT INTO isi_inv_br_import
                    (id_isi_inv_br_import, id_inv_br_import, id_produk_reg, harga_beli, qty, created_by)
                    VALUES
                    ('$id_isi_inv_br_import', '$id_inv_import', '$id_produk', '$harga', '$qty', '$id_user')";
            $query = mysqli_query($connect, $sql);

            if ($query) {
                $_SESSION['info'] = 'Disimpan';
                header("Location:../input-isi-inv-br-import.php?id=$encode");
            } else {
                $_SESSION['info'] = 'Data Gagal Disimpan';
                header("Location:../input-isi-inv-br-import.php?id=$encode");
            }
        }
    } else if (isset($sanitasi_post['simpan-act-br-import'])) {
        $id_act_br_import = $sanitasi_post['id_act_br_import'];
        $id_isi_inv_br_import = decrypt($sanitasi_post['id_isi_inv_br_import'], $key_global);
        $id_inv_import = decrypt($sanitasi_post['id_inv_import'], $key_global);
        $id_produk = $sanitasi_post['id_produk'];
        $harga = $sanitasi_post['harga'];
        $qty_act = $sanitasi_post['qty_act'];
        $qty_act = intval(preg_replace("/[^0-9]/", "", $qty_act));
        $encode = encrypt($id_inv_import, $key_global);

        $simpan_data = "INSERT INTO act_br_import
                            (id_act_br_import, id_isi_inv_br_import, id_produk_reg, harga, qty_act, created_by)
                            VALUES
                            ('$id_act_br_import', '$id_isi_inv_br_import', '$id_produk', '$harga', '$qty_act', '$id_user')";
        $query = mysqli_query($connect, $simpan_data);


        if ($query) {
            $_SESSION['info'] = 'Disimpan';
            header("Location:../tampil-br-import.php?id=$encode");
        } else {
            $_SESSION['info'] = 'Data Gagal Disimpan';
            header("Location:../tampil-br-import.php?id=$encode");
        }
    } else if (isset($sanitasi_post['edit-inv-br-in-import'])) {
        $id_inv_br_import = decrypt($sanitasi_post['id_inv_br_import'], $key_global);
        $no_inv = $sanitasi_post['no_inv'];
        $tgl_inv = $sanitasi_post['tgl_inv'];
        $id_sp = $sanitasi_post['id_sp'];
        $no_order = $sanitasi_post['no_order'];
        $tgl_order = $sanitasi_post['tgl_order'];
        $no_awb = $sanitasi_post['no_awb'];
        $tgl_kirim = $sanitasi_post['tgl_kirim'];
        $ship = $sanitasi_post['ship'];
        $tgl_est = $sanitasi_post['tgl_est'];
        $laut = 'Laut';
        $udara = 'Udara';
        $sampai = 'Sampai Express';


        $cek_data = "SELECT * FROM inv_br_import WHERE id_inv_br_import = '$id_inv_br_import'";
        $query_cek = mysqli_query($connect, $cek_data);
        $data_sebelumnya = mysqli_fetch_assoc($query_cek);

        if ($data_sebelumnya['id_supplier'] == $id_sp && $data_sebelumnya['no_inv'] == $no_inv && $data_sebelumnya['tgl_inv'] == $tgl_inv && $data_sebelumnya['tgl_inv'] == $tgl_inv && $data_sebelumnya['no_order'] == $no_order && $data_sebelumnya['tgl_order'] == $tgl_order && $data_sebelumnya['shipping_by'] == $ship && $data_sebelumnya['no_awb'] == $no_awb && $data_sebelumnya['tgl_kirim'] == $tgl_kirim && $data_sebelumnya['tgl_est'] == $tgl_est) {
            $_SESSION['info'] = 'Tidak Ada Perubahan Data';
            header("Location:../barang-masuk-reg-import.php");
            exit;
        } else if ($ship == 10 OR $ship == $udara) {
            $edit_data = "UPDATE inv_br_import
                                SET 
                                id_supplier = '$id_sp',
                                no_inv = '$no_inv',
                                tgl_inv = '$tgl_inv',
                                no_order = '$no_order',
                                tgl_order = '$tgl_order',
                                shipping_by = '$udara',
                                no_awb = '$no_awb',
                                tgl_kirim = '$tgl_kirim',
                                tgl_est = '$tgl_est'
                                WHERE  id_inv_br_import = '$id_inv_br_import'";
            $query = mysqli_query($connect, $edit_data);

            if ($query) {
                $_SESSION['info'] = 'Diupdate';
                header("Location:../barang-masuk-reg-import.php");
            } else {
                $_SESSION['info'] = 'Data Gagal Diupdate';
                header("Location:../barang-masuk-reg-import.php");
            }
        } else if ($ship == 30 OR $ship == $laut) {
            $edit_data = "UPDATE inv_br_import
                                SET 
                                id_supplier = '$id_sp',
                                no_inv = '$no_inv',
                                tgl_inv = '$tgl_inv',
                                no_order = '$no_order',
                                tgl_order = '$tgl_order',
                                shipping_by = '$laut',
                                no_awb = '$no_awb',
                                tgl_kirim = '$tgl_kirim',
                                tgl_est = '$tgl_est'
                                WHERE  id_inv_br_import = '$id_inv_br_import'";
            $query = mysqli_query($connect, $edit_data);
            if ($query) {
                $_SESSION['info'] = 'Diupdate';
                header("Location:../barang-masuk-reg-import.php");
            } else {
                $_SESSION['info'] = 'Data Gagal Diupdate';
                header("Location:../barang-masuk-reg-import.php");
            }
        } else if ($ship == 45 OR $ship == $sampai) {
            $edit_data = "UPDATE inv_br_import
                                SET 
                                id_supplier = '$id_sp',
                                no_inv = '$no_inv',
                                tgl_inv = '$tgl_inv',
                                no_order = '$no_order',
                                tgl_order = '$tgl_order',
                                shipping_by = '$laut',
                                no_awb = '$no_awb',
                                tgl_kirim = '$tgl_kirim',
                                tgl_est = '$tgl_est'
                                WHERE  id_inv_br_import = '$id_inv_br_import'";
            $query = mysqli_query($connect, $edit_data);
            if ($query) {
                $_SESSION['info'] = 'Diupdate';
                header("Location:../barang-masuk-reg-import.php");
            } else {
                $_SESSION['info'] = 'Data Gagal Diupdate';
                header("Location:../barang-masuk-reg-import.php");
            }
        }
    } else if (isset($sanitasi_post['edit-isi-br-import'])) {
        $id_isi_inv_br_import = decrypt($sanitasi_post['id_isi_inv_br_import'], $key_global);
        $id_inv_import = decrypt($sanitasi_post['id_inv_import'], $key_global);
        $id_produk = $sanitasi_post['id_produk'];
        $qty = $sanitasi_post['qty'];
        $harga = $sanitasi_post['harga'];
        $encode = encrypt($id_inv_import, $key_global);

        $qty = intval(preg_replace("/[^0-9]/", "", $qty));

        // cek apakah data sebelumnya sama dengan data yang akan diubah
        $cek_data = "SELECT id_produk_reg, harga_beli, qty FROM isi_inv_br_import WHERE id_isi_inv_br_import = '$id_isi_inv_br_import'";
        $query_cek = mysqli_query($connect, $cek_data);
        $data_sebelumnya = mysqli_fetch_assoc($query_cek);

        if ($data_sebelumnya['id_produk_reg'] == $id_produk && $data_sebelumnya['qty'] == $qty && $data_sebelumnya['harga_beli'] == $harga) {
            $_SESSION['info'] = 'Tidak Ada Perubahan Data';
            header("Location:../tampil-br-import.php?id=$encode");
            exit;
        } else {
            $edit_data = "UPDATE isi_inv_br_import
                                SET 
                                id_produk_reg = '$id_produk',
                                qty = '$qty',
                                harga_beli = '$harga'
                                WHERE id_isi_inv_br_import = '$id_isi_inv_br_import'";

            $query = mysqli_query($connect, $edit_data);

            if ($query) {
                $_SESSION['info'] = 'Diupdate';
                header("Location:../tampil-br-import.php?id=$encode");
            } else {
                $_SESSION['info'] = 'Data Gagal Diupdate';
                header("Location:../tampil-br-import.php?id=$encode");
            }
        }
    } else if (isset($sanitasi_post['edit-act-br-import'])) {
        $id_act_br_import = decrypt($sanitasi_post['id_act_br_import'], $key_global);
        $id_isi_inv_br_import = decrypt($sanitasi_post['id_isi_inv_br_import'], $key_global);
        $id_inv_br_import = decrypt($sanitasi_post['id_inv_br_import'], $key_global);
        $id_produk = $sanitasi_post['id_produk'];
        $qty = $sanitasi_post['qty_act'];
        $id_isi = encrypt($id_isi_inv_br_import, $key_global);
        $id_inv = encrypt($id_inv_br_import, $key_global);

        $qty = intval(preg_replace("/[^0-9]/", "", $qty));

        // cek apakah data sebelumnya sama dengan data yang akan diubah
        $cek_data = "SELECT id_produk_reg, qty_act FROM act_br_import WHERE id_act_br_import = '$id_act_br_import'";
        $query_cek = mysqli_query($connect, $cek_data);
        $data_sebelumnya = mysqli_fetch_assoc($query_cek);

        if ($data_sebelumnya['id_produk_reg'] == $id_produk && $data_sebelumnya['qty_act'] == $qty) {
            $_SESSION['info'] = 'Tidak Ada Perubahan Data';
            header("Location:../list-act-br-import.php?id=$id_isi && id_inv=$id_inv");
            exit;
        } else {
            $edit_data = "UPDATE act_br_import
                                SET 
                                id_produk_reg = '$id_produk',
                                qty_act = '$qty',
                                updated_by = '$id_user'
                                WHERE id_act_br_import = '$id_act_br_import'";

            $query = mysqli_query($connect, $edit_data);

            if ($query) {
                $_SESSION['info'] = 'Diupdate';
                header("Location:../list-act-br-import.php?id=$id_isi && id_inv=$id_inv");
            } else {
                $_SESSION['info'] = 'Data Gagal Diupdate';
                header("Location:../list-act-br-import.php?id=$id_isi && id_inv=$id_inv");
            }
        }
    } else if (isset($sanitasi_get['hapus'])) {
        //tangkap URL dengan $sanitasi_get
        $idh = decrypt($sanitasi_get['hapus'], $key_global);
        $id_inv = decrypt($sanitasi_get['id_inv'], $key_global);
        $encode = encrypt($id_inv, $key_global);

        //perintah queery sql untuk hapus data
        $sql = "DELETE iibi, act 
                    FROM isi_inv_br_import iibi
                    LEFT JOIN act_br_import act ON iibi.id_isi_inv_br_import = act.id_isi_inv_br_import
                    WHERE iibi.id_isi_inv_br_import = '$idh'";
        $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

        if ($query_del) {
            $_SESSION['info'] = 'Dihapus';
            header("Location:../tampil-br-import.php?id=$encode");
        } else {
            $_SESSION['info'] = 'Data Gagal Dihapus';
            header("Location:../tampil-br-import.php?id=$encode");
        }
    } else if (isset($sanitasi_get['hapus-act'])) {
        $id_act = decrypt($sanitasi_get['hapus-act'], $key_global);
        $id = decrypt($sanitasi_get['id'], $key_global);
        $id_inv = decrypt($sanitasi_get['id_inv'], $key_global);
        $encode_id = encrypt($id, $key_global);
        $encode_inv = encrypt($id_inv, $key_global);

        $sql = "DELETE FROM act_br_import WHERE id_act_br_import='$id_act'";
        $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

        if ($query_del) {
            $_SESSION['info'] = 'Dihapus';
            header("Location:../list-act-br-import.php?id= $encode_id && id_inv= $encode_inv");
        } else {
            $_SESSION['info'] = 'Data Gagal Dihapus';
            header("Location:../list-act-br-import.php?id= $encode_id && id_inv= $encode_inv");
        }

        // Update Status Pengiriman
    } else if (isset($sanitasi_post['update-status'])) {
        $id_inv_br_import = decrypt($sanitasi_post['id_inv_br_import'], $key_global);
        $tgl_est_str = $sanitasi_post['tgl_est'];
        $status = $sanitasi_post['status'];
        $hub_sp = 'Mohon tunggu / silahkan hubungi supplier kembali';
        $keterangan = '';
        $tgl_terima_str = '';
        
        // Menentukan nilai `keterangan` dan `tgl_terima_str`
        if ($status == "Sudah Diterima") {
            $tgl_terima_str = $sanitasi_post['tgl_terima'];
            $tgl_est = DateTime::createFromFormat('d/m/Y', $tgl_est_str)->format('Y/m/d');
            $tgl_terima = DateTime::createFromFormat('d/m/Y', $tgl_terima_str)->format('Y/m/d');
            $selisih_hari = abs(strtotime($tgl_terima) - strtotime($tgl_est)) / 86400;
        
            $keterangan = $tgl_est == $tgl_terima ? 'Tepat Waktu' 
                        : ($tgl_est < $tgl_terima ? "Barang Diterima Telat $selisih_hari hari" 
                        : "Barang Diterima Lebih Awal $selisih_hari hari");
        } elseif (in_array($status, ["Masih Dalam Perjalanan", "Belum Dikirim"])) {
            $keterangan = $hub_sp;
        } elseif ($status == "Kendala Di Pelabuhan") {
            $keterangan = $sanitasi_post['keterangan'];
        }
        
        // Menyiapkan query menggunakan prepared statement
        $query = "UPDATE inv_br_import 
                  SET status_pengiriman = ?, 
                      tgl_terima = ?, 
                      keterangan = ? 
                  WHERE id_inv_br_import = ?";
        
        $stmt = $connect->prepare($query);
        
        // Binding parameter ke prepared statement
        $stmt->bind_param("ssss", $status, $tgl_terima_str, $keterangan, $id_inv_br_import);
        
        // Eksekusi query
        if ($stmt->execute()) {
            $_SESSION['info'] = 'Disimpan';
            header("Location:../barang-masuk-reg-import.php");
        } else {
            echo "Error: " . $stmt->error;
        }
        
        // Menutup statement
        $stmt->close();        
    } else if (isset($sanitasi_get['id'])) {
        //tangkap URL dengan $sanitasi_get
        $idh = decrypt($sanitasi_get['id'], $key_global);

        // Menampilkan data berelasi
        $sql = mysqli_query($connect, "SELECT 
                                            ibi.id_inv_br_import AS id_inv, 
                                            iibi.id_isi_inv_br_import,
                                            act.id_act_br_import
                                        FROM inv_br_import AS ibi
                                        LEFT JOIN isi_inv_br_import iibi ON ibi.id_inv_br_import = iibi.id_inv_br_import
                                        LEFT JOIN act_br_import act ON (iibi.id_isi_inv_br_import = act.id_isi_inv_br_import)
                                        WHERE ibi.id_inv_br_import= '$idh'");
        $data = mysqli_fetch_array($sql);
        $id_inv = $data['id_inv'];
        $id_inv_isi = $data['id_isi_inv_br_import'];

        if ($id_inv_isi == '') {
            // //perintah queery sql untuk hapus data
            $sql = "DELETE FROM inv_br_import
                    WHERE id_inv_br_import = '$id_inv'";
            $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));
            $_SESSION['info'] = 'Dihapus';
            header("Location:../barang-masuk-reg-import.php");
        } else {
            $sql = "DELETE ibi, iibi, act  
                    FROM inv_br_import ibi
                    LEFT JOIN isi_inv_br_import iibi ON (ibi.id_inv_br_import = iibi.id_inv_br_import)
                    LEFT JOIN act_br_import act ON (iibi.id_isi_inv_br_import = act.id_isi_inv_br_import)
                    WHERE ibi.id_inv_br_import = '$id_inv' AND iibi.id_inv_br_import = '$id_inv' AND act.id_isi_inv_br_import = '$id_inv_isi'";
            $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));
            $_SESSION['info'] = 'Dihapus';
            header("Location:../barang-masuk-reg-import.php");
        }
    }
?>