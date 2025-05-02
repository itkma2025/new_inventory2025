<?php 
    require_once '../akses.php';
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);
    $sql_waiting_verif = "SELECT
                            sk.id_inv_ecat AS id_inv,
                            sk.id_driver AS dikirim_driver,
                            sk.jenis_pengiriman,
                            sk.jenis_penerima,
                            sk.status_review,
                            STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') AS tgl_kirim,

                            -- spk
                            spk_ecat.tgl_pesanan_ecat AS tgl_pesanan,

                            -- inv
                            ecat.id_inv_ecat AS id_inv,
                            ecat.no_inv_ecat AS no_inv,
                            COALESCE(ecat.satker_inv) AS satker,
                            ecat.status_transaksi AS status_trx,
                            ecat.created_date AS created_date,

                            -- Customer
                            ecat.alamat_inv AS alamat,
                            ibt.approval

                            FROM status_kirim AS sk
                            LEFT JOIN inv_ecat ecat ON sk.id_inv_ecat = ecat.id_inv_ecat
                            LEFT JOIN tb_spk_ecat spk_ecat ON ecat.id_inv_ecat = spk_ecat.id_inv_ecat
                            LEFT JOIN inv_bukti_terima ibt ON sk.id_inv_ecat = ibt.id_inv_ecat

                            WHERE sk.id_driver = '$id_user'
                            AND ecat.status_transaksi = 'Diterima'
                            AND STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') <= CURDATE()
                            AND ibt.approval = '0'
                            GROUP BY no_inv";
?>