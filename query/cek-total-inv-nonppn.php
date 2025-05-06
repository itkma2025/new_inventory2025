<?php
    $sp_disc = $data_inv['sp_disc'] / 100;
    $ongkir = $data_inv['ongkir'];
    $sub_total_spdisc = $sub_total * $sp_disc;
    $grand_total_fix = round($sub_total - $sub_total_spdisc + $ongkir);
    if ($total_inv != $grand_total_fix) {
        mysqli_query($connect, "UPDATE inv_nonppn SET total_inv = '$grand_total_fix' WHERE id_inv_nonppn = '$id_inv'");
    }
?>