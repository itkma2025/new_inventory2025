<?php  
     require_once '../akses.php';
     require_once __DIR__ . "/../../koneksi-ecat.php";
     // require_once __DIR__ . "/../../koneksi-pengiriman.php";
     $id_user = decrypt($_SESSION['tiket_id'], $key_global);

     $sql_total_menunggu_verif_reg = " SELECT COUNT(*) AS total_verif_reg
                                            FROM status_kirim AS sk
                                            LEFT JOIN inv_nonppn nonppn ON sk.id_inv = nonppn.id_inv_nonppn
                                            LEFT JOIN inv_ppn ppn ON sk.id_inv = ppn.id_inv_ppn
                                            LEFT JOIN inv_bum bum ON sk.id_inv = bum.id_inv_bum
                                            LEFT JOIN spk_reg spk_nonppn ON nonppn.id_inv_nonppn = spk_nonppn.id_inv 
                                            LEFT JOIN spk_reg spk_ppn ON ppn.id_inv_ppn = spk_ppn.id_inv
                                            LEFT JOIN spk_reg spk_bum ON bum.id_inv_bum = spk_bum.id_inv
                                            LEFT JOIN tb_customer cs_spk_nonppn ON spk_nonppn.id_customer = cs_spk_nonppn.id_cs
                                            LEFT JOIN tb_customer cs_spk_ppn ON spk_ppn.id_customer = cs_spk_ppn.id_cs
                                            LEFT JOIN tb_customer cs_spk_bum ON spk_bum.id_customer = cs_spk_bum.id_cs
                                            LEFT JOIN inv_bukti_terima ibt ON sk.id_inv = ibt.id_inv
                                            WHERE sk.dikirim_driver = '$id_user'
                                            AND COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) = 'Diterima'
                                            AND STR_TO_DATE(sk.tgl_kirim, '%d/%m/%Y') <= CURDATE()
                                            AND sk.status_review = '0'";


     $sql_total_menunggu_verif_ecat = "SELECT COUNT(DISTINCT ecat.no_inv_ecat) AS total_verif_ecat
                                        FROM status_kirim AS sk
                                        LEFT JOIN inv_ecat ecat ON sk.id_inv_ecat = ecat.id_inv_ecat
                                        LEFT JOIN tb_spk_ecat spk_ecat ON ecat.id_inv_ecat = spk_ecat.id_inv_ecat
                                        LEFT JOIN inv_bukti_terima ibt ON sk.id_inv_ecat = ibt.id_inv_ecat
                                        WHERE sk.id_driver = '$id_user'
                                        AND ecat.status_transaksi = 'Diterima'
                                        AND STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') <= CURDATE()
                                        AND ibt.approval = '0'";

    $sql_total_menunggu_verif_pl = "SELECT COUNT(DISTINCT pl.no_inv_pl) AS total_verif_pl
                                        FROM status_kirim AS sk
                                        LEFT JOIN inv_pl pl ON sk.id_inv_ecat = pl.id_inv_pl
                                        LEFT JOIN tb_spk_pl spk_pl ON pl.id_inv_pl = spk_pl.id_inv_pl
                                        LEFT JOIN inv_bukti_terima ibt ON sk.id_inv_ecat = ibt.id_inv_ecat
                                        WHERE sk.id_driver = '$id_user'
                                        AND pl.status_transaksi = 'Diterima'
                                        AND STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') <= CURDATE()
                                        AND ibt.approval = '0'";
                                    
    
    // Jalankan query total review reguler
    $result_verif_reg = $connect->query($sql_total_menunggu_verif_reg);
    $data_verif_reg = $result_verif_reg->fetch_assoc();
    $total_verif_reg = $data_verif_reg['total_verif_reg'];

    $result_verif_ecat = $connect_ecat->query($sql_total_menunggu_verif_ecat);
    $data_verif_ecat = $result_verif_ecat->fetch_assoc();
    $total_verif_ecat = $data_verif_ecat['total_verif_ecat'];

    // Jalankan query total review ecat
    $result_verif_pl = $connect_ecat->query($sql_total_menunggu_verif_pl);
    $data_verif_pl = $result_verif_pl->fetch_assoc();
    $total_verif_pl = $data_verif_pl['total_verif_pl'];
                                        

    





?>