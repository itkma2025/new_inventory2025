<?php
    require_once __DIR__ . "/../../koneksi-ecat.php";
    $sql_spk_trim = "SELECT DISTINCT
                        spk.no_spk_ecat,
                        spk.no_paket, 
                        spk.tgl_pesanan_ecat,
                        ecat.no_inv_ecat,
                        ecat.tgl_inv_ecat,
                        spk.nama_paket,
                        ecat.satker_inv AS satker,
                        ecat.alamat_inv AS alamat,
                        ecat.notes,
                        ps.nama_perusahaan,
                        tk.nama_kota_kab AS kota,
                        tp.nama_provinsi AS provinsi
                    FROM inv_ecat AS ecat
                    LEFT JOIN tb_spk_ecat spk ON ecat.id_inv_ecat = spk.id_inv_ecat
                    LEFT JOIN tb_perusahaan ps ON spk.id_perusahaan = ps.id_perusahaan
                    LEFT JOIN tb_kota tk ON ps.id_kota_kab = tk.id_kota_kab
                    LEFT JOIN tb_provinsi tp ON ps.id_provinsi = tk.id_provinsi
                    WHERE ecat.id_inv_ecat = '$id_inv_decrypt' ";

    $sql_produk = " SELECT DISTINCT
                        sr.id_spk_ecat,
                        sr.status_spk_ecat,
                        tps.id_transaksi_ecat,
                        sr.no_spk_ecat,
                        sr.created_date,
                        tps.nama_produk_spk,
                        tps.harga,
                        tps.ongkir,
                        tps.total_harga,
                        tps.id_produk,
                        spr.stock, 
                        CASE 
                            WHEN tpsm.nama_set_ecat IS NOT NULL THEN 'Set'
                            WHEN tpsm_reg.nama_set_marwa IS NOT NULL THEN 'Set'
                            ELSE COALESCE(tpr.satuan, tpsm.nama_set_ecat, tpr_reg.satuan, tpsm_reg.nama_set_marwa) 
                        END AS satuan,
                        COALESCE(tpr.nama_produk, tpsm.nama_set_ecat, tpr_reg.nama_produk, tpsm_reg.nama_set_marwa) AS nama_produk, 
                        COALESCE(tm.nama_merk, mr_tpsm.nama_merk, mr_tpr_reg.nama_merk, mr_tpsm_reg.nama_merk) AS nama_merk,
                        tps.qty,
                        tps.status_trx,
                        tps.status_surat_jalan,
                        tpr.harga_produk
                    FROM 
                        transaksi_produk_ecat AS tps
                    LEFT JOIN 
                        tb_spk_ecat AS sr ON sr.id_spk_ecat = tps.id_spk
                    LEFT JOIN 
                        $db.stock_produk_ecat AS spr ON tps.id_produk = spr.id_produk_ecat
                    LEFT JOIN 
                        $db.tb_produk_set_ecat AS tpsm ON (tpsm.id_set_ecat = spr.id_produk_ecat)
                    LEFT JOIN 
                        $db.tb_produk_ecat AS tpr ON tps.id_produk = tpr.id_produk_ecat
                    LEFT JOIN 
                        $db.tb_merk AS tm ON tpr.id_merk = tm.id_merk
                    LEFT JOIN 
                        $db.tb_merk AS mr_tpsm ON tpsm.id_merk = mr_tpsm.id_merk
                        LEFT JOIN $db.stock_produk_reguler AS spr_reg ON tps.id_produk = spr_reg.id_produk_reg
                    LEFT JOIN $db.tb_produk_reguler AS tpr_reg ON tps.id_produk = tpr_reg.id_produk_reg
                    LEFT JOIN $db.tb_kat_penjualan AS tkp ON tkp.id_kat_penjualan = spr_reg.id_kat_penjualan
                    LEFT JOIN $db.tb_produk_set_marwa AS tpsm_reg ON tpsm_reg.id_set_marwa = spr_reg.id_produk_reg
                    LEFT JOIN $db.tb_merk AS mr_tpr_reg ON tpr_reg.id_merk = mr_tpr_reg.id_merk
                    LEFT JOIN $db.tb_merk AS mr_tpsm_reg ON tpsm_reg.id_merk = mr_tpsm_reg.id_merk
                    WHERE 
                        tps.status_trx = 1 AND sr.id_inv_ecat = '$id_inv_decrypt'
                    ORDER BY
                        sr.created_date ASC";

                    
    // Data Untuk Menampilkan detail
    $spk_trim =  $connect_ecat->query($sql_spk_trim . " GROUP BY spk.no_spk_ecat ORDER BY spk.no_spk_ecat ASC");

    // Data Untuk Menampilkan detail
    $produk = $connect_ecat->query($sql_produk);
?>
