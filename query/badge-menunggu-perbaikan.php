<?php
    // Koneksi DB
    include 'koneksi.php';
    include 'koneksi-ecat.php';

    $query_perbaikan_reg = "SELECT COUNT(*) AS total_perbaikan_reg
                            FROM (
                                SELECT DISTINCT
                                    COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv
                                FROM spk_reg AS sr
                                LEFT JOIN inv_nonppn AS nonppn ON sr.id_inv = nonppn.id_inv_nonppn
                                LEFT JOIN inv_ppn AS ppn ON sr.id_inv = ppn.id_inv_ppn
                                LEFT JOIN inv_bum AS bum ON sr.id_inv = bum.id_inv_bum
                                LEFT JOIN tb_customer cs ON sr.id_customer = cs.id_cs
                                LEFT JOIN status_kirim sk ON sr.id_inv = sk.id_inv
                                LEFT JOIN ekspedisi ex ON sk.dikirim_ekspedisi = ex.id_ekspedisi
                                LEFT JOIN inv_bukti_terima ibt ON sk.id_inv = ibt.id_inv
                                LEFT JOIN inv_penerima ip ON sr.id_inv = ip.id_inv
                                LEFT JOIN $database2.user AS us ON sk.dikirim_driver = us.id_user
                                LEFT JOIN $database2.user AS uc ON ibt.created_by = uc.id_user
                                WHERE 
                                    COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi)
                                    IN ('Diterima', 'Transaksi Selesai', 'Komplain Selesai')
                                    AND sk.status_review = '1' 
                                    AND approval = '1' 
                                    AND ibt.status_perbaikan = '0' 
                            ) AS total";

    $query_perbaikan_ecat = "SELECT COUNT(*) AS total_perbaikan_ecat
                            FROM (
                                SELECT DISTINCT
                                    ecat.id_inv_ecat
                                FROM tb_spk_ecat AS spk_ecat
                                LEFT JOIN inv_ecat AS ecat ON spk_ecat.id_inv_ecat = ecat.id_inv_ecat
                                LEFT JOIN status_kirim sk ON spk_ecat.id_inv_ecat = sk.id_inv_ecat
                                LEFT JOIN inv_penerima ip ON spk_ecat.id_inv_ecat = ip.id_inv_ecat 
                                LEFT JOIN tb_perusahaan tp ON spk_ecat.id_perusahaan = tp.id_perusahaan
                                LEFT JOIN tb_provinsi tbp ON tp.id_provinsi = tbp.id_provinsi
                                LEFT JOIN inv_bukti_terima ibt ON sk.id_inv_ecat = ibt.id_inv_ecat
                                LEFT JOIN $database2.user AS us ON sk.id_driver = us.id_user
                                LEFT JOIN $database2.user AS uc ON ibt.created_by = uc.id_user
                                LEFT JOIN $db.ekspedisi ex ON sk.id_ekspedisi = ex.id_ekspedisi
                                WHERE 
                                    COALESCE(ecat.status_transaksi) IN ('Diterima', 'Transaksi Selesai', 'Komplain Selesai')
                                    AND sk.status_review = '1' 
                                    AND approval = '1' 
                                    AND ibt.status_perbaikan = '0' 
                            ) AS total";


    $query_perbaikan_ecat_pl = "SELECT COUNT(*) AS total_perbaikan_ecat_pl
                                FROM (
                                    SELECT DISTINCT
                                        pl.id_inv_pl
                                    FROM tb_spk_pl AS spk_pl
                                    LEFT JOIN inv_pl AS pl ON spk_pl.id_inv_pl = pl.id_inv_pl
                                    LEFT JOIN status_kirim sk ON spk_pl.id_inv_pl = sk.id_inv_ecat
                                    LEFT JOIN inv_penerima ip ON spk_pl.id_inv_pl = ip.id_inv_ecat
                                    LEFT JOIN tb_perusahaan tp ON spk_pl.id_perusahaan = tp.id_perusahaan
                                    LEFT JOIN tb_provinsi tbp ON tp.id_provinsi = tbp.id_provinsi
                                    LEFT JOIN $database2.user AS us ON sk.id_driver = us.id_user
                                    LEFT JOIN $db.ekspedisi ex ON sk.id_ekspedisi = ex.id_ekspedisi
                                    LEFT JOIN inv_bukti_terima ibt ON sk.id_inv_ecat = ibt.id_inv_ecat
                                    WHERE 
                                        COALESCE(pl.status_transaksi) IN ('Diterima', 'Transaksi Selesai', 'Komplain Selesai')
                                        AND sk.status_review = '1' 
                                        AND approval = '1' 
                                        AND ibt.status_perbaikan = '0'
                                ) AS total";

    // Eksekusi query menunggu perbaikan
    $result_perbaikan_reg = $connect->query($query_perbaikan_reg);
    $row_perbaikan_reg = $result_perbaikan_reg->fetch_assoc();
    $total_perbaikan_reg = $row_perbaikan_reg['total_perbaikan_reg'];

    $result_perbaikan_ecat = $connect_ecat->query($query_perbaikan_ecat);
    $row_perbaikan_ecat = $result_perbaikan_ecat->fetch_assoc();
    $total_perbaikan_ecat = $row_perbaikan_ecat['total_perbaikan_ecat'];

    $result_perbaikan_ecat_pl = $connect_ecat->query($query_perbaikan_ecat_pl);
    $row_perbaikan_ecat_pl = $result_perbaikan_ecat_pl->fetch_assoc();
    $total_perbaikan_ecat_pl = $row_perbaikan_ecat_pl['total_perbaikan_ecat_pl'];

?>
