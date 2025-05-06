<?php  
    include "../koneksi.php";

   // query untuk detail
   $sql_detail = "SELECT DISTINCT
                    bum.id_inv_bum AS id_inv,
                    bum.no_inv AS no_inv,
                    bum.tgl_inv AS tgl_inv,
                    bum.kategori_inv AS kategori_inv,
                    bum.cs_inv AS cs_inv,
                    bum.alamat_inv AS alamat_inv,
                    bum.ongkir AS ongkir,
                    rev.pelanggan_revisi AS cs_revisi,
                    rev.alamat_revisi AS alamat_revisi,
                    STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                    ik.id_komplain,
                    ik.no_komplain,
                    ik.status_komplain,
                    ik.created_date,
                    spk_bum.no_spk AS no_spk,
                    spk_bum.no_po AS no_po,
                    spk_bum.tgl_pesanan AS tgl_pesanan,
                    cs_bum.id_cs AS id_cs,
                    cs_bum.nama_cs AS nama_cs,
                    cs_bum.alamat AS alamat,
                    order_bum.order_by AS order_by,
                    sales_bum.nama_sales AS nama_sales
                FROM inv_komplain AS ik
                LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                LEFT JOIN inv_revisi rev ON rev.id_inv = bum.id_inv_bum
                LEFT JOIN spk_reg spk_bum ON ik.id_inv = spk_bum.id_inv
                LEFT JOIN tb_customer cs_bum ON spk_bum.id_customer = cs_bum.id_cs
                LEFT JOIN tb_orderby order_bum ON spk_bum.id_orderby = order_bum.id_orderby
                LEFT JOIN tb_sales sales_bum ON spk_bum.id_sales = sales_bum.id_sales
                WHERE ik.id_komplain = '$id' ORDER BY ik.created_date DESC LIMIT 1";
    $query_detail = mysqli_query($connect, $sql_detail);
    $data_detail = mysqli_fetch_array($query_detail);
    $query_detail2 = mysqli_query($connect, $sql_detail);
    // Query Driver
    $sql_driver = " SELECT DISTINCT
                        ik.id_komplain,
                        bum.id_inv_bum AS id_inv,
                        status_kirim_bum.jenis_pengiriman AS jenis_pengiriman,
                        status_kirim_bum.jenis_ongkir AS jenis_ongkir,
                        status_kirim_bum.jenis_penerima AS jenis_penerima,
                        status_kirim_bum.no_resi AS no_resi,
                        status_kirim_bum.dikirim_oleh AS dikirim_oleh,
                        status_kirim_bum.penanggung_jawab AS penanggung_jawab,
                        user_bum.nama_user AS nama_driver,
                        ekspedisi_bum.nama_ekspedisi AS nama_ekspedisi,
                        penerima.nama_penerima AS nama_penerima,
                        penerima.created_date
                    FROM inv_komplain AS ik
                    LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                    LEFT JOIN status_kirim status_kirim_bum ON status_kirim_bum.id_inv = bum.id_inv_bum
                    LEFT JOIN $database2.user user_bum ON status_kirim_bum.dikirim_driver = user_bum.id_user
                    LEFT JOIN ekspedisi ekspedisi_bum ON status_kirim_bum.dikirim_ekspedisi = ekspedisi_bum.id_ekspedisi
                    LEFT JOIN inv_penerima penerima ON bum.id_inv_bum = penerima.id_inv
                    WHERE ik.id_komplain = '$id' ORDER BY penerima.created_date DESC LIMIT 1";
    $query_driver = mysqli_query($connect, $sql_driver);
    $data_driver = mysqli_fetch_array($query_driver);


    $sql_driver_rev = " SELECT DISTINCT 
                            sk.id_komplain,
                            sk.jenis_pengiriman,
                            sk.jenis_penerima,
                            sk.no_resi,
                            sk.jenis_ongkir,
                            sk.ongkir,
                            sk.free_ongkir,
                            sk.dikirim_oleh,
                            sk.diambil_oleh,
                            sk.penanggung_jawab,
                            user.nama_user AS nama_driver,
                            ekspedisi.nama_ekspedisi,
                            penerima.nama_penerima AS nama_penerima,
                            COALESCE(MAX(penerima.created_date), 'Tidak Ada Data') AS created_date
                        FROM 
                            revisi_status_kirim AS sk
                        LEFT JOIN 
                            $database2.user user ON sk.dikirim_driver = user.id_user
                        LEFT JOIN 
                            ekspedisi ekspedisi ON sk.dikirim_ekspedisi = ekspedisi.id_ekspedisi
                        LEFT JOIN 
                            inv_penerima_revisi penerima ON sk.id_komplain = penerima.id_komplain
                        WHERE 
                            sk.id_komplain = '$id'
                        GROUP BY
                            sk.id_komplain,
                            sk.jenis_pengiriman,
                            sk.jenis_penerima,
                            sk.no_resi,
                            sk.dikirim_oleh,
                            sk.penanggung_jawab,
                            user.nama_user,
                            ekspedisi.nama_ekspedisi,
                            penerima.nama_penerima
                        ORDER BY 
                            created_date DESC
                        LIMIT 1";
    $query_driver_rev = mysqli_query($connect, $sql_driver_rev);
    $data_driver_rev = mysqli_fetch_array($query_driver_rev);
    $total_driver_rev = mysqli_num_rows($query_driver_rev);

    // Query untuk total inv
    $sql_total = " SELECT DISTINCT
                        ik.id_komplain,
                        bum.id_inv_bum AS id_inv,
                        bum.total_inv AS total_inv
                    FROM inv_komplain AS ik
                    LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                    WHERE ik.id_komplain = '$id'";
    $query_total = mysqli_query($connect, $sql_total);
    $data_total = mysqli_fetch_array($query_total);
    
    // Query Umtuk kondisi komplain
    $sql_kondisi = "SELECT DISTINCT
                        bum.id_inv_bum AS id_inv,
                        bum.no_inv AS no_inv,
                        ik.id_inv,
                        kk.kat_komplain,
                        kk.kondisi_pesanan,
                        kk.catatan,
                        kk.created_date
                    FROM inv_komplain AS ik
                    LEFT JOIN komplain_kondisi kk ON ik.id_komplain = kk.id_komplain
                    LEFT JOIN inv_bum bum ON ik.id_inv = bum.id_inv_bum
                    WHERE ik.id_komplain = '$id' ORDER BY kk.created_date DESC LIMIT 1";
    $query_kondisi = mysqli_query($connect, $sql_kondisi);
    $data_kondisi = mysqli_fetch_array($query_kondisi);
    
    // Query Untuk Table
    $sql = "SELECT DISTINCT
                bum.id_inv_bum AS id_inv,
                bum.no_inv AS no_inv,
                STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                ik.id_komplain,
                spk.id_spk_reg AS id_spk,
                spk.no_spk AS no_spk,
                tpr.id_transaksi AS id_transaksi,
                tpr.id_produk AS id_produk,
                tpr.nama_produk_spk AS nama_produk_spk,
                tpr.harga AS harga,
                tpr.qty AS qty,
                tpr.disc AS disc,
                tpr.created_date AS created_date,
                COALESCE(mr_produk.nama_merk, mr_produk_set.nama_merk) AS merk
            FROM
                inv_komplain AS ik
            LEFT JOIN
                inv_bum AS bum ON ik.id_inv = bum.id_inv_bum
            LEFT JOIN
                spk_reg AS spk ON ik.id_inv = spk.id_inv
            LEFT JOIN
                transaksi_produk_reg AS tpr ON spk.id_spk_reg = tpr.id_spk
            LEFT JOIN
                tb_produk_reguler AS pr ON tpr.id_produk = pr.id_produk_reg
            LEFT JOIN
                tb_merk AS mr_produk ON pr.id_merk = mr_produk.id_merk
            LEFT JOIN
                tb_produk_set_marwa AS tpsm ON tpr.id_produk = tpsm.id_set_marwa
            LEFT JOIN
                tb_merk AS mr_produk_set ON tpsm.id_merk = mr_produk_set.id_merk
            WHERE ik.id_komplain = '$id' ORDER BY tpr.created_date ASC";
    $query = mysqli_query($connect, $sql);
   
?>