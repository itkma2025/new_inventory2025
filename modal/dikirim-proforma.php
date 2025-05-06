<div class="modal fade" id="Dikirim" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-body">
                <div class="card-header">
                    <h1 class="text-center fs-5" id="exampleModalLabel">Proses Dikirim</h1>
                </div>
                <div class="card-body">
                    <?php
                        require_once "function/uuid.php";
                        $uuid = uuid();
                        $year = date('y');
                        $day = date('d');
                        $month = date('m');

                        $action = "";
                        if ($_GET['jenis'] == 'nonppn') {
                            $action = "proses/proses-invoice-nonppn.php";
                        } else if ($_GET['jenis'] == 'ppn') {
                            $action = "proses/proses-invoice-ppn.php";
                        } else if ($_GET['jenis'] == 'bum') {
                            $action = "proses/proses-invoice-bum.php";
                        } else {
                            ?>
                                <script type="text/javascript">
                                    window.location.href = "../404.php";
                                </script>
                            <?php
                        }
                    ?>
                    <form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data" id="form-kirim">
                        <input type="hidden" name="id_status" value="SK_REG<?php echo $year ?><?php echo $month ?><?php echo $uuid ?><?php echo $day ?>">
                        <input type="hidden" name="id_inv" value="<?php echo encrypt($id_inv, $key_global) ?>">
                        <input type="hidden" name="nonce_token" value="<?php echo $_SESSION['nonce_token']; ?>">
                        <div class="mb-3">
                            <label>Jenis Pengiriman</label>
                            <select id="jenisPengiriman" name="jenis_pengiriman" class="form-select" required>
                                <option value="">Pilih...</option>
                                <option value="Driver">Driver</option>
                                <option value="Ekspedisi">Ekspedisi</option>
                                <option value="Diambil Langsung">Diambil Langsung</option>
                            </select>
                        </div>
                        <div class="mb-3 d-none" id="driver">
                            <div class="mb-3">
                                <label>Kernet</label>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kernet" id="inlineRadio1" value="1">
                                    <label class="form-check-label" for="inlineRadio1">Ada</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="kernet" id="inlineRadio2" value="0">
                                    <label class="form-check-label" for="inlineRadio2">Tidak</label>
                                </div>
                            </div>
                            <div class="mb-3 d-none" id="divKernet">
                                <label>Pilih Kernet</label>
                                <select id="pilihKernet" name="kernet_driver" class="form-select selectize-js" required>
                                    <option value="">Pilih...</option>
                                    <?php
                                    include "koneksi.php";
                                    $sql_kernet = mysqli_query($koneksi2, "SELECT us.id_user_role, us.id_user, us.nama_user, us.is_approval, rl.nama_role FROM user AS us JOIN user_role rl ON (us.id_user_role = rl.id_user_role) WHERE rl.nama_role = 'Operator Gudang' AND is_approval = '1'");
                                    while ($data_kernet = mysqli_fetch_array($sql_kernet)) {
                                    ?>
                                        <option value="<?php echo $data_kernet['id_user'] ?>"><?php echo $data_kernet['nama_user'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3 d-none" id="divDriver">
                                <label id="labelDriver">Pilih Driver</label>
                                <select id="pilihDriver" name="pengirim" class="form-select selectize-js" required>
                                    <option value="">Pilih...</option>
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
                        <div class="mb-3 d-none" id="ekspedisi">
                            <div class="mb-3">
                                <label>Pilih Ekspedisi</label>
                                <select name="ekspedisi" id="pilihEkspedisi" class="form-select selectize-js" required>
                                    <option value="">Pilih...</option>
                                    <?php
                                    include "koneksi.php";
                                    $sql_ekspedisi = mysqli_query($connect, "SELECT * FROM ekspedisi");
                                    while ($data_ekspedisi = mysqli_fetch_array($sql_ekspedisi)) {
                                    ?>
                                        <option value="<?php echo $data_ekspedisi['id_ekspedisi'] ?>"><?php echo $data_ekspedisi['nama_ekspedisi'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Dikirim Oleh</label>
                                <input type="text" class="form-control" name="dikirim" id="dikirimOleh">
                            </div>
                            <div class="mb-3">
                                <label>Penanggung Jawab</label>
                                <input type="text" class="form-control" name="pj" id="pj">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label id="labelDate">Tanggal Kirim</label>
                            <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl" id="date" required>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" class="form-control" name="ubah-dikirim">
                            <button type="submit" class="btn btn-primary" id="dikirim"><i class="bi bi-arrow-left-right"></i> Ubah Status</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDikirim"><i class="bi bi-x-circle"> Cancel</i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- kode JS Dikirim -->
    <?php include "page/upload-img.php";  ?>
    <!-- kode JS Dikirim -->
    <script>
        // Inisialisasi Selectize.js untuk elemen pertama
        $('.selectize-js').selectize();
    </script>

    <!-- Pencegahan double klik -->
    <script>
        document.getElementById("dikirim").addEventListener("click", function() {
            this.disabled = true;
            this.form.submit();
        });
    </script>
    <style>
        .preview-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</div>