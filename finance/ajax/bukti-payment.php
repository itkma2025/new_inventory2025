<?php  
    if(isset($_POST['buktiId'])){
        // Mendapatkan data ID pembayaran dari permintaan POST
        $buktiId = $_POST['buktiId'];
        include "../../koneksi.php";
        $bukti_tf = $connect->query("SELECT 
                                        fbtp.id_bukti_tf,
                                        fbtp.bukti_tf,
                                        fbtp.path_tf,
                                        bsp.atas_nama,
                                        bsp.no_rekening,
                                        bk.nama_bank,
                                        sp.nama_sp
                                    FROM finance_bukti_tf_pembelian AS fbtp
                                    LEFT JOIN bank_sp bsp ON fbtp.id_bank_sp = bsp.id_bank_sp
                                    LEFT JOIN bank bk ON bsp.id_bank = bk.id_bank
                                    LEFT JOIN tb_supplier sp ON bsp.id_sp = sp.id_sp
                                    LEFT JOIN finance_bayar_pembelian fbp ON fbtp.id_bukti_tf = fbp.id_bukti
                                    LEFT JOIN inv_pembelian_lokal ipl ON ipl.id_inv_pembelian = fbp.id_inv_pembelian
                                    WHERE fbtp.id_bukti_tf = '$buktiId'
                                    ");
        $data_bukti = mysqli_fetch_array($bukti_tf);
        $nama_sp = $data_bukti['nama_sp'];
        $path = $data_bukti['path_tf'];
        $folder_path = "../Supplier";
        $bukti_tf = $data_bukti['bukti_tf'];
        // Periksa karakter nama supplier yang tidak valid
        $gambar = $folder_path . "/" . $path . "/" . $bukti_tf;
    }

    ?>
        <div class="card mb-3">
            <div class="row g-0">
                <div class="col-md-6">
                    <img src="<?php echo $gambar ?>" class="img-fluid" style="background-size: cover;" alt="...">
                </div>
                <div class="col-md-6">
                    <div class="card-body p-5">
                        <div class="mb-3">
                            <label class="fw-bold">Nama Penerima:</label>
                            <p><?php echo $data_bukti['atas_nama']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Rek Penerima:</label>
                            <p><?php echo $data_bukti['no_rekening']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Bank Penerima:</label>
                            <p><?php echo $data_bukti['nama_bank']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
?>