<?php
    $id_inv = decrypt($_GET['id'], $key_global);
    $jenis_inv = htmlspecialchars($_GET['jenis']);

    // Generate a secure random token
    $nonce = bin2hex(random_bytes(16));
    $_SESSION['nonce_token'] = $nonce; 

    // Kondisi untuk menampilkan data berdasarkan jenis invoice
    $modal_edit_inv = "";
    $action_proforma = '';
    if($jenis_inv == 'nonppn'){
        $label_jenis = 'NON PPN';
        require_once 'query/data-inv-nonppn.php';
        require_once 'query/data-spk-proforma.php';
        require_once 'query/jenis-cb-proforma.php';
        $action_proforma = 'proses/proses-invoice-nonppn.php';
        $cetak_inv = 'cetak-inv-nonppn-reg.php';
        $cetak_pdf = 'generate_pdf_nonppn.php';
        $sql_total_inv = mysqli_query($connect, "SELECT total_inv FROM inv_nonppn WHERE id_inv_nonppn = '$id_inv'");
        if($status_transaksi_inv != 'Diterima'){
            ?>
                <script>
                    window.location.href = 'invoice-reguler-diterima.php?sort=baru';
                </script>
            <?php
        }
    } else if ($jenis_inv == 'ppn'){
        $label_jenis = 'PPN';
        require_once 'query/data-inv-ppn.php';
        require_once 'query/data-spk-proforma.php';
        require_once 'query/jenis-cb-proforma.php';
        $action_proforma = 'proses/proses-invoice-ppn.php';
        $cetak_inv = 'cetak-inv-ppn-reg.php';
        $cetak_pdf = 'generate_pdf_ppn.php';
        $sql_total_inv = mysqli_query($connect, "SELECT total_inv FROM inv_ppn WHERE id_inv_ppn = '$id_inv'");
        if($status_transaksi_inv != 'Diterima'){
            ?>
                <script>
                    window.location.href = 'invoice-reguler-diterima.php?sort=baru';
                </script>
            <?php
        }
    } else if ($jenis_inv == 'bum') {
        $label_jenis = 'BUM';
        require_once 'query/data-inv-bum.php';
        require_once 'query/data-spk-proforma.php';
        require_once 'query/jenis-cb-proforma.php';
        $action_proforma = 'proses/proses-invoice-bum.php';
        $cetak_inv = 'cetak-inv-bum-reg.php';
        $cetak_pdf = 'generate_pdf_bum.php';
        $sql_total_inv = mysqli_query($connect, "SELECT total_inv FROM inv_bum WHERE id_inv_bum = '$id_inv'");
        if($status_transaksi_inv != 'Diterima'){
            ?>
                <script>
                    window.location.href = 'invoice-reguler-diterima.php?sort=baru';
                </script>
            <?php
        }
    } else {
        header("Location:404.php");
    }
?>
<div class="card shadow p-2">
<?php  
    $sql_inv_penerima = $connect->query("SELECT COUNT(id_inv) AS total FROM history_inv_bukti_terima WHERE id_inv = '$id_inv'");
    $data_history = $sql_inv_penerima->fetch_assoc();

    $sql_bukti_terima =  $connect->query("SELECT approval AS status_approval FROM inv_bukti_terima WHERE id_inv = '$id_inv'");
    $data_cek_bukti = $sql_bukti_terima->fetch_assoc();

    $status_inv = '';
    $bg_color = '';
    if($data_history['total'] == '0') {
        $status_inv = 'New';
        $bg_color = 'btn-secondary';
    } else if ($data_history['total'] != '0' && $data_cek_bukti['status_approval'] == '0') {
        $status_inv = 'Reject';
        $bg_color = 'btn-danger';
    } else if ($data_history['total'] != '0' && $data_cek_bukti['status_approval'] == '1') {
        $status_inv = 'Reject';
        $bg_color = 'btn-danger';
    } else if ($data_history['total'] != '0' && $data_cek_bukti['status_approval'] == '2') {
        $status_inv = 'Approval';
        $bg_color = 'btn-success';
    } else {
        $status_inv = 'Kondisi Tidak Ditemukan';
        $bg_color = 'btn-warning';
    }
