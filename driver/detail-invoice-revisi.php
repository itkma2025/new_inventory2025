<?php
$page = 'list-inv';
include "akses.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Inventory KMA</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <?php include "page/head.php" ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
        <!-- SWEET ALERT -->
        <section>
            <div class="card shadow p-2" id="detail">
                <div class="card-header text-center">
                    <h5>
                        <strong>DETAIL INVOICE REVISI</strong>
                    </h5>
                </div>
                <?php
                    require_once "../function/function-enkripsi.php";
                    require_once "../function/uuid.php";
                    $day = date('d');
                    $month = date('m');
                    $year = date('Y');
                    $key = "Driver2024?";
                    $id_inv = decrypt($_GET['id'], $key);
                    $id_komplain = decrypt($_GET['id_komplain'], $key);
                    require_once "query/detail-invoice-revisi.php";
                    $data =  mysqli_fetch_array($detail);
                    $sp_disc = $data['sp_disc'];
                    $ongkir = $data['ongkir'];
                    $id_spk = $data['id_spk'];
                    $no_spk = $data['no_spk'];
                    $no_komplain = $data['no_komplain'];
                ?>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div class="card-body p-3 border">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. Pesanan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['tgl_pesanan'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. SPK</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php 
                                        $no = 1;
                                        while($data2 = mysqli_fetch_array($detail2)){
                                                $id_inv = $data2['id_inv'];
                                                $kat_inv = $data2['kategori_inv'];
                                                $id_cs = $data2['id_customer'];
                                                $tgl_pesanan = $data2['tgl_pesanan'];
                                                $no_spk = $data2['no_spk'];
                                                $no_po = $data2['no_po'];
                                            ?>
                                   
                                    <p>
                                        <?php echo $no ?>. (<?php echo $tgl_pesanan ?>) / <?php if (!empty($no_po)) { echo "(" . $no_po . ") /";} else {} ?>
                                        (<?php echo $no_spk ?>)
                                    </p>
                                    <?php $no++ ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php  
                                        $sql_inv_revisi = mysqli_query($connect, "SELECT id_inv, max(no_inv_revisi) AS no_inv_revisi FROM inv_revisi WHERE id_inv = '$id_inv'");
                                        $data_inv_revisi = mysqli_fetch_array($sql_inv_revisi);
                                    ?>
                                    <?php echo $data_inv_revisi['no_inv_revisi'] ?>
                                </div>
                            </div>
                            <?php
                               if ($data['no_po'] != '') {
                                    echo '
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">No. PO</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            ' . $data['no_po'] . '
                                        </div>
                                    </div>';
                                }
                            ?>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. Invoice</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['tgl_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data['tgl_tempo'] != '') {
                                        echo '
                                        <div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Tgl. Tempo</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data['tgl_tempo'] . '
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
                                    <?php echo $data['kategori_inv'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data['kategori_inv'] == 'Spesial Diskon') {
                                    echo '<div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Spesial Diskon</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data['sp_disc'] . ' %
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
                                    <?php echo $data['order_by'] ?>
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
                                    <?php echo $data['nama_sales'] ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['nama_cs'] ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan Inv</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['cs_inv'] ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <p style="float: left;">Alamat</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['alamat'] ?>
                                </div>
                            </div>
                            <?php
                                if ($data['note_inv'] != '') {
                                        echo '
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Note Invoice</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data['note_inv'] . '
                                            </div>
                                        </div>';
                                    }
                            ?>
                            <?php
                                if ($data['ongkir'] != 0) {
                                    echo '<div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Ongkir</p>
                                                <p style="float: right;"> :</p>
                                            </div>
                                            <div class="col-7">
                                                ' . number_format($data['ongkir']) . '
                                            </div>
                                        </div>';
                                }
                            ?>

                            <?php  
                                $status_kirim = mysqli_query($connect, "SELECT jenis_pengiriman, dikirim_ekspedisi, jenis_penerima, dikirim_driver, dikirim_oleh, penanggung_jawab FROM revisi_status_kirim WHERE id_komplain = '$id_komplain'");
                                $data_status_kirim = mysqli_fetch_array($status_kirim);
                                $jenis_pengiriman =  $data_status_kirim['jenis_pengiriman'];
                                $ekspedisi = $data_status_kirim['dikirim_ekspedisi'];
                                $driver = $data_status_kirim['dikirim_driver'];


                                $ekspedisi_kirim =  mysqli_query($connect, "SELECT sk.jenis_pengiriman, sk.dikirim_ekspedisi, sk.jenis_penerima, ex.nama_ekspedisi
                                                                            FROM revisi_status_kirim AS sk
                                                                            JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                                            WHERE sk.dikirim_ekspedisi = '$ekspedisi'");
                                $data_ekspedisi_kirim = mysqli_fetch_array($ekspedisi_kirim);
                                
                                $driver_kirim =  mysqli_query($connect, "SELECT sk.jenis_pengiriman, sk.dikirim_driver, us.nama_user 
                                                                            FROM revisi_status_kirim AS sk
                                                                            JOIN user us ON (sk.dikirim_driver = us.id_user)
                                                                            WHERE sk.dikirim_driver = '$driver'");
                                $data_driver_kirim = mysqli_fetch_array($driver_kirim);

                                $penerima =  mysqli_query($connect,"SELECT id_komplain, nama_penerima, created_date 
                                                                FROM inv_penerima_revisi
                                                                WHERE id_komplain = '$id_komplain' ORDER BY created_date DESC");
                                $data_penerima = mysqli_fetch_array($penerima);
                            ?>

                            <?php
                                if ($jenis_pengiriman == 'Ekspedisi') {
                                    ?> 
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">Jenis Pengiriman</p>
                                            <p style="float: right;"> :</p>
                                        </div>
                                        <div class="col-7">
                                            <?php echo $data_ekspedisi_kirim['jenis_penerima'] ?> (<?php echo $data_ekspedisi_kirim['nama_ekspedisi'] ?>)
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
                                                <?php echo $jenis_pengiriman ?> (<?php echo $data_driver_kirim['nama_user'] ?>)
                                            </div>
                                        </div>
                                    <?php
                                        if(!empty($data_status_kirim['jenis_penerima'])){
                                            ?>
                                                <div class="row">
                                                    <div class="col-5">
                                                        <p style="float: left;">Jenis Penerima</p>
                                                        <p style="float: right;"> :</p>
                                                    </div>
                                                    <div class="col-7">
                                                        <?php
                                                            if($data_status_kirim['jenis_penerima'] == 'Ekspedisi'){
                                                                ?>
                                                                    <?php echo $data_status_kirim['jenis_penerima'] ?> (<?php echo $data_ekspedisi_kirim['nama_ekspedisi'] ?>)
                                                                <?php
                                                            } else {
                                                                ?>
                                                                    <?php echo $data_status_kirim['jenis_penerima'] ?> (<?php echo $data_penerima['nama_penerima'] ?>)
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
                                if (!empty($data_status_kirim['dikirim_oleh'])) {
                                    echo '<div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Dikirim Oleh</p>
                                                <p style="float: right;"> :</p>
                                            </div>
                                            <div class="col-7">
                                                ' . $data_status_kirim['dikirim_oleh'] . '
                                            </div>
                                        </div>';
                                    }
                            ?>
                            <?php
                                if (!empty($data_status_kirim['penanggung_jawab'])) {
                                    echo '  <div class="row">
                                                <div class="col-5">
                                                    <p style="float: left;">PJ. Paket Kirim</p>
                                                    <p style="float: right;"> :</p>
                                                </div>
                                                <div class="col-7">
                                                    ' . $data_status_kirim['penanggung_jawab'] . '
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
                            <a href="list-invoice-revisi.php" class="btn btn-warning btn-detail mb-2">
                                <i class="bi bi-arrow-left"></i>
                                Halaman Sebelumnya
                            </a>
                            <a id="btnDiterima" href="form-diterima-revisi.php?id=<?php echo encrypt($id_inv, $key); ?>&&idk=<?php echo encrypt($id_komplain, $key) ?>&&ids=<?php echo encrypt($id_spk, $key) ?>&&ida=<?php echo encrypt($data['alamat'], $key) ?>&&nok=<?php echo encrypt($no_komplain, $key) ?>" class="btn btn-secondary btn-detail mb-2">
                                <i class="bi bi-send-fill"></i> Diterima
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive" id="dataProduk" style="display: block;">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="text-white" style="background-color: #051683;">
                                    <th class="text-center text-nowrap p-3" style="width:20px">No</th>
                                    <th class="text-center text-nowrap p-3" style="width:200px">Nama Produk</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Satuan</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Merk</th>
                                    <th class="text-center text-nowrap p-3" style="width:80px">Qty Order</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Harga</th>
                                    <th class="text-center text-nowrap p-3" style="width:100px">Diskon</th>
                                    <th class="text-center text-nowrap p-3" style="width:80px">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    include "koneksi.php";
                                    $year = date('y');
                                    $day = date('d');
                                    $month = date('m');
                                    $id_nonppn_decode = base64_decode($_GET['id']);
                                    $no = 1;
                                    while ($data_trx = mysqli_fetch_array($produk)) {
                                        $id_produk = $data_trx['id_produk'];
                                        $satuan = $data_trx['satuan'];
                                        $merk = $data_trx['merk'];
                                        $disc = $data_trx['disc'];
                                        $satuan_produk = '';
                                        $id_produk_substr = substr($id_produk, 0, 2);
                                        if ($id_produk_substr == 'BR') {
                                            $satuan_produk = $satuan;
                                        } else {
                                            $satuan_produk = 'Set';
                                        }
                                    ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                    <td class="text-nowrap"><?php echo $data_trx['nama_produk'] ?></td>
                                    <td class="text-center text-nowrap"><?php echo $satuan_produk ?></td>
                                    <td class="text-center text-nowrap"><?php echo $merk ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['qty']) ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['harga']) ?></td>
                                    <td class='text-end'><?php echo $data_trx['disc'] ?></td>
                                    <td class="text-end text-nowrap">
                                        <?php echo number_format($data_trx['total_harga']) ?></td>
                                </tr>
                                <?php $no++;?>
                                <?php }?>
                            </tbody>
                            <!-- Modal -->
                        </table>
                    </div>
                    <div id="prosesDiterima" style="display: none;">
                        aaaa
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