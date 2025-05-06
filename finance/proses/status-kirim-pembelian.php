<?php  
    require_once "../../akses.php";
    include "../page/resize-image.php";

    if(isset($_POST['simpan-pengiriman'])){
        $id_status_kirim = htmlspecialchars($_POST['id_status_kirim']);
        $id_inv_pembelian = htmlspecialchars($_POST['id_inv_pembelian']);
        $id_inv_pembelian_encode = base64_encode($id_inv_pembelian);
        $jenis_pengiriman = $_POST['jenis_pengiriman'];
        $diambil_oleh = htmlspecialchars($_POST['diambil_oleh']);
        $nama_kurir_pengirim = htmlspecialchars($_POST['nama_kurir_pengirim']);
        $ongkir = $_POST['ongkir'];
        $nominal_ongkir = str_replace(',', '', $ongkir); // Menghapus tanda ribuan (,)
        $nominal_ongkir = intval($nominal_ongkir); // Mengubah string harga menjadi integer
        $tanggal = htmlspecialchars($_POST['tanggal']);
        $created_by = $_SESSION['tiket_nama'];
        // Kondisi dikirim oleh
        if (isset($_POST['dikirim_oleh'])) {
            $dikirim_oleh = htmlspecialchars($_POST['dikirim_oleh']);
        } else {
            $dikirim_oleh = '';
        }
        // Kondisi Ekspedisi
        if (isset($_POST['ekspedisi'])) {
            $ekspedisi = htmlspecialchars($_POST['ekspedisi']);
        } else {
            $ekspedisi = '';
        }
        // Kondisi jenis ongkir
        if (isset($_POST['jenis_ongkir'])) {
            $jenis_ongkir = htmlspecialchars($_POST['jenis_ongkir']);
        } else {
            $jenis_ongkir = '';
        }
        // kondisi free ongkir
        if (isset($_POST['free_ongkir'])) {
            $free_ongkir = htmlspecialchars($_POST['free_ongkir']);
        } else {
            $free_ongkir = '';
        }

        if ($jenis_pengiriman == 'Diambil'){
            $simpan_data = $connect->query("INSERT INTO status_kirim_pembelian 
                                                (id_status_kirim, id_inv_pembelian, jenis_pengiriman, diambil_oleh, tanggal, status, created_by)
                                                VALUES
                                                ('$id_status_kirim', '$id_inv_pembelian', '$jenis_pengiriman', '$diambil_oleh', '$tanggal', '1', '$created_by')    
                                            ");
            if($simpan_data){
                $_SESSION['info'] = "Disimpan";
                header("Location:../detail-produk-pembelian-lokal.php?id=$id_inv_pembelian_encode");
            } else {
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../detail-produk-pembelian-lokal.php?id=$id_inv_pembelian_encode");
            }
            

        } else if ($jenis_pengiriman == 'Dikirim'){
            $simpan_data = $connect->query("INSERT INTO status_kirim_pembelian 
                                                (id_status_kirim, id_inv_pembelian, jenis_pengiriman, dikirim_oleh, nama_kurir_pengirim, nama_ekspedisi, jenis_ongkir, nominal_ongkir, free_ongkir, tanggal, status, created_by)
                                                VALUES
                                                ('$id_status_kirim', '$id_inv_pembelian', '$jenis_pengiriman', '$dikirim_oleh', '$nama_kurir_pengirim', '$ekspedisi', '$jenis_ongkir', '$nominal_ongkir', '$free_ongkir', '$tanggal', '1', '$created_by')    
                                            ");
            if($simpan_data){
                $_SESSION['info'] = "Disimpan";
                header("Location:../detail-produk-pembelian-lokal.php?id=$id_inv_pembelian_encode");
            } else {
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../detail-produk-pembelian-lokal.php?id=$id_inv_pembelian_encode");
            }
        }
    } else if(isset($_POST['upload'])){
        $year = date('y');
        $day = date('d');
        $month = date('m');
        $img_uuid = img_uuid();
        $id_bukti_terima = $_POST['id_bukti_terima'];
        $id_inv_pembelian = $_POST['id_inv'];
        $id_encode = base64_encode($id_inv_pembelian);
        $nama_sp = $_POST['nama_sp'];
        $no_faktur = $_POST['no_faktur'];
        $tgl_terima = $_POST['tgl_terima'];
        $created_by = $_SESSION['tiket_nama'];


        // Periksa karakter nama supplier yang tidak valid
        $nama_sp_replace = preg_replace("/[^a-zA-Z0-9]/", "_", $nama_sp);
        
        // Convert $no_inv_bum to the desired format
        $tgl_terima_converted = str_replace('/', '_', $tgl_terima);
        $no_inv_converted = str_replace('/', '_', $no_faktur);

        // Generate folder name based on invoice details
        $folder_name = $no_inv_converted;

        // Encode a portion of the folder name
        $encoded_portion = base64_encode($folder_name);

        // Combine the original $no_inv, encoded portion, and underscore
        $encoded_folder_name = $no_inv_converted . '_' . $encoded_portion;

        $file1_name = $_FILES['fileku1']['name'];
        $file1_tmp = $_FILES['fileku1']['tmp_name'];
        $file1_destination = "../gambar/pembelian/" .  $nama_sp_replace . "/" . $encoded_folder_name . "/" . $file1_name ;
        try{
            move_uploaded_file($file1_tmp, $file1_destination);

            $new_file1_name = "bukti_pembelian_" .$tgl_terima_converted. "". $year . "" . $month . "" . $img_uuid . "" . $day . ".jpg";
        
            $compressed_file1_destination = "../gambar/pembelian/" .  $nama_sp_replace . "/" . $encoded_folder_name . "/" . $new_file1_name;

            // Mendapatkan informasi gambar asli
            $image_info = getimagesize($file1_destination);
            $width = $image_info[0];
            $height = $image_info[1];
            compressAndResizeImage($file1_destination, $compressed_file1_destination, $width * 0.8, $height * 0.8, 100);
            unlink($file1_destination);

            $update_data = mysqli_query($connect, "UPDATE inv_pembelian_lokal SET status_pembelian = '1', tgl_terima = '$tgl_terima' WHERE id_inv_pembelian = '$id_inv_pembelian'");
            $insert_data = $connect->query("INSERT INTO inv_bukti_terima_pembelian 
                                                (id_bukti_terima, id_inv_pembelian, bukti_pembelian, created_by) 
                                                VALUES 
                                                ('$id_bukti_terima', '$id_inv_pembelian', '$new_file1_name', '$created_by')");

            if (!$update_data && !$insert_data) {
                throw new Exception("Error updating data");
            }
            // Commit the  
            mysqli_commit($connect);
            // Redirect to the invoice page
            $_SESSION['info'] = "Disimpan";
            header("Location:../data-pembelian.php?date_range=year");
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
                        window.location.href = "../data-pembelian.php?date_range=year";
                    });
                    });
                </script>
                <?php
        } 
    } else if ($_GET['edit-pengiriman']){
        $idh = base64_decode($_GET['edit-pengiriman']);
        $id_inv_pembelian_encode = base64_encode($idh);
        $delete = $connect->query("DELETE FROM status_kirim_pembelian WHERE id_inv_pembelian = '$idh'");

        if($delete){
            $_SESSION['info'] = "Diupdate";
            header("Location:../detail-produk-pembelian-lokal.php?id=$id_inv_pembelian_encode");
        } else {
            $_SESSION['info'] = "Data Gagal Diupdate";
            header("Location:../detail-produk-pembelian-lokal.php?id=$id_inv_pembelian_encode");
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