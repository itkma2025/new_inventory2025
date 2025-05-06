<?php
include '../akses.php';
$page = 'bank';
$page2 = 'bank-master';
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
        <!-- End Loading -->
        <div class="pagetitle">
            <h1>Data Bank</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Data Bank</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section>
             <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="card p-3">
                <div class="row" style="margin-left: 2px;">
                    <div class="col-md-2 mb-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBank">
                            <i class="bi bi-plus-circle"></i> Tambah Data Bank
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table2">
                        <thead>
                            <tr class="text-white" style="background-color: navy;">
                                <th class="text-center text-nowrap p-3" style="width: 100px;">No</th>
                                <th class="text-center text-nowrap p-3" style="width: 700px;">Nama Bank</th>
                                <th class="text-center text-nowrap p-3" style="width: 200px;">Logo Bank</th>
                                <th class="text-center text-nowrap p-3" style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                                $no = 1;
                                $sql_bank = "SELECT id_bank, nama_bank, logo FROM bank ORDER BY nama_bank ASC";
                                $query_bank = mysqli_query($connect, $sql_bank);
                                while($data_bank = mysqli_fetch_array($query_bank)){
                                    $id_bank = $data_bank['id_bank'];
                                    $nama_bank = $data_bank['nama_bank'];
                                    $logo = $data_bank['logo'];
                                    $tampil_logo = "logo-bank/$logo";
                            ?>
                            <tr>
                                <td class="text-nowrap text-center"><?php echo $no; ?></td>
                                <td class="text-nowrap"><?php echo $data_bank['nama_bank'] ?></td>
                                <td class="text-nowrap p-3"> 
                                    <img src="<?php echo $tampil_logo ?>" width="200px" height="50px" class="img-fluid" alt="logo-bank">
                                </td>
                                <td class="text-nowrap text-center">
                                    <a href="proses/bank.php?id=<?php echo base64_encode($id_bank)?>&&logo=<?php echo base64_encode($logo)?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                            <?php $no++ ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main><!-- End #main -->
    <!-- Modal Bank-->
    <div class="modal fade" id="addBank" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Tambah Data Bank</h1>
                </div>
                <div class="modal-body">
                    <form action="proses/bank.php" method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            <div class="mb-3">
                                <label>Nama Bank</label>
                                <input type="text" name="nama_bank" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Pilih Gambar</label>
                                <input type="file" name="fileku" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="simpan" class="btn btn-primary">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Bank -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>
</html>