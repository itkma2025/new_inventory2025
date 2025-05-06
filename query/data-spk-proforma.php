<?php  
    if ($_GET['jenis'] == 'nonppn') {
        $sql_data_spk = "SELECT
                    nonppn.id_inv_nonppn,
                    nonppn.kategori_inv,
                    sr.id_spk_reg,
                    sr.id_user, 
                    sr.id_customer, 
                    sr.id_inv, 
                    sr.no_spk, 
                    sr.no_po, 
                    sr.tgl_pesanan,
                    cs.nama_cs, 
                    cs.alamat, 
                    ordby.order_by, 
                    sl.nama_sales 
                FROM inv_nonppn AS nonppn
                JOIN spk_reg sr ON (nonppn.id_inv_nonppn = sr.id_inv)
                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                WHERE nonppn.id_inv_nonppn = '$id_inv'";
        $query_data_spk = $connect->query($sql_data_spk);
        $totalData = mysqli_num_rows($query_data_spk);
    } else if ($_GET['jenis'] == 'ppn') {
        $sql_data_spk = "SELECT
                    ppn.id_inv_ppn,
                    ppn.kategori_inv,
                    sr.id_spk_reg,
                    sr.id_user, 
                    sr.id_customer, 
                    sr.id_inv, 
                    sr.no_spk, 
                    sr.no_po, 
                    sr.tgl_pesanan,
                    cs.nama_cs, 
                    cs.alamat, 
                    ordby.order_by, 
                    sl.nama_sales 
                FROM inv_ppn AS ppn
                JOIN spk_reg sr ON (ppn.id_inv_ppn = sr.id_inv)
                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                WHERE ppn.id_inv_ppn = '$id_inv'";
        $query_data_spk = $connect->query($sql_data_spk);
        $totalData = mysqli_num_rows($query_data_spk);
    } else if ($_GET['jenis'] == 'bum') {
        $sql_data_spk = "SELECT
                    bum.id_inv_bum,
                    bum.kategori_inv,
                    sr.id_spk_reg,
                    sr.id_user, 
                    sr.id_customer, 
                    sr.id_inv, 
                    sr.no_spk, 
                    sr.no_po, 
                    sr.tgl_pesanan,
                    cs.nama_cs, 
                    cs.alamat, 
                    ordby.order_by, 
                    sl.nama_sales 
                FROM inv_bum AS bum
                JOIN spk_reg sr ON (bum.id_inv_bum = sr.id_inv)
                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                WHERE bum.id_inv_bum = '$id_inv'";
        $query_data_spk = $connect->query($sql_data_spk);
        $totalData = mysqli_num_rows($query_data_spk);
    } else {
        ?>
            <script type="text/javascript">
                window.location.href = "../404.php";
            </script>
        <?php
    }
?>