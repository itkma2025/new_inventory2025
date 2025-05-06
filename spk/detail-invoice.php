<div class="row mt-3">
    <div class="col-sm-6">
        <div class="card-body p-3 border">
            <div class="row">
                <div class="col-5">
                    <p style="float: left;">Tgl. Pesanan</p>
                    <p style="float: right;">:</p>
                </div>
                <div class="col-7">
                    <?php echo $data_inv['tgl_pesanan'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-5">
                    <p style="float: left;">No. SPK</p>
                    <p style="float: right;">:</p>
                </div>
                <div class="col-7 overflow-auto">
                    <?php
                        $no = 1;
                        while ($data_spk = mysqli_fetch_array($query_data_spk)) {
                            $id_cs = $data_spk['id_customer'];
                        ?>
                            <p><?php echo $no; ?>. (<?php echo $data_spk['tgl_pesanan'] ?>) / <?php if (!empty($data_spk['no_po'])) {
                                                                                                echo "(" . $data_spk['no_po'] . ")/";
                                                                                            } ?> (<?php echo $data_spk['no_spk'] ?>)</p>
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
                    <?php echo $data_inv['no_inv'] ?>
                </div>
            </div>
            <?php
                if ($data_inv['no_po'] != '') {
                    echo '
                    <div class="row">
                        <div class="col-5">
                            <p style="float: left;">No. PO</p>
                            <p style="float: right;">:</p>
                        </div>
                        <div class="col-7">
                            ' . $data_inv['no_po'] . '
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
                    <?php echo $data_inv['tgl_inv'] ?>
                </div>
            </div>
            <?php
                if ($data_inv['tgl_tempo'] != '') {
                        echo '
                        <div class="row">
                            <div class="col-5">
                                <p style="float: left;">Tgl. Tempo</p>
                                <p style="float: right;">:</p>
                            </div>
                            <div class="col-7">
                                ' . $data_inv['tgl_tempo'] . '
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
                    <?php echo $data_inv['kategori_inv'] ?>
                </div>
            </div>
            <?php
                if ($data_inv['kategori_inv'] == 'Spesial Diskon') {
                    echo '<div class="row">
                            <div class="col-5">
                                <p style="float: left;">Spesial Diskon</p>
                                <p style="float: right;">:</p>
                            </div>
                            <div class="col-7">
                                ' . $data_inv['sp_disc'] . ' %
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
                    <?php echo $data_inv['order_by'] ?>
                </div>
            </div>
            <div class="row">
                <div class="col-5">
                    <p style="float: left;">Sales</p>
                    <p style="float: right;">:</p>
                </div>
                <div class="col-7">
                    <?php echo $data_inv['nama_sales'] ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="card-body p-3 border" style="min-height: 234px;">
            <div class="row mt-2">
                <div class="col-5">
                    <p style="float: left;">Pelanggan</p>
                    <p style="float: right;">:</p>
                </div>
                <div class="col-7">
                    <?php echo $data_inv['nama_cs'] ?>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-5">
                    <p style="float: left;">Pelanggan Inv</p>
                    <p style="float: right;">:</p>
                </div>
                <div class="col-7">
                    <?php echo $data_inv['cs_inv'] ?>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-5">
                    <p style="float: left;">Alamat</p>
                    <p style="float: right;">:</p>
                </div>
                <div class="col-7">
                    <?php 
                        if($data_inv['alamat_inv'] == ''){
                            echo $data_inv['alamat'];
                        } else {
                            echo $data_inv['alamat_inv']; 
                        }
                    ?>
                </div>
            </div>
            <?php
                if ($data_inv['note_inv'] != '') {
                        echo '
                        <div class="row mt-2">
                            <div class="col-5">
                                <p style="float: left;">Note Invoice</p>
                                <p style="float: right;">:</p>
                            </div>
                            <div class="col-7">
                                ' . $data_inv['note_inv'] . '
                            </div>
                        </div>';
                    }
            ?>

            <!-- Kode untuk menampilkan status pengiriman -->
            <?php  
                if($jenis_pengiriman == 'Driver'){
                    $sql_driver = $connect_pengiriman->query("SELECT 
                                                                skd.jenis_penerima, 
                                                                ud.nama_user AS nama_driver,
                                                                uk.nama_user AS nama_kernet,
                                                                eks.nama_ekspedisi,
                                                                skd.no_resi,
                                                                skd.jenis_ongkir,
                                                                skd.status_free_ongkir,
                                                                skd.nominal_free_ongkir,
                                                                skd.nominal_ongkir
                                                            FROM status_kirim_reg_driver AS skd
                                                            LEFT JOIN $database2.user ud ON (skd.nama_driver = ud.id_user)
                                                            LEFT JOIN $database2.user uk ON (skd.nama_kernet = uk.id_user)
                                                            LEFT JOIN $db.ekspedisi eks ON (skd.nama_ekspedisi = eks.id_ekspedisi)
                                                            WHERE skd.id_inv = '$id_inv' COLLATE utf8mb4_general_ci");
                    $data_pengiriman = $sql_driver->fetch_assoc();
                    $pengirim = $data_pengiriman['nama_driver'];
                } else if ($jenis_pengiriman == 'Ekspedisi') {

                } else if ($jenis_pengiriman == 'Diambil Langsung') {

                } else {
                    header("Location:404.php");
                }
            ?>
            <div class="row mt-2">
                <div class="col-5">
                    <p style="float: left;">Jenis Pengiriman</p>
                    <p style="float: right;">:</p>
                </div>
                <div class="col-7">
                    <?php echo $jenis_pengiriman . ' (' . $pengirim . ')' ?>
                </div>
            </div>  
            <div class="row mt-2">
                <div class="col-5">
                    <p style="float: left;">Jenis Penerima</p>
                    <p style="float: right;">:</p>
                </div>
                <div class="col-7">
                    <?php echo $jenis_pengiriman . ' (' . $pengirim . ')' ?>
                </div>
            </div>
        </div>
    </div>
</div>