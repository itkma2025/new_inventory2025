<?php
session_start();
include "../akses.php";
include "../page/resize-image.php";

if (isset($_POST['simpan-inv-br-in-lokal'])) {
    $id_inv_br_in_lokal = $_POST['id_inv_br_in_lokal'];
    $no_inv = $_POST['no_inv'];
    $tgl_inv = $_POST['tgl_inv'];
    $ongkir = str_replace(',', '', $_POST['ongkir']); // Menghapus tanda ribuan (,)
    $ongkir = intval($ongkir); // Mengubah string harga menjadi integer
    $id_sp = $_POST['id_sp'];
    $nama_sp = $_POST['sp'];
    $id_user = $_POST['id_user'];
    $created = $_POST['created']; 

    $cek_data = mysqli_query($connect, "SELECT id_inv_br_in_lokal FROM inv_br_in_lokal WHERE id_inv_br_in_lokal = '$id_inv_br_in_lokal'");

    if ($cek_data->num_rows > 0) {
        $_SESSION['info'] = "Data sudah ada";
        header('Location:../barang-masuk-lokal.php');
    } else {
        mysqli_begin_transaction($connect);
        try{
            // Convert $no_inv_bum to the desired format
            $no_inv_converted = str_replace('/', '_', $no_inv);

            // Generate folder name based on invoice details
            $folder_name = $no_inv_converted;

            // Encode a portion of the folder name
            $encoded_portion = base64_encode($folder_name);

            // Combine the original $no_inv_bum, encoded portion, and underscore
            $encoded_folder_name = $no_inv_converted . '_' . $encoded_portion;

            // Set the path for the customer's folder
            $customer_folder_path = "../gambar/pembelian/" . $nama_sp . "/";

            // Create the customer's folder if it doesn't exist
            if (!is_dir($customer_folder_path)) {
                mkdir($customer_folder_path, 0777, true); // Set permission to 0777 to ensure the folder is writable
            }


            $simpan_data = mysqli_query($connect, "INSERT INTO inv_br_in_lokal 
            (id_inv_br_in_lokal, id_user, id_sp, no_inv, tgl_inv, ongkir, created_date)
            VALUES
            ('$id_inv_br_in_lokal', '$id_user', '$id_sp', '$no_inv', '$tgl_inv', '$ongkir', '$created')");

            if (!$simpan_data) {
                throw new Exception("Error updating data");
            }
            // Commit the transaction
            mysqli_commit($connect);
            // Redirect to the invoice page
            $_SESSION['info'] = "Disimpan";
            header('Location:../barang-masuk-lokal.php');
            exit();
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            mysqli_rollback($connect);
            // Handle the error (e.g., display an error message)
            $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
                ?>
                <!-- Sweet Alert -->
                <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "<?php echo $error_message; ?>",
                        icon: "error",
                    }).then(function() {
                        window.location.href = "../barang-masuk-lokal.php";
                    });
                    });
                </script>
                <?php
        } 
    }
} else if (isset($_POST['edit-br-in-lokal'])) {
    $id_inv_br_in_lokal = $_POST['id_inv_br_in_lokal'];
    $no_inv = $_POST['no_inv'];
    $tgl_inv = $_POST['tgl_inv'];
    $id_sp = $_POST['id_sp'];

    $cek_data = mysqli_query($connect, "SELECT * FROM inv_br_in_lokal WHERE id_inv_br_in_lokal = '$id_inv_br_in_lokal'");
    $data = mysqli_fetch_array($cek_data);

    if ($data['id_sp'] == $id_sp && $data['no_inv'] == $no_inv && $data['tgl_inv'] == $tgl_inv) {
        $_SESSION['info'] = 'Tidak Ada Perubahan Data';
        header('Location:../barang-masuk-lokal.php');
        exit;
    } else {
        $edit_data = "UPDATE inv_br_in_lokal
                            SET 
                            id_sp = '$id_sp',
                            no_inv = '$no_inv',
                            tgl_inv = '$tgl_inv'
                            WHERE  id_inv_br_in_lokal = '$id_inv_br_in_lokal'";
        $query = mysqli_query($connect, $edit_data);

        if ($query) {
            $_SESSION['info'] = 'Diupdate';
            header('Location:../barang-masuk-lokal.php');
        } else {
            $_SESSION['info'] = 'Data Gagal Diupdate';
            header('Location:../barang-masuk-lokal.php');
        }
    }
} else if (isset($_GET['id'])) {
    $idh = base64_decode($_GET['id']);
    //perintah queery sql untuk hapus data
    $sql = "DELETE ibil, iibil 
                FROM inv_br_in_lokal ibil
                LEFT JOIN isi_inv_br_in_lokal iibil ON (ibil.id_inv_br_in_lokal = iibil.id_inv_br_in_lokal)
                WHERE ibil.id_inv_br_in_lokal = '$idh'";
    $query_del = mysqli_query($connect, $sql) or die(mysqli_error($connect));

    if ($query_del) {
        $_SESSION['info'] = 'Dihapus';
        header('Location:../barang-masuk-lokal.php');
    } else {
        $_SESSION['info'] = 'Data Gagal Dihapus';
        header('Location:../barang-masuk-lokal.php');
    }


    // Proses CRUD isi barang in lokal
} else if (isset($_POST['simpan-isi-br-in-lokal'])) {
    $id_isi = $_POST['id_isi_inv_br_in_lokal'];
    $id_inv = $_POST['id_inv_br_in_lokal'];
    $id_produk = $_POST['id_produk'];
    $qty = $_POST['qty'];
    $harga = $_POST['harga'];
    $id_user = $_POST['id_user'];
    $created = $_POST['created'];
    $encode = base64_encode($id_inv);
    $qty = intval(preg_replace("/[^0-9]/", "", $qty));
    $harga = intval(preg_replace("/[^0-9]/", "", $harga));

    $sql = mysqli_query($connect, "SELECT * FROM isi_inv_br_in_lokal WHERE id_inv_br_in_lokal = '$id_inv'");
    $data = mysqli_fetch_array($sql);

    if ($data['id_produk_reg'] == $id_produk) {
        $_SESSION['info'] = "Data sudah ada";
        header("Location:../list-br-in-lokal.php?id=$encode");
    } else {
        $simpan_data = mysqli_query($connect, "INSERT INTO isi_inv_br_in_lokal
                                                (id_isi_inv_br_in_lokal, id_inv_br_in_lokal, id_produk_reg, harga, qty, id_user, created_date )
                                                VALUES
                                                ('$id_isi', '$id_inv', '$id_produk', '$harga', '$qty', '$id_user', '$created')
                                                ");

        $_SESSION['info'] = "Disimpan";
        header("Location:../list-br-in-lokal.php?id=$encode");
    }
} else if (isset($_POST['edit-isi-br-in-lokal'])) {
    $id_isi = $_POST['id_isi_inv_br_in_lokal'];
    $id_inv = $_POST['id_inv_br_in_lokal'];
    $id_produk = $_POST['id_produk'];
    $qty = $_POST['qty'];
    $encode = base64_encode($id_inv);
    $harga = $_POST['harga'];

    $qty = intval(preg_replace("/[^0-9]/", "", $qty));
    $harga = intval(preg_replace("/[^0-9]/", "", $harga));

    $cek_data = mysqli_query($connect, "SELECT * FROM isi_inv_br_in_lokal WHERE id_isi_inv_br_in_lokal = '$id_isi'");
    $data = mysqli_fetch_array($cek_data);


    if ($data['id_produk_reg'] == $id_produk && $data['qty'] == $qty) {
        $_SESSION['info'] = "Tidak Ada Perubahan Data";
        header("Location:../list-br-in-lokal.php?id=$encode");
    } else {
        $update = mysqli_query($connect, "UPDATE isi_inv_br_in_lokal
                                          SET 
                                          id_produk_reg = '$id_produk',
                                          qty = '$qty'
                                          WHERE id_isi_inv_br_in_lokal = '$id_isi'");
        $_SESSION['info'] = "Diupdate";
        header("Location:../list-br-in-lokal.php?id=$encode");
    }
} else if (isset($_GET['hapus_isi'])) {
    $idh = base64_decode($_GET['hapus_isi']);
    $id_inv = base64_decode($_GET['id_inv']);
    $encode = base64_encode($id_inv);

    $hapus_data = mysqli_query($connect, "DELETE FROM isi_inv_br_in_lokal WHERE id_isi_inv_br_in_lokal = '$idh'");

    if ($hapus_data) {
        $_SESSION['info'] = 'Dihapus';
        header("Location:../list-br-in-lokal.php?id=$encode");
    }
} else if (isset($_POST['upload'])){
    $year = date('y');
    $day = date('d');
    $month = date('m');
    $img_uuid = img_uuid();
    $id = $_POST['id'];
    $id_encode = base64_encode($id);
    $nama_sp = $_POST['nama_sp'];
    $tgl_inv = $_POST['tgl_inv'];
    $tgl_inv_converted = str_replace('/', '_', $tgl_inv);
    $file1_name = $_FILES['fileku1']['name'];
    $file1_tmp = $_FILES['fileku1']['tmp_name'];
    $file1_destination = "../gambar/pembelian/" . $nama_sp . "/" . $file1_name;

    try{
        move_uploaded_file($file1_tmp, $file1_destination);

        $new_file1_name = "bukti_pembelian_" .$tgl_inv_converted. "". $year . "" . $month . "" . $img_uuid . "" . $day . ".jpg";
    
        $compressed_file1_destination = "../gambar/pembelian/" . $nama_sp . "/" . $new_file1_name;
        compressAndResizeImage($file1_destination, $compressed_file1_destination, 500, 500, 100);
        unlink($file1_destination);

        $update_data = mysqli_query($connect, "UPDATE inv_br_in_lokal SET bukti_pembelian = '$new_file1_name' WHERE id_inv_br_in_lokal = '$id'");

        if (!$update_data) {
            throw new Exception("Error updating data");
        }
        // Commit the  
        mysqli_commit($connect);
        // Redirect to the invoice page
        $_SESSION['info'] = "Disimpan";
        header("Location:../list-br-in-lokal.php?id=$id_encode");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        mysqli_rollback($connect);
        // Handle the error (e.g., display an error message)
        $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
            ?>
            <!-- Sweet Alert -->
            <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
            <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error!",
                    text: "<?php echo $error_message; ?>",
                    icon: "error",
                }).then(function() {
                    window.location.href = "../list-br-in-lokal.php?id=<?php echo $id_encode ?>";
                });
                });
            </script>
            <?php
    } 
} else if (isset($_POST['hapus-bukti'])) {
    $id = $_POST['id'];
    $id_encode = base64_encode($id);
    $nama_sp = $_POST['nama_sp'];
    $bukti_pembelian = $_POST['bukti_pembelian'];
    $file_destination = "../gambar/pembelian/" . $nama_sp . "/" . $bukti_pembelian;

    $update_data = mysqli_query($connect, "UPDATE inv_br_in_lokal SET bukti_pembelian = '' WHERE id_inv_br_in_lokal = '$id'");

    if($update_data){
        unlink($file_destination);
        $_SESSION['info'] = "Dihapus";
        header("Location:../list-br-in-lokal.php?id=$id_encode");
    } else {
        $_SESSION['info'] = "Data Gagal Dihapus";
        header("Location:../list-br-in-lokal.php?id=$id_encode");
    }
}

function img_uuid() {
    $data = openssl_random_pseudo_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s', str_split(bin2hex($data), 4));
}
?>
