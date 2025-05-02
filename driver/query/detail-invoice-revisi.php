<?php 
    require_once "akses.php";
    $sql_inv = "SELECT
                        COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                        COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                        COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                        COALESCE(nonppn.kategori_inv, ppn.kategori_inv, bum.kategori_inv) AS kategori_inv,
                        COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                        COALESCE(nonppn.sp_disc, ppn.sp_disc, bum.sp_disc) AS sp_disc,
                        COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo) AS tgl_tempo,
                        COALESCE(nonppn.ongkir, ppn.ongkir, bum.ongkir) AS ongkir,
                        COALESCE(nonppn.note_inv, ppn.note_inv, bum.note_inv) AS note_inv,
                        COALESCE(spk_nonppn.id_spk_reg, spk_ppn.id_spk_reg, spk_bum.id_spk_reg) AS id_spk,
                        COALESCE(spk_nonppn.no_spk, spk_ppn.no_spk, spk_bum.no_spk) AS no_spk,
                        sr.id_user, 
                        sr.no_spk,
                        sr.id_customer, 
                        sr.no_po, 
                        sr.tgl_pesanan,
                        cs.nama_cs, 
                        cs.alamat,
                        ordby.order_by,
                        sl.nama_sales,
                        STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                        ik.id_komplain,
                        ik.no_komplain,
                        kk.kat_komplain,
                        kk.kondisi_pesanan,
                        kk.status_refund,
                        tpk.id_produk,
                        tpk.nama_produk,
                        tpk.harga,
                        tpk.qty,
                        tpk.disc,
                        tpk.total_harga,
                        tpk.created_date,
                        pr.satuan,
                        COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS merk
                    FROM spk_reg AS sr
                    LEFT JOIN inv_nonppn nonppn ON sr.id_inv = nonppn.id_inv_nonppn
                    LEFT JOIN inv_ppn ppn ON sr.id_inv = ppn.id_inv_ppn
                    LEFT JOIN inv_bum bum ON sr.id_inv = bum.id_inv_bum
                    JOIN tb_customer cs ON sr.id_customer = cs.id_cs
                    JOIN tb_orderby ordby ON sr.id_orderby = ordby.id_orderby
                    JOIN tb_sales sl ON sr.id_sales = sl.id_sales
                    LEFT JOIN inv_komplain AS ik ON nonppn.id_inv_nonppn = ik.id_inv OR ppn.id_inv_ppn = ik.id_inv OR bum.id_inv_bum = ik.id_inv
                    LEFT JOIN komplain_kondisi kk ON kk.id_komplain = ik.id_komplain
                    LEFT JOIN spk_reg spk_nonppn ON ik.id_inv = spk_nonppn.id_inv
                    LEFT JOIN spk_reg spk_ppn ON ik.id_inv = spk_ppn.id_inv
                    LEFT JOIN spk_reg spk_bum ON ik.id_inv = spk_bum.id_inv
                    LEFT JOIN tmp_produk_komplain tpk ON spk_nonppn.id_inv = tpk.id_inv OR spk_ppn.id_inv = tpk.id_inv OR spk_bum.id_inv = tpk.id_inv
                    LEFT JOIN tb_produk_reguler pr ON tpk.id_produk = pr.id_produk_reg
                    LEFT JOIN tb_produk_set_marwa tpsm ON tpk.id_produk = tpsm.id_set_marwa
                    LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                    LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                    WHERE COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) = '$id_inv'";
    // Data Untuk Menampilkan detail
    $detail = $connect->query($sql_inv . " GROUP BY sr.no_spk ORDER BY sr.no_spk ASC");
    $detail2 = $connect->query($sql_inv . " GROUP BY sr.no_spk ORDER BY sr.no_spk ASC");

    // Data Untuk Menampilkan detail
    $produk = $connect->query($sql_inv . " ORDER BY tpk.created_date ASC");


?>
