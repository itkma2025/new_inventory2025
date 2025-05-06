<?php  
    require_once "../akses.php";

    // query untuk detail
    $sql_detail = "SELECT
                        COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                        COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                        COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                        COALESCE(nonppn.kategori_inv, ppn.kategori_inv, bum.kategori_inv) AS kategori_inv,
                        COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                        COALESCE(nonppn.ongkir, ppn.ongkir, bum.ongkir) AS ongkir,
                        STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                        ik.id_komplain,
                        ik.no_komplain,
                        ik.status_komplain,
                        COALESCE(spk_nonppn.no_spk, spk_ppn.no_spk, spk_bum.no_spk) AS no_spk,
                        COALESCE(spk_nonppn.no_po, spk_ppn.no_po, spk_bum.no_po) AS no_po,
                        COALESCE(spk_nonppn.tgl_pesanan, spk_ppn.tgl_pesanan, spk_bum.tgl_pesanan) AS tgl_pesanan,
                        COALESCE(cs_nonppn.nama_cs, cs_ppn.nama_cs, cs_bum.nama_cs) AS nama_cs,
                        COALESCE(cs_nonppn.alamat, cs_ppn.alamat, cs_bum.alamat) AS alamat,
                        COALESCE(order_nonppn.order_by, order_ppn.order_by, order_bum.order_by) AS order_by,
                        COALESCE(sales_nonppn.nama_sales, sales_ppn.nama_sales, sales_bum.nama_sales) AS nama_sales
                    FROM inv_komplain AS ik
                    LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
                    LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
                    LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                    LEFT JOIN spk_reg spk_nonppn ON ik.id_inv = spk_nonppn.id_inv
                    LEFT JOIN spk_reg spk_ppn ON ik.id_inv = spk_ppn.id_inv
                    LEFT JOIN spk_reg spk_bum ON ik.id_inv = spk_bum.id_inv
                    LEFT JOIN tb_customer cs_nonppn ON spk_nonppn.id_customer = cs_nonppn.id_cs
                    LEFT JOIN tb_customer cs_ppn ON  spk_ppn.id_customer = cs_ppn.id_cs
                    LEFT JOIN tb_customer cs_bum ON  spk_bum.id_customer = cs_bum.id_cs
                    LEFT JOIN tb_orderby order_nonppn ON spk_nonppn.id_orderby = order_nonppn.id_orderby
                    LEFT JOIN tb_orderby order_ppn ON  spk_ppn.id_orderby = order_ppn.id_orderby
                    LEFT JOIN tb_orderby order_bum ON  spk_bum.id_orderby = order_bum.id_orderby
                    LEFT JOIN tb_sales sales_nonppn ON spk_nonppn.id_sales = sales_nonppn.id_sales
                    LEFT JOIN tb_sales sales_ppn ON  spk_ppn.id_sales = sales_ppn.id_sales
                    LEFT JOIN tb_sales sales_bum ON  spk_bum.id_sales = sales_bum.id_sales
                    WHERE ik.id_komplain = '$id'";
    $query_detail = mysqli_query($connect, $sql_detail);
    $data_detail = mysqli_fetch_array($query_detail);
    $query_detail2 = mysqli_query($connect, $sql_detail);


    // Query Driver
    $sql_driver = "SELECT
                        ik.id_komplain,
                        COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                        COALESCE(status_kirim_nonppn.jenis_pengiriman, status_kirim_ppn.jenis_pengiriman, status_kirim_bum.jenis_pengiriman) AS jenis_pengiriman,
                        COALESCE(status_kirim_nonppn.jenis_penerima, status_kirim_ppn.jenis_penerima, status_kirim_bum.jenis_penerima) AS jenis_penerima,
                        COALESCE(status_kirim_nonppn.no_resi, status_kirim_ppn.no_resi, status_kirim_bum.no_resi) AS no_resi,
                        COALESCE(status_kirim_nonppn.dikirim_oleh, status_kirim_ppn.dikirim_oleh, status_kirim_bum.dikirim_oleh) AS dikirim_oleh,
                        COALESCE(status_kirim_nonppn.penanggung_jawab, status_kirim_ppn.penanggung_jawab, status_kirim_bum.penanggung_jawab) AS penanggung_jawab,
                        COALESCE(user_nonppn.nama_user, user_ppn.nama_user, user_bum.nama_user) AS nama_driver,
                        COALESCE(ekspedisi_nonppn.nama_ekspedisi, ekspedisi_ppn.nama_ekspedisi, ekspedisi_bum.nama_ekspedisi) AS nama_ekspedisi,
                        COALESCE(penerima_nonppn.nama_penerima, penerima_ppn.nama_penerima, penerima_bum.nama_penerima) AS nama_penerima
                    FROM inv_komplain AS ik
                    LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
                    LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
                    LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                    LEFT JOIN status_kirim status_kirim_nonppn ON status_kirim_nonppn.id_inv = nonppn.id_inv_nonppn
                    LEFT JOIN status_kirim status_kirim_ppn ON status_kirim_ppn.id_inv = ppn.id_inv_ppn
                    LEFT JOIN status_kirim status_kirim_bum ON status_kirim_bum.id_inv = bum.id_inv_bum
                    LEFT JOIN $database2.user user_nonppn ON status_kirim_nonppn.dikirim_driver = user_nonppn.id_user
                    LEFT JOIN $database2.user user_ppn ON status_kirim_ppn.dikirim_driver = user_ppn.id_user
                    LEFT JOIN $database2.user user_bum ON status_kirim_bum.dikirim_driver = user_bum.id_user
                    LEFT JOIN ekspedisi ekspedisi_nonppn ON status_kirim_nonppn.dikirim_ekspedisi = ekspedisi_nonppn.id_ekspedisi
                    LEFT JOIN ekspedisi ekspedisi_ppn ON status_kirim_ppn.dikirim_ekspedisi = ekspedisi_ppn.id_ekspedisi
                    LEFT JOIN ekspedisi ekspedisi_bum ON status_kirim_bum.dikirim_ekspedisi = ekspedisi_bum.id_ekspedisi
                    LEFT JOIN inv_penerima penerima_nonppn ON nonppn.id_inv_nonppn = penerima_nonppn.id_inv
                    LEFT JOIN inv_penerima penerima_ppn ON ppn.id_inv_ppn = penerima_ppn.id_inv
                    LEFT JOIN inv_penerima penerima_bum ON bum.id_inv_bum = penerima_bum.id_inv
                    WHERE ik.id_komplain = '$id'";
    $query_driver = mysqli_query($connect, $sql_driver);
    $data_driver = mysqli_fetch_array($query_driver);

    // Query untuk total inv
    $sql_total = " SELECT
                        ik.id_komplain,
                        COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                        COALESCE(nonppn.total_inv, ppn.total_inv, bum.total_inv) AS total_inv
                    FROM inv_komplain AS ik
                    LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
                    LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
                    LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                    WHERE ik.id_komplain = '$id'";
    $query_total = mysqli_query($connect, $sql_total);
    $data_total = mysqli_fetch_array($query_total);

    // Query Untuk Table
    $sql = "SELECT
                COALESCE(nonppn.id_inv_nonppn, ppn.id_inv_ppn, bum.id_inv_bum) AS id_inv,
                STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                ik.id_komplain,
                ik.kat_komplain,
                ik.kondisi_pesanan,
                COALESCE(spk_nonppn.id_spk_reg, spk_ppn.id_spk_reg, spk_bum.id_spk_reg) AS id_spk,
                COALESCE(spk_nonppn.no_spk, spk_ppn.no_spk, spk_bum.no_spk) AS no_spk,
                tpr.id_transaksi,
                tpr.id_produk,
                tpr.nama_produk_spk,
                tpr.harga,
                tpr.qty,
                tpr.disc,
                COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS merk
            FROM inv_komplain AS ik
            LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
            LEFT JOIN inv_ppn ppn ON ik.id_inv = ppn.id_inv_ppn
            LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
            LEFT JOIN spk_reg spk_nonppn ON ik.id_inv = spk_nonppn.id_inv
            LEFT JOIN spk_reg spk_ppn ON ik.id_inv = spk_ppn.id_inv
            LEFT JOIN spk_reg spk_bum ON ik.id_inv = spk_bum.id_inv
            LEFT JOIN transaksi_produk_reg tpr ON spk_nonppn.id_spk_reg = tpr.id_spk OR spk_ppn.id_spk_reg = tpr.id_spk OR spk_bum.id_spk_reg = tpr.id_spk
            LEFT JOIN tb_produk_reguler pr ON tpr.id_produk = pr.id_produk_reg
            LEFT JOIN tb_produk_set_marwa tpsm ON tpr.id_produk = tpsm.id_set_marwa
            LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
            LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
            WHERE ik.id_komplain = '$id'";
    $query = mysqli_query($connect, $sql);
    $query_kondisi = mysqli_query($connect, $sql);
    $data_kondisi = mysqli_fetch_array($query_kondisi);
    
?>