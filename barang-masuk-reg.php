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
    <div class="pagetitle">
      <h1>Barang Masuk Reguler</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Barang Masuk Reguler</li>
        </ol>
      </nav>
    </div>
    <!-- End Page Title -->
    <section>
      <div class="container-fluid">
        <div class="row">
          <div class="col-sm-4 text-center">
            <a href="barang-masuk-reg-import.php">
              <div class="card p-3 shadow rounded-4">
                <div class="row">
                  <div class="col-sm-4 text-center"><i class="bi bi-clipboard-data" style="color: blue; font-size: 50px;"></i></div>
                  <div class="col-sm-8 pt-4 text-start">Import</div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-sm-4">
            <a href="barang-masuk-lokal.php">
              <div class="card p-3 shadow rounded-4">
                <div class="row">
                  <div class="col-sm-4 text-center"><i class="bi bi-clipboard-data" style="color: blue; font-size: 50px;"></i></div>
                  <div class="col-sm-8 pt-4 text-start">Lokal</div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-sm-4 text-center">
            <a href="barang-masuk-tambahan.php">
              <div class="card p-3 shadowrounded-4">
                <div class="row">
                  <div class="col-sm-4 text-center"><i class="bi bi-clipboard-data" style="color: blue; font-size: 50px;"></i></div>
                  <div class="col-sm-8 pt-4 text-start">Tambahan</div>
                </div>
              </div>
            </a>
          </div>
        </div>
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