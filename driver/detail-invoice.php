<?php
require_once "../akses.php";
$page = 'list-inv';
include "../function/class-spk.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link rel="stylesheet" href="../assets/css/camera.css">
    <?php include "page/head.php" ?>

    <style type="text/css">
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .col-5 {
            flex: 0 0 50%; /* Gunakan 50% dari lebar kolom saat tampilan mobile */
            max-width: 50%;
        }

        .col-7 {
            flex: 0 0 50%; /* Gunakan 50% dari lebar kolom saat tampilan mobile */
            max-width: 50%;
        }

        p {
            white-space: nowrap; /* Mencegah teks berjalan ke baris baru */
            overflow: hidden;
            text-overflow: ellipsis; /* Menggantikan teks yang terpotong dengan elipsis (...) jika terlalu panjang */
        }
        
        #Diterima{
            cursor: pointer;
        }
    
        @media only screen and (max-width: 500px) {
            body {
                font-size: 14px;
            }
        }
    </style>
</head>

<body>
    <!-- nav header -->
    <?php include "page/nav-header.php"?>
    <!-- end nav header -->

    <!-- sidebar -->
    <?php include "page/sidebar.php";?>
    <!-- end sidebar -->

    <main id="main" class="main">
        <section>
            <?php
                require_once "../function/function-enkripsi.php";
                $key = "Driver2024?";
                $id_inv = $_GET['id'];
                $id_inv_decrypt = decrypt($id_inv, $key);
                $jenis_inv = $_GET['jenis_inv'];
                if($jenis_inv == "nonppn"){
                    require_once "query/detail-inv-nonppn.php";
                    $data_detail = mysqli_fetch_array($detail);
                    $label = "DETAIL INVOICE NONPPN";
                } else if($jenis_inv == "ppn"){
                    require_once "query/detail-inv-ppn.php";
                    $data_detail = mysqli_fetch_array($detail);
                    $label = "DETAIL INVOICE PPN";
                } else if($jenis_inv == "bum"){
                    require_once "query/detail-inv-bum.php";
                    $data_detail = mysqli_fetch_array($detail);
                    $label = "DETAIL INVOICE BUM";
                } else {
                    // Mengarahkan ke halaman 404
                    ?>
                        <script type='text/javascript'>
                            window.location.href = '../404.php';
                        </script>
                    <?php
                    exit();
                }
            ?>
            <div class="card shadow p-2">
                <div class="card-header text-center">
                    <h5>
                        <strong><?php echo $label ?></strong>
                    </h5>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div class="card-body p-3 border">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. SPK</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php  
                                        $no = 1;
                                        while($detail_spk_trim = mysqli_fetch_array($spk_trim)){
                                            $no_spk = $detail_spk_trim['no_spk'];
                                            $no_po = $detail_spk_trim['no_po'];
                                            $tgl_pesanan = $detail_spk_trim['tgl_pesanan'];
                                    ?>
                                        <p><?php echo $no; ?>. (<?php echo $no_spk ?> - <?php echo $no_po ?> - <?php echo $tgl_pesanan ?>)</p>
                                    <?php $no++; ?>
                                    <?php } ?>
                                    
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_detail['no_inv'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_detail['tgl_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data_detail['tgl_tempo'] != '') {
                                        echo '
                                        <div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Tgl. Tempo</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data_detail['tgl_tempo'] . '
                                            </div>
                                        </div>';
                                    }
                            ?>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Jenis Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_detail['kategori_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data_detail['kategori_inv'] == 'Spesial Diskon') {
                                    echo '<div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Spesial Diskon</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data_detail['sp_disc'] . ' %
                                            </div>
                                        </div>';
                                }
                            ?>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Order Via</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_detail['order_by'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card-body p-3 border" style="min-height: 234px;">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Sales</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_detail['nama_sales'] ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_detail['nama_cs'] ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan Inv</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_detail['cs_inv'] ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Alamat</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_detail['alamat'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data_detail['note_inv'] != '') {
                                        echo '
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Note Invoice</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data_detail['note_inv'] . '
                                            </div>
                                        </div>';
                                    }
                            ?>
                            <?php
                                if ($data_detail['ongkir'] != 0) {
                                    echo '<div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Ongkir</p>
                                                <p style="float: right;"> :</p>
                                            </div>
                                            <div class="col-7">
                                                ' . number_format($data_detail['ongkir']) . '
                                            </div>
                                        </div>';
                                }
                            ?>

                            <?php
                                if ($data_detail['jenis_pengiriman'] == 'Ekspedisi') {
                                    ?> 
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">Jenis Pengiriman</p>
                                            <p style="float: right;"> :</p>
                                        </div>
                                        <div class="col-7">
                                            <?php echo $data_detail_ekspedisi_kirim['jenis_penerima'] ?> (<?php echo $data_detail_ekspedisi_kirim['nama_ekspedisi'] ?>)
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Jenis Pengiriman</p>
                                                <p style="float: right;"> :</p>
                                            </div>
                                            <div class="col-7">
                                                <?php echo $data_detail['jenis_pengiriman'] ?> (<?php echo $data_detail['nama_driver'] ?>)
                                            </div>
                                        </div>
                                    <?php
                                        if(!empty($data_detail_status_kirim['jenis_penerima'])){
                                            ?>
                                                <div class="row">
                                                    <div class="col-5">
                                                        <p style="float: left;">Jenis Penerima</p>
                                                        <p style="float: right;"> :</p>
                                                    </div>
                                                    <div class="col-7">
                                                        <?php  
                                                            if($data_detail_status_kirim['jenis_penerima'] == 'Ekspedisi'){
                                                                ?>
                                                                    <?php echo $data_detail_status_kirim['jenis_penerima'] ?> (<?php echo $data_detail_ekspedisi_kirim['nama_ekspedisi'] ?>)
                                                                <?php
                                                            } else {
                                                                ?>
                                                                    <?php echo $data_detail_status_kirim['jenis_penerima'] ?> (<?php echo $data_detail_penerima['nama_penerima'] ?>)
                                                                <?php
                                                            }
                                                        ?>
                                                        
                                                    </div>
                                                </div>
                                            <?php
                                        }
                                }
                            ?>
                            <?php
                                if (!empty($data_detail_status_kirim['dikirim_oleh'])) {
                                    echo '<div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Dikirim Oleh</p>
                                                <p style="float: right;"> :</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data_detail_status_kirim['dikirim_oleh'] . '
                                            </div>
                                        </div>';
                                    }
                            ?>
                            <?php
                                if (!empty($data_detail_status_kirim['penanggung_jawab'])) {
                                    echo '  <div class="row">
                                                <div class="col-5">
                                                    <p style="float: left;">PJ. Paket Kirim</p>
                                                    <p style="float: right;"> :</p>
                                                </div>
                                                <div class="col-7">
                                                    ' . $data_detail_status_kirim['penanggung_jawab'] . '
                                                </div>
                                            </div>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tampil data -->
            <div class="card shadow">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <div class="text-start mb-3">
                            <a href="list-invoice.php" class="btn btn-warning btn-detail mb-2">
                                <i class="bi bi-arrow-left"></i>
                                Halaman Sebelumnya
                            </a>
                            <?php  
                                $sql_bukti_terima = $connect->query("SELECT jenis_reject FROM inv_bukti_terima WHERE id_inv = '$id_inv_decrypt'");
                                $data_bukti_terima = mysqli_fetch_assoc($sql_bukti_terima);
                                $jenis_reject = (isset($data_bukti_terima['jenis_reject']) && $data_bukti_terima['jenis_reject'] === '1') ? '1' : '';
                                if($jenis_reject == '1'){
                                    ?>
                                        <a id="btnDiterima" href="form-reupload.php?id=<?php echo $id_inv ?>&&jenis_inv=<?php echo $jenis_inv ?>" class="btn btn-secondary mb-2"><i class="bi bi-send"></i> Reupload Bukti Kirim</a>
                                    <?php
                                } else if ($jenis_reject == '2'){
                                    ?>
                                        <a id="btnDiterima" href="form-diterima.php?id=<?php echo $id_inv ?>&&jenis_inv=<?php echo $jenis_inv ?>" class="btn btn-secondary mb-2"><i class="bi bi-send"></i> Diterima</a>
                                    <?php
                                } else {
                                    ?>
                                        <a id="btnDiterima" href="form-diterima.php?id=<?php echo $id_inv ?>&&jenis_inv=<?php echo $jenis_inv ?>" class="btn btn-secondary mb-2"><i class="bi bi-send"></i> Diterima</a>
                                    <?php
                                }
                            ?>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="text-white" style="background-color: #051683;">
                                    <th class="text-center text-nowrap p-3" style="width:20px">No</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">No. SPK</th>
                                    <th class="text-center text-nowrap p-3" style="width:200px">Nama Produk</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Satuan</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Merk</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Harga</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Diskon</th>
                                    <th class="text-center text-nowrap p-3" style="width:80px">Qty Order</th>
                                    <th class="text-center text-nowrap p-3" style="width:80px">Total</th>
                                </tr>
                            </thead>  
                            <tbody>
                                <?php  
                                    $no = 1;
                                    while($data_produk = mysqli_fetch_array($produk)){
                                        $id_produk = $data_produk['id_produk'];
                                        $id_produk_substr = substr($id_produk, 0, 2);
                                ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data_produk['no_spk']; ?></td>
                                    <td class="text-nowrap"><?php echo $data_produk['nama_produk']; ?></td>
                                    <td class="text-center text-nowrap"><?php echo ($id_produk_substr == 'BR') ? $data_produk['satuan'] : "Set"; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data_produk['nama_merk']; ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_produk['harga']) ?></td>
                                    <td class="text-end"></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_produk['qty']) ?></td>
                                    <td class="text-end text-nowrap">
                                        <?php echo number_format($data_produk['total_harga']) ?>
                                    </td>
                                </tr>
                                <?php $no++ ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- End #main -->

    <!-- Footer -->
    <?php include "page/footer.php"?>
    <!-- End Footer -->

    <?php include "page/script.php"?>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-up-short"></i>
    </a>
</body>

</html>
<!-- Wajib aktifkan lokasi -->
<script>
    document.getElementById('btnDiterima').addEventListener('click', function(event) {
        event.preventDefault(); // Mencegah pengalihan halaman

        getLocation(function(success, lat, lon) {
            if (success) {
                // Menampilkan pesan loading selama 2 detik setelah mendapatkan lokasi
                Swal.fire({ 
                    icon: 'info', 
                    title: 'Memproses...', 
                    text: 'Meminta akses lokasi, mohon tunggu.', 
                    showConfirmButton: false, 
                    allowOutsideClick: false 
                });

                // Setel timeout 2 detik sebelum melanjutkan ke langkah berikutnya
                setTimeout(function() {
                    // Setelah 2 detik, lakukan pengalihan halaman dan kirim data lokasi
                    console.log("Latitude: " + lat);
                    console.log("Longitude: " + lon);

                    // Mengirim data ke PHP menggunakan AJAX
                    var xhr = new XMLHttpRequest();
                    xhr.open("POST", "proses-location.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.send("latitude=" + lat + "&longitude=" + lon);

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            console.log(xhr.responseText);
                            window.location.href = event.target.href; // Pengalihan halaman
                        }
                    };
                }, 1000); // Delay selama 1 detik sebelum melanjutkan
            }
        });

        function getLocation(callback) {
            if (navigator.geolocation) {
                // Menampilkan pesan loading saat meminta akses lokasi
                Swal.fire({ 
                    icon: 'info', 
                    title: 'Memproses...', 
                    text: 'Meminta akses lokasi, mohon tunggu.', 
                    showConfirmButton: false, 
                    allowOutsideClick: false 
                });

                // Set timeout lebih ketat, misalnya 3 detik
                var timeout = setTimeout(function() {
                    Swal.close(); // Tutup pesan loading jika timeout
                    Swal.fire({
                        icon: 'error',
                        title: 'Timeout Akses Lokasi',
                        text: 'Permintaan lokasi melebihi batas waktu. Silakan coba lagi.'
                    });
                    callback(false, null, null); // Gagal karena timeout
                }, 3000); // 3 detik timeout

                // Coba untuk mendapatkan lokasi dengan lebih cepat
                navigator.geolocation.getCurrentPosition(function(position) {
                    clearTimeout(timeout); // Hentikan timeout jika lokasi berhasil diambil
                    Swal.close(); // Tutup pesan loading setelah mendapatkan lokasi
                    var lat = position.coords.latitude;
                    var lon = position.coords.longitude;

                    // Memanggil callback dengan sukses dan mengirimkan lat, lon
                    callback(true, lat, lon);
                }, function(error) {
                    clearTimeout(timeout); // Hentikan timeout jika terjadi kesalahan
                    Swal.close(); // Tutup pesan loading jika terjadi kesalahan
                    showError(error);
                    callback(false, null, null); // Gagal mengambil lokasi
                }, {
                    enableHighAccuracy: false,  // Matikan akurasi tinggi untuk mempercepat
                    timeout: 3000,  // Set timeout lebih rendah (misal 3 detik)
                    maximumAge: 0  // Jangan menggunakan lokasi yang sudah kadaluarsa
                });
            } else {
                Swal.close(); // Tutup pesan loading jika geolocation tidak didukung
                Swal.fire({
                    icon: 'error',
                    title: 'Geolocation Tidak Didukung',
                    text: 'Geolocation tidak didukung oleh browser ini.'
                });
                callback(false, null, null); // Gagal karena geolocation tidak didukung
            }
        }

        function showError(error) {
            let errorMessage = '';

            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = 'User menolak permintaan untuk geolokasi. Pastikan layanan lokasi diaktifkan dan coba lagi.';
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = 'Informasi lokasi tidak tersedia. Pastikan GPS dan layanan lokasi diaktifkan.';
                    break;
                case error.TIMEOUT:
                    errorMessage = 'Permintaan lokasi melebihi batas waktu. Silakan coba lagi.';
                    break;
                case error.UNKNOWN_ERROR:
                    errorMessage = 'Terjadi kesalahan tak terduga. Periksa pengaturan lokasi Anda dan coba lagi.';
                    break;
            }

            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Akses Lokasi',
                text: errorMessage
            });
        }
    });
</script>



