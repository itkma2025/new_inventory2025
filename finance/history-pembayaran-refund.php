<?php  
    require_once "../akses.php";    
    $key = "Fin@nce2024?";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        if(isset($_POST["data_id"])){
            $refund_id = decrypt($_POST["data_id"], $key);
        }

        // Menampilkan id inv data pada finance
        $sql_finance = $connect->query("SELECT 
                                            fnc_refund.id_refund,
                                            fnc_refund.id_inv, 
                                            COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                            COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                                            COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv
                                        FROM finance_refund AS fnc_refund
                                        LEFT JOIN inv_nonppn nonppn ON (fnc_refund.id_inv = nonppn.id_inv_nonppn)
                                        LEFT JOIN inv_ppn ppn ON (fnc_refund.id_inv = ppn.id_inv_ppn)
                                        LEFT JOIN inv_bum bum ON (fnc_refund.id_inv = bum.id_inv_bum)
                                        WHERE  fnc_refund.id_refund  = '$refund_id'");
        $data_finance = mysqli_fetch_array($sql_finance);    
        $id_inv = $data_finance['id_inv'];
        $no_inv = $data_finance['no_inv'];
        $tgl_inv = $data_finance['tgl_inv'];
        $cs_inv = $data_finance['cs_inv'];
        
        // Menampilkan nama cs data pada spk
        $sql_spk = $connect->query("SELECT 
                                        spk.id_inv,
                                        spk.id_customer,
                                        cs.nama_cs
                                    FROM spk_reg AS spk 
                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                    WHERE spk.id_inv = '$id_inv'");
        $data_spk = mysqli_fetch_array($sql_spk);
        $nama_cs = $data_spk['nama_cs'];
    ?>
    <div class="row mt-2">
        <div class="col-md-6">
            <div class="row p-2">
                <div class="col-4">
                    <label class="col-form-label">No. Invoice :</label>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control" value="<?php echo $no_inv ?>" readonly>
                </div>
            </div>
            <div class="row p-2">
                <div class="col-4">
                    <label class="col-form-label">Tanggal Invoice :</label>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control" value="<?php echo $tgl_inv ?>" readonly>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row p-2">
                <div class="col-4">
                    <label class="col-form-label">Nama Customer :</label>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control" value="<?php echo $nama_cs ?>" readonly>
                </div>
            </div>
            <div class="row p-2"> 
                <div class="col-4">
                    <label class="col-form-label">Customer Invoice :</label>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control" value="<?php echo $cs_inv ?>" readonly>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive mt-2">
        <table class="table table-responsive table-striped" id="table2">
            <thead>
                <tr class="text-white" style="background-color: navy;">
                    <td class="text-center p-3 text-nowrap">No</td>
                    <td class="text-center p-3 text-nowrap">Tgl. Pembayaran</td>
                    <td class="text-center p-3 text-nowrap">Nominal Bayar</td>
                    <td class="text-center p-3 text-nowrap">Metode Bayar</td>
                    <td class="text-center p-3 text-nowrap">Keterangan</td>
                    <td class="text-center p-3 text-nowrap">Bank</td>
                    <td class="text-center p-3 text-nowrap">No. Rekening</td>
                    <td class="text-center p-3 text-nowrap">Atas Nama</td>
                    <td class="text-center p-3 text-nowrap">Aksi</td>
                </tr>
            </thead>
            <tbody>
                <?php  

                    $no = 1;
                    $sql_history = "SELECT 
                                    byr.id_bayar,
                                    byr.id_bank_pt AS byr_bank, 
                                    byr.id_refund, 
                                    byr.id_bukti, 
                                    byr.metode_pembayaran, 
                                    byr.total_bayar, 
                                    byr.tgl_bayar,
                                    byr.keterangan_bayar,
                                    byr.created_date,
                                    byr.created_by,

                                    bnk.nama_bank,
                                    pt.id_bank,
                                    pt.no_rekening,
                                    pt.atas_nama,

                                    refund.id_refund
                                    FROM finance_bayar_refund AS byr
                                    LEFT JOIN bank_pt pt ON (byr.id_bank_pt = pt.id_bank_pt)
                                    LEFT JOIN bank bnk ON (pt.id_bank = bnk.id_bank)
                                    LEFT JOIN finance_refund refund ON (byr.id_refund = refund.id_refund)
                                    WHERE byr.id_refund = '$refund_id' ORDER BY byr.created_date ASC";
                    $query_history = mysqli_query($connect, $sql_history);
                    while($data_history = mysqli_fetch_array($query_history)){
                        $no_rek = $data_history['no_rekening'];
                        $atas_nama = $data_history['atas_nama'];
                        $id_bayar = $data_history['id_bayar'];
                      
                ?>
                <tr>
                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                    <td class="text-center text-nowrap"><?php echo $data_history['tgl_bayar'] ?></td>
                    <td class="text-end text-nowrap"><?php echo number_format($data_history['total_bayar'],0,'.','.') ?></td>
                    <td class="text-center text-nowrap"><?php echo $data_history['metode_pembayaran'] ?></td>
                    <td class="text-center text-nowrap"><?php echo $data_history['keterangan_bayar'] ?></td>
                    <td class="text-center text-nowrap">
                        <?php 
                            if($data_history['byr_bank'] == ''){
                                echo '-';
                            } else {
                                echo $data_history['nama_bank'];
                            }
                        
                        ?>
                    </td>
                    <td class="text-center text-nowrap">
                        <?php 
                            if($data_history['byr_bank'] == ''){
                                echo '-';
                            } else {
                                echo $no_rek;
                            }
                        ?>
                    </td>
                    <td class="text-center text-nowrap">
                        <?php 
                            if($data_history['byr_bank'] == ''){
                                echo '-';
                            } else {
                                echo $atas_nama;
                            }
                        ?>
                    </td>
                    <td class="text-center text-nowrap">
                        <?php 
                            if($data_history['byr_bank'] == ''){
                                echo '<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" disabled>Lihat Gambar</button>';
                            } else {
                                echo '<button type="button" class="btn btn-primary btn-sm view_bukti" data-bs-toggle="modal" data-bs-target="#bukti" data-id="' . $id_bayar . '">Lihat Gambar</button>';
                            }
                        ?>
                    </td>
                </tr>
                <?php $no++ ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function(){
            $('.view_bukti').click(function(){
                var data_id = $(this).data("id")
                $.ajax({
                    url: "history-pembayaran-refund-bukti.php",
                    method: "POST",
                    data: {data_id: data_id},
                    success: function(data){
                        $("#detail_bukti").html(data)
                        $("#bukti").modal('show')
                    }
                })
            })
        })
    </script>                    
</body>
</html>