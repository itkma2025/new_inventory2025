<?php 
    require_once '../akses.php';
    require_once __DIR__ . "/../../koneksi-ecat.php";
    // require_once __DIR__ . "/../../koneksi-pengiriman.php";
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);
    $sql_total_inv_rev_reg = "
                                SELECT COUNT(DISTINCT COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv)) AS total_inv_rev_reg
                                FROM revisi_status_kirim AS sk
                                LEFT JOIN inv_komplain ik ON ik.id_komplain = sk.id_komplain
                                LEFT JOIN inv_revisi ir ON ir.id_inv = ik.id_inv
                                LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
                                LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
                                LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                                WHERE sk.dikirim_driver = 'US-241216-c89d1aeb-17343403-af74371'
                                AND ir.status_pengiriman = '0'
                                AND sk.jenis_penerima = ''
                                AND STR_TO_DATE(sk.tgl_kirim, '%d/%m/%Y') <= CURDATE()
                            ";

    // Jalankan query total review reguler
    $result_rev_reg = $connect->query($sql_total_inv_rev_reg);
    $data_rev_reg = $result_rev_reg->fetch_assoc();
    $total_rev_reg = $data_rev_reg['total_inv_rev_reg'];
?>