<?php 
    require_once '../akses.php';
    require_once __DIR__ . "/../../koneksi-ecat.php";
    // require_once __DIR__ . "/../../koneksi-pengiriman.php";
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);
    $sql_total_inv_new_reg = "
                                SELECT COUNT(*) AS total_inv_new_reg FROM (
                                    (
                                        SELECT COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv
                                        FROM status_kirim AS sk
                                        LEFT JOIN inv_nonppn nonppn ON sk.id_inv = nonppn.id_inv_nonppn
                                        LEFT JOIN inv_ppn ppn ON sk.id_inv = ppn.id_inv_ppn
                                        LEFT JOIN inv_bum bum ON sk.id_inv = bum.id_inv_bum
                                        WHERE sk.dikirim_driver = '$id_user' 
                                        AND COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) = 'Dikirim' 
                                        AND STR_TO_DATE(sk.tgl_kirim, '%d/%m/%Y') <= CURDATE()
                                        GROUP BY no_inv
                                    )
                                    UNION ALL
                                    (
                                        SELECT COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv
                                        FROM status_kirim AS sk
                                        LEFT JOIN inv_nonppn nonppn ON sk.id_inv = nonppn.id_inv_nonppn
                                        LEFT JOIN inv_ppn ppn ON sk.id_inv = ppn.id_inv_ppn
                                        LEFT JOIN inv_bum bum ON sk.id_inv = bum.id_inv_bum
                                        LEFT JOIN inv_bukti_terima ibt ON sk.id_inv = ibt.id_inv
                                        WHERE sk.dikirim_driver = '$id_user' 
                                        AND COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) = 'Diterima' 
                                        AND STR_TO_DATE(sk.tgl_kirim, '%d/%m/%Y') <= CURDATE() 
                                        AND ibt.approval = '1'
                                        GROUP BY no_inv
                                    )
                                ) AS combined_data";

    $sql_total_inv_new_ecat = "
                                SELECT COUNT(*) AS total_inv_new_ecat FROM (
                                    SELECT ecat.no_inv_ecat AS no_inv
                                    FROM status_kirim AS sk
                                    LEFT JOIN inv_ecat ecat ON sk.id_inv_ecat = ecat.id_inv_ecat
                                    LEFT JOIN tb_spk_ecat spk_ecat ON ecat.id_inv_ecat = spk_ecat.id_inv_ecat
                                    LEFT JOIN tb_customer cs_spk_ecat ON spk_ecat.id_perusahaan = cs_spk_ecat.id_perusahaan
                                    WHERE sk.id_driver = '$id_user' 
                                        AND COALESCE(ecat.status_transaksi) = 'Dikirim' 
                                        AND STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') <= CURDATE()
                                    GROUP BY no_inv
                                
                                    UNION ALL
                                
                                    SELECT ecat.no_inv_ecat AS no_inv
                                    FROM status_kirim AS sk
                                    LEFT JOIN inv_ecat ecat ON sk.id_inv_ecat = ecat.id_inv_ecat
                                    LEFT JOIN tb_spk_ecat spk_ecat ON ecat.id_inv_ecat = spk_ecat.id_inv_ecat
                                    LEFT JOIN inv_bukti_terima ibt ON sk.id_inv_ecat = ibt.id_inv_ecat
                                    WHERE sk.id_driver = '$id_user'
                                        AND ecat.status_transaksi = 'Diterima'
                                        AND STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') <= CURDATE()
                                        AND ibt.approval = '1'
                                    GROUP BY no_inv
                                ) AS combined_data";
                                

    $sql_total_inv_new_pl = "
                                SELECT COUNT(*) AS total_inv_new_pl FROM (
                                    (
                                        SELECT sk.id_inv_ecat
                                        FROM status_kirim AS sk
                                        LEFT JOIN inv_pl pl ON sk.id_inv_ecat = pl.id_inv_pl
                                        WHERE sk.id_driver = '$id_user' 
                                        AND COALESCE(pl.status_transaksi) = 'Dikirim' 
                                        AND STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') <= CURDATE()
                                        GROUP BY pl.no_inv_pl
                                    )
                                    UNION ALL
                                    (
                                        SELECT sk.id_inv_ecat
                                        FROM status_kirim AS sk
                                        LEFT JOIN inv_pl pl ON sk.id_inv_ecat = pl.id_inv_pl
                                        LEFT JOIN inv_bukti_terima ibt ON sk.id_inv_ecat = ibt.id_inv_ecat
                                        WHERE sk.id_driver = '$id_user'
                                        AND pl.status_transaksi = 'Diterima'
                                        AND STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') <= CURDATE()
                                        AND ibt.approval = '1'
                                        GROUP BY pl.no_inv_pl
                                    )
                                ) AS subquery";
    // Jalankan query total review reguler
    $result_reg = $connect->query($sql_total_inv_new_reg);
    $data_reg = $result_reg->fetch_assoc();
    $total_reg = $data_reg['total_inv_new_reg'];

    // Jalankan query total review ecat
    $result_ecat = $connect_ecat->query($sql_total_inv_new_ecat);
    $data_ecat = $result_ecat->fetch_assoc();
    $total_ecat = $data_ecat['total_inv_new_ecat'];

    // Jalankan query total review ecat PL
    $result_pl = $connect_ecat->query($sql_total_inv_new_pl);
    $data_pl = $result_pl->fetch_assoc();
    $total_pl = $data_pl['total_inv_new_pl'];

?>