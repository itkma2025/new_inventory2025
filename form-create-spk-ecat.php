<?php
    $page  = 'transaksi';
    $page2 = 'spk';
    include "akses.php";
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
      <div class="container-fluid">
        <div class="card shadow p-2">
          <div class="card-header text-center"><h5><strong>FORM SPK E-CAT ORDER</strong></h5></div>
          <form action="input-produk-spk.php" method="POST">
            <?php 
              date_default_timezone_set('Asia/Jakarta');
            ?>
            <div class="card-body">
              <div class="row mt-3">
                <div class="col-sm-6">  
                  <div class="card-body">
                    <div class="mt-3">
                      <label for="no_spk" class="form-label">No. SPK</label>
                      <input type="text" class="form-control" id="no_spk" name="no_spk" required>
                    </div>
                    <div class="mt-3">
                      <label for="tgl_spk" class="form-label">Tanggal SPK</label>
                      <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl_spk" value="<?php echo date('d/m/Y, G:i'); ?>" readonly>
                    </div>
                    <div class="mt-3">
                      <label for="no_po" class="form-label">NO. PO</label>
                      <input type="text" class="form-control" id="no_po" name="no_po">
                    </div>
                    <div class="mt-3">
                      <label for="tgl_pesan" class="form-label">Tanggal Pesanan</label>
                      <input type="date" style="background-color:white;" class="bg-white form-control" name="tgl_pesan" id="date" placeholder="dd/mm/yyyy" required>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="card-body">
                    <div class="mt-3">
                      <label for="instansi" class="form-label">Instansi</label>
                      <input type="text" class="form-control" id="instansi" name="instansi">
                    </div>
                    <div class="mt-3">
                      <label for="order_via" class="form-label">Order Via</label>
                      <select class="selectize-js form-select" name="order_via" required>
                        <option value=""></option>
                        <option value="1">E-Cat</option>
                        <option value="2">PL</option>
                      </select>
                    </div>
                    <div class="mt-3">
                      <label for="sales" class="form-label">Sales</label>
                      <select class="selectize-js form-select" name="sales" required>
                        <option value=""></option>
                        <option value="1">Annas</option>
                        <option value="2">Agung</option>
                        <option value="3">Rita</option>
                        <option value="4">Sugiyanto</option>
                      </select>
                    </div>
                    <div class="mt-3">
                      <label for="note" class="form-label">Note</label>
                      <textarea type="text" class="form-control" id="note" name="note"></textarea>
                    </div>
                  </div>
                </div>
                <div class="text-center mt-3">
                  <button type="submit" class="btn btn-primary btn-md m-2"><i class="bx bx-save"></i> Simpan Data</button>
                  <a href="spk-ecat.php" class="btn btn-secondary m-2"><i class="bi bi-x-circle"></i> Cancel</a>
                </div>
              </div>
            </div>
          </form>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- Large Modal -->
  <div class="modal fade" id="modal-cs" tabindex="-1">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Data Pelanggan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped table-bordered" id="select-cs">
              <thead>
                  <tr class="bg-primary bg-gradient text-white">
                    <td class="col-1">No</td>
                    <td class="col-4">Nama Customer</td>
                    <td class="col-5">Alamat</td>
                    <td class="col-2">Telepon</td>
                  </tr>
              </thead>
              <tbody>
                  <tr id="data-cs" pelanggan="Ibu Melly" alamat="Jakarta" data-bs-dismiss="modal">
                    <td class="text-center">1</td>
                    <td>Ibu Melly</td>
                    <td>Jakarta</td>
                    <td>0812xxxx</td>
                  </tr>
              </tbody>
            </table>          
          </div>
        </div>
      </div>
    </div>
  </div><!-- End Large Modal-->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>
</html>