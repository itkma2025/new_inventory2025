<?php
    $page = 'br-masuk';
    $page2 = 'br-masuk-reg';
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_blue.css">

    <style>
        #table2{
            cursor: pointer;
        }
        #table3{
            cursor: pointer;
        }

        input[type="text"]:read-only {
        background: #e9ecef;
        }

        textarea[type="text"]:read-only {
        background: #e9ecef;
        }
    </style>
</head>

<body>
  <!-- nav header -->
  <?php include "page/nav-header.php" ?>
  <!-- end nav header -->
  
  <!-- sidebar  -->
  <?php include "page/sidebar.php"; ?>
  <!-- end sidebar -->
  

  <main id="main" class="main">
     <!-- Loading -->
     <div class="loader loader">
      <div class="loading">
        <img src="img/loading.gif" width="200px" height="auto">
      </div>
    </div>
    <!-- ENd Loading -->
    <section>
        <div class="container-fluid">
        <form method="post" action="form-multiple-br-import.php" class="form">
            <?php
                if(isset($_POST['submit'])) {
                    $id_inv_import = $_POST['id_inv_import'];
                    $id_produk = $_POST['id_produk'];
                    $nama_produk = $_POST['nama_produk'];
                    $start_karton = $_POST['start_karton'];
                    $end_karton = $_POST['end_karton'];
                    if($start_karton > 0 && $end_karton > $start_karton) {
                        $num_of_forms = $end_karton - $start_karton + 1;
                        $current_carton = $start_karton;
                        for($i = $start_karton; $i <= $end_karton; $i++) {
            ?>
            <div class="row">
                <div class="col mb-3">
                    <label>ID Inv Import</label>
                    <input type="text" class="form-control" name="nama_produk" value="<?php echo $id_inv_import; ?>" readonly>
                </div>
                <div class="col mb-3">
                    <label>ID Barang</label>
                    <input type="text" class="form-control" name="nama_produk" value="<?php echo $id_produk; ?>" readonly>
                </div>
                <div class="col mb-3">
                    <label>No. Karton</label>
                    <input type="text" class="form-control" name="nama_produk" value="<?php echo $current_carton; ?>" readonly>
                    <?php $current_carton++; ?>
                </div>
                <div class="col mb-3">
                    <label>Nama Produk</label>
                    <input type="text" class="form-control" name="nama_produk" value="<?php echo $nama_produk; ?>" readonly>
                </div>
                <div class="col mb-3">
                    <label>Qty</label>
                    <input type="text" class="form-control" name="form-<?php echo $i; ?>" id="form-<?php echo $i; ?>">
                </div>
            </div>
            <?php } } } ?>   
            <button type="submit" name="submit" class="btn btn-primary">Simpan Data</button>
        </form> 
        </div>
    </section>
  </main><!-- End #main -->
  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>
</html>