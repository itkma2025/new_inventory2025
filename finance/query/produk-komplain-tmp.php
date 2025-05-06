<?php  
    include "../akses.php";
    // Query Untuk Table
    $sql_tmp = "SELECT DISTINCT
                    COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                    STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                    ik.id_komplain,
                    ik.kat_komplain,
                    ik.kondisi_pesanan,
                    ik.status_refund,
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
                LEFT JOIN spk_reg spk_nonppn ON ik.id_inv = spk_nonppn.id_inv
                LEFT JOIN spk_reg spk_ppn ON ik.id_inv = spk_ppn.id_inv
                LEFT JOIN spk_reg spk_bum ON ik.id_inv = spk_bum.id_inv
                LEFT JOIN tmp_produk_komplain tpk ON spk_nonppn.id_inv = tpk.id_inv OR spk_ppn.id_inv = tpk.id_inv OR spk_bum.id_inv = tpk.id_inv
                LEFT JOIN tb_produk_reguler pr ON tpk.id_produk = pr.id_produk_reg
                LEFT JOIN tb_produk_set_marwa tpsm ON tpk.id_produk = tpsm.id_set_marwa
                LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                LEFT JOIN stock_produk_reguler spr ON tpk.id_produk = spr.id_produk_reg
                WHERE nonppn.id_inv_nonppn = '$id_inv' OR ppn.id_inv_ppn = '$id_inv' OR bum.id_inv_bum = '$id_inv' AND status_tmp = '1' AND status_br_refund = '0'";
    $query_tmp = mysqli_query($connect, $sql_tmp);
    $query_total = mysqli_query($connect, $sql_tmp);


?>