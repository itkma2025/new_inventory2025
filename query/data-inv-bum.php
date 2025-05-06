<?php  
    $sql_data_inv = "SELECT 
                        bum.id_inv_bum, 
                        bum.no_inv, 
                        bum.tgl_inv, 
                        bum.cs_inv,
                        bum.alamat_inv,
                        bum.tgl_tempo,
                        bum.sp_disc,   
                        bum.note_inv, 
                        bum.kategori_inv, 
                        bum.ongkir,
                        bum.free_ongkir, 
                        bum.ongkir_free, 
                        bum.total_inv, 
                        bum.kwitansi, 
                        bum.surat_jalan,
                        bum.status_transaksi,
                        sr.id_user, 
                        sr.id_customer, 
                        sr.id_inv, 
                        sr.no_spk, 
                        sr.no_po, 
                        sr.tgl_pesanan, 
                        sr.petugas, sr.note AS note_spk,
                        cs.nama_cs, 
                        cs.alamat, 
                        ordby.order_by, 
                        sl.nama_sales,
                        trx.status_trx,
                        sk.jenis_pengiriman, 
                        sk.jenis_penerima 
                    FROM inv_bum AS bum
                    LEFT JOIN spk_reg sr ON (bum.id_inv_bum = sr.id_inv)
                    LEFT JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                    LEFT JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                    LEFT JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                    LEFT JOIN transaksi_produk_reg trx ON(sr.id_spk_reg = trx.id_spk)
                    LEFT JOIN status_kirim sk ON (bum.id_inv_bum = sk.id_inv)
                    WHERE bum.id_inv_bum = '$id_inv'";
    $query_data_inv = $connect->query($sql_data_inv);
    $data_inv = mysqli_fetch_array($query_data_inv);
    $ongkir = $data_inv['ongkir'];
    $sp_disc = $data_inv['sp_disc'];
    $kat_inv = $data_inv['kategori_inv'];
    $petugas = $data_inv['petugas'];
    $status_transaksi_inv = $data_inv['status_transaksi'];
    $total_inv =  $data_inv['total_inv'];
    $id_cs = $data_inv['id_customer'];

    // kondisi status trx = 1
    $sql_status_trx_1 = $sql_data_inv . " AND trx.status_trx = '1' ORDER BY no_spk ASC";
    $query_status_trx_1 = $connect->query($sql_status_trx_1);
    $total_data_status_trx_1 = mysqli_num_rows($query_status_trx_1);
    $data_cek = mysqli_fetch_array($query_status_trx_1);

    // kondisi status trx = 0
    $sql_status_trx_0 = $sql_data_inv . " AND trx.status_trx = '0' ORDER BY no_spk ASC";
    $query_status_trx_0 = $connect->query($sql_status_trx_0);
    $total_data_status_trx_0 = mysqli_num_rows($query_status_trx_0);
?>