<?php  
    require_once "../akses.php";
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);
    require_once '../koneksi-ecat.php';
    $sql_tmp = "SELECT 
                    tmp.id_tmp,
                    tmp.id_transaksi, 
                    tmp.id_produk_ks,
                    tmp.id_spk_ks,
                    tmp.status_barang,
                    tmp.qty_ks,
                    tmp.input_date,
                    tmp.input_by,
                    COALESCE(spk.no_spk, spk_ecat.no_spk_ecat, spk_pl.no_spk_pl) AS no_spk,
                    COALESCE(spk.tgl_spk, spk_ecat.tgl_spk_ecat, spk_pl.tgl_spk_pl) AS tgl_spk,
                    COALESCE(cs.nama_cs, cs_ecat.nama_perusahaan, cs_pl.nama_perusahaan) AS nama_cs,
                    COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                    COALESCE(tpr.id_merk, tpe.id_merk, tpsm.id_merk, tpse.id_merk) AS id_merk
                FROM tmp_kartu_stock AS tmp
                LEFT JOIN $db.spk_reg spk ON tmp.id_spk_ks = spk.id_spk_reg
                LEFT JOIN $db_ecat.tb_spk_ecat spk_ecat ON tmp.id_spk_ks = spk_ecat.id_spk_ecat
                LEFT JOIN $db_ecat.tb_spk_pl spk_pl  ON tmp.id_spk_ks = spk_pl.id_spk_pl
                LEFT JOIN $db.tb_customer cs ON spk.id_customer = cs.id_cs
                LEFT JOIN $db_ecat.tb_perusahaan cs_ecat ON spk_ecat.id_perusahaan = cs_ecat.id_perusahaan
                LEFT JOIN $db_ecat.tb_perusahaan cs_pl ON spk_pl.id_perusahaan = cs_pl.id_perusahaan
                LEFT JOIN $db.tb_produk_reguler tpr ON tmp.id_produk_ks = tpr.id_produk_reg
                LEFT JOIN $db.tb_produk_ecat tpe ON tmp.id_produk_ks = tpe.id_produk_ecat
                LEFT JOIN $db.tb_produk_set_marwa tpsm ON tmp.id_produk_ks = tpsm.id_set_marwa
                LEFT JOIN $db.tb_produk_set_ecat tpse ON tmp.id_produk_ks = tpse.id_set_ecat";

    // Menampilkan data dengan grouping 
    $sql_tmp_grouping = $sql_tmp . " WHERE tmp.input_by = '" . $id_user . "' GROUP BY tmp.id_spk_ks ORDER BY tmp.input_date";
    $tmp_grouping = $connect->query($sql_tmp_grouping);

    // Menampilkan data tanpa grouping
    // $sql_tmp_grouping = $sql_tmp . " GROUP BY tmp.id_spk_ks ORDER BY tmp.input_date";
    // $tmp_grouping = $connect->query($sql_tmp_grouping);

?>