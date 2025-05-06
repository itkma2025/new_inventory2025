<div class="modal fade" id="buktiKirim" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Bukti Kirim</h1>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-dismiss="modal">X</button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <?php
                        include "koneksi.php";
                        $sql_bukti = "SELECT 
                                        ibt.bukti_satu, 
                                        ibt.bukti_dua, 
                                        ibt.bukti_tiga, 
                                        ibt.lokasi,
                                        ibt.created_date,
                                        ip.id_inv, 
                                        ip.nama_penerima, 
                                        ip.tgl_terima, 
                                        sk.jenis_penerima, 
                                        sk.dikirim_ekspedisi, 
                                        sk.no_resi, ex.nama_ekspedisi
                                        FROM inv_bukti_terima AS ibt
                                        LEFT JOIN inv_penerima ip ON (ibt.id_inv = ip.id_inv)
                                        LEFT JOIN status_kirim sk ON (ibt.id_inv = sk.id_inv)
                                        LEFT JOIN ekspedisi ex ON (ex.id_ekspedisi = sk.dikirim_ekspedisi) 
                                        WHERE ibt.id_inv = '$id_inv'";
                        $query_bukti = mysqli_query($connect, $sql_bukti);
                        $data_bukti = mysqli_fetch_array($query_bukti);

                        $gambar1 = $data_bukti['bukti_satu'] ?? null;
                        $gambar_bukti1 = "gambar/bukti1/$gambar1";
                        $gambar2 = $data_bukti['bukti_dua'] ?? null;
                        $gambar_bukti2 = "gambar/bukti2/$gambar2";
                        $gambar3 = $data_bukti['bukti_tiga'] ?? null;
                        $gambar_bukti3 = "gambar/bukti3/$gambar3";
                        $diterima = isset($data_bukti['nama_ekspedisi']) ? $data_bukti['nama_ekspedisi'] : (isset($data_bukti['nama_penerima']) ? $data_bukti['nama_penerima'] : null);
                        $lokasi = $data_bukti['lokasi'] ?? null;
                        $created_date = isset($data_bukti['created_date']) ? date('d/m/Y H:i:s', strtotime($data_bukti['created_date'])) : null;

                        $img = "";
                        if ($gambar1 && file_exists("gambar/bukti1/" . $gambar1)) {
                            $img = "gambar/bukti1/" . $gambar1;
                        } else if($gambar1 && file_exists("gambar/bukti_kirim/" . $nama_driver . "/" . $gambar1)){
                            $img = "gambar/bukti_kirim/" . $nama_driver . "/" . $gambar1;
                        } else {
                            $img = "assets/img/no_img.jpg";
                        }
                    ?>
                    <div class="mb-3">
                        <h6>Penerima : <?php echo isset($data_bukti['jenis_penerima']) ? $data_bukti['jenis_penerima'] : 'Tidak Diketahui'; ?> (<?php echo $diterima ?>)</h6>
                        <?php  
                            if (isset($data_bukti['no_resi']) && $data_bukti['no_resi'] != '') {
                                ?>
                                    <h6>No. Resi : <?php echo $data_bukti['no_resi'] ?></h6>
                                <?php
                            }
                        ?>
                        <h6>Tgl. Terima : <?php echo isset($data_bukti['created_date']) ? date('d/m/Y', strtotime($data_bukti['created_date'])) : 'Tidak Diketahui'; ?></h6>
                    </div>
                    <div class="carousel-item active">
                        <a href="<?php echo $img; ?>" data-fancybox="gallery" data-width="1600" data-height="1200">
                            <img src="<?php echo $img; ?>" class="d-block w-100">
                        </a>
                        <div class="text-center mt-3">
                            <h6><?php echo $lokasi ?></h6>
                            <h6><?php echo $created_date ?></h6>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>