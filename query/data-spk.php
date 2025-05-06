<?php  
    // $sql_spk = "SELECT 
    //                 trx.id_spk,
    //                 trx.id_produk,
    //                 spk.id_spk_reg, 
    //                 spk.no_spk,
    //                 cs.nama_cs,
    //                 tks.id_transaksi,
    //                 tks.status_input
    //             FROM `transaksi_produk_reg` AS trx
    //             LEFT JOIN spk_reg spk ON (trx.id_spk = spk.id_spk_reg)
    //             LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
    //             LEFT JOIN tmp_kartu_stock tks ON (trx.id_transaksi = tks.id_transaksi)
    //             WHERE id_produk = '$id_produk' AND tks.status_input = '0'";
    // $query_spk = $connect->query($sql_spk);

    require_once 'koneksi-ecat.php';

    $sql_spk = "SELECT 
                    COALESCE(spk.id_spk_reg, spk_ecat.id_spk_ecat, spk_pl.id_spk_pl) AS id_spk,
                    COALESCE(spk.no_spk, spk_ecat.no_spk_ecat, spk_pl.no_spk_pl) AS no_spk,
                    COALESCE(trx.id_produk, trx_ecat.id_produk, trx_pl.id_produk) AS id_produk,
                    COALESCE(cs.nama_cs, cs_ecat.nama_perusahaan, cs_pl.nama_perusahaan) AS nama_cs,
                    tks.id_transaksi,
                    tks.status_input
                FROM tmp_kartu_stock AS tks
                LEFT JOIN $db.transaksi_produk_reg trx ON (tks.id_transaksi = trx.id_transaksi)
                LEFT JOIN $db_ecat.transaksi_produk_ecat trx_ecat ON (tks.id_transaksi = trx_ecat.id_transaksi_ecat)
                LEFT JOIN $db_ecat.transaksi_produk_pl trx_pl ON (tks.id_transaksi = trx_pl.id_transaksi_pl)
                LEFT JOIN $db.spk_reg spk ON (trx.id_spk = spk.id_spk_reg)
                LEFT JOIN $db_ecat.tb_spk_ecat spk_ecat ON (trx_ecat.id_spk = spk_ecat.id_spk_ecat)
                LEFT JOIN $db_ecat.tb_spk_pl spk_pl  ON (trx_pl.id_spk = spk_pl.id_spk_pl)
                LEFT JOIN $db.tb_customer cs ON (spk.id_customer = cs.id_cs)
                LEFT JOIN $db_ecat.tb_perusahaan cs_ecat ON (spk_ecat.id_perusahaan = cs_ecat.id_perusahaan) 
                LEFT JOIN $db_ecat.tb_perusahaan cs_pl ON (spk_pl.id_perusahaan = cs_pl.id_perusahaan)
                WHERE COALESCE(trx.id_produk, trx_ecat.id_produk, trx_pl.id_produk) = '$id_produk' AND tks.status_input = '0'";
    $query_spk = $connect->query($sql_spk);
?>