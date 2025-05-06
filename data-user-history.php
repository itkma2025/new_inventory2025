<?php
$page = 'history-user';
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
  <link rel="stylesheet" href="assets/css/wrap-text.css">
  <style>
    th{
        padding-top: 15px !important;
        padding-bottom: 15px !important;
        padding-left: 25px !important;
        padding-right: 35px !important;
        text-align: center !important;
        white-space: nowrap !important;
        margin: 10px !important;
        background-color: navy !important;
    }
  </style>
  <?php include "page/head.php"; ?>
</head>

<body>
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
    <!-- end sidebar -->

    <!-- SWEET ALERT -->
    <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
    <!-- END SWEET ALERT -->

    <main id="main" class="main">
    <!-- Loading -->
    <div class="loader loader">
        <div class="loading">
        <img src="img/loading.gif" width="200px" height="auto">
        </div>
    </div>
    <!-- ENd Loading -->
    <div class="pagetitle">
        <h1>Data User History</h1>
        <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
            <li class="breadcrumb-item active">Data User History</li>
        </ol>
        </nav>
    </div><!-- End Page Title -->
    <section class="section dashboard">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table1">
                            <thead>
                                <tr class="text-white">
                                    <th>No</th>
                                    <th>Nama User</th>
                                    <th>Role</th>
                                    <th>Waktu Login</th>
                                    <th>Waktu Logout</th>
                                    <th>IP Login</th>
                                    <th>OS</th>
                                    <th>Jenis Perangkat</th>
                                    <th>Lokasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  
                                    include "koneksi.php";
                                    $no = 1;
                                    // $id_user = base64_decode($_SESSION['tiket_id']);
                                    $sql_history = $connect->query("SELECT 
                                                                        uh.login_time, uh.logout_time, uh.ip_login, uh.os, uh.jenis_perangkat, uh.lokasi, ur.role, u.nama_user
                                                                    FROM user_history AS uh 
                                                                    LEFT JOIN user u ON (uh.id_user = u.id_user)
                                                                    LEFT JOIN user_role ur ON (u.id_user_role = ur.id_user_role)
                                                                    -- WHERE uh.id_user = '$id_user'
                                                                    ");
                                    while($data = mysqli_fetch_array($sql_history)){
                                ?>
                                <tr>
                                    <td class="text-nowrap text-center"><?php echo $no; ?></td>
                                    <td class="text-nowrap"><?php echo $data['nama_user']; ?></td>
                                    <td class="text-nowrap"><?php echo $data['role']; ?></td>
                                    <td class="text-nowrap text-center"><?php echo $data['login_time']; ?></td>
                                    <td class="text-nowrap text-center"><?php echo $data['logout_time']; ?></td>
                                    <td class="text-nowrap text-center"><?php echo $data['ip_login']; ?></td>
                                    <td class="wrap-text"><?php echo $data['os']; ?></td>
                                    <td class="text-nowrap text-center"><?php echo $data['jenis_perangkat']; ?></td>
                                    <td class="text-nowrap text-center"><?php echo $data['lokasi']; ?></td>
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

    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <?php include "page/script.php" ?>
</body>

</html>
<script>
    $(document).ready(function () {
        $("#tableHistory").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
        });
    });
</script>