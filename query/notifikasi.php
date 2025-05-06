<?php
    //Atur jumlah item per halaman dan halaman saat ini
    // Query SQL
    $sql_spk = "SELECT 
                    spk.id_spk_reg, spk.no_spk, cs.nama_cs, spk.status_spk, 
                    STR_TO_DATE(spk.notif_date, '%d/%m/%Y, %H:%i:%s') AS notif_date, 
                    spk.status_notif
                FROM spk_reg AS spk
                LEFT JOIN tb_customer cs ON spk.id_customer = cs.id_cs
                WHERE spk.status_spk = 'Siap Kirim' ORDER BY notif_date DESC";

    $query1 = $connect->query($sql_spk);
    $query2 = $connect->query($sql_spk);
?>