?>
<div class="card-header">
    <div class="row d-flex align-items-center justify-content-between">
        <div class="col-md-3 col-3 text-start">
            <!-- Kosong untuk keseimbangan layout -->
        </div>
        <div class="col-md-6 col-6 text-center">
            <h5 class="mb-0"><strong>DETAIL INVOICE <?php echo $label_jenis ?></strong></h5>
        </div>
        <div class="col-md-3 col-3 text-end">
            <button class="btn <?php echo $bg_color ?> btn-sm fs-6"><?php echo $status_inv ?></button>
        </div>
    </div>
</div>
<!-- Start Row -->
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

            <?php
                $status_kirim = mysqli_query($connect, "SELECT id_status_kirim, jenis_pengiriman, dikirim_ekspedisi, jenis_penerima, no_resi,dikirim_driver, dikirim_oleh, penanggung_jawab FROM status_kirim WHERE id_inv = '$id_inv'");
                $data_status_kirim = mysqli_fetch_array($status_kirim);
                $id_status_kirim = $data_status_kirim['id_status_kirim'];
                $jenis_pengiriman =  $data_status_kirim['jenis_pengiriman'];
                $ekspedisi = $data_status_kirim['dikirim_ekspedisi'];
                $driver = $data_status_kirim['dikirim_driver'];
                $no_resi = $data_status_kirim['no_resi'];

                $ekspedisi_kirim =  mysqli_query($connect, "SELECT 
                                                            sk.jenis_pengiriman, sk.dikirim_ekspedisi, sk.jenis_penerima, ex.nama_ekspedisi
                                                            FROM status_kirim AS sk
                                                            JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                            WHERE sk.dikirim_ekspedisi = '$ekspedisi'");
                $data_ekspedisi_kirim = mysqli_fetch_array($ekspedisi_kirim);
                
                $driver_kirim =  mysqli_query($connect, "SELECT sk.jenis_pengiriman, sk.dikirim_driver, us.nama_user 
                                                            FROM status_kirim AS sk
                                                            LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                                                            WHERE sk.dikirim_driver = '$driver'");
                $data_driver_kirim = mysqli_fetch_array($driver_kirim);
                $nama_driver = $data_driver_kirim['nama_user'] ?? '';
                $nama_driver = str_replace(' ', '_', $nama_driver);

                $penerima =  mysqli_query($connect,"SELECT id_inv, nama_penerima 
                                                FROM inv_penerima
                                                WHERE id_inv = '$id_inv'");
                $data_penerima = mysqli_fetch_array($penerima);
                $nama_penerima = "";
                if($data_penerima['nama_penerima'] != ""){
                    $nama_penerima = $data_penerima['nama_penerima'];
                }
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
                    <div class="row">
                        <div class="col-5">
                            <p style="float: left;">No. Resi</p>
                            <p style="float: right;"> :</p>
                        </div>
                        <div class="col-7">
                            <?php 
                                if($no_resi != ''){
                                    echo $no_resi; 
                                } else {
                                    echo "<b>No Resi Belum Di Input</b>";
                                }
                            ?>
                        </div>
                    </div>
                    <?php
                    }else if ($jenis_pengiriman == 'Diambil Langsung'){
                    ?>
                        <div class="row mt-2">
                            <div class="col-5">
                                <p style="float: left;">Jenis Pengiriman</p>
                                <p style="float: right;"> :</p>
                            </div>
                            <div class="col-7">
                                <?php echo $jenis_pengiriman ?>
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
                                    <?php echo $data_status_kirim['jenis_penerima'] ?> 
                                    <?php  
                                        if(!empty($data_ekspedisi_kirim['nama_ekspedisi'])){
                                            ?>
                                                (<?php echo $data_ekspedisi_kirim['nama_ekspedisi'] ?>)
                                            <?php
                                        }
                                    ?>
                                    
                                </div>
                            </div>
                            <?php  
                                if(!empty($data_penerima['nama_penerima'])){
                                    ?>
                                            <div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">Nama Penerima</p>
                                                <p style="float: right;"> :</p>
                                            </div>
                                            <div class="col-7">
                                                <?php echo $data_penerima['nama_penerima'] ?>
                                            </div>
                                        </div>
                                    <?php
                                }
                            
                            ?>
                        <?php
                    }
                } else {
                    if($data_status_kirim['jenis_penerima'] == "Customer" && $jenis_pengiriman == 'Driver'){
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
                                        <?php echo $data_status_kirim['jenis_penerima'] ?> (<?php echo $nama_penerima ?>)
                                    </div>
                                </div>
                            <?php
                        }
                    } else if ($data_status_kirim['jenis_penerima'] == "Ekspedisi" && $jenis_pengiriman == 'Driver'){
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
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Jenis Penerima</p>
                                    <p style="float: right;"> :</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_ekspedisi_kirim['jenis_penerima'] ?> (<?php echo $data_ekspedisi_kirim['nama_ekspedisi'] ?>)
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. Resi</p>
                                    <p style="float: right;"> :</p>
                                </div>
                                <div class="col-7">
                                    <?php 
                                        if($no_resi != ''){
                                            echo $no_resi; 
                                        } else {
                                            echo "<b>No Resi Belum Di Input</b>";
                                        }
                                    ?>
                                </div>
                            </div>
                        <?php

                    }
                }
            ?>
            <?php
                if ($data_status_kirim['jenis_penerima'] == 'Ekspedisi'){
                    if($no_resi != ''){
                        if ($data_inv['free_ongkir'] == 0) {
                            echo '<div class="row">
                                    <div class="col-5">
                                        <p style="float: left;">Ongkir</p>
                                        <p style="float: right;"> :</p>
                                    </div>
                                    <div class="col-7">
                                        ' . number_format($data_inv['ongkir'],0,'.','.') . '
                                    </div>
                                </div>';
                        } else {
                            echo '<div class="row">
                                    <div class="col-5">
                                        <p style="float: left;">Ongkir</p>
                                        <p style="float: right;"> :</p>
                                    </div>
                                    <div class="col-7">
                                        ' . number_format($data_inv['ongkir_free'],0,'.','.') . ' (Free Ongkir)
                                    </div>
                                </div>';  
                        }
                    } else {
                        echo '<div class="row">
                                    <div class="col-5">
                                        <p style="float: left;">Ongkir</p>
                                        <p style="float: right;"> :</p>
                                    </div>
                                    <div class="col-7">
                                        <b>Ongkir Belum Di Input</b>
                                    </div>
                                </div>';  
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
<!-- End Row -->
    <!-- Start Row -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="text-start">
            <a href="invoice-reguler-diterima.php?sort=baru" class="btn btn-warning btn-detail mb-2 btn-mobile">
                <i class="bi bi-arrow-left"></i>
                Halaman Sebelumnya
            </a> 
            
            <!-- End Button Modal Bukti Terima -->
            <?php
                // Button modal Bukti Terima
                $button_bukti_terima = '<button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#buktiKirim">
                                            <i class="bi bi-file-earmark-image"></i> Bukti Terima
                                        </button>';
                if ($role == "Super Admin" || $role == "Admin Penjualan") {
                    $sql_bukti_terima = $connect->query("SELECT approval, jenis_reject  FROM inv_bukti_terima WHERE id_inv = '$id_inv'");
                    $data_bukti_terima = mysqli_fetch_assoc($sql_bukti_terima);
                    $approval = $data_bukti_terima['approval'];

                    if($approval == '2'){
                        echo $button_bukti_terima;
                        ?>
                            <!-- Trx Selesai -->
                            <button type="button" class="btn btn-secondary mb-2" data-bs-toggle="modal" data-bs-target="#trxSelesai">
                                <i class="bi bi-check2-circle"></i> Ubah Status
                            </button>
                            <!-- End Trx Selesai -->
                        <?php
                    } else if ($approval == '1'){
                        if($data_status_kirim['jenis_penerima'] == "Ekspedisi"){
                            ?>
                                <button class="btn btn-warning btn-detail mb-2 me-1 btn-mobile btn-mobile" data-bs-toggle="modal" data-bs-target="#editOngkir">
                                    <i class="bi bi-pencil"></i>
                                    Reupload Bukti Kirim
                                </button>
                            <?php
                        } else if($data_status_kirim['jenis_pengiriman'] == "Diambil Langsung"){
                            ?>
                                <button type="button" class="btn btn-secondary mb-2 btn-mobile" data-bs-toggle="modal" data-bs-target="#diambil">
                                    <i class="bi bi-arrow-repeat"></i> Diambil Oleh
                                </button>
                            <?php
                        }
                    } else if ($approval == '0'){
                        echo $button_bukti_terima;
                        ?>
                        <button type="button" class="btn btn-info mb-2 btn-mobile">
                            <i class="bi bi-arrow-repeat"></i> Menunggu Verifikasi PJT
                        </button>
                        <?php
                    }
                    ?>

                        
            <?php } ?>
        </div>
    </div>
</div>
<!-- End Row -->