<?php
    $page = 'list-tagihan';
    $page2 = 'tagihan-refund';
    require_once "../akses.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Inventory KMA</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <?php include "page/head.php"; ?>
</head>

<body>
  <!-- nav header -->
  <?php include "page/nav-header.php" ?>
  <!-- end nav header -->

  <!-- sidebar  -->
  <?php include "page/sidebar.php"; ?>
  <!-- end sidebar -->

  <main id="main" class="main">
    <section>
      <div class="card">
        <div class="card-header text-center">
          <h5>Form Komplain Pesanan</h5>
        </div>
        <div class="card-body">
            <?php  
                include "../function/uuid.php"; 
                require_once "../function/function-enkripsi.php";
                $key = "K@rsa2024?";           
                $uuid = uuid();
                $year = date('y');
                $year_komplain = date('Y');
                $day = date('d');
                $month = date('m');
                $id_komplain = "KMPLN" . $year . "". $month . "" . $uuid . "" . $day;   
                $id_kondisi = "KNDSI" . $year . "". $month . "" . $uuid . "" . $day;
            ?>
            <form action="proses/refund-komplain.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_inv" value="<?php echo encrypt($_POST['id_inv'], $key); ?>" readonly>
                <input type="hidden" name="id_komplain" value="<?php echo encrypt($id_komplain, $key); ?>" readonly>
                <input type="hidden" name="id_kondisi" value="<?php echo encrypt($id_kondisi, $key); ?>" readonly>
                <div id="tidak_sesuai_form">
                    <div class="mb-3">
                        <label><b>Tanggal Komplain</b></label>
                        <input type="date" class="form-control" name="tgl" id="date" maxlength="10" autocomplete="off" required>
                    </div>
                    <div class="mb-3" style="display:block;">
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
                    <a href="list-refund-dana.php?date_range=year" class="btn btn-secondary me-2">Tutup</a>
                    <button type="submit" class="btn btn-primary" name="komplain">Proses Komplain</button>
                </div>
            </form>
        </div>
      </div>
    </section>
  </main><!-- End #main -->
  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
  <?php include "../page/kondisi-diterima.php"; ?>
</body>
</html>