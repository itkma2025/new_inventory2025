<?php
require_once "akses.php"; 
$page = 'produk';
$page2 = 'data-produk-set-marwa';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <?php include "page/head.php"; ?>
    <script src="assets/js/dom-to-img.js"></script>
    <style>
        body{
            margin-left: 80px;
            margin-top: 50px;
        }
        .vertical-text {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 30px;
            word-wrap: break-word;
            
        }

        .img-thumbnail {
            border: none;
        }
        #content {
            margin: 0;
            padding: 0px;
            background-color: white;
            border: 4px solid #A9A9A9;
            width: 832px; /* Set width to 832px, equivalent to 22cm */
            height: auto;
            image-rendering: crisp-edges;
            box-sizing: border-box;
            transform: scale(1);
        }

        #content2 {
            margin: 0;
            padding: 0;
            background-color: white; /* Atur latar belakang elemen menjadi putih */
            border: 4px solid #A9A9A9;
        }
    </style>
</head>

<body>
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
    <!-- end sidebar -->
    <?php  
        $display = "";
        if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan" || $role == "Kepala Gudang") {
            $display = "";
        } else {
            $display = "d-none";
            ?>
                <!-- Sweet Alert -->
                <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                <script src="assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Maaf Anda Tidak Memiliki Akses Fitur Ini",
                        icon: "error",
                    }).then(function() {
                        window.location.href = "data-produk-set-marwa.php";
                    });
                    });
                </script>
            <?php
        }
    ?>
    <main id="main" class="main  <?php echo $display ?>">
        <section class="section dashboard">
            <?php  
                include 'assets/Qrcode/qrlib.php';
                date_default_timezone_set('Asia/Jakarta');
                $today = date('d/m/Y, H:i:s');
                $key = "KM@2024?SET";
                $id = $_GET['id'];
                $id_produk = decrypt($id, $key);
                
                $sql = "SELECT 
                            tpsm.nama_set_marwa, 
                            qr.id_set_qr,
                            qr.url_qr,
                            qr.qr_img,
                            kp.no_izin_edar,
                            mr.nama_merk
                        FROM tb_produk_set_marwa AS tpsm
                        LEFT JOIN qr_link_set_reg qr ON (tpsm.id_set_marwa = qr.id_set_qr)
                        LEFT JOIN tb_kat_produk kp ON (tpsm.id_kat_produk = kp.id_kat_produk)
                        LEFT JOIN tb_merk mr ON (tpsm.id_merk = mr.id_merk)
                        WHERE qr.id_set_qr = '$id_produk'";
                $query = mysqli_query($connect, $sql);
                $data = mysqli_fetch_array($query);
                $cek_data = mysqli_num_rows($query);
            ?>
            <?php  
                if ($cek_data > 0) {
                    $id_set_qr = $data['id_set_qr'];
                    $id_produk_encode = base64_encode($id_set_qr);
                    $img = $data['qr_img'];
                    $no_img = $data["qr_img"] == "" ? "gambar/QRcode-set-marwa/no-image.png" : "gambar/QRcode/$img";
                    ?>
                        <div id="content">
                            <div class="row">
                                <div class="col-10">     
                                    <p class="p-2" style="font-weight: bold; font-size: 26px; margin-bottom: 0px !important;"><?php echo $data['nama_set_marwa']; ?> (<?php echo $data['nama_merk']; ?>) </p>
                                    <div class="row">
                                        <div class="ms-2" style="font-size: 20px;">Kemenkes RI AKL : <?php echo $data['no_izin_edar'] ?></div>
                                    </div>  
                                </div>
                                <div class="col-2 text-end">
                                    <img src="gambar/QRcode-set-marwa/<?php echo $img ?>" style="width: 115px; height: 125px" >
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="text-center" style="max-width: 21cm;">
                            <button onclick="convertToImage()" class="btn btn-primary">Download</button>
                        </div>
                        <script>
                            function convertToImage() {
                                var contentElement = document.getElementById("content");

                                domtoimage.toPng(contentElement)
                                    .then(function (dataUrl) {
                                        var link = document.createElement("a");
                                        link.href = dataUrl;
                                        link.download = "<?php echo $data['nama_set_marwa']; ?>.png";
                                        link.click();
                                    })
                                    .catch(function (error) {
                                        console.error('Error:', error);
                                    });
                            }
                        </script>
                        <br>
                        <div id="content2" style="max-width: 21cm;">
                            <div class="row">
                                <div class="col-9">
                                    <p class="p-2 text-center" style="font-weight: bold; font-size: 26px; margin-bottom: 0px !important; background-color: #d3d3d3;"><?php echo $data['nama_merk']; ?> </p> 
                                    <p class="p-2" style="font-weight: bold; font-size: 26px; margin-bottom: 0px !important;"><?php echo $data['nama_set_marwa']; ?></p>
                                    <div class="row p-1">
                                        <div class="col-9 ms-2" style="font-size: 18px;">Kemenkes RI AKL : <?php echo $data['no_izin_edar'] ?></div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <img src="gambar/QRcode-set-marwa/<?php echo $img ?>" style="width:175px; height:175px" >
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="text-center" style="max-width: 21cm;">
                            <button onclick="convertToImage2()" class="btn btn-primary">Download</button>
                        </div>
                        <script>
                            function convertToImage2() {
                                var contentElement = document.getElementById("content2");

                                domtoimage.toPng(contentElement)
                                    .then(function (dataUrl) {
                                        var link = document.createElement("a");
                                        link.href = dataUrl;
                                        link.download = "<?php echo $data['nama_set_marwa']; ?>.png";
                                        link.click();
                                    })
                                    .catch(function (error) {
                                        console.error('Error:', error);
                                    });
                            }
                        </script>
                    <?php
                } else {
                    ?>
                        <script>
                            // Mengarahkan pengguna ke halaman 404.php
                            window.location.replace("404.php");
                        </script>
                    <?php
                }
            
            ?>
        </section>
    </main><!-- End #main -->

    <!-- Footer -->
    <div class="<?php echo $display ?>">
        <?php include "page/footer.php" ?>
    </div>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>