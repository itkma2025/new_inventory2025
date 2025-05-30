<div class="modal fade" id="edit-details" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Data Detail</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="proses/proses-ubah-detail-revisi.php" method="post">
                    <?php  
                        // Cek kondisi detail, jika data pada inv rev tidak ada maka harus ubah status dikirim terlebih dahulu
                        $cek_detail = $connect->query("SELECT id_inv FROM inv_revisi WHERE id_inv = '$id_inv'");
                        $total_cek_detail = mysqli_num_rows($cek_detail);
                        if($total_cek_detail == 0){
                            echo "Silahkan ubah status dikirim terlebih dahulu";
                        } else {
                            ?>  
                                <input type="hidden" value="<?php echo base64_encode($id) ?>" name="id_komplain">
                                <input type="hidden" value="<?php echo $no_inv_fix ?>" name="no_inv_rev">
                                <div class="mb3">
                                    <label>Pelanggan Invoice</label>
                                    <input type="text" class="form-control" name="cs_inv" value="<?php echo $pelanggan?>">
                                </div>
                                <div class="mb3">
                                    <label>Alamat</label>
                                    <textarea type="text" class="form-control" name="alamat" rows="3"><?php echo $alamat ?></textarea>
                                </div>
                            <?php 
                        }
                    
                    ?>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="ubah-detail-rev-nonppn">Ubah data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>