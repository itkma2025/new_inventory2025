<?php  
    $sql_inv = "SELECT 
                    nonppn.id_inv_nonppn AS id_inv, 
                    nonppn.no_inv,
                    nonppn.alamat_inv,
                    cs.alamat
                FROM inv_nonppn AS nonppn
                LEFT JOIN spk_reg spk ON nonppn.id_inv_nonppn = spk.id_inv
                LEFT JOIN tb_customer cs ON spk.id_customer = cs.id_cs
                WHERE nonppn.id_inv_nonppn = '$id_inv_decrypt'";
    // Data Untuk Menampilkan detail
    $detail = $connect->query($sql_inv . " GROUP BY nonppn.id_inv_nonppn");
?>