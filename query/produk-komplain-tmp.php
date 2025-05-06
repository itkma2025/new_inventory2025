<?php  
    include "koneksi.php";
    // Query Untuk Table
    $sql_tmp = "SELECT DISTINCT
                    COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                    STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                    ik.id_inv,
                    ik.id_komplain,
                    kk.kat_komplain,
                    kk.kondisi_pesanan,
                    kk.status_refund,
                    COALESCE(spk_nonppn.id_spk_reg, spk_ppn.id_spk_reg, spk_bum.id_spk_reg) AS id_spk,
                    COALESCE(spk_nonppn.no_spk, spk_ppn.no_spk, spk_bum.no_spk) AS no_spk,
                    tpk.id_tmp,
                    tpk.id_produk,
                    tpk.nama_produk,
                    tpk.harga,
                    tpk.qty,
                    tpk.disc,
                    tpk.total_harga,
                    COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS merk,
                    spr.stock
                FROM inv_komplain AS ik
                LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
                LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
                LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                LEFT JOIN komplain_kondisi kk ON ik.id_komplain = kk.id_komplain
                LEFT JOIN spk_reg spk_nonppn ON nonppn.id_inv_nonppn = spk_nonppn.id_inv
                LEFT JOIN spk_reg spk_ppn ON ppn.id_inv_ppn = spk_ppn.id_inv
                LEFT JOIN spk_reg spk_bum ON bum.id_inv_bum = spk_bum.id_inv
                LEFT JOIN tmp_produk_komplain tpk ON nonppn.id_inv_nonppn = tpk.id_inv OR ppn.id_inv_ppn = tpk.id_inv OR bum.id_inv_bum = tpk.id_inv
                LEFT JOIN tb_produk_reguler pr ON tpk.id_produk = pr.id_produk_reg
                LEFT JOIN tb_produk_set_marwa tpsm ON tpk.id_produk = tpsm.id_set_marwa
                LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                LEFT JOIN stock_produk_reguler spr ON tpk.id_produk = spr.id_produk_reg
                WHERE ik.id_inv = '$id_inv' AND status_tmp = '1' AND status_br_refund = '0'";
    $query_tmp = mysqli_query($connect, $sql_tmp);
    $query_total = mysqli_query($connect, $sql_tmp);

    // Query tampil Produk
    $sql_produk = " SELECT DISTINCT
                        tpk.id_tmp,
                        tpk.id_produk,
                        tpk.nama_produk,
                        tpk.harga,
                        tpk.qty,
                        tpk.disc,
                        tpk.total_harga,
                        tpk.created_date,
                        COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS merk,
                        spr.stock
                    FROM tmp_produk_komplain AS tpk
                    LEFT JOIN tb_produk_reguler pr ON tpk.id_produk = pr.id_produk_reg
                    LEFT JOIN tb_produk_set_marwa tpsm ON tpk.id_produk = tpsm.id_set_marwa
                    LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                    LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                    LEFT JOIN stock_produk_reguler spr ON tpk.id_produk = spr.id_produk_reg
                    WHERE tpk.id_inv = '$id_inv' AND status_tmp = '1' AND status_br_refund = '0' ORDER BY tpk.created_date ASC";
    $query_produk = mysqli_query($connect, $sql_produk);
    $query_produk_total = mysqli_query($connect, $sql_produk);


    $sql_refund = "SELECT DISTINCT
                    COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                    STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                    ik.id_komplain,
                    kk.kat_komplain,
                    kk.kondisi_pesanan,
                    kk.status_refund,
                    tpk.id_tmp,
                    tpk.id_produk,
                    tpk.nama_produk,
                    tpk.harga,
                    tpk.qty,
                    tpk.disc,
                    tpk.total_harga,
                    COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS merk,
                    spr.stock
                FROM inv_komplain AS ik
                LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
                LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
                LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                LEFT JOIN komplain_kondisi kk ON ik.id_komplain = kk.id_komplain
                LEFT JOIN tmp_produk_komplain tpk ON nonppn.id_inv_nonppn = tpk.id_inv OR ppn.id_inv_ppn = tpk.id_inv OR bum.id_inv_bum = tpk.id_inv
                LEFT JOIN tb_produk_reguler pr ON tpk.id_produk = pr.id_produk_reg
                LEFT JOIN tb_produk_set_marwa tpsm ON tpk.id_produk = tpsm.id_set_marwa
                LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                LEFT JOIN stock_produk_reguler spr ON tpk.id_produk = spr.id_produk_reg
                WHERE (nonppn.id_inv_nonppn = '$id_inv' OR ppn.id_inv_ppn = '$id_inv' OR bum.id_inv_bum = '$id_inv') AND status_tmp = '1' AND status_br_refund = '1'";
    $query_refund = mysqli_query($connect, $sql_refund);
    $query_total_refund = mysqli_query($connect, $sql_refund);

    // Query tampil Produk
    $sql_produk_cancel = " SELECT DISTINCT
                            trx.id_trx,
                            trx.id_produk,
                            trx.nama_produk,
                            trx.harga,
                            trx.qty,
                            trx.disc,
                            trx.total_harga,
                            STR_TO_DATE(trx.created_produk, '%Y-%m-%d  %H:%i:%s') AS created_produk_date,
                            COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS merk        
                        FROM trx_prod_cancel_komplain AS trx
                        LEFT JOIN tb_produk_reguler pr ON trx.id_produk = pr.id_produk_reg
                        LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                        LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                        LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                        WHERE trx.id_inv = '$id_inv' ORDER BY created_produk_date ASC";
    $query_produk_cancel = mysqli_query($connect, $sql_produk_cancel);
?>