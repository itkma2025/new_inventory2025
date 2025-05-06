<?php
session_start();
require_once "../akses.php";
require_once "../function/function-enkripsi.php";
$key = "KM@2024?SET";
$id_user = decrypt($_SESSION['tiket_id'], $key_global);
$created_by = $id_user;

if (isset($_POST['simpan'])) {
    $token_csrf = htmlspecialchars($_POST['csrf_token']);
    $id_kartu_stock = htmlspecialchars($_POST['id_kartu_stock']);
    $id_history = htmlspecialchars($_POST['id_history']); 
    $jenis_produk = htmlspecialchars($_POST['jenis_produk']);
    $id_produk = htmlspecialchars($_POST['id_produk']);
    $id_produk_decrypt = decrypt($id_produk, $key);
    $status = $_POST['status'];
    $jenis_barang_masuk = htmlspecialchars($_POST['jenis_barang_masuk']);
    $jenis_barang_keluar = htmlspecialchars($_POST['jenis_barang_keluar']);
    $values_spk = !empty($_POST['id_spk']) ? $_POST['id_spk'] : '-';
    $explode_spk = explode(',',  $values_spk);

    $qty = $_POST['qty'];
    $qty_in = ($status == 0) ? $qty : 0;
    $qty_out = ($status == 1) ? $qty : 0;
    $keterangan = htmlspecialchars($_POST['keterangan']);
    $status_aksi = "Tambah Data";
    $created_by = base64_decode($_SESSION['tiket_id']);
    // Cek token
    $exp_token = $_SESSION['token_exp'];
    $date_now = date('Y-m-d H:i:s');

    // Menampilkan ID untuk jenis barang masuk
    $sql_ket_in = " SELECT 
                            id_ket_in,
                            ket_in
                        FROM keterangan_in 
                        WHERE ket_in = '$jenis_barang_masuk'";
    $query_ket_in = $connect->query($sql_ket_in);
    $data_ket_in = mysqli_fetch_array($query_ket_in);
    $id_ket_in = $data_ket_in['id_ket_in'] ?? null;

    // Menampilkan ID untuk jenis barang keluar
    $sql_ket_out = " SELECT 
                        id_ket_out,
                        ket_out
                    FROM keterangan_out 
                    WHERE ket_out = '$jenis_barang_keluar'";
    $query_ket_out = $connect->query($sql_ket_out);
    $data_ket_out = mysqli_fetch_array($query_ket_out);
    $id_ket_out = $data_ket_out['id_ket_out'] ?? null;

    if ($status == 0) {
        if ($token_csrf != "") {
            if ($date_now > $exp_token) {
                $_SESSION['info'] = 'Token expired';
                header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                exit();
            } else {
                try {
                    // Memulai transaksi
                    $connect->begin_transaction();

                    // Eksekusi query SQL pertama
                    $simpan_data_kartu_stock = "INSERT INTO kartu_stock_set_reg 
                                                    (id_kartu_stock, id_produk, status_barang, jenis_barang_masuk, qty_in, keterangan, created_date, created_by)
                                                    VALUES
                                                    ('$id_kartu_stock', '$id_produk_decrypt', '$status', '$id_ket_in', '$qty_in', '$keterangan', '$date_now', '$created_by')";
                    if (!$connect->query($simpan_data_kartu_stock)) {
                        throw new Exception("Kesalahan dalam eksekusi simpan data kartu stock: " . $connect->error);
                    }

                    // Eksekusi query SQL kedua
                    $simpan_history_kartu_stock = "INSERT INTO kartu_stock_set_reg_history
                                                        (id_history, id_kartu_stock, qty, status_aksi, created_by)
                                                        VALUES
                                                        ('$id_history', '$id_kartu_stock', '$qty', '$status_aksi', '$created_by')";
                    if (!$connect->query($simpan_history_kartu_stock)) {
                        throw new Exception("Kesalahan dalam eksekusi history: " . $connect->error);
                    }

                    // Commit transaksi
                    $connect->commit();
                    $_SESSION['info'] = 'Disimpan';
                    header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                    exit();
                } catch (Exception $e) {
                    // Rollback transaksi jika terjadi kesalahan
                    $connect->rollback();
                    $_SESSION['info'] = 'Data Gagal Disimpan';
                    header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                    exit();
                }
            }
        } else {
            $_SESSION['info'] = 'Token not found';
            header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
            exit();
        }
    } else if ($status == 1) {
        if ($jenis_barang_keluar == "Penjualan") {
            // Pastikan array hasil explode memiliki setidaknya dua elemen
            if (count($explode_spk) >= 2) {
                $id_trx = $explode_spk[0];
                $id_spk = $explode_spk[1];

                // Melakukan pengecekan untuk menentukan keterangan KS
                $cek_spk = $connect->query("SELECT id_spk, id_produk, qty FROM transaksi_produk_reg WHERE id_transaksi = '$id_trx'");
                $data_spk = mysqli_fetch_array($cek_spk);
                $trx_spk_id = $data_spk['id_spk'];
                $trx_id_produk = $data_spk['id_produk'];
                $trx_qty = $data_spk['qty'];


                $keterangan_ks = "";
                if ($qty_out == $trx_qty) {
                    $keterangan_ks = "1";
                } else {
                    $keterangan_ks = "0";
                }
            } else {
                // Handle jika tidak ada dua elemen yang diharapkan
                $id_trx = ''; // Atur default value
                $id_spk = ''; // Atur default value
            }

            if ($token_csrf != "") {
                if ($date_now > $exp_token) {
                    $_SESSION['info'] = 'Token expired';
                    header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                    exit();
                } else {
                    try {
                        // Memulai transaksi
                        $connect->begin_transaction();


                        $stmt = $connect->prepare("UPDATE tmp_kartu_stock 
                                                            SET 
                                                                id_tmp_ks = ?, 
                                                                id_spk_ks = ?, 
                                                                id_produk_ks = ?, 
                                                                qty_ks = ?, 
                                                                status_barang = ?,
                                                                status_input = 1,
                                                                keterangan_ks = ?,
                                                                keterangan_input = ?,
                                                                input_date = ?,
                                                                input_by = ?
                                                        WHERE id_transaksi = ?");
                        $stmt->bind_param('sssiiissss', $id_kartu_stock, $id_spk, $id_produk_decrypt, $qty_out, $status, $keterangan_ks, $keterangan, $date_now, $created_by,  $id_trx);
                        $simpan_tmp_ks = $stmt->execute();

                        if (!$simpan_tmp_ks) {
                            throw new Exception("Gagal menyimpan data kartu stock: " . $stmt->error);
                        }

                        // Commit transaksi
                        $connect->commit();
                        $_SESSION['info'] = 'Disimpan';
                        header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                        exit();
                    } catch (Exception $e) {
                        // Rollback transaksi jika terjadi kesalahan
                        $connect->rollback();
                        // $error_message = "Gagal saat proses data: " . $e->getMessage();
                        // echo $error_message;
                        $_SESSION['info'] = 'Data Gagal Disimpan';
                        header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                        exit();
                    }
                }
            } else {
                $_SESSION['info'] = 'Token not found';
                header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                exit();
            }
        } else if ($jenis_barang_keluar != "Penjualan") {
            if ($token_csrf != "") {
                if ($date_now > $exp_token) {
                    $_SESSION['info'] = 'Token expired';
                    header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                    exit();
                } else {
                    try {
                        // Memulai transaksi
                        $connect->begin_transaction();

                        // Eksekusi query SQL pertama
                        $simpan_data_kartu_stock = "INSERT INTO kartu_stock_set_reg 
                                                        (id_kartu_stock, id_produk, status_barang,  jenis_barang_keluar, id_spk, qty_in, qty_out, keterangan, created_date, created_by)
                                                        VALUES
                                                        ('$id_kartu_stock', '$id_produk_decrypt', '$status', '$id_ket_out', '-', '$qty_in', '$qty_out', '$keterangan', '$date_now', '$created_by')";
                        if (!$connect->query($simpan_data_kartu_stock)) {
                            throw new Exception("Kesalahan dalam eksekusi simpan data kartu stock: " . $connect->error);
                        }

                        // Eksekusi query SQL kedua
                        $simpan_history_kartu_stock = "INSERT INTO kartu_stock_set_reg_history
                                                            (id_history, id_kartu_stock, qty, status_aksi, created_by)
                                                            VALUES
                                                            ('$id_history', '$id_kartu_stock', '$qty', '$status_aksi', '$created_by')";
                        if (!$connect->query($simpan_history_kartu_stock)) {
                            throw new Exception("Kesalahan dalam eksekusi history: " . $connect->error);
                        }

                        // Commit transaksi
                        $connect->commit();
                        $_SESSION['info'] = 'Disimpan';
                        header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                        exit();
                    } catch (Exception $e) {
                        // Rollback transaksi jika terjadi kesalahan
                        $connect->rollback();
                        $_SESSION['info'] = 'Data Gagal Disimpan';
                        header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                        exit();
                    }
                }
            } else {
                $_SESSION['info'] = 'Token not found';
                header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                exit();
            }
        } else {
            header("Location:../404.php");
        }
    }
} else if (isset($_POST['edit-in'])) {
    $token_csrf = $_POST['csrf_token'];
    $id_kartu_stock = $_POST['id_kartu_stock'];
    $id_history = $_POST['id_history'];
    $id_kartu_stock_decrypt = decrypt($id_kartu_stock, $key);
    $jenis_produk = $_POST['jenis_produk'];
    $id_produk = $_POST['id_produk'];
    $keterangan = $_POST['keterangan'];
    $qty = $_POST['qty'];
    $status_aksi = "Edit Data";
    $updated_by = base64_decode($_SESSION['tiket_id']);
    $updated_date = date('d/m/Y H:i:s');
    // Cek token
    $exp_token = $_SESSION['token_exp'];
    $date_now = date('Y-m-d H:i:s');
    if ($token_csrf != "") {
        if ($date_now > $exp_token) {
            $_SESSION['info'] = 'Token expired';
            header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
            exit();
        } else {
            try {
                // Memulai transaksi
                $connect->begin_transaction();

                // Eksekusi query SQL pertama
                $edit_in = "UPDATE kartu_stock_set_reg SET keterangan = '$keterangan', qty_in = '$qty' WHERE id_kartu_stock = '$id_kartu_stock_decrypt'";
                $proses_edit_in = $connect->query($edit_in);

                if (!$proses_edit_in) {
                    throw new Exception("Kesalahan dalam eksekusi simpan data kartu stock: " . $connect->error);
                }

                // Eksekusi query SQL kedua
                $simpan_history_kartu_stock = "INSERT INTO kartu_stock_set_reg_history
                                                    (id_history, id_kartu_stock, qty, status_aksi, created_by)
                                                    VALUES
                                                    ('$id_history', '$id_kartu_stock_decrypt', '$qty', '$status_aksi', '$created_by')";
                if (!$connect->query($simpan_history_kartu_stock)) {
                    throw new Exception("Kesalahan dalam eksekusi history: " . $connect->error);
                }

                // Commit transaksi
                $connect->commit();
                $_SESSION['info'] = 'Diupdate';
                header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                exit();
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                $connect->rollback();
                $_SESSION['info'] = 'Data Gagal Diupdate';
                header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                exit();
            }
        }
    } else {
        $_SESSION['info'] = 'Token not found';
        echo "<script>document.location.href='../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data'</script>";
    }
} else if (isset($_POST['edit-out'])) {
    $token_csrf = $_POST['csrf_token'];
    $id_kartu_stock = $_POST['id_kartu_stock'];
    $id_history = $_POST['id_history'];
    $id_kartu_stock_decrypt = decrypt($id_kartu_stock, $key);
    $jenis_produk = $_POST['jenis_produk'];
    $id_produk = $_POST['id_produk'];
    $keterangan = $_POST['keterangan'];
    $status_aksi = "Edit Data";
    $qty = $_POST['qty'];

    $updated_by = base64_decode($_SESSION['tiket_id']);
    $updated_date = date('d/m/Y H:i:s');
    // Cek token
    $exp_token = $_SESSION['token_exp'];
    $date_now = date('Y-m-d H:i:s');
    if ($token_csrf != "") {
        if ($date_now > $exp_token) {
            $_SESSION['info'] = 'Token expired';
            header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
            exit();
        } else {
            try {
                // Memulai transaksi
                $connect->begin_transaction();

                // Eksekusi query SQL pertama
                $edit_out = "UPDATE kartu_stock_set_reg SET keterangan = '$keterangan', qty_out = '$qty' WHERE id_kartu_stock = '$id_kartu_stock_decrypt'";
                $proses_edit_out = $connect->query($edit_out);

                if (!$proses_edit_out) {
                    throw new Exception("Kesalahan dalam eksekusi simpan data kartu stock: " . $connect->error);
                }

                // Eksekusi query SQL kedua
                $simpan_history_kartu_stock = "INSERT INTO kartu_stock_set_reg_history
                                                    (id_history, id_kartu_stock, qty, status_aksi, created_by)
                                                    VALUES
                                                    ('$id_history', '$id_kartu_stock_decrypt', '$qty', '$status_aksi', '$created_by')";
                if (!$connect->query($simpan_history_kartu_stock)) {
                    throw new Exception("Kesalahan dalam eksekusi history: " . $connect->error);
                }

                // Commit transaksi
                $connect->commit();
                $_SESSION['info'] = 'Diupdate';
                header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                exit();
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi kesalahan
                $connect->rollback();
                $_SESSION['info'] = 'Data Gagal Diupdate';
                header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
                exit();
            }
        }
    } else {
        $_SESSION['info'] = 'Token not found';
        echo "<script>document.location.href='../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data'</script>";
    }
} else if ($_GET['id_kartu_stock']) {
    $id_kartu_stock = $_GET['id_kartu_stock'];
    $id_kartu_stock_decrypt = decrypt($id_kartu_stock, $key);
    $id_history = $_GET['id_history'];
    $qty = $_GET['qty'];
    $jenis_produk = $_GET['jenis_produk'];
    $id_produk = $_GET['id_produk'];
    $status_aksi = "Hapus Data";

    try {
        // Memulai transaksi
        $connect->begin_transaction();

        // Eksekusi query SQL pertama
        $hapus = $connect->query("DELETE FROM kartu_stock_set_reg WHERE id_kartu_stock = '$id_kartu_stock_decrypt'");
        if (!$hapus) {
            throw new Exception("Kesalahan dalam eksekusi simpan data kartu stock: " . $connect->error);
        }

        // Eksekusi query SQL kedua
        $simpan_history_kartu_stock = "INSERT INTO kartu_stock_set_reg_history
                                            (id_history, id_kartu_stock, qty, status_aksi, created_by)
                                            VALUES
                                            ('$id_history', '$id_kartu_stock_decrypt', '$qty', '$status_aksi', '$created_by')";
        if (!$connect->query($simpan_history_kartu_stock)) {
            throw new Exception("Kesalahan dalam eksekusi history: " . $connect->error);
        }

        // Commit transaksi
        $connect->commit();
        $_SESSION['info'] = 'Dihapus';
        header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
        exit();
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        $connect->rollback();
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header("Location:../kartu-persediaan-barang.php?jenis_produk=$jenis_produk&id=$id_produk&sort_data=semua_data");
        exit();
    }
}
