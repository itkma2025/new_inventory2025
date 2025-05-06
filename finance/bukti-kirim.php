<?php  
    include "../akses.php";
    require_once "../function/format-tanggal.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .img-fluid{
            height: 300px !important;
            width: 400px !important;
        }
        .card-header {
            padding: 10px !important;
        }
    </style>
</head>
<body>
    <?php
        if(isset($_POST["data_id"])){
            $finance_id = $_POST["data_id"];
            $label = isset($_POST["label"]) ? $_POST["label"] : '';
        }

        // Menampilkan id inv data pada finance
        $sql_finance = $connect->query("SELECT 
                                            fnc.id_inv, 
                                            COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                            COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                                            COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv
                                        FROM finance AS fnc
                                        LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                                        LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                                        LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                                        WHERE id_finance = '$finance_id'");
        $data_finance = mysqli_fetch_array($sql_finance);    
        $id_inv = $data_finance['id_inv'];
        $no_inv = $data_finance['no_inv'];
        $tgl_inv = $data_finance['tgl_inv'];
        $cs_inv = $data_finance['cs_inv'];
        
        // Menampilkan nama cs data pada spk
        $sql_bukti = "  SELECT 
                            ibt.bukti_satu, 
                            ibt.bukti_dua, 
                            ibt.bukti_tiga, 
                            ibt.lokasi,
                            ibt.created_date,
                            COALESCE(nonppn.ongkir, ppn.ongkir, bum.ongkir) AS ongkir,   
                            ip.id_inv, 
                            ip.nama_penerima,
                            STR_TO_DATE(ip.tgl_terima, '%d/%m/%Y') AS tgl_terima,
                            ip.alamat, 
                            sk.jenis_pengiriman, 
                            sk.jenis_penerima, 
                            sk.dikirim_ekspedisi, 
                            sk.dikirim_driver,
                            sk.no_resi, 
                            STR_TO_DATE(sk.tgl_kirim, '%d/%m/%Y') AS tgl_kirim,
                            ex.nama_ekspedisi,
                            us.nama_user
                        FROM inv_bukti_terima AS ibt
                        LEFT JOIN inv_penerima ip ON (ibt.id_inv = ip.id_inv)
                        LEFT JOIN inv_nonppn nonppn ON (ibt.id_inv = nonppn.id_inv_nonppn)
                        LEFT JOIN inv_ppn ppn ON (ibt.id_inv = ppn.id_inv_ppn)
                        LEFT JOIN inv_bum bum ON (ibt.id_inv = bum.id_inv_bum)
                        LEFT JOIN status_kirim sk ON (ibt.id_inv = sk.id_inv)
                        LEFT JOIN ekspedisi ex ON (ex.id_ekspedisi = sk.dikirim_ekspedisi) 
                        LEFT JOIN user us ON (sk.dikirim_driver = us.id_user)
                        WHERE ibt.id_inv = '$id_inv'";
                        $query_bukti = mysqli_query($connect, $sql_bukti);
                        $data_bukti = mysqli_fetch_array($query_bukti);
                        $nama_driver = $data_bukti['nama_user'];
                        $nama_driver = str_replace(' ', '_', $nama_driver);
                        $lokasi = $data_bukti['lokasi'];
                        $created_date = $data_bukti['created_date'];
                        $gambar1 = $data_bukti['bukti_satu'];
                        $gambar2 = $data_bukti['bukti_dua'];
                        $gambar_bukti2 = "../gambar/bukti2/$gambar2";
                        $gambar3 = $data_bukti['bukti_tiga'];
                        $gambar_bukti3 = "../gambar/bukti3/$gambar3";
                        $jenis_penerima = $data_bukti['jenis_penerima'];
                        $no_resi = $data_bukti['no_resi'];
                        $img = "";
                        if ($gambar1 && file_exists("../gambar/bukti1/" . $gambar1)) {
                            $img = "../gambar/bukti1/" . $gambar1;
                        } else if($gambar1 && file_exists("../gambar/bukti_kirim/" . $nama_driver . "/" . $gambar1)){
                            $img = "../gambar/bukti_kirim/" . $nama_driver . "/" . $gambar1;
                        } else {
                            $img = "../assets/img/no_img.jpg";
                        }
    ?>
    <div class="card mb-3 p-2">
        <div class="card-header text-center fw-bold fs-5 text-dark">
            <?php  
              if($label == ''){
                echo " Bukti Pengiriman Barang";
              } else {
                echo $label;
              }
            ?>
           
        </div>
        <div class="row g-0 mt-3">
            <div class="col-md-5 container-img">
                <img src="<?php echo $img ?>" class="image img-fluid rounded" alt="..." id="buktiTerimaImg" data-src="<?php echo $img ?>">
                <?php  
                    if($data_bukti['jenis_penerima'] == "Diambil Langsung"){
                        ?>
                            <div class="text-center"><span class="text-dark fw-bold fs-6">Tanggal Upload</span></div>
                            <p class="card-text text-center"><?php echo formatTanggalIndonesia($created_date) ?></p>
                        <?php
                    } else if($data_bukti['jenis_penerima'] == "Ekspedisi" || $data_bukti['jenis_penerima'] == "Customer"){
                        ?>
                            <div class="card-body mt-2">
                                <?php  
                                    if($lokasi != ""){
                                        ?>
                                            <div class="text-center"><span class="text-dark fw-bold fs-6">Lokasi Upload</span></div>
                                            <p class="card-text text-wrap" style="text-align: justify;">
                                                <?php echo $lokasi ?>
                                            </p>
                                        <?php
                                    }
                                ?>
                                <div class="text-center"><span class="text-dark fw-bold fs-6">Tanggal Upload</span></div>
                                <p class="card-text text-center"><?php echo formatTanggalIndonesia($created_date) ?></p>
                            </div>
                        <?php
                    } else {
                        echo "Maaf data tidak ditemukan";
                    }
                ?>
            </div>
            <div class="col-md-7">
                <?php  
                    if($data_bukti['jenis_penerima'] == "Ekspedisi"){
                        ?>
                            <div class="card-header text-dark">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <?php  
                                            if ($data_bukti['nama_user'] != "") {
                                                ?>
                                                    <tr>
                                                        <td class="text-nowrap" style="width:180px;">Nama Pengirim</td>
                                                        <td>:</td>
                                                        <td class="text-nowrap"><?php echo $data_bukti['nama_user'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-nowrap" style="width:180px;">Nama Pengirim</td>
                                                        <td>:</td>
                                                        <td class="text-nowrap"><?php echo $data_bukti['nama_user'] ?></td>
                                                    </tr>
                                                <?php
                                            }
                                        ?>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Tanggal Pengiriman</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo formatTanggalIndonesia($data_bukti['tgl_kirim'])?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="card-header text-dark">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Jenis Pengiriman</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['jenis_pengiriman']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Jenis Penerima</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['jenis_penerima']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Nama Ekspedisi</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['nama_ekspedisi']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Nominal Ongkir</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['ongkir']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">No Resi</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['no_resi']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Tujuan Pengiriman</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['alamat']; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Nama Penerima Paket</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['nama_penerima']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Tanggal Diterima</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo formatTanggalIndonesia($data_bukti['tgl_terima']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php
                    } else if ($data_bukti['jenis_penerima'] == "Customer"){
                        ?>
                            <div class="card-header text-dark">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <?php  
                                            if ($data_bukti['jenis_pengiriman'] != "Diambil Langsung"){
                                                ?>
                                                    <tr>
                                                        <td class="text-nowrap" style="width:180px;">Nama Pengirim</td>
                                                        <td>:</td>
                                                        <td class="text-nowrap"><?php echo $data_bukti['nama_user'] ?></td>
                                                    </tr>
                                                <?php
                                            }
                                        
                                        ?>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Tanggal Pengiriman</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo formatTanggalIndonesia($data_bukti['tgl_kirim'])?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="card-header text-dark">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Jenis Pengiriman</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['jenis_pengiriman']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Jenis Penerima</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['jenis_penerima']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Nama Penerima Paket</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['nama_penerima']; ?></td>
                                        </tr>
                                        <?php  
                                            if ($data_bukti['jenis_pengiriman'] != "Diambil Langsung"){
                                                ?>
                                                    <tr>
                                                        <td class="text-nowrap" style="width:180px;">Tujuan Pengiriman</td>
                                                        <td>:</td>
                                                        <td class="text-nowrap"><?php echo $data_bukti['alamat']; ?></td>
                                                    </tr>
                                                <?php
                                            }
                                        
                                        ?>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Tanggal Diterima</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo formatTanggalIndonesia($data_bukti['tgl_terima']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php
                    } else if ($data_bukti['jenis_pengiriman'] == "Diambil Langsung"){
                        ?>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Nama Penerima</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo $data_bukti['nama_penerima']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width:180px;">Tanggal Diterima</td>
                                            <td>:</td>
                                            <td class="text-nowrap"><?php echo formatTanggalIndonesia($data_bukti['tgl_terima']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php
                    } else {
                        echo "Maaf data tidak di temukan";
                    }
                ?>
            </div>
        </div>
    </div>

</body>
</html>
<!-- Script untuk lighbox -->
<script>
    $(document).ready(function() {
        // Function untuk menampilkan modal bukti kirim
        $('#buktiKirim').on('show.bs.modal', function(event) {
            var imgSrc = '<?php echo $img ?>';
            $('#buktiTerimaImg').attr('src', imgSrc).attr('data-src', imgSrc);
        });

        // Event handler saat gambar bukti terima diklik
        $(document).on('click', '#buktiTerimaImg', function() {
            // Inisialisasi lightGallery
            $(this).lightGallery({
                dynamic: true,
                dynamicEl: [{
                    src: $(this).data('src') // URL gambar yang akan ditampilkan
                }]
            });

            // Atur z-indeks modal Bootstrap menjadi 0
            $('#buktiKirim').css('z-index', 5);

            // Ubah atribut dan gaya CSS dari elemen body
            $('body').attr('scroll', 'no').addClass('disable-scroll');
        });

        // Event handler saat lightGallery ditutup
        $(document).on('onCloseAfter.lg', '#buktiTerimaImg', function() {
            // Kembalikan z-indeks modal Bootstrap ke nilai aslinya
            $('#buktiKirim').css('z-index', ''); // Menghapus properti z-indeks agar kembali ke nilai aslinya

            // Hapus atribut dan gaya CSS dari elemen body
            $('body').removeAttr('scroll').removeClass('disable-scroll');
        });
    });
</script>