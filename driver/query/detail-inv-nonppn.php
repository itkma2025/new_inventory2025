<?php  
    $sql_spk_trim = "SELECT DISTINCT
                        trx.status_trx,
                        spk.no_spk,
                        spk.no_po, 
                        spk.tgl_pesanan
                    FROM inv_nonppn AS nonppn
                    LEFT JOIN spk_reg spk ON nonppn.id_inv_nonppn = spk.id_inv
                    LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                    WHERE nonppn.id_inv_nonppn = '$id_inv_decrypt' AND trx.status_trx = '1'";
     $sql_inv = "SELECT 
                    nonppn.id_inv_nonppn, 
                    nonppn.no_inv,
                    nonppn.kategori_inv,
                    nonppn.tgl_inv,
                    nonppn.cs_inv,
                    nonppn.sp_disc,
                    nonppn.tgl_tempo,
                    nonppn.ongkir,
                    nonppn.note_inv,
                    spk.no_spk,
                    spk.id_spk_reg, 
                    cs.nama_cs, 
                    cs.alamat, 
                    ordby.order_by, 
                    sl.nama_sales,
                    trx.id_transaksi,
                    trx.id_produk,
                    COALESCE(trx.nama_produk_spk, tpsm.nama_set_marwa) AS nama_produk,
                    COALESCE(trx.harga, tpsm.harga_set_marwa) AS harga_produk,
                    COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS nama_merk,
                    trx.qty,
                    trx.harga,
                    trx.disc,
                    trx.total_harga,
                    trx.status_trx,
                    trx.created_date,
                    tpr.satuan,
                    sk.jenis_pengiriman, 
                    sk.dikirim_ekspedisi, 
                    sk.jenis_penerima, 
                    ex.nama_ekspedisi,
                    us.nama_user AS nama_driver
                FROM inv_nonppn AS nonppn
                LEFT JOIN spk_reg spk ON nonppn.id_inv_nonppn = spk.id_inv
                LEFT JOIN transaksi_produk_reg trx ON trx.id_spk = spk.id_spk_reg
                LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                LEFT JOIN status_kirim sk ON nonppn.id_inv_nonppn = sk.id_inv
                LEFT JOIN ekspedisi ex ON sk.dikirim_ekspedisi = ex.id_ekspedisi
                LEFT JOIN $database2.user us ON sk.dikirim_driver = us.id_user
                LEFT JOIN tb_customer cs ON spk.id_customer = cs.id_cs
                LEFT JOIN tb_orderby ordby ON spk.id_orderby = ordby.id_orderby
                LEFT JOIN tb_sales sl ON spk.id_sales = sl.id_sales
                WHERE nonppn.id_inv_nonppn = '$id_inv_decrypt' AND trx.status_trx = '1'";
    // Data Untuk Menampilkan detail
    $detail = $connect->query($sql_inv . " GROUP BY spk.no_spk ORDER BY spk.no_spk ASC");
    $spk_trim =  $connect->query($sql_spk_trim . " GROUP BY spk.no_spk ORDER BY spk.no_spk ASC");

    // Data Untuk Menampilkan detail
    $produk = $connect->query($sql_inv . " ORDER BY trx.created_date ASC");
?>
