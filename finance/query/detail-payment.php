<?php
    include "../function/function-enkripsi.php";
    $key = "payment2024";
    $id_pembayaran = $_GET['id'];
    $id_pembayaran_decrypt = decrypt($id_pembayaran, $key);
    $sql_inv_pembelian = (" SELECT 
                                ipl.id_inv_pembelian,
                                ipl.id_pembayaran,
                                ipl.no_trx,
                                ipl.tgl_pembelian,
                                ipl.tgl_tempo,
                                STR_TO_DATE(ipl.tgl_tempo, '%d/%m/%Y') AS tgl_tempo_convert,
                                ipl.no_inv,
                                ipl.jenis_trx,
                                ipl.status_pembelian,
                                ipl.status_pembayaran,
                                ipl.total_pembelian,
                                sp.id_sp,
                                sp.nama_sp,
                                fp.total_tagihan,
                                fp.no_pembayaran,
                                fp.tgl_pembayaran,
                                byr.id_bayar,
                                COALESCE(SUM(byr.total_bayar), 0) AS total_bayar
                            FROM 
                                inv_pembelian_lokal AS ipl
                            LEFT JOIN 
                                tb_supplier sp ON ipl.id_sp = sp.id_sp
                            LEFT JOIN 
                                finance_pembayaran_produk_lokal fp ON ipl.id_pembayaran = fp.id_pembayaran
                            LEFT JOIN 
                                (SELECT id_bayar, id_inv_pembelian, COALESCE(total_bayar, 0) AS total_bayar FROM finance_bayar_pembelian) byr 
                                ON ipl.id_inv_pembelian = byr.id_inv_pembelian
                            WHERE ipl.id_pembayaran = '$id_pembayaran_decrypt'
                            GROUP BY ipl.id_inv_pembelian
                        ");
?>