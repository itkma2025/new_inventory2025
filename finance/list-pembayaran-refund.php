<?php
$page = 'list-tagihan';
$page2 = 'tagihan-refund';
require_once "../akses.php";

?>
<!DOCTYPE html>
<html lang="en">
<?php include 'page/head.php'; ?>
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Inventory KMA</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <link rel="stylesheet" type="text/css" media="all" href="daterangepicker.css" />

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.1/moment.min.js"></script>
  <script type="text/javascript" src="daterangepicker.js"></script>
  <style>
    /* Custom styling for the date inputs */
    .form-control[type="date"] {
      appearance: none;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    /* Optional: Adjust the date input height and font-size */
    .form-control[type="date"] {
      height: 38px;
      font-size: 14px;
    }

    /* Adjust the position of the dropdown */
    .dropdown {
      display: inline-block;
      position: relative;
    }

    /* Adjust the style of the dropdown menu items */
    .dropdown-menu {
      min-width: 350px;
      padding: 20px;
    }

    .dropdown-item{
      text-align: center;
      border: 1px solid #ced4da;
      margin-bottom: 10px;
    }

    .separator {
      display: inline-block;
      width: 40px; /* Atur panjang pemisah sesuai keinginan */
      font-size: 1.2rem;
      font-weight: bold;
      text-align: center;
      color: #333; /* Ubah warna sesuai keinginan */
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

  <div id="content">
    <main id="main" class="main">
      <!-- Loading -->
      <div class="loader loader">
        <div class="loading">
          <img src="img/loading.gif" width="200px" height="auto">
        </div>
      </div>
      <!-- End Loading -->
      <div class="pagetitle">
        <h1>List Pembayaran Refund</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">List Pembayaran Refund</li>
          </ol>
        </nav>
      </div><!-- End Page Title -->
      <section>
        <!-- SWEET ALERT -->
        <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
        <!-- END SWEET ALERT -->
        
      </section>
    </main><!-- End #main -->
  </div>
  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>
</html>