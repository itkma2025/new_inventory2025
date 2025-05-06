<?php  
    require_once "../../akses.php";

    if(isset($_GET['hapus_tmp'])){
        $id_tmp = base64_decode($_GET['hapus_tmp']);
        $id_komplain = $_GET['id_komplain'];
        $delete_data = mysqli_query($connect, "DELETE FROM tmp_produk_komplain WHERE id_tmp = '$id_tmp'");
        if($delete_data){
            ?>
                <!-- Sweet Alert -->
                <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Success",
                        text: "Data berhasil dihapus",
                        icon: "success",
                    }).then(function() {
                        window.location.href = "../detail-komplain-bum-revisi.php?id=<?php echo $id_komplain ?>";
                    });
                    });
                </script>
            <?php
        }

    } else if (isset($_POST['simpan-tmp'])){
        // Mendapatkan data yang dikirimkan melalui form
        $id_komplain = base64_encode($_POST['id_komplain']);
        $id_tmp = $_POST['id_tmp']; // Mengambil ID transaksi dari form
        $nama_produk = $_POST['nama_produk'];
        $harga = $_POST['harga']; // Mengambil Nilai Harga
        $qty = $_POST['qty_tmp']; // Mengambil nilai qty yang diperbarui
        $disc = $_POST['disc'];
        for ($i = 0; $i < count($id_tmp); $i++) {
            $id = $id_tmp[$i];
            $nama_produk_array = $nama_produk[$i];
            $hrg = str_replace(',', '', $harga[$i]); // Menghapus tanda ribuan (,)
            $hrg = intval($hrg); // Mengubah string harga menjadi integer
            $newQtyInt = str_replace(',', '', $qty[$i]); // Menghapus tanda ribuan (,)
            $newQtyInt = intval($newQtyInt); // Mengubah string harga menjadi integer
            $total_harga = $hrg * $newQtyInt;
            $disc_array = $disc[$i];
            $array_disc = $disc_array / 100;
            $total_harga_disc = $total_harga * (1 - $array_disc);

            // Lakukan proses penyimpanan data qty ke dalam database sesuai dengan ID transaksi
            // Contoh: Lakukan kueri SQL untuk memperbarui data qty dalam tabel transaksi menggunakan ID transaksi
            $sql = "UPDATE tmp_produk_komplain SET nama_produk = '$nama_produk_array', harga = $hrg, qty = '$newQtyInt', disc = '$disc_array', total_harga = '$total_harga_disc', status_tmp = '1' WHERE id_tmp = '$id'";
            $query = mysqli_query($connect, $sql);

            if($query){
                ?>
                    <!-- Sweet Alert -->
                    <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                    <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Success",
                            text: "Data berhasil disimpan",
                            icon: "success",
                        }).then(function() {
                            window.location.href = "../detail-komplain-bum-revisi.php?id=<?php echo $id_komplain ?>";
                        });
                        });
                    </script>
                <?php
             }
        }
    } else if (isset($_POST['ubah-data'])){
        //Mendapatkan data yang dikirimkan melalui form
        $id_komplain = $_POST['id_komplain'];
        $id_tmp = $_POST['id_tmp'];
        $nama_produk = $_POST['nama_produk'];
        $harga = $_POST['harga'];
        $hrg = str_replace(',', '', $harga); // Menghapus tanda ribuan (,)
        $hrg = intval($hrg); // Mengubah string harga menjadi integer
        $disc = $_POST['disc'];
        $disc_array = $_POST['disc'] / 100;
        $qty = str_replace(',', '', $_POST['qty']); // Menghapus tanda ribuan (,)
        $qty = intval($qty); // Mengubah string harga menjadi integer

        // kalulasi total harga
        $sub_harga = $hrg * $qty;
        $total_harga = $sub_harga * (1 - $disc_array);

        //Lakukan proses penyimpanan data qty ke dalam database sesuai dengan ID transaksi
        //Contoh: Lakukan kueri SQL untuk memperbarui data qty dalam tabel transaksi menggunakan ID transaksi
        $sql = "UPDATE tmp_produk_komplain SET nama_produk = '$nama_produk', harga = $hrg, qty = '$qty', disc = '$disc', total_harga = '$total_harga', status_tmp = '1' WHERE id_tmp = '$id_tmp'";
        $query = mysqli_query($connect, $sql);

        if($query){
            ?>
                <!-- Sweet Alert -->
                <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Success",
                        text: "Data berhasil disimpan",
                        icon: "success",
                    }).then(function() {
                        window.location.href = "../detail-komplain-bum-revisi.php?id=<?php echo $id_komplain ?>";
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
            ?>
                <!-- Sweet Alert -->
                <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Success",
                        text: "Data berhasil dihapus",
                        icon: "success",
                    }).then(function() {
                        window.location.href = "../detail-komplain-bum-revisi.php?id=<?php echo base64_encode($id_komplain) ?>";
                    });
                    });
                </script>
            <?php
        }
    } else if (isset($_POST['hapus-produk-refund'])){
        $id_tmp = $_POST['id_tmp'];
        $id_komplain = $_POST['id_komplain'];
        $nominal = $_POST['total_harga'];
        $uuid = uuid();
        $day = date('d');
        $month = date('m');
        $year = date('y');
        $id_refund = "REFUND" . $year . "" . $month . "" . $uuid . "" . $day ;

        // Begin transaction
        mysqli_begin_transaction($connect);
        try {
            //Insert Data Refund
            $simpan_refund = mysqli_query($connect, "INSERT INTO inv_refund (id_refund, id_komplain, nominal) VALUES ('$id_refund', '$id_komplain', '$nominal')");

            //Hapus data refund
            $delete_data = mysqli_query($connect, "DELETE FROM tmp_produk_komplain WHERE id_tmp = '$id_tmp'");
            
            if (!$simpan_refund && !$delete_data) {
                throw new Exception("Proses Gagal");
            }
            // Commit the transaction
            mysqli_commit($connect);
            // Redirect to the invoice page
            ?>
            <!-- Sweet Alert -->
            <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
            <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire(
                        'Berhasil',
                        'Data Berhasil Direfund',
                        'success'
                    ).then(function() {
                        window.location.href = "../detail-komplain-bum-revisi.php?id=<?php echo base64_encode($id_komplain) ?>";
                    });
                });
            </script>
            <?php
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
                            window.location.href = "../detail-komplain-bum-revisi.php?id=<?php echo base64_encode($id_komplain) ?>";
                        });
                    });
               </script>
               <?php
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