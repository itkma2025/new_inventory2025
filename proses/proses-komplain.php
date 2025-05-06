<?php  
    include "../akses.php";
    if(isset($_POST['komplain-nonppn'])){
        $id_inv = $_POST['id_inv'];
        $kat_komplain = $_POST['kat_komplain'];
        $kondisi_pesanan = $_POST['kondisi_pesanan'];
        $retur = $_POST['retur'];
        $catatan = $_POST['catatan'];
        $tgl = $_POST['tgl'];
        $uuid = generate_uuid();
        $year = date('y');
        $year_komplain = date('Y');
        $day = date('d');
        $month = date('m');
        $id_komplain = "KMPLN" . $year . "". $month . "" . $uuid . "" . $day;
        $id_kondisi = "KNDSI" . $year . "". $month . "" . $uuid . "" . $day;
        $sql  = mysqli_query($connect, "SELECT max(no_komplain) as maxID, STR_TO_DATE(tgl_komplain, '%d/%m/%Y') AS tgl FROM inv_komplain WHERE YEAR(STR_TO_DATE(tgl_komplain, '%d/%m/%Y')) = '$year_komplain'");
        $data = mysqli_fetch_array($sql);
        $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $kode = $data['maxID'];
        $ket1 = "/CC/KMA/";
        $bln = $array_bln[date('n')];
        $ket2 = "/";
        $ket3 = date("Y");
        $urutkan = (int)substr($kode, 0, 3);
        $urutkan++;
        $no_komplain = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;
        
        if($retur == '1'){
            $refund = $_POST['refund'];
            $connect->begin_transaction();
            try{
                $query_update_inv = mysqli_query($connect, "UPDATE inv_nonppn SET status_transaksi = 'Komplain' WHERE id_inv_nonppn = '$id_inv'");

                $query_komplain = mysqli_query($connect, "INSERT INTO inv_komplain (id_komplain, id_inv, no_komplain, tgl_komplain) VALUES ('$id_komplain', '$id_inv', '$no_komplain', '$tgl')");

                $query_kondisi_komplain = mysqli_query($connect, "INSERT INTO komplain_kondisi (id_kondisi, id_komplain, kat_komplain, kondisi_pesanan, status_retur, status_refund, catatan) VALUES ('$id_kondisi', '$id_komplain', '$kat_komplain', '$kondisi_pesanan', '$retur', '$refund', '$catatan')");

                $query_tmp_ref = mysqli_query($connect, "   INSERT IGNORE INTO 
                                                                tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp, created_date)
                                                            SELECT
                                                                tpr.id_transaksi,
                                                                spk.id_inv,
                                                                tpr.id_produk,
                                                                tpr.nama_produk_spk,
                                                                tpr.harga,
                                                                tpr.qty,
                                                                tpr.disc,
                                                                tpr.total_harga,
                                                                1 as status_tmp,
                                                                tpr.created_date
                                                            FROM spk_reg AS spk
                                                            LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk 
                                                            WHERE spk.id_inv = '$id_inv'");

                if ( $query_update_inv && $query_komplain && $query_kondisi_komplain && $query_tmp_ref ) {
                    // Commit transaksi
                    $connect->commit();
                    ?>
                    <!-- Sweet Alert -->
                    <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                    <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Sukses",
                            text: "Data Berhasil Disimpan",
                            icon: "success",
                        }).then(function() {
                            window.location.href = "../invoice-reguler-diterima.php";
                        });
                        });
                    </script>
                    <?php
                }
            } catch (Exception $e) {
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
                        window.location.href = "../invoice-reguler-diterima.php";
                    });
                    });
                </script>
                <?php
            }
        } else {
            $connect->begin_transaction();
            try{
                $query_update_inv = mysqli_query($connect, "UPDATE inv_nonppn SET status_transaksi = 'Komplain' WHERE id_inv_nonppn = '$id_inv'");

                $query_komplain = mysqli_query($connect, "INSERT INTO inv_komplain (id_komplain, id_inv, no_komplain, tgl_komplain) VALUES ('$id_komplain', '$id_inv', '$no_komplain', '$tgl')");

                $query_kondisi_komplain = mysqli_query($connect, "INSERT INTO komplain_kondisi (id_kondisi, id_komplain, kat_komplain, kondisi_pesanan, status_retur, catatan) VALUES ('$id_kondisi', '$id_komplain', '$kat_komplain', '$kondisi_pesanan', '$retur', '$catatan')");

                $query_tmp_ref = mysqli_query($connect, "   INSERT IGNORE INTO 
                                                                tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp, created_date)
                                                            SELECT
                                                                tpr.id_transaksi,
                                                                spk.id_inv,
                                                                tpr.id_produk,
                                                                tpr.nama_produk_spk,
                                                                tpr.harga,
                                                                tpr.qty,
                                                                tpr.disc,
                                                                tpr.total_harga,
                                                                1 as status_tmp,
                                                                tpr.created_date
                                                            FROM spk_reg AS spk
                                                            LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk 
                                                            WHERE spk.id_inv = '$id_inv'");

                if ( $query_update_inv && $query_komplain && $query_kondisi_komplain && $query_tmp_ref ) {
                    // Commit transaksi
                    $connect->commit();
                    ?>
                    <!-- Sweet Alert -->
                    <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                    <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Sukses",
                            text: "Data Berhasil Disimpan",
                            icon: "success",
                        }).then(function() {
                            window.location.href = "../invoice-reguler-diterima.php";
                        });
                        });
                    </script>
                    <?php
                }
            } catch (Exception $e) {
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
                        window.location.href = "../invoice-reguler-diterima.php";
                    });
                    });
                </script>
                <?php
            }
        }
    } else if(isset($_POST['komplain-ppn'])) {
        $id_inv = $_POST['id_inv'];
        $kat_komplain = $_POST['kat_komplain'];
        $kondisi_pesanan = $_POST['kondisi_pesanan'];
        $retur = $_POST['retur'];
        $catatan = $_POST['catatan'];
        $tgl = $_POST['tgl'];
        $uuid = generate_uuid();
        $year = date('y');
        $year_komplain = date('Y');
        $day = date('d');
        $month = date('m');
        $id_komplain = "KMPLN" . $year . "". $month . "" . $uuid . "" . $day;
        $id_kondisi = "KNDSI" . $year . "". $month . "" . $uuid . "" . $day;
        $sql  = mysqli_query($connect, "SELECT max(no_komplain) as maxID, STR_TO_DATE(tgl_komplain, '%d/%m/%Y') AS tgl FROM inv_komplain WHERE YEAR(STR_TO_DATE(tgl_komplain, '%d/%m/%Y')) = '$year_komplain'");
        $data = mysqli_fetch_array($sql);
        $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $kode = $data['maxID'];
        $ket1 = "/CC/KMA/";
        $bln = $array_bln[date('n')];
        $ket2 = "/";
        $ket3 = date("Y");
        $urutkan = (int)substr($kode, 0, 3);
        $urutkan++;
        $no_komplain = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;
        if($retur == '1'){
            $refund = $_POST['refund'];
            $connect->begin_transaction();
            try{
                $query_update_inv = mysqli_query($connect, "UPDATE inv_ppn SET status_transaksi = 'Komplain' WHERE id_inv_ppn = '$id_inv'");

                $query_komplain = mysqli_query($connect, "INSERT INTO inv_komplain (id_komplain, id_inv, no_komplain, tgl_komplain) VALUES ('$id_komplain', '$id_inv', '$no_komplain', '$tgl')");

                $query_kondisi_komplain = mysqli_query($connect, "INSERT INTO 
                komplain_kondisi (id_kondisi, id_komplain, kat_komplain, kondisi_pesanan, status_retur, status_refund, catatan) VALUES ('$id_kondisi', '$id_komplain', '$kat_komplain', '$kondisi_pesanan', '$retur', '$refund', '$catatan')");

                $query_tmp_ref = mysqli_query($connect, "   INSERT IGNORE INTO 
                                                                tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp, created_date)
                                                            SELECT
                                                                tpr.id_transaksi,
                                                                spk.id_inv,
                                                                tpr.id_produk,
                                                                tpr.nama_produk_spk,
                                                                tpr.harga,
                                                                tpr.qty,
                                                                tpr.disc,
                                                                tpr.total_harga,
                                                                1 as status_tmp,
                                                                tpr.created_date
                                                            FROM spk_reg AS spk
                                                            LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk 
                                                            WHERE spk.id_inv = '$id_inv'");

                if ( $query_update_inv && $query_komplain && $query_kondisi_komplain && $query_tmp_ref ) {
                    // Commit transaksi
                    $connect->commit();
                    ?>
                    <!-- Sweet Alert -->
                    <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                    <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Sukses",
                            text: "Data Berhasil Disimpan",
                            icon: "success",
                        }).then(function() {
                            window.location.href = "../invoice-reguler-diterima.php";
                        });
                        });
                    </script>
                    <?php
                }
            } catch (Exception $e) {
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
                        window.location.href = "../invoice-reguler-diterima.php";
                    });
                    });
                </script>
                <?php
            }
        } else {
            $connect->begin_transaction();
            try{
                $query_update_inv = mysqli_query($connect, "UPDATE inv_ppn SET status_transaksi = 'Komplain' WHERE id_inv_ppn = '$id_inv'");

                $query_komplain = mysqli_query($connect, "INSERT INTO inv_komplain (id_komplain, id_inv, no_komplain, tgl_komplain) VALUES ('$id_komplain', '$id_inv', '$no_komplain', '$tgl')");

                $query_kondisi_komplain = mysqli_query($connect, "INSERT INTO komplain_kondisi (id_kondisi, id_komplain, kat_komplain, kondisi_pesanan, status_retur, catatan) VALUES ('$id_kondisi', '$id_komplain', '$kat_komplain', '$kondisi_pesanan', '$retur', '$catatan')");

                $query_tmp_ref = mysqli_query($connect, "   INSERT IGNORE INTO 
                                                                tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp, created_date)
                                                            SELECT
                                                                tpr.id_transaksi,
                                                                spk.id_inv,
                                                                tpr.id_produk,
                                                                tpr.nama_produk_spk,
                                                                tpr.harga,
                                                                tpr.qty,
                                                                tpr.disc,
                                                                tpr.total_harga,
                                                                1 as status_tmp,
                                                                tpr.created_date
                                                            FROM spk_reg AS spk
                                                            LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk 
                                                            WHERE spk.id_inv = '$id_inv'");

                if ( $query_update_inv && $query_komplain && $query_kondisi_komplain && $query_tmp_ref ) {
                    // Commit transaksi
                    $connect->commit();
                    ?>
                    <!-- Sweet Alert -->
                    <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                    <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Sukses",
                            text: "Data Berhasil Disimpan",
                            icon: "success",
                        }).then(function() {
                            window.location.href = "../invoice-reguler-diterima.php";
                        });
                        });
                    </script>
                    <?php
                }
            } catch (Exception $e) {
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
                        window.location.href = "../invoice-reguler-diterima.php";
                    });
                    });
                </script>
                <?php
            }
        }
    } else if(isset($_POST['komplain-bum'])){
        $id_inv = $_POST['id_inv'];
        $kat_komplain = $_POST['kat_komplain'];
        $kondisi_pesanan = $_POST['kondisi_pesanan'];
        $retur = $_POST['retur'];
        $catatan = $_POST['catatan'];
        $tgl = $_POST['tgl'];
        $uuid = generate_uuid();
        $year = date('y');
        $day = date('d');
        $month = date('m');
        $year_komplain = date('Y');
        $id_komplain = "KMPLN" . $year . "". $month . "" . $uuid . "" . $day;
        $id_kondisi = "KNDSI" . $year . "". $month . "" . $uuid . "" . $day;
        $sql  = mysqli_query($connect, "SELECT max(no_komplain) as maxID, STR_TO_DATE(tgl_komplain, '%d/%m/%Y') AS tgl FROM inv_komplain WHERE YEAR(STR_TO_DATE(tgl_komplain, '%d/%m/%Y')) = '$year_komplain'");
        $data = mysqli_fetch_array($sql);
        $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $kode = $data['maxID'];
        $ket1 = "/CC/KMA/";
        $bln = $array_bln[date('n')];
        $ket2 = "/";
        $ket3 = date("Y");
        $urutkan = (int)substr($kode, 0, 3);
        $urutkan++;
        $no_komplain = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;
        if($retur == '1'){
            $refund = $_POST['refund'];
            $connect->begin_transaction();
            try{
                $query_update_inv = mysqli_query($connect, "UPDATE inv_bum SET status_transaksi = 'Komplain' WHERE id_inv_bum = '$id_inv'");

                $query_komplain = mysqli_query($connect, "INSERT INTO inv_komplain (id_komplain, id_inv, no_komplain, tgl_komplain) VALUES ('$id_komplain', '$id_inv', '$no_komplain', '$tgl')");

                $query_kondisi_komplain = mysqli_query($connect, "INSERT INTO komplain_kondisi (id_kondisi, id_komplain, kat_komplain, kondisi_pesanan, status_retur, status_refund, catatan) VALUES ('$id_kondisi', '$id_komplain', '$kat_komplain', '$kondisi_pesanan', '$retur', '$refund', '$catatan')");

                $query_tmp_ref = mysqli_query($connect, "   INSERT IGNORE INTO 
                                                                tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp, created_date)
                                                            SELECT
                                                                tpr.id_transaksi,
                                                                spk.id_inv,
                                                                tpr.id_produk,
                                                                tpr.nama_produk_spk,
                                                                tpr.harga,
                                                                tpr.qty,
                                                                tpr.disc,
                                                                tpr.total_harga,
                                                                1 as status_tmp,
                                                                tpr.created_date
                                                            FROM spk_reg AS spk
                                                            LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk 
                                                            WHERE spk.id_inv = '$id_inv'");

                if ( $query_update_inv && $query_komplain && $query_kondisi_komplain && $query_tmp_ref ) {
                    // Commit transaksi
                    $connect->commit();
                    ?>
                    <!-- Sweet Alert -->
                    <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                    <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Sukses",
                            text: "Data Berhasil Disimpan",
                            icon: "success",
                        }).then(function() {
                            window.location.href = "../invoice-reguler-diterima.php";
                        });
                        });
                    </script>
                    <?php
                }
            } catch (Exception $e) {
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
                        window.location.href = "../invoice-reguler-diterima.php";
                    });
                    });
                </script>
                <?php
            }
        } else {
            $connect->begin_transaction();
            try{
                $query_update_inv = mysqli_query($connect, "UPDATE inv_bum SET status_transaksi = 'Komplain' WHERE id_inv_bum = '$id_inv'");
            
                $query_update_status = mysqli_query($connect, "UPDATE status_kirim SET jenis_penerima = 'Customer' WHERE id_inv = '$id_inv'");

                $query_komplain = mysqli_query($connect, "INSERT INTO inv_komplain (id_komplain, id_inv, no_komplain, tgl_komplain) VALUES ('$id_komplain', '$id_inv', '$no_komplain', '$tgl')");

                $query_kondisi_komplain = mysqli_query($connect, "INSERT INTO komplain_kondisi (id_kondisi, id_komplain, kat_komplain, kondisi_pesanan, status_retur, catatan) VALUES ('$id_kondisi', '$id_komplain', '$kat_komplain', '$kondisi_pesanan', '$retur', '$catatan')");

                $query_tmp_ref = mysqli_query($connect, "   INSERT IGNORE INTO 
                                                                tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp, created_date)
                                                            SELECT
                                                                tpr.id_transaksi,
                                                                spk.id_inv,
                                                                tpr.id_produk,
                                                                tpr.nama_produk_spk,
                                                                tpr.harga,
                                                                tpr.qty,
                                                                tpr.disc,
                                                                tpr.total_harga,
                                                                1 as status_tmp,
                                                                tpr.created_date
                                                            FROM spk_reg AS spk
                                                            LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk 
                                                            WHERE spk.id_inv = '$id_inv'");

                if ( $query_update_inv && $query_komplain && $query_kondisi_komplain && $query_tmp_ref ) {
                    // Commit transaksi
                    $connect->commit();
                    ?>
                    <!-- Sweet Alert -->
                    <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                    <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Sukses",
                            text: "Data Berhasil Disimpan",
                            icon: "success",
                        }).then(function() {
                            window.location.href = "../invoice-reguler-diterima.php";
                        });
                        });
                    </script>
                    <?php
                }
            } catch (Exception $e) {
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
                        window.location.href = "../invoice-reguler-diterima.php";
                    });
                    });
                </script>
                <?php
            }
        }
    }

     // generate UUID
     function generate_uuid()
     {
         return sprintf(
             '%04x%04x%04x',
             mt_rand(0, 0xffff),
             mt_rand(0, 0xffff),
             mt_rand(0, 0xffff),
             mt_rand(0, 0x0fff) | 0x4000,
             mt_rand(0, 0x3fff) | 0x8000,
             mt_rand(0, 0xffff),
             mt_rand(0, 0xffff),
             mt_rand(0, 0xffff)
         );
     }
?>