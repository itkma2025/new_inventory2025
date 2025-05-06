<?php  
    require_once "../akses.php";
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
        if(isset($_POST['data_id'])){
            echo $finance_id = htmlspecialchars(decrypt($_POST['data_id'], $key_finance));
        }
        // Menampilkan id inv data pada finance
        $sql_finance = $connect->query("SELECT 
                                            fnc.id_inv, 
                                            fnc.id_tagihan,
                                            COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                            COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                                            COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv
                                        FROM finance AS fnc
                                        LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                                        LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                                        LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                                        WHERE id_finance = '$finance_id'");
        $data_finance = mysqli_fetch_array($sql_finance);    
        $id_inv = $data_finance['id_inv'];
        $no_inv = $data_finance['no_inv'];
        $tgl_inv = $data_finance['tgl_inv'];
        $cs_inv = $data_finance['cs_inv'];
        $id_tagihan = $data_finance['id_tagihan'];
        
        // Menampilkan nama cs data pada spk
        $sql_spk = $connect->query("SELECT 
                                        spk.id_customer,
                                        cs.nama_cs
                                    FROM spk_reg AS spk 
                                    LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                                    WHERE id_inv = '$id_inv'");
        $data_spk = mysqli_fetch_array($sql_spk);
        $nama_cs = $data_spk['nama_cs'];
    ?>
    <div class="text-end mt-3 me-2">
        <button class="btn btn-success btn-sm" id="status_tempo">Lunas</button>
    </div>
    <div class="row mt-2 mb-2">
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
    <div class="border-bottom"></div>
    <div class="row mt-2">
        <?php 
            $grand_total_inv = 0; 
            $sql_bayar = $connect->query("  SELECT  
                                                bill.id_tagihan,
                                                COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo) AS tgl_tempo,
                                                STR_TO_DATE(COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo), '%d/%m/%Y') AS tgl_tempo_convert,
                                                COALESCE(nonppn.total_inv, ppn.total_inv, bum.total_inv) AS total_inv,
                                                COALESCE(SUM(total_bayar), 0) AS total_pembayaran,
                                                COALESCE(SUM(total_potongan), 0) AS total_potongan
                                            FROM finance_tagihan AS bill 
                                            JOIN finance fnc ON (bill.id_tagihan = fnc.id_tagihan)
                                            LEFT JOIN finance_bayar byr ON (fnc.id_finance = byr.id_finance)
                                            LEFT JOIN inv_nonppn nonppn ON (fnc.id_inv = nonppn.id_inv_nonppn)
                                            LEFT JOIN inv_ppn ppn ON (fnc.id_inv = ppn.id_inv_ppn)
                                            LEFT JOIN inv_bum bum ON (fnc.id_inv = bum.id_inv_bum)
                                            WHERE byr.id_finance = '$finance_id'
                                        ");
            $data_bayar = mysqli_fetch_array($sql_bayar);
            $tgl_tempo_cek = $data_bayar['tgl_tempo'];
            $tgl_tempo = $data_bayar['tgl_tempo_convert'];
            $date_now = date('Y-m-d');
            $total_inv = $data_bayar['total_inv'];
            $grand_total_inv += $total_inv;
            $total_bayar = $data_bayar['total_pembayaran'];
            $total_potongan = $data_bayar['total_potongan'];
            $sisa_tagihan = $total_inv - $total_potongan - $total_bayar;

            // Kondisi Jatuh Tempo
            $tgl_tempo = '';
            if (!empty($tgl_tempo_cek)) {
                $tgl_tempo = $tgl_tempo_cek;
            } else {
                $tgl_tempo = 'Tidak Ada Tempo';
            }


            if (!empty($tgl_tempo_cek)) {
                $timestamp_tgl_tempo = strtotime($tgl_tempo);
                $timestamp_now = strtotime($date_now);
                // Hitung selisih timestamp
                $selisih_timestamp = $timestamp_tgl_tempo - $timestamp_now;
                // Konversi selisih timestamp ke dalam hari
                $selisih_hari = floor($selisih_timestamp / (60 * 60 * 24));
                $status_tempo = "";
                if ($tgl_tempo > $date_now){
                    $status_tempo = "Tempo < " .$selisih_hari. " Hari";
                } else if ($tgl_tempo < $date_now){
                    $status_tempo = "Tempo > " . abs($selisih_hari). " Hari";
                } else if ($tgl_tempo == $date_now) {
                    $status_tempo = "Jatuh Tempo Hari ini";
                } else if ($sisa_tagihan == '0'){
                    $status_tempo = "Lunas";
                } else {
                    $status_tempo = "Tidak Ada Tempo";
                }
            } else {
                $status_tempo = "Tidak Ada Tempo";
            }
        ?>
        <div class="col-xl-3">
            <div class="col-12">
                <label class="col-form-label">Total Pembayaran :</label>
            </div>
            <div class="col-12">
                <input type="text" class="form-control text-end" value="<?php echo number_format($total_bayar,0,'.','.'); ?>" readonly>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="col-12">
                <label class="col-form-label">Total Potongan :</label>
            </div>
            <div class="col-12">
                <input type="text" class="form-control text-end" value="<?php echo number_format($total_potongan,0,'.','.') ?>" readonly>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="col-12">
                <label class="col-form-label">Sisa Tagihan :</label>
            </div>
            <div class="col-12">
                <input type="text" class="form-control text-end" value="<?php echo number_format($sisa_tagihan,0,'.','.') ?>" readonly>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="col-12">
                <label class="col-form-label">Jatuh Tempo :</label>
            </div>
            <div class="col-12">
                <input type="text" class="form-control" value="<?php echo $tgl_tempo ?>" readonly>
            </div>
        </div>
    </div>
    <div class="table-responsive mt-4">
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
                                        byr.id_tagihan, 
                                        byr.id_finance, 
                                        byr.id_bukti, 
                                        byr.metode_pembayaran, 
                                        byr.total_bayar, 
                                        byr.tgl_bayar,
                                        byr.keterangan_bayar,
                                        byr.created_date,
                                        bnk.nama_bank,
                                        pt.id_bank,
                                        pt.no_rekening,
                                        pt.atas_nama,
                                        fnc.id_inv
                                    FROM finance_bayar AS byr
                                    LEFT JOIN bank_pt pt ON (byr.id_bank_pt = pt.id_bank_pt)
                                    LEFT JOIN bank bnk ON (pt.id_bank = bnk.id_bank)
                                    LEFT JOIN finance fnc ON (byr.id_finance = fnc.id_finance)
                                    WHERE byr.id_finance = '$finance_id' ORDER BY byr.created_date ASC";
                    $query_history = mysqli_query($connect, $sql_history);
                    while($data_history = mysqli_fetch_array($query_history)){
                        $id_bayar = $data_history['id_bayar'];
                        $metode_pembayaran = $data_history['metode_pembayaran'];
                        $keterangan = $data_history['keterangan_bayar'] ?? '-' ?: '-';
                        $nama_bank = "";
                        $no_rek = "";
                        $atas_nama = "";
                        $bukti_tf = "";
                        if($metode_pembayaran == 'cash'){
                            $nama_bank = "-";
                            $no_rek = "-";
                            $atas_nama = "-";
                            $bukti_tf = '<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" disabled><i class="bi bi-eye-fill"</i></button>';
                        } else if($metode_pembayaran == 'transfer'){
                            $nama_bank = $data_history['nama_bank'];
                            $no_rek = $data_history['no_rekening'];
                            $atas_nama = $data_history['atas_nama'];
                            $bukti_tf = '<button type="button" class="btn btn-primary btn-sm view_bukti" data-bs-toggle="modal" data-bs-target="#bukti" data-id="' . $id_bayar . '"><i class="bi bi-eye-fill"</i></button>';
                        } else {
                            echo 'Metode Pembayaran Kosong';
                        }

                      
                ?>
                <tr>
                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                    <td class="text-center text-nowrap"><?php echo $data_history['tgl_bayar'] ?></td>
                    <td class="text-end text-nowrap"><?php echo number_format($data_history['total_bayar'],0,'.','.') ?></td>
                    <td class="text-center text-nowrap"><?php echo $data_history['metode_pembayaran'] ?></td>
                    <td class="text-center text-nowrap"><?php echo $keterangan ?></td>
                    <td class="text-center text-nowrap"><?php echo $nama_bank; ?></td>
                    <td class="text-center text-nowrap"><?php echo $no_rek; ?></td>
                    <td class="text-center text-nowrap"><?php echo $atas_nama; ?></td>
                    <td class="text-center text-nowrap"><?php echo $bukti_tf; ?></td>
                </tr>
                <?php $no++ ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function(){
            // Untuk menampilkan bukti transfer
            $('.view_bukti').click(function(){
                var data_id = $(this).data("id")
                $.ajax({
                    url: "convert-json-modal-bukti.php",
                    method: "POST",
                    data: {data_id: data_id},
                    success: function(data){
                        $("#detail_bukti").html(data)
                        $("#bukti").modal('show')
                    }
                })
            })

            // Untuk menampilkan status tempo
            var statusTempo = '<?php echo $status_tempo ?>';
            if (statusTempo == 'Tidak Ada Tempo'){
                // Hidden status tempo
                $('#status_tempo').addClass('d-none');
            } else {
                // Tampilkan nilai status tempo
                $('#status_tempo').html(statusTempo);
            }
        })
    </script>
</body>
</html>