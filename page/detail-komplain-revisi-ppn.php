<div class="row mb-2">
    <!-- Kolom No Komplain (di atas) -->
    <div class="col-md-3">
        <button class="btn btn-secondary">No Komplain : <?php echo $data_detail['no_komplain'] ?></button>
    </div>
    <!-- Kolom Open (di tengah) -->
    <div class="col-md-6 text-center">
        <p><b>Detail Invoice Revisi</b></p>
    </div>
    <!-- Kolom Details (paling bawah) -->
    <div class="col-md-3 text-end">
        <button class="btn btn-secondary">
            <?php 
                if($data_detail['status_komplain'] == 0){
                    echo "Open";
                } else {
                    echo "Selesai";
                }
            ?>
        </button>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="border p-3">
            <div class="table-responsive">
                <table class="table table-borderless">
                    <tr>
                        <td class="col-md-6 text-nowrap">Tgl. Pesanan</td>
                        <td class="text-nowrap">: <?php echo $data_detail['tgl_pesanan'] ?></td>
                    </tr>
                    <tr>
                        <td class="col-md-6 text-nowrap">No. SPK</td>
                        <td class="text-nowrap">
                            : <?php 
                                    $no = 1;
                                    $total_rows = mysqli_num_rows($query_detail2); // Menghitung total baris data
                                    while ($data_detail2 = mysqli_fetch_array($query_detail2)) {
                                        $no_spk = $data_detail2['no_spk'];
                                        $tgl_pesanan = $data_detail2['tgl_pesanan'];
                                        $no_po = $data_detail2['no_po'];
                                        
                                        // Mengecek apakah ini adalah baris kedua atau lebih
                                        if ($no > 1) {
                                            echo "<br>"; // Menambahkan baris baru setelah baris pertama
                                        }
                                        
                                        echo $no . ". (" . $tgl_pesanan . ")";
                                        
                                        // Menampilkan nomor PO jika tersedia
                                        if (!empty($no_po)) {
                                            echo " / (" . $no_po . ")";
                                        }
                                        
                                        echo " / (" . $no_spk . ")";
                                        
                                        $no++;
                                    }
                                ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-6 text-nowrap">No. Invoice</td>
                        <td class="text-nowrap">: <?php echo $no_inv_fix ?></td>
                    </tr>
                    <tr>
                        <td class="col-md-6 text-nowrap">Tgl.Invoice</td>
                        <td class="text-nowrap">: <?php echo $data_detail['tgl_inv'] ?></td>
                    </tr>
                    <tr>
                        <td class="col-md-6 text-nowrap">Jenis Invoice</td>
                        <td class="text-nowrap">: <?php echo $data_detail['kategori_inv'] ?></td>
                    </tr>
                    <tr>
                        <td class="col-md-6 text-nowrap">Order Via</td>
                        <td class="text-nowrap">: <?php echo $data_detail['order_by'] ?></td>
                    </tr>
                    <tr>
                        <td class="col-md-6 text-nowrap">Sales</td>
                        <td class="text-nowrap">: <?php echo $data_detail['nama_sales'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="border p-3">
            <div class="table-responsive">
                <table class="table table-borderless">
                    <?php  
                        $cek_inv_revisi = $connect->query("SELECT pelanggan_revisi, alamat_revisi FROM inv_revisi WHERE id_inv = '$id_inv' ORDER BY created_date DESC");
                        $total_data_inv_rev = mysqli_num_rows($cek_inv_revisi);
                        $data_cek_inv_rev = mysqli_fetch_array($cek_inv_revisi);
                        
                        $pelanggan = '';
                        $alamat = '';
                        if ($total_data_inv_rev > 0){
                            $pelanggan = $data_cek_inv_rev['pelanggan_revisi'];
                            $alamat = $data_cek_inv_rev['alamat_revisi'];
                        } else {
                            if($data_detail['alamat_inv'] == ''){
                                $pelanggan = $data_detail['cs_inv'];
                                $alamat = $data_detail['alamat'];
                            } else {
                                $pelanggan = $data_detail['cs_inv'];
                                $alamat = $data_detail['alamat_inv'];  
                            }
                            
                        }
                    ?>
                    <tr>
                        <td class="col-md-6 text-nowrap">Pelanggan</td>
                        <td class="text-nowrap">: <?php echo $data_detail['nama_cs'] ?></td>
                    </tr>
                    <tr>
                        <td class="col-md-6 text-nowrap">Pelanggan Invoice</td>
                        <td class="text-nowrap">: <?php echo $pelanggan ?></td>
                    </tr>
                    <tr>
                        <td class="col-md-6 text-nowrap">Alamat</td>
                        <td class="wrap-text">: <?php echo $alamat ?></td>
                    </tr>
                    <?php 
                        if($total_driver_rev != 0 && $data_driver_rev['jenis_pengiriman'] == 'Ekspedisi'){
                            ?>
                                <tr>
                                    <td class="col-md-6 text-nowrap">Ongkos Kirim</td>
                                    <td class="text-nowrap">: <?php 
                                            if($data_driver_rev['free_ongkir'] == 1){
                                                echo "0 (Free Ongkir)";
                                            } else  {
                                                if($data_driver_rev['jenis_ongkir'] == 1){
                                                    echo number_format($data_driver_rev['ongkir']) . " (COD)";
                                                } else {
                                                    echo number_format($data_driver_rev['ongkir']);
                                                }
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php
                        }
                    ?>
                    <?php  
                        if($total_driver_rev != 0){
                            ?>
                                <tr>
                                    <td class="col-md-6 text-nowrap">Jenis Pengiriman</td>
                                    <td class="text-nowrap">
                                        : <?php  
                                                if($data_driver_rev['jenis_pengiriman'] == 'Driver'){
                                                    ?>
                                                        <?php echo $data_driver_rev['jenis_pengiriman']?>
                                                        (<?php echo $data_driver_rev['nama_driver'] ?>)
                                                    <?php
                                                } else if($data_driver_rev['jenis_pengiriman'] == 'Ekspedisi'){
                                                    ?>
                                                        <?php echo $data_driver_rev['jenis_pengiriman']?>
                                                        <?php
                                                } else {
                                                    ?>
                                                        <?php echo $data_driver_rev['jenis_pengiriman']?>
                                                        (<?php echo $data_driver_rev['diambil_oleh'] ?>)
                                                    <?php
                                                }
                                            ?>

                                    </td>
                                </tr>
                                <?php  
                                    if(!empty($data_driver_rev['jenis_pengiriman'] && $data_driver_rev['jenis_penerima'])){
                                        ?>
                                            <tr>
                                                <td class="col-md-6 text-nowrap">Diterima Oleh</td>
                                                <td class="text-nowrap">
                                                    : <?php 
                                                            if($data_driver_rev['jenis_penerima'] == 'Customer'){
                                                                ?>
                                                                    <?php echo $data_driver_rev['jenis_penerima'] ?>
                                                                    <?php
                                                            } else {
                                                                ?>
                                                                    <?php echo $data_driver_rev['nama_ekspedisi'] ?>
                                                                <?php
                                                            }
                                                    
                                                        ?>
                                                </td>
                                            </tr>
                                        <?php
                                    }          
                                ?>
                                <?php  
                                    if(!empty($data_driver_rev['jenis_pengiriman'] && $data_driver_rev['jenis_penerima'])){
                                        if($data_driver_rev['jenis_penerima'] == 'Customer'){
                                        } else {
                                            ?>
                                                <tr>
                                                    <td class="col-md-6 text-nowrap">No. Resi</td>
                                                    <td class="text-nowrap">
                                                        : <?php echo $data_driver_rev['no_resi'] ?>
                                                    </td>
                                                </tr>
                                            <?php
                                        }
                                        ?>
                                            
                                        <?php
                                    }          
                                ?>
                                <?php   
                                    if(!empty($data_driver_rev['nama_penerima'])){
                                        ?>
                                            <tr>
                                                <td class="col-md-6 text-nowrap">Nama Penerima (CS)</td>
                                                <td class="text-nowrap">: <?php echo $data_driver_rev['nama_penerima'] ?></td>
                                            </tr>
                                        <?php
                                    }
                                ?>
                                <?php  
                                    if(!empty($data_driver_rev['dikirim_oleh']) && !empty($data_driver_rev['penanggung_jawab'])){
                                        ?>
                                            <tr>
                                                <td class="col-md-6 text-nowrap">Dikirim Oleh</td>
                                                <td class="text-nowrap">: <?php echo $data_driver_rev['dikirim_oleh'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="col-md-6 text-nowrap">PJ. Paket Kirim</td>
                                                <td class="text-nowrap">: <?php echo $data_driver_rev['penanggung_jawab'] ?>
                                                </td>
                                            </tr>
                                        <?php
                                    }
                                            
                                ?>
                            <?php  
                        } 
                    ?>
                    <?php  
                        if ($data_kondisi['catatan'] != ""){
                            ?>
                                <tr>
                                    <td class="col-md-6 text-nowrap">Catatan</td>
                                    <td class="text-nowrap">: <?php echo $data_kondisi['catatan'] ?></td>
                                </tr>
                            <?php 
                        } 
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>