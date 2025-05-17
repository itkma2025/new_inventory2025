<?php  
    require_once "../akses.php";
    echo $id_user = decrypt($_SESSION['tiket_id'], $key_global);
    $sql_waiting_verif_revisi = "SELECT 
                                    max(ir.no_inv_revisi) AS no_inv_rev,
                                    ik.id_inv,
                                    ik.id_komplain,
                                    ir.status_pengiriman,
                                    sk.dikirim_driver,
                                    sk.jenis_pengiriman,
                                    sk.jenis_penerima,
                                    sk.status_review,
                                    STR_TO_DATE(sk.tgl_kirim, '%d/%m/%Y') AS tgl_kirim,
                                    COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                    COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                                    COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) AS status_trx,
                                    COALESCE(spk_nonppn.tgl_pesanan, spk_ppn.tgl_pesanan, spk_bum.tgl_pesanan) AS spk_tgl_pesanan,
                                    COALESCE(nonppn.created_date, ppn.created_date, bum.created_date) AS created_date,
                                    COALESCE(cs_spk_nonppn.alamat, cs_spk_ppn.alamat, cs_spk_bum.alamat) AS alamat,
                                    ibt.approval
                                FROM revisi_status_kirim AS sk
                                LEFT JOIN inv_komplain ik ON (ik.id_komplain = sk.id_komplain)
                                LEFT JOIN inv_revisi ir ON (ir.id_inv = ik.id_inv)
                                LEFT JOIN inv_nonppn nonppn ON (ik.id_inv = nonppn.id_inv_nonppn)
                                LEFT JOIN inv_ppn ppn ON (ik.id_inv = ppn.id_inv_ppn)
                                LEFT JOIN inv_bum bum ON (ik.id_inv = bum.id_inv_bum)
                                LEFT JOIN spk_reg spk_nonppn ON (nonppn.id_inv_nonppn = spk_nonppn.id_inv)
                                LEFT JOIN spk_reg spk_ppn ON (ppn.id_inv_ppn = spk_ppn.id_inv)
                                LEFT JOIN spk_reg spk_bum ON (bum.id_inv_bum = spk_bum.id_inv)
                                LEFT JOIN tb_customer cs_spk_nonppn ON (spk_nonppn.id_customer = cs_spk_nonppn.id_cs)
                                LEFT JOIN tb_customer cs_spk_ppn ON (spk_ppn.id_customer = cs_spk_ppn.id_cs)
                                LEFT JOIN tb_customer cs_spk_bum ON (spk_bum.id_customer = cs_spk_bum.id_cs)
                                LEFT JOIN inv_bukti_terima_revisi ibt ON sk.id_komplain = ibt.id_komplain
                                WHERE sk.dikirim_driver = '$id_user' AND COALESCE(nonppn.status_transaksi, ppn.status_transaksi, bum.status_transaksi) = 'Komplain Dikirim' AND STR_TO_DATE(sk.tgl_kirim, '%d/%m/%Y') <= CURDATE() AND sk.status_review = '0'
                                GROUP BY no_inv"; 
?>