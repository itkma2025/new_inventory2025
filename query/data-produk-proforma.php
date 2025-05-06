<?php  
    if ($_GET['jenis'] == 'nonppn') {
        $sub_total = 0;
        $sql_trx = "SELECT 
                        nonppn.id_inv_nonppn, 
                        nonppn.kategori_inv,
                        nonppn.sp_disc,  
                        spk.id_inv, 
                        spk.no_spk,
                        trx.id_transaksi,
                        trx.id_produk,
                        trx.nama_produk_spk,
                        trx.harga,
                        trx.qty,
                        trx.disc,
                        trx.disc_cb,
                        trx.total_harga,
                        trx.status_trx,
                        trx.created_date,
                        COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                        COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                        COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk)AS merk_produk -- Nama merk untuk produk reguler
                    FROM inv_nonppn AS nonppn
                    LEFT JOIN spk_reg spk ON (nonppn.id_inv_nonppn = spk.id_inv)
                    LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                    LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                    LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                    LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                    LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                    LEFT JOIN tb_produk_ecat tpe ON trx.id_produk = tpe.id_produk_ecat
                    LEFT JOIN tb_produk_set_ecat tpse ON trx.id_produk = tpse.id_set_ecat
                    LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk reguler
                    LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                    WHERE nonppn.id_inv_nonppn = '$id_inv'";

        // kondisi status trx = 1
        $sql_trx_1 = $sql_trx . " AND status_trx = '1' ORDER BY trx.created_date ASC";
        $trx_produk_reg = $connect->query($sql_trx_1);

        // kondisi status trx = 1
        $sql_trx_0 = $sql_trx . " AND status_trx = '0' ORDER BY trx.created_date ASC";
        $query_cek_harga = $connect->query($sql_trx_0);
    } else if ($_GET['jenis'] == 'ppn') {
        $sub_total = 0;
        $sql_trx = "SELECT 
                        ppn.id_inv_ppn, 
                        ppn.kategori_inv,
                        ppn.sp_disc,
                        STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y') AS tgl_inv,
                        spk.id_inv, 
                        spk.no_spk,
                        trx.id_transaksi,
                        trx.id_produk,
                        trx.nama_produk_spk,
                        trx.harga,
                        trx.qty,
                        trx.disc,
                        trx.disc_cb,
                        trx.total_harga,
                        trx.status_trx,
                        trx.created_date,
                        COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                        COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                        COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk)AS merk_produk -- Nama merk untuk produk reguler
                    FROM inv_ppn AS ppn
                    LEFT JOIN spk_reg spk ON (ppn.id_inv_ppn = spk.id_inv)
                    LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                    LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                    LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                    LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                    LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                    LEFT JOIN tb_produk_ecat tpe ON trx.id_produk = tpe.id_produk_ecat
                    LEFT JOIN tb_produk_set_ecat tpse ON trx.id_produk = tpse.id_set_ecat
                    LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk reguler
                    LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                    WHERE ppn.id_inv_ppn = '$id_inv'";

        // kondisi status trx = 1
        $sql_trx_1 = $sql_trx . " AND status_trx = '1' ORDER BY trx.created_date ASC";
        $trx_produk_reg = $connect->query($sql_trx_1);

        // kondisi status trx = 1
        $sql_trx_0 = $sql_trx . " AND status_trx = '0' ORDER BY trx.created_date ASC";
        $query_cek_harga = $connect->query($sql_trx_0);
    } else if ($_GET['jenis'] == 'bum') {
        $sub_total = 0;
        $sql_trx = "SELECT 
                        bum.id_inv_bum, 
                        bum.kategori_inv,
                        bum.sp_disc,  
                        spk.id_inv, 
                        spk.no_spk,
                        trx.id_transaksi,
                        trx.id_produk,
                        trx.nama_produk_spk,
                        trx.harga,
                        trx.qty,
                        trx.disc,
                        trx.disc_cb,
                        trx.total_harga,
                        trx.status_trx,
                        trx.created_date,
                        COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                        COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                        COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk)AS merk_produk -- Nama merk untuk produk reguler
                    FROM inv_bum AS bum
                    LEFT JOIN spk_reg spk ON (bum.id_inv_bum = spk.id_inv)
                    LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                    LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                    LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                    LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                    LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                    LEFT JOIN tb_produk_ecat tpe ON trx.id_produk = tpe.id_produk_ecat
                    LEFT JOIN tb_produk_set_ecat tpse ON trx.id_produk = tpse.id_set_ecat
                    LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk reguler
                    LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                    WHERE bum.id_inv_bum = '$id_inv'";

        // kondisi status trx = 1
        $sql_trx_1 = $sql_trx . " AND status_trx = '1' ORDER BY trx.created_date ASC";
        $trx_produk_reg = $connect->query($sql_trx_1);

        // kondisi status trx = 1
        $sql_trx_0 = $sql_trx . " AND status_trx = '0' ORDER BY trx.created_date ASC";
        $query_cek_harga = $connect->query($sql_trx_0);
    } else {
        ?>
            <script type="text/javascript">
                window.location.href = "../404.php";
            </script>
        <?php
    }

    
?>