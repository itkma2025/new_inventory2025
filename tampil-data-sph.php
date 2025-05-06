<?php
    $page  = 'transaksi';
    $page2 = 'sph';
    include "akses.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Create SPH</title>
    <?php include "page/head.php"; ?>
    <style type="text/css">
        /* Menghilangkan garis pada input */
        td{
            padding: 0;
        }


        .wrap-text {
            max-width: 300px; /* Contoh lebar maksimum */
            overflow: hidden; /* Sembunyikan teks yang melebihi max-width */
            white-space: pre-line; /* Tetapkan spasi putih dan pecah baris sesuai dengan teks */
            word-wrap: break-word; /* Pecah kata jika melebihi max-width */
        }
  
        .mobile-label{
            display: none;
        }
        @media (max-width: 800px) { /* Media query untuk tampilan mobile */
            
            .wrap-text {
                min-width: 400px; /* Contoh lebar maksimum */
                overflow: hidden; /* Sembunyikan teks yang melebihi max-width */
                white-space: pre-line; /* Tetapkan spasi putih dan pecah baris sesuai dengan teks */
                word-wrap: break-word; /* Pecah kata jika melebihi max-width */
            }

            .qty {
                width: 100px;
            }

            .mobile-no{
                display: none;
            }

            .div-none{
                display: none;
            }
        }

        @media (max-width: 680px) { /* Media query untuk tampilan mobile */
            
            .qty {
                width: 80px;
            }

            .mobile-no{
                display: none;
            }
        }

        @media only screen and (max-width: 600px) {
            body {
                font-size: 16px;
            }

            .mobile {
                display: none;
            }

            .mobile-text {
                text-align: left !important;
            }

            .mobile-label{
                display: none;
            }

            .mobile-no{
                display: none;
            }

            .qty {
                width: 70px;
            }
        }
        @media (max-width: 580px) { /* Media query untuk tampilan mobile */
            body {
                font-size: 16px;
            }

            .mobile {
                display: none;
            }

            .mobile-text {
                text-align: left !important;
            }

            .mobile-label{
                display: none;
            }

            .mobile-no{
                display: none;
            }

            .qty {
                width: 60px;
            }
        }

        @media (max-width: 578px) { /* Media query untuk tampilan mobile */
            body {
                font-size: 16px;
            }

            .mobile {
                display: none;
            }

            .mobile-text {
                text-align: left !important;
            }

            .mobile-label{
                display: block;
            }

            .mobile-no{
                display: none;
            }

            .qty {
                width: 100%;
            }
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
    <main id="main" class="main">
        <!-- Loading -->
        <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div>
        <!-- ENd Loading -->
        <section class="pagetitle">
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="card p-3">
                <div class="card-header text-center">
                    <h5>Form Surat Penawaran Harga</h5>
                </div>
                <div class="row mt-3">
                    <?php  
                        include "koneksi.php";
                        $id_sph = decrypt($_GET['id'], $key_global);

                        $sph = " SELECT 
                                        sph.id_sph, sph.no_sph, sph.tanggal, sph.up, sph.id_cs, sph.alamat, sph.ttd_oleh, sph.jabatan, sph.perihal, sph.note, cs.nama_cs
                                 FROM sph as sph
                                 LEFT JOIN tb_customer_sph cs ON (cs.id_cs = sph.id_cs) 
                                 WHERE sph.id_sph = '$id_sph'";
                        $query_sph = mysqli_query($connect, $sph);
                        $data_sph = mysqli_fetch_array($query_sph);
                        $id_sph = $data_sph['id_sph'];
                    ?>
                    <div class="col-sm-6">
                        <div class="card-body p-3 border">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. SPH</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_sph['no_sph'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. SPH</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_sph['tanggal'] ?>
                                </div>
                            </div>
                            <?php  
                                if($data_sph['up'] == ''){
                                }else{
                                    echo '<div class="row">
                                            <div class="col-5">
                                                <p style="float: left;">U.P</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                                '.$data_sph['up'].'
                                            </div>
                                        </div>';
                                }
                            
                            
                            ?>
                            
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Customer</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_sph['nama_cs'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Alamat</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_sph['alamat'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card-body p-3 border">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">TTD</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_sph['ttd_oleh'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Jabatan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_sph['jabatan'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Perihal</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data_sph['perihal'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Notes</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                <?php
                                    $note = $data_sph['note'];

                                    $items = explode("\n", trim($note));

                                    foreach ($items as $notes) {
                                        echo trim($notes) . '<br>';
                                    }
                                ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php  
                        if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan") { 
                            ?>
                                <div class="text-center mt-3 mb-3">
                                    <button class="btn btn-info btn-md" data-bs-toggle="modal" data-bs-target="#editPelanggan"><i class="bi bi-pencil"></i> Edit Pelanggan SPH</button>
                                </div>
                            <?php
                        }
                    ?>
                    <!-- Modal -->
                    <div class="modal fade" id="editPelanggan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Pelanggan SPH</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="proses/proses-sph.php" method="POST">
                                    <input type="hidden" name="id_sph" value="<?php echo encrypt($data_sph['id_sph'], $key_global) ?>">
                                    <div class="mb-3">
                                        <label>Tanggal</label>
                                        <input type="text" class="form-control" name="tanggal" id="date" value="<?php echo $data_sph['tanggal'] ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>U.P</label>
                                        <input type="text" class="form-control" name="up" id="up" value="<?php echo $data_sph['up'] ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label>Customer</label>
                                        <input type="hidden" class="form-control" id="id" name="id_cs" value="<?php echo $data_sph['id_cs'] ?>">
                                        <input type="text" class="form-control" name="cs" id="cs" value="<?php echo $data_sph['nama_cs'] ?>" data-bs-toggle="modal" data-bs-target="#modalCs" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label>Alamat</label>
                                        <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo $data_sph['alamat'] ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>TTD</label>
                                        <input type="text" class="form-control" name="ttd" value="<?php echo $data_sph['ttd_oleh'] ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>Jabatan</label>
                                        <input type="text" class="form-control" name="jabatan" value="<?php echo $data_sph['jabatan'] ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>Perihal</label>
                                        <input type="text" class="form-control" name="perihal" value="<?php echo $data_sph['perihal'] ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label>Notes</label>
                                        <textarea type="text" class="form-control" name="note" cols="30" style="max-height: 200px; min-height: 200px;"><?php echo $data_sph['note'] ?></textarea>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="ubah-cs-sph" class="btn btn-primary">Ubah Data</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="reloadPage()">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-2">
                <div class="d-flex justify-content-start flex-wrap">
                    <a href="sph.php" class="btn btn-warning mb-3 me-2">
                        <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                    </a>
                    <?php  
                        if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan" ) { 
                            ?>
                                <button class="btn btn-primary btn-detail mb-3 me-2" data-sph="<?php echo  $id_sph ?>" data-bs-toggle="modal" data-bs-target="#modalBarang">
                                    <i class="bi bi-plus-circle"></i> Tambah Produk
                                </button>
                            
                                <a class="btn btn-secondary mb-3 me-2" href="cetak-sph.php?id=<?php echo encrypt($id_sph, $key_global) ?>">
                                    <i class="bi bi-printer"></i> Cetak SPH 
                                </a>       
                            <?php
                        }
                    ?>
                </div>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table2">
                        <?php
                            $no = 1;
                            $sql_sph = "SELECT DISTINCT
                                            sr.id_sph,
                                            tps.id_transaksi,
                                            tps.id_produk,
                                            tps.nama_produk_sph,
                                            tps.harga AS harga_produk_sph,
                                            tps.status_trx,
                                            tps.qty,
                                            tps.created_date,
                                            COALESCE(spr.stock, spe.stock) AS stock,
                                            COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                                            COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                            COALESCE(tpr.harga_produk, tpe.harga_produk, tpsm.harga_set_marwa, tpse.harga_set_ecat) AS harga_produk,
                                            COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk) AS merk_produk -- Nama merk untuk produk
                                        FROM transaksi_produk_sph AS tps
                                        LEFT JOIN sph sr ON sr.id_sph = tps.id_sph
                                        LEFT JOIN stock_produk_reguler spr ON tps.id_produk = spr.id_produk_reg
                                        LEFT JOIN stock_produk_ecat spe ON tps.id_produk = spe.id_produk_ecat
                                        LEFT JOIN tb_produk_reguler tpr ON tps.id_produk = tpr.id_produk_reg
                                        LEFT JOIN tb_produk_ecat tpe ON tps.id_produk = tpe.id_produk_ecat
                                        LEFT JOIN tb_produk_set_marwa tpsm ON tps.id_produk = tpsm.id_set_marwa
                                        LEFT JOIN tb_produk_set_ecat tpse ON tps.id_produk = tpse.id_set_ecat
                                        LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                        LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk ecat
                                        LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                        LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                                        WHERE sr.id_sph = '$id_sph' AND tps.status_trx = '1' ORDER BY tps.created_date ASC";
                            $query_sph = mysqli_query($connect, $sql_sph);
                            $totalRows = mysqli_num_rows($query_sph);

                            if ($totalRows != 0) {
                                ?>
                                    <thead>
                                        <tr class="text-white" style="background-color: navy">
                                            <td class="text-center text-nowrap p-3">No</td>
                                            <td class="text-center text-nowrap p-3">Nama Produk</td>
                                            <td class="text-center text-nowrap p-3">Merk</td>
                                            <td class="text-center text-nowrap p-3">Satuan</td>
                                            <td class="text-center text-nowrap p-3">Harga</td>
                                            <td class="text-center text-nowrap p-3">Qty</td>
                                            <?php  
                                                if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan" ) { 
                                                    ?>
                                                        <td class="text-center text-nowrap p-3">Aksi</td>
                                                    <?php
                                                }
                                            ?>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        while ($data_sph = mysqli_fetch_array($query_sph)) {
                                            $id_produk = $data_sph['id_produk'];
                                            $id_produk_substr = substr($id_produk, 0, 2);
                                            $nama_produk = !empty($data_sph['nama_produk_sph']) ? $data_sph['nama_produk_sph'] : $data_sph['nama_produk'];
                                        ?>
                                                <tr>
                                                    <td class="text-center text-nowrap"><?php echo $no ?></td>
                                                    <td class="text-nowrap"><?php echo $nama_produk ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data_sph['merk_produk']?></td>
                                                    <td class="text-center text-nowrap"><?php ($id_produk_substr == 'BR' ? "Pcs" : "Set")?></td>
                                                    <td class="text-end text-nowrap"><?php echo number_format($data_sph['harga_produk_sph'],0,'.','.')?></td>
                                                    <td class="text-end text-nowrap"><?php echo $data_sph['qty']?></td>
                                                    <?php  
                                                        if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan" ) { 
                                                            ?>
                                                                <td class="text-center text-nowrap">
                                                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProduk" data-id="<?php echo encrypt($data_sph['id_transaksi'], $key_global) ?>" data-nama="<?php echo $data_sph['nama_produk'] ?>" data-nama-edit="<?php echo $data_sph['nama_produk_sph'] ?>" data-merk="<?php echo $data_sph['merk_produk'] ?>" data-harga="<?php echo $data_sph['harga_produk_sph'] ?>" data-stock="<?php echo number_format($data_sph['stock']) ?>" data-qty="<?php echo $data_sph['qty'] ?>" title="edit-data"><i class="bi bi-pencil"></i></button>
                                                                    <a href="proses/proses-sph.php?hapus=<?php echo encrypt($data_sph['id_transaksi'], $key_global) ?> && id_sph= <?php echo encrypt($data_sph['id_sph'], $key_global) ?>" class="btn btn-danger btn-sm delete-data" title="Hapus Data"><i class="bi bi-trash"></i> 
                                                                </td>
                                                            <?php
                                                        }
                                                    ?>  
                                                </tr>
                                        
                                            <?php $no++; ?>
                                       <?php } ?>
                                     
                                    </tbody>
                                <?php
                            }
                        ?>
                    </table>
                </div>
            </div>
             <!-- Kode untuk menampilkan data yang belum di input Qty dan pengecekan harga -->
            <div class="card-body p-2">
                <form action="proses/proses-sph.php" method="post">
                    <?php
                        $no = 1;
                        $sql_sph_cek = "SELECT DISTINCT
                                            sr.id_sph,
                                            tps.id_transaksi,
                                            tps.id_produk,
                                            tps.status_trx,
                                            tps.created_date,
                                            COALESCE(spr.stock, spe.stock) AS stock,
                                            COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                                            COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                            COALESCE(tpr.harga_produk, tpe.harga_produk, tpsm.harga_set_marwa, tpse.harga_set_ecat) AS harga_produk,
                                            COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk) AS merk_produk -- Nama merk untuk produk
                                        FROM transaksi_produk_sph AS tps
                                        LEFT JOIN sph sr ON sr.id_sph = tps.id_sph
                                        LEFT JOIN stock_produk_reguler spr ON tps.id_produk = spr.id_produk_reg
                                        LEFT JOIN stock_produk_ecat spe ON tps.id_produk = spe.id_produk_ecat
                                        LEFT JOIN tb_produk_reguler tpr ON tps.id_produk = tpr.id_produk_reg
                                        LEFT JOIN tb_produk_ecat tpe ON tps.id_produk = tpe.id_produk_ecat
                                        LEFT JOIN tb_produk_set_marwa tpsm ON tps.id_produk = tpsm.id_set_marwa
                                        LEFT JOIN tb_produk_set_ecat tpse ON tps.id_produk = tpse.id_set_ecat
                                        LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                        LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk ecat
                                        LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                        LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                                        WHERE sr.id_sph = '$id_sph' AND tps.status_trx = '0' ORDER BY tps.created_date ASC";
                        $query_sph_cek = mysqli_query($connect, $sql_sph_cek);
                        $totalRows_cek = mysqli_num_rows($query_sph_cek);
                        if ($totalRows_cek != 0) { 
                            echo' 
                                <div class="text-center">
                                    <h5>Tambahan Produk SPH</h5>
                                </div>
                            ';
                        }
                        
                       
                        if ($totalRows_cek != 0) { ?>
                           
                                <div class="row p-2 mobile mobile-no">
                                    <div class="col-sm-1 mb-1 text-center">
                                        <label class="mobile-no">No</label>
                                    </div>
                                    <div class="col-sm-4 mb-1 text-center">
                                        <label>Nama Produk</label>
                                    </div>
                                    <div class="col-sm-2 mb-1 text-center">
                                        <label>Merk</label>
                                    </div>
                                    <div class="col-sm-2 mb-1 text-center">
                                        <label>Harga</label>
                                    </div>
                                    <div class="col-sm-2 mb-1 text-center">
                                        <label>Stock</label>
                                    </div>
                                    <div class="col-sm-1 mb-1 text-center">
                                        <label>Qty</label>
                                    </div>
                                </div>
                            <?php       
                            while ($data_sph_cek = mysqli_fetch_array($query_sph_cek)) { ?>
                                <div class="row p-2">
                                    <div class="col-sm-1 mb-1 div-none">
                                        <label class="mobile-label">No</label>
                                        <input type="hidden" name="id_sph[]" value="<?php echo encrypt($id_sph, $key_global) ?>">
                                        <input type="hidden" class="form-control text-center" name="id_trx[]" id="id_<?php echo $data_sph_cek['id_transaksi'] ?>" value="<?php echo $data_sph_cek['id_transaksi'] ?>">
                                        <input type="text" class="form-control text-center" value="<?php echo $no ?>">
                                    </div>
                                    <div class="col-sm-6 mb-1">
                                        <label class="mobile-label">Nama Produk</label>
                                        <input type="text" class="form-control mobile-text" name="nama_produk[]" value="<?php echo $data_sph_cek['nama_produk'] ?>" required>
                                    </div>
                                    <div class="col-sm-2 mb-1">
                                        <label class="mobile-label">Merk</label>
                                        <input type="text" class="form-control mobile-text text-center bg-light" value="<?php echo $data_sph_cek['merk_produk'] ?>" readonly>
                                    </div>
                                    <div class="col-sm-1 mb-1">
                                        <label class="mobile-label">Harga</label>
                                        <input type="text" class="form-control mobile-text text-end" name="harga[]" id="hargaInput_<?php echo $data_sph_cek['id_transaksi']; ?>" value="<?php echo number_format($data_sph_cek['harga_produk']) ?>" oninput="formatCurrency(this)" required>
                                    </div>
                                    <div class="col-sm-1 mb-1">
                                        <label class="mobile-label">Stock</label>
                                        <input type="text" class="form-control mobile-text text-end bg-light" value="<?php echo number_format($data_sph_cek['stock']) ?>" id="stock_<?php echo $data_sph_cek['id_transaksi'] ?>" readonly>
                                    </div>
                                    <div class="col-sm-1 qty mb-1">
                                        <label class="mobile-label">Qty</label>
                                        <input type="text" class="form-control mobile-text text-end" name="qty[]" id="qtyInput_<?php echo $data_sph_cek['id_transaksi'] ?>" oninput="checkStock('<?php echo $data_sph_cek['id_transaksi'] ?>')">
                                    </div>
                                    
                                </div>
                            <?php
                            $no++;
                            }
                        } 
                    ?>
                    <div class="mt-3 text-center">
                        <?php  
                            if ($totalRows_cek != 0) { 
                                echo '<button type="submit" class="btn btn-primary btn-md" name="simpan-cek-produk"> Simpan Data</button>';
                            }
                        ?>
                    </div>
                </form>
            </div>
        </section>
    </main><!-- End #main -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>
</html>
<!-- Modal -->
<div class="modal fade" id="editProduk" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Produk</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="card-body">
            <form action="proses/proses-sph.php" method="POST">
                <input type="hidden" id="idTmpValue" name="id_trx" class="form-control">
                <input type="hidden" name="id_sph" value="<?php echo encrypt($id_sph, $key_global) ?>" class="form-control">
                <div class="mb-3">
                    <label class="text-start">Nama Produk Asli</label>
                    <input type="text" id="namaTmpValue" class="form-control bg-light" readonly>
                </div>
                <div class="mb-3">
                    <label class="text-start">Nama Produk Edit</label>
                    <input type="text" name="nama_produk_edit" id="nama_produk_edit" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="text-start">Merk Produk</label>
                    <input type="text" id="merkTmpValue" class="form-control bg-light" readonly>
                </div>
                <div class="mb-3">
                    <label class="text-start">Stock Tersedia</label>
                    <input type="text" id="stockTmpValue" class="form-control bg-light" readonly>
                </div>
                <div class="mb-3">
                    <label class="text-start">Qty Order</label>
                    <input type="text" id="qtyTmpValue" name="qty_edit" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="text-start">Harga Produk</label>
                    <input type="text" id="hargaTmpValue" name="harga" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="edit" name="edit-br" disabled>Simpan Perubahan</button>
                </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- Modal Cs-->
<div class="modal fade" id="modalCs" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <style>
                    .wrap-text {
                        max-width: 300px; /* Contoh lebar maksimum */
                        overflow: hidden; /* Sembunyikan teks yang melebihi max-width */
                        white-space: pre-line; /* Tetapkan spasi putih dan pecah baris sesuai dengan teks */
                        word-wrap: break-word; /* Pecah kata jika melebihi max-width */
                    }
                    @media (max-width: 767px) { /* Media query untuk tampilan mobile */
                        .wrap-text {
                            min-width: 400px; /* Contoh lebar maksimum */
                            overflow: hidden; /* Sembunyikan teks yang melebihi max-width */
                            white-space: pre-line; /* Tetapkan spasi putih dan pecah baris sesuai dengan teks */
                            word-wrap: break-word; /* Pecah kata jika melebihi max-width */
                        }
                    }
                </style>
                <div class="card p-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered" id="table3">
                        <thead>
                            <tr class="text-white" style="background-color: navy;">
                            <td class="col-4 text-nowrap">Nama Customer</td>
                            <td class="col-6 text-nowrap">Alamat Customer</td>
                            <td class="col-2 text-nowrap">Telepon</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "koneksi.php";
                            $sql_cs = "SELECT id_cs, nama_cs, alamat, no_telp FROM tb_customer_sph";
                            $query_cs = mysqli_query($connect, $sql_cs);
                            while ($data_cs = mysqli_fetch_array($query_cs)) {
                            ?>
                            <tr data-id="<?php echo $data_cs['id_cs'] ?>" data-nama="<?php echo $data_cs['nama_cs'] ?>" data-alamat="<?php echo $data_cs['alamat'] ?>" data-bs-dismiss="modal">
                                <td><?php echo $data_cs['nama_cs'] ?></td>
                                <td class="wrap-text"><?php echo $data_cs['alamat'] ?></td>
                                <td class="text-nowrap"><?php echo $data_cs['no_telp'] ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Large Modal-->

<!-- Modal Barang -->
<div class="modal fade" id="modalBarang" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form method="post" action=""> <!-- Tambahkan form dengan method POST -->
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Data Barang</h1>
                </div>
                <div class="modal-body">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="produk-reg-tab" data-bs-toggle="pill" data-bs-target="#produk-reg" type="button" role="tab" aria-controls="produk-reg" aria-selected="true">Produk Reguler</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="produk-ecat-tab" data-bs-toggle="pill" data-bs-target="#produk-ecat" type="button" role="tab" aria-controls="produk-ecat" aria-selected="false">Produk E-Cat</button>
                    </li>
                </ul>
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="produk-reg" role="tabpanel" aria-labelledby="produk-reg-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="tableBr">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="text-center p-3 text-nowrap" style="width: 50px">No</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 200px">Kode Produk</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 350px">Nama Produk</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Satuan</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Merk</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Stock</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $selected_produk = [];
                                    $id_sph_produk = $id_sph;
                                    $no = 1;

                                    // Mengambil data produk yang ada dalam tmp_produk_sph untuk id_sph yang sedang aktif
                                    $query_selected_produk = mysqli_query($connect, "SELECT id_produk FROM transaksi_produk_sph WHERE id_sph = '$id_sph_produk'");
                                    while ($selected_data = mysqli_fetch_array($query_selected_produk)) {
                                        $selected_produk[] = $selected_data['id_produk'];
                                    }

                                    $sql = "SELECT 
                                                COALESCE(tpr.id_produk_reg, tpsm.id_set_marwa) AS id_produk,
                                                COALESCE(tpr.kode_produk, tpsm.kode_set_marwa) AS kode_produk,
                                                COALESCE(tpr.nama_produk, tpsm.nama_set_marwa) AS nama_produk,
                                                COALESCE(mr_tpr.nama_merk, mr_tpsm.nama_merk) AS nama_merk,
                                                tpr.satuan,
                                                spr.id_stock_prod_reg,
                                                spr.stock,
                                                tkp.min_stock, 
                                                tkp.max_stock
                                            FROM stock_produk_reguler AS spr
                                            LEFT JOIN tb_produk_reguler AS tpr ON (tpr.id_produk_reg = spr.id_produk_reg)
                                            LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spr.id_kat_penjualan)
                                            LEFT JOIN tb_produk_set_marwa AS tpsm ON (tpsm.id_set_marwa = spr.id_produk_reg)
                                            LEFT JOIN tb_merk AS mr_tpr ON (tpr.id_merk = mr_tpr.id_merk)
                                            LEFT JOIN tb_merk AS mr_tpsm ON (tpsm.id_merk = mr_tpsm.id_merk)
                                            ORDER BY nama_produk ASC";

                                    $query = mysqli_query($connect, $sql);

                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_produk = $data['id_produk'];
                                        $id_produk_substr = substr($id_produk, 0, 2);
                                        $isChecked = in_array($id_produk, $selected_produk);
                                        $isDisabled = false;

                                        if ($data['stock'] == 0) {
                                            $isDisabled = true; // Jika stock = 0, maka tombol pilih akan menjadi disabled
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                            <td class="text-nowrap text-center"><?php echo $data['kode_produk']; ?></td>
                                            <td class="text-nowrap"><?php echo $data['nama_produk']; ?></td>
                                            <td class="text-center text-nowrap">
                                                <?php 
                                                if($id_produk_substr == 'BR'){
                                                    echo $data['satuan'];
                                                } else {
                                                    echo "Set";
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center text-nowrap"><?php echo $data['nama_merk']; ?></td>
                                            <td class="text-center text-nowrap"><?php echo number_format($data['stock']); ?></td>
                                            <td class="text-center text-nowrap">
                                                <button class="btn-pilih btn btn-primary btn-sm" data-id="<?php echo $id_produk; ?>"  data-sph="<?php echo $id_sph_produk; ?>" <?php echo ($isChecked || $isDisabled) ? 'disabled' : ''; ?>>Pilih</button>
                                            </td>
                                        </tr>
                                        <?php $no++; ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="produk-ecat" role="tabpanel" aria-labelledby="produk-ecat-tab" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered" id="tableBr2">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <td class="text-center p-3 text-nowrap" style="width: 50px">No</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 200px">Kode Produk</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 350px">Nama Produk</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Satuan</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Merk</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Stock</td>
                                        <td class="text-center p-3 text-nowrap" style="width: 100px">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "koneksi.php";
                                    $id_sph = $_GET['id'];
                                    $selected_produk = [];
                                    $id_sph_produk = $id_sph;
                                    $no = 1;

                                    // Mengambil data produk yang ada dalam tmp_produk_sph untuk id_sph yang sedang aktif
                                    $query_selected_produk = mysqli_query($connect, "SELECT id_produk FROM transaksi_produk_sph WHERE id_sph = '$id_sph_produk'");
                                    while ($selected_data = mysqli_fetch_array($query_selected_produk)) {
                                        $selected_produk[] = $selected_data['id_produk'];
                                    }
                                    $sql = "SELECT 
                                                COALESCE(tpe.id_produk_ecat, tpse.id_set_ecat) AS id_produk,
                                                COALESCE(tpe.kode_produk, tpse.kode_set_ecat) AS kode_produk,
                                                COALESCE(tpe.nama_produk, tpse.nama_set_ecat) AS nama_produk,
                                                COALESCE(mr_tpe.nama_merk, mr_tpse.nama_merk) AS nama_merk,
                                                tpe.satuan,
                                                spe.id_stock_prod_ecat,
                                                spe.stock,
                                                tkp.min_stock, 
                                                tkp.max_stock
                                            FROM stock_produk_ecat AS spe
                                            LEFT JOIN tb_produk_ecat AS tpe ON (tpe.id_produk_ecat = spe.id_produk_ecat)
                                            LEFT JOIN tb_kat_penjualan AS tkp ON (tkp.id_kat_penjualan = spe.id_kat_penjualan)
                                            LEFT JOIN tb_produk_set_ecat AS tpse ON (tpse.id_set_ecat = spe.id_produk_ecat)
                                            LEFT JOIN tb_merk AS mr_tpe ON (tpe.id_merk = mr_tpe.id_merk)
                                            LEFT JOIN tb_merk AS mr_tpse ON (tpse.id_merk = mr_tpse.id_merk)
                                            ORDER BY nama_produk ASC";
                                    $query = mysqli_query($connect, $sql);
                                    while ($data = mysqli_fetch_array($query)) {
                                        $id_produk = $data['id_produk'];
                                        $id_produk_substr = substr($id_produk, 0, 2);
                                        $isChecked = in_array($id_produk, $selected_produk);
                                        $isDisabled = false;

                                        if ($data['stock'] == 0) {
                                            $isDisabled = true; // Jika stock = 0, maka tombol pilih akan menjadi disabled
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                            <td class="text-nowrap text-center"><?php echo $data['kode_produk']; ?></td>
                                            <td class="text-nowrap"><?php echo $data['nama_produk']; ?></td>
                                            <td class="text-center text-nowrap">
                                                <?php 
                                                if($id_produk_substr == 'BR'){
                                                    echo $data['satuan'];
                                                } else {
                                                    echo "Set";
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center text-nowrap"><?php echo $data['nama_merk']; ?></td>
                                            <td class="text-center text-nowrap"><?php echo number_format($data['stock']); ?></td>
                                            <td class="text-center text-nowrap">
                                                <button class="btn-pilih btn btn-primary btn-sm" data-id="<?php echo $id_produk; ?>"  data-sph="<?php echo $id_sph_produk; ?>" <?php echo ($isChecked || $isDisabled) ? 'disabled' : ''; ?>>Pilih</button>
                                            </td>
                                        </tr>
                                        <?php $no++; ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="refreshPage()">Close</button>
                </div>
            </form> <!-- Akhir dari form -->
        </div>
    </div>
</div>
<!-- End Modal -->

<!-- Edit Data Produk -->
<script>
    $(document).ready(function() {
        var initialData = {
            qty: null,
            harga: null,
            nama_produk_edit: null
        };

        $('#editProduk').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var idTmp = button.data('id');
            var sphTmp = button.data('sph');
            var namaTmp = button.data('nama');
            var namaEdit = button.data('nama-edit');
            var merkTmp = button.data('merk');
            var hargaTmp = button.data('harga');
            var qtyTmp = button.data('qty');
            var stockTmp = button.data('stock');

            // Simpan nilai awal hanya untuk input yang dapat diedit
            initialData.qty = parseFloat(qtyTmp);
            initialData.harga = parseFloat(hargaTmp);
            initialData.nama_produk_edit = namaEdit;

            console.log(initialData);

            // Format angka qty, stock, dan harga dengan ribuan separator
            $('#idTmpValue').val(idTmp);
            $('#namaTmpValue').val(namaTmp);
            $('#merkTmpValue').val(merkTmp);
            $('#hargaTmpValue').val(numberWithCommas(hargaTmp));
            $('#stockTmpValue').val(numberWithCommas(stockTmp));
            $('#qtyTmpValue').val(numberWithCommas(qtyTmp));
            $('#nama_produk_edit').val(namaEdit);
        });

        // Fungsi untuk memformat angka dengan ribuan separator
        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
        }

        // Fungsi validasi tombol edit
        function validateForm() {
            var currentQty = parseFloat($('#qtyTmpValue').val().replace(/,/g, '')) || 0;
            var currentHarga = parseFloat($('#hargaTmpValue').val().replace(/,/g, '')) || 0;
            var currentNama = $('#nama_produk_edit').val().trim();

            // Periksa perubahan hanya pada input yang bisa diedit
            if (currentQty !== initialData.qty || 
                currentHarga !== initialData.harga || 
                currentNama !== initialData.nama_produk_edit) {
                $('#edit').prop('disabled', false);
            } else {
                $('#edit').prop('disabled', true);
            }
        }

        // Event listener untuk input yang dapat diubah
        $('#qtyTmpValue, #hargaTmpValue, #nama_produk_edit').on('input', function() {
            var inputId = $(this).attr('id');
            var value = $(this).val().replace(/,/g, '');

            if (isNaN(value) || value === '') {
                value = 0;
            }

            if (inputId === 'qtyTmpValue' || inputId === 'hargaTmpValue') {
                $(this).val(numberWithCommas(value));
            }

            validateForm();
        });
    });
</script>


<!-- date picker with flatpick -->
<script type="text/javascript">
  flatpickr("#date", {
    dateFormat: "d/m/Y",
  });
</script>
<!-- end date picker -->

<script>
    $(document).ready(function() {
        var table = $('#tableBr').DataTable({
            "lengthChange": false,
            "ordering": false,
            "autoWidth": false
        });

        var table = $('#tableBr2').DataTable({
            "lengthChange": false,
            "ordering": false,
            "autoWidth": false
        });
    });
</script>


<!-- Select Data -->
<script>
  $(document).on('click', '#table3 tbody tr', function(e) {
    $('#id').val($(this).data('id'));
    $('#cs').val($(this).data('nama'));
    $('#alamat').val($(this).data('alamat'));
    $('#modalCs').modal('hide');
    $('#editPelanggan').modal('show');
  });
</script>


<script>
    function refreshPage() {
        location.reload();
    }
</script>

<!-- Kode untuk pilih produk sph -->
<script>
    $(document).ready(function() {
        // Mengikat event untuk tombol detail
        $('.btn-detail').click(function() {
            var idsph = $(this).data('sph');
            $('#sph').text(idsph);

            // Mengubah atribut data-sph pada tombol btn-pilih
            $('button.btn-pilih').attr('data-sph', idsph);

            // Menampilkan modal
            $('#modalBarang').modal('show');
        });

        // Menggunakan delegasi event untuk elemen yang baru dimuat oleh DataTables
        $(document).on('click', '.btn-pilih', function(event) {
            event.preventDefault();
            event.stopPropagation();

            var id = $(this).data('id');
            var sph = $(this).attr('data-sph');
            var nama = $(this).attr('data-nama');
            var harga = $(this).attr('data-harga');

            // Panggil fungsi untuk menyimpan data
            saveData(id, sph, nama, harga);
        });

        // Fungsi untuk menyimpan data
        function saveData(id, sph, nama, harga) {
            $.ajax({
                url: 'simpan-data-sph.php',
                type: 'POST',
                data: {
                    id: id,
                    sph: sph,
                    nama: nama,
                    harga: harga
                },
                success: function(response) {
                    console.log('Data berhasil disimpan.');
                    // Menonaktifkan tombol setelah data disimpan
                    $('button[data-id="' + id + '"]').prop('disabled', true);
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan saat menyimpan data:', error);
                }
            });
        }
    });
</script>

<!-- format Curency -->
<script>
function formatCurrency(input) {
    // Menghapus semua karakter non-digit
    var cleanValue = input.value.replace(/\D/g, '');
    
    // Mengonversi ke angka
    var numberValue = parseInt(cleanValue);
    
    // Jika hasil konversi NaN, ganti dengan angka 0
    if (isNaN(numberValue)) {
        numberValue = 0;
    }
    
    // Memformat angka ke format rupiah Indonesia dengan pemisah koma
    var formattedValue = numberValue.toLocaleString('id-ID').replace(/\./g, ',');
    
    // Menetapkan nilai input dengan format yang diformat
    input.value = formattedValue;
}
</script>


<!-- Kode Untuk Qty   -->
<script>
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function formatInputValue(value) {
        // Hanya angka yang diperbolehkan dan batasi hingga 9 digit
        return value.replace(/\D/g, "").slice(0, 9).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function checkStock(inputId) {
        var stock = parseInt(document.getElementById('stock_' + inputId).value.replace(/,/g, '')) || 0; 
        var qtyInput = document.getElementById('qtyInput_' + inputId); 
        var qty = qtyInput.value.replace(/,/g, '');

        // Batasi input hanya untuk angka dan maksimal 9 digit
        qty = qty.slice(0, 9); 

        qtyInput.value = formatInputValue(qty);

        // Cek jika qty melebihi stock
        // if (parseInt(qty) > stock) {
        //     qtyInput.value = formatNumber(stock);
        // }
    }
</script>

<script>
    function reloadPage() {
        location.reload();
    }
</script>
