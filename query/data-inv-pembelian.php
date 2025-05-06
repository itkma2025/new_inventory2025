<?php  
    // Menampilkan data inv pembelian
    $sql_inv_pembelian = "  SELECT 
                                ipl.id_inv_pembelian,
                                ipl.no_trx,
                                STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y') AS tgl_pembelian,
                                sp.nama_sp, 
                                ipl.status_pembayaran
                            FROM inv_pembelian_lokal AS ipl
                            LEFT JOIN tb_supplier sp ON ipl.id_sp = sp.id_sp
                            WHERE ipl.status_pembelian = '1' AND STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)  ORDER BY tgl_pembelian ASC";
    // Tambahkan ORDER BY setelah klausa WHERE
    $query_inv_pembelian = $connect->query($sql_inv_pembelian);
?>