<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php  
        if(isset( $_POST['paymentId'])){
            // Mendapatkan data ID pembayaran dari permintaan POST
            $paymentId = $_POST['paymentId'];
        }

        ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table3">
                            <thead>
                                <tr>
                                    <th class="text-center p-3 text-nowrap text-white" style="background-color: navy;">No</th>
                                    <th class="text-center p-3 text-nowrap text-white" style="background-color: navy;">Tgl. Pembayaran</th>
                                    <th class="text-center p-3 text-nowrap text-white" style="background-color: navy;">Nominal Bayar</th>
                                    <th class="text-center p-3 text-nowrap text-white" style="background-color: navy;">Metode Bayar</th>
                                    <th class="text-center p-3 text-nowrap text-white" style="background-color: navy;">Keterangan</th>
                                    <th class="text-center p-3 text-nowrap text-white" style="background-color: navy;">Bank</th>
                                    <th class="text-center p-3 text-nowrap text-white" style="background-color: navy;">No. Rekening</th>
                                    <th class="text-center p-3 text-nowrap text-white" style="background-color: navy;">Atas Nama</th>
                                    <th class="text-center p-3 text-nowrap text-white" style="background-color: navy;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  
                                    include "../../koneksi.php";
                                    $no = 1;
                                    $sql_history = $connect->query("SELECT
                                                                        fbp.id_bayar, 
                                                                        fbp.id_inv_pembelian,
                                                                        fbp.id_bukti,
                                                                        fbp.tgl_bayar,
                                                                        fbp.total_bayar,
                                                                        fbp.metode_pembayaran,
                                                                        fbp.keterangan_bayar,
                                                                        bk.nama_bank,
                                                                        bpt.no_rekening,
                                                                        bpt.atas_nama
                                                                    FROM finance_bayar_pembelian AS fbp
                                                                    LEFT JOIN bank_pt bpt ON fbp.id_bank_pt = bpt.id_bank_pt
                                                                    LEFT JOIN bank bk ON bpt.id_bank = bk.id_bank
                                                                    WHERE fbp.id_inv_pembelian = '$paymentId';
                                                                    ");
                                    while($history = mysqli_fetch_array($sql_history)){
                                        $id_bayar = $history['id_bayar'];
                                        $id_bukti = $history['id_bukti'];
                                        $bank = "";
                                        $no_rekening = "";
                                        $atas_nama = "";
                                        if ($history['metode_pembayaran'] == "cash") {
                                            $bank = "-";
                                            $no_rekening = "-";
                                            $atas_nama = "-";
                                        } else {
                                            $bank = $history['nama_bank'];
                                            $no_rekening = $history['no_rekening'];
                                            $atas_nama = $history['atas_nama'];
                                        }
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $no; ?></td>
                                    <td class="text-center"><?php echo $history['tgl_bayar']; ?></td>
                                    <td class="text-end"><?php echo number_format($history['total_bayar'],0,',',','); ?></td>
                                    <td class="text-center"><?php echo ucfirst($history['metode_pembayaran']); ?></td>
                                    <td><?php echo $history['keterangan_bayar']; ?></td>
                                    <td class="text-nowrap"><?php echo $bank; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $no_rekening; ?></td>
                                    <td class="text-nowrap"><?php echo $atas_nama; ?></td>
                                    <td class="text-center">
                                        <?php  
                                            if ($history['metode_pembayaran'] == "cash") {
                                                ?>
                                                    <button type="button" class="btn btn-primary btn-sm view-bukti" title="Lihat Bukti Transfer" disabled><i class="bi bi-eye-fill"></i></button>
                                                <?php
                                            } else {
                                                ?>
                                                    <button type="button" class="btn btn-primary btn-sm view_bukti" title="Lihat Bukti Transfer" data-bs-toggle="modal" data-bs-target="#bukti" data-id="<?php echo $id_bukti ?>" ><i class="bi bi-eye-fill"></i></button>
                                                <?php
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php $no++ ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php

    ?>
</body>
</html>
<script>
     $(document).ready(function(){
        $('.view_bukti').click(function(event){
            // Mencegah perilaku default dari tombol yang dipilih
            event.preventDefault();  
            var buktiId = $(this).data('id');
            
            // Mengirimkan data ke server menggunakan AJAX
            $.ajax({
                url: "ajax/bukti-payment.php",
                method: "POST",
                data: {buktiId: buktiId},
                success: function(data){
                    // Menampilkan respons di konsol browser
                    // console.log(data);
                    $("#detail_bukti").html(data)
                }
            });
        });
    });
</script>