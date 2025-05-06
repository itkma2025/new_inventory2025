<?php  
    include "../akses.php";
    require_once "../function/function-enkripsi.php";
    if(isset($_GET['hapus_tmp'])){
        $id_tmp = decrypt($_GET['hapus_tmp'], $key_spk);
        $id_komplain = $_GET['id_komplain'];
        $delete_data = mysqli_query($connect, "DELETE FROM tmp_produk_komplain WHERE id_tmp = '$id_tmp'");
        if($delete_data){
            $_SESSION['info'] = "Dihapus";
            header("Location:../detail-komplain-revisi-nonppn.php?id=$id_komplain");
        } else {
            $_SESSION['info'] = "Data Gagal Dihapus";
            header("Location:../detail-komplain-revisi-nonppn.php?id=$id_komplain");
        }
    } else if (isset($_POST['simpan-tmp'])){
        // Begin transaction
        mysqli_begin_transaction($connect);

        try {
            // Mendapatkan data yang dikirimkan melalui form
            $id_komplain = $_POST['id_komplain'];
            $id_tmp = $_POST['id_tmp']; // Mengambil ID transaksi dari form
            $id_produk = $_POST['id_produk_tmp'];
            $nama_produk = $_POST['nama_produk'];
            $harga = $_POST['harga']; // Mengambil Nilai Harga
            $qty = $_POST['qty_tmp']; // Mengambil nilai qty yang diperbarui
            $disc = $_POST['disc'];
            $stock = $_POST['stock'];

            // Gunakan variabel flag untuk menandai apakah semua update berhasil
            $allUpdatesSuccessful = true;

            for ($i = 0; $i < count($id_tmp); $i++) {
                $id = $id_tmp[$i];
                $id_produk_array = $id_produk[$i];
                $nama_produk_array = $nama_produk[$i];
                $hrg = str_replace(',', '', $harga[$i]); // Menghapus tanda ribuan (,)
                $hrg = intval($hrg); // Mengubah string harga menjadi integer
                $newQtyInt = str_replace(',', '', $qty[$i]); // Menghapus tanda ribuan (,)
                $newQtyInt = intval($newQtyInt); // Mengubah string harga menjadi integer
                $newStock = str_replace(',', '', $stock[$i]); // Menghapus tanda ribuan (,)
                $newStock = intval($newStock); // Mengubah string harga menjadi integer
                $total_harga = $hrg * $newQtyInt;
                $disc_array = $disc[$i];
                $array_disc = $disc_array / 100;
                $total_harga_disc = $total_harga * (1 - $array_disc);
                $newStockUpdate = $newStock - $newQtyInt;

                // Lakukan proses penyimpanan data qty ke dalam database sesuai dengan ID transaksi
                // Contoh: Lakukan kueri SQL untuk memperbarui data qty dalam tabel transaksi menggunakan ID transaksi
                $sql = "UPDATE tmp_produk_komplain SET nama_produk = '$nama_produk_array', harga = $hrg, qty = '$newQtyInt', disc = '$disc_array', total_harga = '$total_harga_disc', status_tmp = '1' WHERE id_tmp = '$id'";
                $query = mysqli_query($connect, $sql);

                $update_stock = mysqli_query($connect, "UPDATE stock_produk_reguler SET stock = '$newStockUpdate' WHERE id_produk_reg = '$id_produk_array'");

                // Periksa keberhasilan setiap kueri
                if (!$query || !$update_stock) {
                    $allUpdatesSuccessful = false;
                    break; // Keluar dari loop jika ada kesalahan
                }
            }

            // Commit transaksi hanya jika semua kueri berhasil
            if ($allUpdatesSuccessful) {
                mysqli_commit($connect);
                $_SESSION['info'] = "Disimpan";
                ?>
                <script>window.location.href = "../detail-komplain-revisi-nonppn.php?id=<?php echo $id_komplain ?>"</script>
                <?php
                exit();
            } else {
                // Rollback transaksi jika ada kesalahan
                mysqli_rollback($connect);
            }
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
                        window.location.href = "../detail-komplain-revisi-nonppn.php?id=<?php echo $id_komplain ?>";
                    });
                    });
                </script>
                <?php
        } 
    } else if (isset($_POST['ubah-data'])){
        $connect->begin_transaction();
        try{
            //Mendapatkan data yang dikirimkan melalui form
            $id_komplain = $_POST['id_komplain'];
            $id_tmp = $_POST['id_tmp'];
            $id_produk = $_POST['id_produk'];
            $nama_produk = $_POST['nama_produk'];
            $harga = $_POST['harga'];
            $hrg = str_replace(',', '', $harga); // Menghapus tanda ribuan (,)
            $hrg = intval($hrg); // Mengubah string harga menjadi integer
            $disc = $_POST['disc'];
            $disc_array = $_POST['disc'] / 100;
            $stock = str_replace(',', '', $_POST['stock']); // Menghapus tanda ribuan (,)
            $stock = intval($stock); // Mengubah string harga menjadi integer
            $qty = str_replace(',', '', $_POST['qty']); // Menghapus tanda ribuan (,)
            $qty = intval($qty); // Mengubah string harga menjadi integer
            $qty_edit = str_replace(',', '', $_POST['qty_edit']); // Menghapus tanda ribuan (,)
            $qty_edit = intval($qty_edit); // Mengubah string harga menjadi integer

            // kalulasi total harga
            $sub_harga = $hrg * $qty_edit;
            $total_harga = $sub_harga * (1 - $disc_array);

            $hasil_stock = $stock - $qty_edit;
        
            //Lakukan proses penyimpanan data qty ke dalam database sesuai dengan ID transaksi
            //Contoh: Lakukan kueri SQL untuk memperbarui data qty dalam tabel transaksi menggunakan ID transaksi
            $update_tmp = "UPDATE tmp_produk_komplain SET nama_produk = '$nama_produk', harga = $hrg, qty = '$qty_edit', disc = '$disc', total_harga = '$total_harga', status_tmp = '1' WHERE id_tmp = '$id_tmp'";
            $query_tmp = mysqli_query($connect, $update_tmp);

            $update_stock = "UPDATE stock_produk_reguler SET stock = '$hasil_stock' WHERE id_produk_reg = '$id_produk'";
            $query_stock = mysqli_query($connect, $update_stock);

            if($query_tmp && $query_stock) {
                // Commit transaksi
                $connect->commit();
                $_SESSION['info'] = "Disimpan";
                ?>
                    <script>window.location.href = "../detail-komplain-revisi-nonppn.php?id=<?php echo $id_komplain ?>"</script>
                <?php
            }

        }catch (Exception $e){
            // Rollback transaksi jika terjadi exception
            $connect->rollback();
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
                    window.location.href = "../list-tagihan.php";
                });
                });
            </script>
            <?php
        }
        
    } else if (isset($_POST['hapus-produk-tmp'])){
        $id_tmp = $_POST['id_tmp'];
        $id_komplain = $_POST['id_komplain'];
        $delete_data = mysqli_query($connect, "DELETE FROM tmp_produk_komplain WHERE id_tmp = '$id_tmp'");
        if($delete_data){
            $_SESSION['info'] = "Dihapus";
            header("Location:../detail-komplain-revisi-nonppn.php?id=$id_komplain");
        }
    } else if (isset($_POST['hapus-produk-refund'])){
        $id_tmp = $_POST['id_tmp'];
        $id_komplain = $_POST['id_komplain'];
        $uuid = uuid();
        $day = date('d');
        $month = date('m');
        $year = date('y');
        $id_produk_refund = "REFUND" . $year . "" . $month . "" . $uuid . "" . $day ;
        $created_by = $_SESSION['tiket_nama'];

        // Begin transaction
        mysqli_begin_transaction($connect);
        try {
            //Insert Data Refund
            $simpan_refund = mysqli_query($connect, "INSERT INTO produk_refund (id_produk_refund, id_komplain, id_tmp, created_by) VALUES ('$id_produk_refund', '$id_komplain', '$id_tmp', '$created_by')");

            //Hapus data refund
            $update_status = mysqli_query($connect, "UPDATE tmp_produk_komplain  SET status_br_refund = '1' WHERE id_tmp = '$id_tmp'");
            
            if (!$simpan_refund && !$update_status) {
                throw new Exception("Proses Gagal");
            }
            // Commit the transaction
            mysqli_commit($connect);
            // Redirect to the invoice page
            $_SESSION['info'] = "Direfund";
            header("Location:../detail-komplain-revisi-nonppn.php?id=$id_komplain");
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
                                window.location.href = "../detail-komplain-revisi-nonppn.php?id=<?php echo base64_encode($id_komplain) ?>";
                            });
                        });
                </script>
                <?php
        }
    } else if(isset($_GET['batal_refund'])){
        $id_tmp = base64_decode($_GET['batal_refund']);
        $id_komplain = $_GET['id_komplain'];
        $update_status = mysqli_query($connect, "UPDATE tmp_produk_komplain  SET status_br_refund = '0' WHERE id_tmp = '$id_tmp'");
        $delete_refund = mysqli_query($connect, "DELETE FROM produk_refund  WHERE id_tmp = '$id_tmp'");
        if($update_status){
            $_SESSION['info'] = "Dibatalkan";
            header("Location:../detail-komplain-revisi-nonppn.php?id=$id_komplain");
        }
    }


    function uuid() {
        $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);
    
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        return vsprintf('%s%s%s', str_split(bin2hex($data), 4));
    }


?>