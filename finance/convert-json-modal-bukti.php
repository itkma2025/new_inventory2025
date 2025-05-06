<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .img-fluid{
            height: 500px !important;
        }
    </style>
</head>
<body>
    <?php
        if(isset($_POST["data_id"])){
            $id_bayar = $_POST["data_id"];

            include "../akses.php";
            $sql_bukti = mysqli_query($connect, "SELECT 
                                                        fnc.jenis_inv, fnc.id_inv, fnc.id_tagihan, byr.id_bayar, byr.id_bukti, bkt.tf_bank, bkt.rek_pengirim, bkt.tf_an, bkt.bukti_tf, bnk.nama_bank 
                                                FROM finance AS fnc 
                                                LEFT JOIN finance_bayar byr ON (fnc.id_finance = byr.id_finance)
                                                LEFT JOIN finance_bukti_tf bkt ON (bkt.id_bukti_tf = byr.id_bukti)
                                                LEFT JOIN bank bnk ON (bnk.id_bank = bkt.tf_bank)
                                                WHERE byr.id_bayar = '$id_bayar'");
            $data_bukti = mysqli_fetch_array($sql_bukti);
            $id_inv = $data_bukti['id_inv'];
            $an = $data_bukti['tf_an'];
            $rek = $data_bukti['rek_pengirim'];
            $bank = $data_bukti['nama_bank'];
            $gambar_bukti = $data_bukti['bukti_tf'];
            // Kondisi Berdasarkan Jenis Invoice
            if($data_bukti['jenis_inv'] == 'nonppn'){
                $sql_inv = mysqli_query($connect, " SELECT  
                                                            nonppn.id_inv_nonppn, 
                                                            nonppn.no_inv, 
                                                            DAY(STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y')) AS day_inv, 
                                                            LPAD(MONTH(STR_TO_DATE(tgl_inv, '%d/%m/%Y')), 2, '0') AS month_inv,
                                                            YEAR(STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y')) AS year_inv,

                                                            fnc.id_inv,
                                                            fnc.id_tagihan,

                                                            bill.id_tagihan,
                                                            bill.no_tagihan,

                                                            cs.id_cs,
                                                            cs.nama_cs,

                                                            spk.id_inv,
                                                            spk.id_customer
                                                    FROM inv_nonppn AS nonppn
                                                    LEFT JOIN finance fnc ON (nonppn.id_inv_nonppn = fnc.id_inv)
                                                    LEFT JOIN finance_tagihan bill ON (fnc.id_tagihan = bill.id_tagihan)
                                                    LEFT JOIN spk_reg spk ON (nonppn.id_inv_nonppn = spk.id_inv)
                                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                    WHERE nonppn.id_inv_nonppn = '$id_inv'");
                    $data_inv = mysqli_fetch_array($sql_inv);

                    $no_inv_nonppn = $data_inv['no_inv'];
                    $no_tagihan = $data_inv['no_tagihan'];
                    $day_inv = $data_inv['day_inv'];
                    $month_inv =  $data_inv['month_inv'];
                    $year_inv =  $data_inv['year_inv'];
                    $cs = $data_inv['nama_cs'];


                    $nama_invoice = 'Invoice_Non_Ppn';

                    // Convert $no_inv_nonppn to the desired format
                    $no_inv_nonppn_converted = str_replace('/', '_', $no_inv_nonppn);

                    // Generate folder name based on invoice details
                    $folder_name = $no_inv_nonppn_converted;

                    // Encode a portion of the folder name
                    $encoded_portion = base64_encode($folder_name);

                    // Combine the original $no_inv_nonppn, encoded portion, and underscore
                    $encoded_folder_name = $no_inv_nonppn_converted . '_' . $encoded_portion;

                    // untuk Membuat Folder Bukti Pembayaran
                    $bukti_pembayaran = "Bukti_Transfer";

                    // Set the path for the customer's folder
                    $customer_folder_path = "../Customer/" . $cs . "/" . $year_inv . "/" . $month_inv . "/" . $day_inv . "/" . ucwords(strtolower(str_replace('_', ' ', $nama_invoice))) . "/" . $encoded_folder_name ."/". $bukti_pembayaran ."/";
                    // Menampilkan Gambar Bukti Transfer
                    $bukti_tf = $customer_folder_path . $gambar_bukti;
                   
                    echo '
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-6">
                                    <img src="'.$bukti_tf.'" class="img-fluid" alt="...">
                                </div>
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <h5 class="card-title">Bukti Transfer</h5>
                                        <p class="card-text" style="font-size: 16px">Nama Pengirim : '.$an.'</p>
                                        <p class="card-text" style="font-size: 16px">Rek Pengirim : '.$rek.'</p>
                                        <p class="card-text" style="font-size: 16px">Bank Pengirim : '.$bank.'</p>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    ';
                    


            }else if($data_bukti['jenis_inv'] == 'ppn'){
                $sql_inv = mysqli_query($connect, " SELECT  
                                                            ppn.id_inv_ppn, 
                                                            ppn.no_inv, 
                                                            DAY(STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y')) AS day_inv, 
                                                            LPAD(MONTH(STR_TO_DATE(tgl_inv, '%d/%m/%Y')), 2, '0') AS month_inv,
                                                            YEAR(STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y')) AS year_inv,

                                                            fnc.id_inv,
                                                            fnc.id_tagihan,

                                                            bill.id_tagihan,
                                                            bill.no_tagihan,

                                                            cs.id_cs,
                                                            cs.nama_cs,

                                                            spk.id_inv,
                                                            spk.id_customer
                                                    FROM inv_ppn AS ppn
                                                    LEFT JOIN finance fnc ON (ppn.id_inv_ppn = fnc.id_inv)
                                                    LEFT JOIN finance_tagihan bill ON (fnc.id_tagihan = bill.id_tagihan)
                                                    LEFT JOIN spk_reg spk ON (ppn.id_inv_ppn = spk.id_inv)
                                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                    WHERE ppn.id_inv_ppn = '$id_inv'");
                    $data_inv = mysqli_fetch_array($sql_inv);

                    $no_inv_ppn = $data_inv['no_inv'];
                    $no_tagihan = $data_inv['no_tagihan'];
                    $day_inv = $data_inv['day_inv'];
                    $month_inv =  $data_inv['month_inv'];
                    $year_inv =  $data_inv['year_inv'];
                    $cs = $data_inv['nama_cs'];


                    $nama_invoice = 'Invoice_Ppn';

                    // Convert $no_inv_ppn to the desired format
                    $no_inv_ppn_converted = str_replace('/', '_', $no_inv_ppn);

                    // Generate folder name based on invoice details
                    $folder_name = $no_inv_ppn_converted;

                    // Encode a portion of the folder name
                    $encoded_portion = base64_encode($folder_name);

                    // Combine the original $no_inv_ppn, encoded portion, and underscore
                    $encoded_folder_name = $no_inv_ppn_converted . '_' . $encoded_portion;

                    // untuk Membuat Folder Bukti Pembayaran
                    $bukti_pembayaran = "Bukti_Transfer";

                    // Set the path for the customer's folder
                    $customer_folder_path = "../Customer/" . $cs . "/" . $year_inv . "/" . $month_inv . "/" . $day_inv . "/" . ucwords(strtolower(str_replace('_', ' ', $nama_invoice))) . "/" . $encoded_folder_name ."/". $bukti_pembayaran ."/";
                    // Menampilkan Gambar Bukti Transfer
                    $bukti_tf = $customer_folder_path . $gambar_bukti;
                    echo '
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-6">
                                    <img src="'.$bukti_tf.'" class="img-fluid" alt="...">
                                </div>
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <h5 class="card-title">Bukti Transfer</h5>
                                        <p class="card-text" style="font-size: 16px">Nama Pengirim : '.$an.'</p>
                                        <p class="card-text" style="font-size: 16px">Rek Pengirim : '.$rek.'</p>
                                        <p class="card-text" style="font-size: 16px">Bank Pengirim : '.$bank.'</p>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    ';
            }else if($data_bukti['jenis_inv'] == 'bum'){
                $sql_inv = mysqli_query($connect, " SELECT  
                                                            bum.id_inv_bum, 
                                                            bum.no_inv, 
                                                            DAY(STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y')) AS day_inv, 
                                                            LPAD(MONTH(STR_TO_DATE(tgl_inv, '%d/%m/%Y')), 2, '0') AS month_inv,
                                                            YEAR(STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y')) AS year_inv,

                                                            fnc.id_inv,
                                                            fnc.id_tagihan,

                                                            bill.id_tagihan,
                                                            bill.no_tagihan,

                                                            cs.id_cs,
                                                            cs.nama_cs,

                                                            spk.id_inv,
                                                            spk.id_customer
                                                    FROM inv_bum AS bum
                                                    LEFT JOIN finance fnc ON (bum.id_inv_bum = fnc.id_inv)
                                                    LEFT JOIN finance_tagihan bill ON (fnc.id_tagihan = bill.id_tagihan)
                                                    LEFT JOIN spk_reg spk ON (bum.id_inv_bum = spk.id_inv)
                                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                                    WHERE bum.id_inv_bum = '$id_inv'");
                    $data_inv = mysqli_fetch_array($sql_inv);

                    $no_inv_bum = $data_inv['no_inv'];
                    $no_tagihan = $data_inv['no_tagihan'];
                    $day_inv = $data_inv['day_inv'];
                    $month_inv =  $data_inv['month_inv'];
                    $year_inv =  $data_inv['year_inv'];
                    $cs = $data_inv['nama_cs'];


                    $nama_invoice = 'Invoice_Bum';

                    // Convert $no_inv_bum to the desired format
                    $no_inv_bum_converted = str_replace('/', '_', $no_inv_bum);

                    // Generate folder name based on invoice details
                    $folder_name = $no_inv_bum_converted;

                    // Encode a portion of the folder name
                    $encoded_portion = base64_encode($folder_name);

                    // Combine the original $no_inv_bum, encoded portion, and underscore
                    $encoded_folder_name = $no_inv_bum_converted . '_' . $encoded_portion;

                    // untuk Membuat Folder Bukti Pembayaran
                    $bukti_pembayaran = "Bukti_Transfer";

                    // Set the path for the customer's folder
                    $customer_folder_path = "../Customer/" . $cs . "/" . $year_inv . "/" . $month_inv . "/" . $day_inv . "/" . ucwords(strtolower(str_replace('_', ' ', $nama_invoice))) . "/" . $encoded_folder_name ."/". $bukti_pembayaran ."/";
                    // Menampilkan Gambar Bukti Transfer
                    $bukti_tf = $customer_folder_path . $gambar_bukti;
                    echo '
                        <div class="card mb-3">
                            <div class="row g-0">
                                <div class="col-md-6">
                                    <img src="'.$bukti_tf.'" class="img-fluid" alt="...">
                                </div>
                                <div class="col-md-6">
                                    <div class="card-body">
                                        <h5 class="card-title">Bukti Transfer</h5>
                                        <p class="card-text" style="font-size: 16px">Nama Pengirim : '.$an.'</p>
                                        <p class="card-text" style="font-size: 16px">Rek Pengirim : '.$rek.'</p>
                                        <p class="card-text" style="font-size: 16px">Bank Pengirim : '.$bank.'</p>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    ';
                    
            }
        }
    ?>
</body>
</html>
