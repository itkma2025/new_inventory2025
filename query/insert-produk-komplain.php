<?php  
    include "../koneksi.php";
    $sql_insert = mysqli_query($connect, " INSERT IGNORE INTO tmp_produk_komplain (id_inv, id_trx, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp)
                                            SELECT
                                                COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                                                tpr.id_transaksi,
                                                tpr.id_produk,
                                                tpr.nama_produk_spk,
                                                tpr.harga,
                                                tpr.qty,
                                                tpr.disc,
                                                tpr.total_harga,
                                                1 as status_tmp
                                            FROM inv_komplain AS ik
                                            LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
                                            LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
                                            LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                                            LEFT JOIN spk_reg spk_nonppn ON ik.id_inv = spk_nonppn.id_inv
                                            LEFT JOIN spk_reg spk_ppn ON ik.id_inv = spk_ppn.id_inv
                                            LEFT JOIN spk_reg spk_bum ON ik.id_inv = spk_bum.id_inv
                                            LEFT JOIN transaksi_produk_reg tpr ON spk_nonppn.id_spk_reg = tpr.id_spk OR spk_ppn.id_spk_reg = tpr.id_spk OR spk_bum.id_spk_reg = tpr.id_spk
                                            WHERE nonppn.id_inv_nonppn = '$id_inv' OR ppn.id_inv_ppn = '$id_inv' OR bum.id_inv_bum = '$id_inv'");
    
?>