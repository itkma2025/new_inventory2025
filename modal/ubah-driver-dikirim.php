<?php  
    require_once "akses.php";
?>
<!-- Modal Ubah Driver -->
<div class="modal fade" id="ubahDriver" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Ubah Driver</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="proses/proses-ubah-driver.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="nonce_token" value="<?php echo $_SESSION['nonce_token']; ?>">
                    <input type="hidden" class="form-control" name="id_inv" value="<?php echo encrypt($id_inv, $key_global) ?>">
                    <input type="hidden" name="jenis_inv" value="<?php echo $jenis_inv = $_GET['jenis']; ?>">
                    <div class="mb-3">
                        <label>Pilih Driver</label>
                        <select id="pengirim" name="pengirim" class="form-select" required>
                            <option value="">Pilih..</option> 
                            <?php
                                include "koneksi.php";
                                $sql_driver = mysqli_query($koneksi2, "SELECT us.id_user_role, us.id_user, us.nama_user, us.is_approval, rl.nama_role FROM user AS us JOIN user_role rl ON (us.id_user_role = rl.id_user_role) WHERE rl.nama_role = 'Driver' AND is_approval = '1'");
                                while ($data_driver = mysqli_fetch_array($sql_driver)) {
                                ?>
                                    <option value="<?php echo $data_driver['id_user'] ?>"><?php echo $data_driver['nama_user'] ?></option>
                                <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">tutup</button>
                    <button type="submit" class="btn btn-primary" name="ubah-driver">Ubah Driver</button>
                </div>
            </form>                                         
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var selectElement = document.getElementById("pengirim");

            selectElement.addEventListener("change", function() {
                var selectedValue = selectElement.value;
                var options = selectElement.options;

                for (var i = 0; i < options.length; i++) {
                    if (options[i].value === selectedValue) {
                        options[i].style.display = "none";
                    } else {
                        options[i].style.display = "block";
                    }
                }
            });
        });
    </script>
</div>