<?php
    // Koneksi DB
    include 'koneksi.php';
    include 'koneksi-ecat.php';

    $sql_total_review_reg = "SELECT COUNT(*) AS total_reg
                                FROM spk_reg AS sr
                                LEFT JOIN inv_nonppn AS nonppn ON sr.id_inv = nonppn.id_inv_nonppn
                                LEFT JOIN inv_ppn AS ppn ON sr.id_inv = ppn.id_inv_ppn
                                LEFT JOIN inv_bum AS bum ON sr.id_inv = bum.id_inv_bum
                                LEFT JOIN status_kirim sk ON sr.id_inv = sk.id_inv
                                WHERE COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) = 'Diterima'
                                AND sk.status_review = '0'";


    $sql_total_review_ecat = "SELECT COUNT(DISTINCT ecat.no_inv_ecat) AS total_ecat
                                FROM tb_spk_ecat AS spk_ecat
                                LEFT JOIN inv_ecat AS ecat ON spk_ecat.id_inv_ecat = ecat.id_inv_ecat
                                LEFT JOIN status_kirim sk ON spk_ecat.id_inv_ecat = sk.id_inv_ecat
                                WHERE COALESCE(ecat.status_transaksi, '') = 'Diterima'
                                AND sk.status_review = '0'";


    $sql_total_review_ecat_pl = "SELECT COUNT(DISTINCT pl.no_inv_pl) AS total_pl
                                    FROM tb_spk_pl AS spk_pl
                                    LEFT JOIN inv_pl AS pl ON spk_pl.id_inv_pl = pl.id_inv_pl
                                    LEFT JOIN status_kirim sk ON spk_pl.id_inv_pl = sk.id_inv_ecat
                                    WHERE COALESCE(pl.status_transaksi) = 'Diterima'
                                    AND sk.status_review = '0'";

    // Jalankan query total review reguler
    $result_reg = $connect->query($sql_total_review_reg);
    $data_reg = $result_reg->fetch_assoc();
    $total_reg = $data_reg['total_reg'];

    // Jalankan query total review ecat
    $result_ecat = $connect_ecat->query($sql_total_review_ecat);
    $data_ecat = $result_ecat->fetch_assoc();
    $total_ecat = $data_ecat['total_ecat'];

    // Jalankan query total review ecat PL
    $result_pl = $connect_ecat->query($sql_total_review_ecat_pl);
    $data_pl = $result_pl->fetch_assoc();
    $total_pl = $data_pl['total_pl'];
?>
