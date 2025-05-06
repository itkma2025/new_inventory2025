<?php
    $page = 'list-tagihan';
    $page2 = 'tagihan-pembelian';
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
        <h1>List Tagihan Pembelian</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">List Tagihan Pembelian</li>
            </ol>
        </nav>
      </div><!-- End Page Title -->
      <section>
        <!-- SWEET ALERT -->
        <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
        <!-- END SWEET ALERT -->
        <div class="card">
          <div class="card-body p-3">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a href="list-tagihan-pembelian.php?date_range=year" class="nav-link">Tagihan Belum Lunas</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a href="#" class="nav-link active">Tagihan Lunas</a>
                </li>
            </ul>
            <?php include "filter/list-tagihan-pembelian.php"?>
            <div class="table-responsive">
                <table class="table table-responsive table-striped" id="table2">
                  <thead>
                    <tr class="text-white" style="background-color: navy;">
                        <td class="p-3 text-center text-nowrap">No</td>
                        <td class="p-3 text-center text-nowrap">No. Pembayaran</td>
                        <td class="p-3 text-center text-nowrap">Tgl. Pembayaran</td>
                        <td class="p-3 text-center text-nowrap">Nama Supplier</td>
                        <td class="p-3 text-center text-nowrap">Total Tagihan</td>
                        <td class="p-3 text-center text-nowrap">Total Bayar</td>
                        <td class="p-3 text-center text-nowrap">Sisa Tagihan</td>
                        <td class="p-3 text-center text-nowrap">Aksi</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php  
                      include "query/list-tagihan-pembelian-lunas.php";
                      $total_bayar = 0;
                      while($data_pembayaran = mysqli_fetch_array($sql_pembayaran)){
                        $total_bayar += $data_pembayaran['total_bayar'];
                        $sisa_tagihan = $data_pembayaran['total_tagihan'] - $total_bayar;
                        $id_pembayaran = $data_pembayaran['id_pembayaran'];
                        $id_pembayaran_encrypt = encrypt($id_pembayaran, $key);
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $no; ?></td>
                        <td class="text-center text-nowrap"><?php echo $data_pembayaran['no_pembayaran'] ?></td>
                        <td class="text-center text-nowrap"><?php echo $data_pembayaran['tgl_pembayaran'] ?></td>
                        <td><?php echo $data_pembayaran['nama_sp'] ?></td>
                        <td class="text-end"><?php echo number_format($data_pembayaran['total_tagihan'],0,'.','.') ?></td>
                        <td class="text-end"><?php echo number_format($data_pembayaran['total_bayar'],0,'.','.') ?></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-secondary btn-sm mb-2">
                                <i class="bi bi-check-circle"></i> Lunas
                            </button>
                        </td>
                        <td class="text-center text-nowrap">
                            <a href="detail-payment.php?id=<?php echo $id_pembayaran_encrypt; ?>" class="btn btn-primary btn-sm" title="Detail Tagihan"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                    <?php $no++ ?>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
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