<?php 
    $date_now = '2025-03-31';
    $status = ($tgl_inv_convert > $date_now) ? 'PPN Baru' : 'PPN Lama';

    if($status == 'PPN Baru'){
        $ppn_dpp = ($data_inv['ppn_dpp'] == '') ? '11/12' : $data_inv['ppn_dpp'];          
        // Pastikan $ppn adalah angka
        $ppn = ($data_inv['ppn'] == 0) ? 12 : floatval($data_inv['ppn']);
        $sp_disc = floatval($data_inv['sp_disc']) / 100;
        $ongkir = floatval($data_inv['ongkir']);

        // Perhitungan Sub total - Spesial Discount
        $sub_total_spdisc = round(floatval($sub_total) * (1 - $sp_disc)); 
        $nominal_sp_disc = round(floatval($sub_total) * $sp_disc); 

        // Perhitungan DPP 
        if (strpos($ppn_dpp, '/') !== false) {
            list($num, $den) = explode('/', $ppn_dpp);
            $ppn_dpp = floatval($num) / floatval($den);
        } else {
            $ppn_dpp = floatval($ppn_dpp);
        }
        $dpp = round(floatval($ppn_dpp) * floatval($sub_total_spdisc));

        // Mendapatkan nominal PPN DPP
        $nominal_ppn_dpp = floatval($sub_total_spdisc) - floatval($dpp);

        // Perhitungan Nominal PPN
        $nominal_ppn = round(floatval($dpp) * (floatval($ppn) / 100));

        // Perhitungan grand total
        $grand_total = floatval($sub_total_spdisc) + floatval($ongkir) + floatval($nominal_ppn);

        if ($data_inv['ppn_dpp'] == '' || $data_inv['ppn'] == '' || $data_inv['total_inv'] != $grand_total) {
            $stmt = $connect->prepare("UPDATE inv_ppn SET sub_total = ?, total_inv = ?, nominal_spdisc = ?, ppn_dpp = '11/12', nominal_ppn_dpp = ?, nominal_dpp = ?, ppn = '12', total_ppn = ? WHERE id_inv_ppn = ?");
            $stmt->bind_param("iiiiiis", $sub_total, $grand_total, $nominal_sp_disc, $nominal_ppn_dpp, $dpp, $nominal_ppn, $id_inv);
            $stmt->execute();
            
            // if ($stmt->execute()) {
            //     echo "Data berhasil diperbarui.";
            // } else {
            //     echo "Error: " . $stmt->error;
            // }
            $stmt->close();
        }
    } else if ($status == 'PPN Lama') {       
        // Pastikan $ppn adalah angka
        $ppn = 11;
        $sp_disc = floatval($data_inv['sp_disc']) / 100;
        $ongkir = floatval($data_inv['ongkir']);

        // Perhitungan Sub total - Spesial Discount
        $sub_total_spdisc = round(floatval($sub_total) * (1 - $sp_disc)); 
        $nominal_sp_disc = round(floatval($sub_total) * $sp_disc); 

        // Mendapatkan nominal PPN DPP
        $nominal_ppn_dpp = floatval($sub_total_spdisc) - floatval($dpp);

        // Perhitungan Nominal PPN
        $nominal_ppn = round(floatval($sub_total) * (floatval($ppn) / 100));

        // Perhitungan grand total
        $grand_total = floatval($sub_total_spdisc) + floatval($ongkir) + floatval($nominal_ppn);

        if ($data_inv['total_inv'] != $grand_total_ppn) {
            $stmt = $connect->prepare("UPDATE inv_ppn SET sub_total = ?, total_inv = ?, nominal_spdisc = ?, ppn_dpp = '0', nominal_ppn_dpp = '0', nominal_dpp = ?, ppn = '11', total_ppn = ? WHERE id_inv_ppn = ?");
            $stmt->bind_param("iiiiis", $sub_total_spdisc, $grand_total, $nominal_sp_disc, $sub_total_spdisc, $grand_total, $id_inv);
            $stmt->execute();
            
            // if ($stmt->execute()) {    
            //     echo "Data berhasil diperbarui.";
            // } else {
            //     echo "Error: " . $stmt->error;
            // }
            $stmt->close();
        }
    } else {
        die('Tidak di temukan');
    }

    
?>