<?php
    $action = "";
    $action_komplain = "";
    if ($_GET['jenis'] == 'nonppn') {
        $action = "proses/proses-invoice-nonppn.php";
        $action_komplain = "proses/proses-komplain-nonppn.php";
    } else if ($_GET['jenis'] == 'ppn') {
        $action = "proses/proses-invoice-ppn.php";
        $action_komplain = "proses/proses-komplain-ppn.php";
    } else if ($_GET['jenis'] == 'bum') {
        $action = "proses/proses-invoice-bum.php";
        $action_komplain = "proses/proses-komplain-bum.php";
    } else {
        ?>
            <script type="text/javascript">
                window.location.href = "../404.php";
            </script>
        <?php
    }
?>
<!-- Modal Trx Selesai -->
<div class="modal fade" id="trxSelesai" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                <h5>Apakah anda yakin ingin menyelesaikan pesanan ini?</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x"></i> Tutup</button>
                <a href="proses/proses-trx-selesai.php?id_inv=<?php echo encrypt($id_inv, $key_global) ?>&&total_inv=<?php echo $total_inv ?>&&jenis_inv=<?php echo $_GET['jenis'] ?>&&nonce_token=<?php echo $_SESSION['nonce_token']; ?>" class="btn btn-primary mb-2">
                    <i class="bi bi-check2-circle"></i> Transaksi Selesai
                </a>
                <button class="btn btn-warning mb-2" data-bs-toggle="modal" data-bs-target="#modalKomplain"><i class="bi bi-info-circle"></i> Komplain</button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Trx Selesai -->

<!-- Modal Komplain -->
<div class="modal fade" id="modalKomplain" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Komplain Invoice</h1>
            </div>
            <div class="modal-body">
                <form action="<?php echo $action_komplain ?>" method="POST">
                    <input type="hidden" name="id_inv" value="<?php echo encrypt($id_inv, $key_global); ?>">
                    <div id="tidak_sesuai_form">
                        <div class="mb-3">
                            <label><b>Tanggal Komplain</b></label>
                            <input type="text" class="form-control" name="tgl" id="date" required>
                        </div>
                        <div class="mb-3">
                            <label><b>Pilih Kategori Komplain</b></label>
                            <select name="kat_komplain" id="kat_komplain" class="form-select" required>
                                <option value="">Pilih Kategori...</option> 
                                <option value="0">Invoice</option>
                                <option value="1">Barang</option>
                            </select>
                        </div>
                        <label><b>Pilih Kondisi Pesanan</b></label>
                        <div class="mb-3 border p-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan0" value="0" required>
                                <label class="form-check-label" for="kondisi_pesanan0">
                                    Faktur sesuai, tetapi barang yang diterima adalah jenis yang salah.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan1" value="1" required>
                                <label class="form-check-label" for="kondisi_pesanan1">
                                    Faktur sesuai, namun jumlah barang yang diterima kurang dari yang diharapkan.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan2" value="2" required>
                                <label class="form-check-label" for="kondisi_pesanan2">
                                    Faktur sesuai, tetapi pelanggan meminta revisi harga.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan3" value="3" required>
                                <label class="form-check-label" for="kondisi_pesanan3">
                                    Faktur dan barang sesuai, tetapi barang yang diterima rusak, cacat,atau memiliki masalah kualitas sehingga tidak berfungsi sesuai yang diharapkan.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan4" value="4" required>
                                <label class="form-check-label" for="kondisi_pesanan4">
                                    Faktur tidak sesuai, tetapi barang dan jumlahnya cocok dengan pesanan.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kondisi_pesanan" id="kondisi_pesanan5" value="5" required>
                                <label class="form-check-label" for="kondisi_pesanan5">
                                    Pelanggan meminta pengembalian barang / uang karena ketidakcocokan pesanan.
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label><b>Retur Barang</b></label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="retur" id="retur_ya" value="1" required>
                                        <label class="form-check-label" for="inlineRadio1">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="retur" id="retur_tidak" value="0" required>
                                        <label class="form-check-label" for="inlineRadio2">Tidak</label>
                                    </div>
                                </div>
                                <div class="col-md-6" id="refundDana" style="display: none;">
                                    <label><b>Refund Dana</b></label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="refund" id="refund_ya" value="1" required>
                                        <label class="form-check-label" for="inlineRadio1">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="refund" id="refund_tidak" value="0" required>
                                        <label class="form-check-label" for="inlineRadio2">Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label><b>Catatan Khusus (*)</b></label>
                            <textarea class="form-control" name="catatan" id="catatan" cols="30" rows="5"></textarea>
                            <p>Jumlah Karakter: <span id="hitungKarakter">0</span></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelKomplain" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary" name="komplain">Proses Komplain</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- kode JS Dikirim -->
<?php include "page/kondisi-diterima.php"; ?>