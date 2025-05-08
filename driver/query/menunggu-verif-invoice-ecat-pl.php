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
                            spk_pl.tgl_pesanan_pl AS tgl_pesanan,

                            -- inv
                            pl.id_inv_pl AS id_inv,
                            pl.no_inv_pl AS no_inv,
                            COALESCE(pl.satker_inv) AS satker,
                            pl.status_transaksi AS status_trx,
                            pl.created_date AS created_date,

                            -- Customer
                            pl.alamat_inv AS alamat,
                            ibt.approval

                            FROM status_kirim AS sk
                            LEFT JOIN inv_pl pl ON sk.id_inv_ecat = pl.id_inv_pl
                            LEFT JOIN tb_spk_pl spk_pl ON pl.id_inv_pl = spk_pl.id_inv_pl
                            LEFT JOIN inv_bukti_terima ibt ON sk.id_inv_ecat = ibt.id_inv_ecat

                            WHERE sk.id_driver = '$id_user'
                            AND pl.status_transaksi = 'Diterima'
                            AND STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') <= CURDATE()
                            AND ibt.approval = '0'
                            GROUP BY no_inv";
?>