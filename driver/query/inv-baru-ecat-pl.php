<?php 
    require_once '../akses.php';
    // require_once __DIR__ . "/../../koneksi-pengiriman.php";
    echo $id_user = decrypt($_SESSION['tiket_id'], $key_global);
    $sql = "(
                SELECT
                    sk.id_inv_ecat,
                    sk.id_driver,
                    sk.jenis_pengiriman,
                    sk.jenis_penerima,
                    NULL AS status_review,
                    STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') AS tgl_kirim,
                    -- spk
                    COALESCE(spk_pl.tgl_pesanan_pl) AS tgl_pesanan,
                    -- inv
                    COALESCE(pl.id_inv_pl) AS id_inv,
                    COALESCE(pl.no_inv_pl) AS no_inv,
                    COALESCE(pl.satker_inv) AS satker,
                    COALESCE(pl.status_transaksi) AS status_trx,
                    COALESCE(pl.created_date) AS created_date,
                    -- Customer
                    COALESCE(pl.alamat_inv) AS alamat,
                    NULL AS approval
                FROM status_kirim AS sk
                LEFT JOIN inv_pl pl ON sk.id_inv_ecat = pl.id_inv_pl
                LEFT JOIN tb_spk_pl spk_pl ON pl.id_inv_pl = spk_pl.id_inv_pl
                LEFT JOIN tb_customer cs_spk_pl ON spk_pl.id_perusahaan = cs_spk_pl.id_perusahaan
                WHERE sk.id_driver = '$id_user' 
                AND COALESCE(pl.status_transaksi) = 'Dikirim' 
                AND STR_TO_DATE(sk.tgl_kirim, '%Y-%m-%d') <= CURDATE()
                GROUP BY no_inv
            )
                UNION ALL
            (
                SELECT
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
                AND ibt.approval = '1'
                GROUP BY no_inv
            )
                ORDER BY no_inv ASC";
?>