<?php
    $page = 'perubahan-merk';
    $page2  = 'ganti-merk';
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
  <style>
    .table-responsive {
        overflow-x: auto;
    }
    .table {
        width: 100% !important;
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
    <!-- <div class="loader loader">
        <div class="loading">
            <img src="img/loading.gif" width="200px" height="auto">
        </div>
    </div> -->
    <!-- ENd Loading -->
    <div class="pagetitle">
      <h1>Ganti Merk Produk Reguler</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Ganti Merk</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section>
      <!-- SWEET ALERT -->
      <div class="info-data" data-infodata="<?php if(isset($_SESSION['info'])){ echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
      <!-- END SWEET ALERT -->
      <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <!-- Bordered Tabs -->
                <ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
                    <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#bordered-home" type="button" role="tab" aria-controls="home" aria-selected="true">Merk Awal</button>
                    </li>
                    <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#bordered-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Merk Akhir</button>
                    </li>
                </ul>
                <div class="tab-content pt-2" id="borderedTabContent">
                    <div class="tab-pane fade show active" id="bordered-home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="card-body p-3">
                            <?php  
                                if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Gudang") { 
                                    ?>
                                        <a href="input-ganti-merk-reg.php" class="btn btn-primary btn-md"><i class="bi bi-plus-circle"></i> Tambah data</a>
                                    <?php
                                }
                            ?>
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered table-striped" id="tableExport">
                                    <thead>
                                        <tr class="text-white" style="background-color: #051683;">
                                            <th class="text-center p-3" style="width: 50px">No</th>
                                            <th class="text-center p-3" style="width: 150px">Kode Produk</th>
                                            <th class="text-center p-3" style="width: 350px">Nama Produk</th>
                                            <th class="text-center p-3" style="width: 100px">Merk</th>
                                            <th class="text-center p-3" style="width: 80px">Qty</th>
                                            <th class="text-center p-3" style="width: 150px">Dibuat Oleh</th>
                                            <?php  
                                                if ($role == "Super Admin" || $role == "Manager Gudang" ) { 
                                                    ?>
                                                        <th class="text-center p-3" style="width: 100px">Aksi</th>
                                                    <?php
                                                }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php  
                                            include "koneksi.php";
                                            $no = 1;
                                            $sql = "SELECT 
                                                        gmro.id_ganti_merk_out, 
                                                        gmro.created_by,
                                                        gmro.id_produk_reg,
                                                        gmro.qty,
                                                        STR_TO_DATE(gmro.created_date, '%d/%m/%Y, %H:%i:%s') AS created_date,
                                                        COALESCE(tpr.nama_produk, tpe.nama_produk) AS nama_produk,
                                                        COALESCE(tpr.kode_produk, tpe.kode_produk) AS kode_produk,
                                                        COALESCE(mr.nama_merk, mr_ecat.nama_merk) AS nama_merk,
                                                        uc.nama_user AS user_created 
                                                    FROM ganti_merk_reg_out AS gmro
                                                     LEFT JOIN $database2.user AS uc ON (gmro.created_by = uc.id_user)
                                                    LEFT JOIN tb_produk_reguler tpr ON (gmro.id_produk_reg = tpr.id_produk_reg)
                                                    LEFT JOIN tb_produk_ecat tpe ON (gmro.id_produk_reg = tpe.id_produk_ecat)
                                                    LEFT JOIN tb_merk mr ON (tpr.id_merk = mr.id_merk)
                                                    LEFT JOIN tb_merk mr_ecat ON (tpe.id_merk = mr.id_merk)
                                                    ORDER BY created_date DESC ";
                                            $query = mysqli_query($connect, $sql) OR DIE (mysqli_error($connect, $sql));
                                            while($data = mysqli_fetch_array($query)){
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td class="text-center"><?php echo $data['kode_produk']; ?></td>
                                            <td><?php echo $data['nama_produk']; ?></td>
                                            <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                                            <td class="text-end"><?php echo $data['qty']; ?></td>
                                            <td class="text-center">
                                                <?php echo $data['user_created']; ?><br>
                                                (<?php echo date('d/m/Y, H:i:s', strtotime($data['created_date'])); ?>)
                                            </td>
                                            <?php  
                                                if ($role == "Super Admin" || $role == "Manager Gudang" ) { 
                                                    ?>
                                                        <td class="text-center">
                                                            <a href="proses/proses-ganti-merk.php?hapus_id=<?php echo encrypt($data['id_ganti_merk_out'], $key_global); ?>" class="btn btn-sm btn-danger delete-data"><i class="bi bi-trash"></i></a>
                                                        </td>
                                                    <?php
                                                }
                                            ?>
                                        </tr>
                                        <?php $no++; ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="bordered-profile" role="tabpanel" aria-labelledby="profile-tab">
                        <div class="card-body p-3">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered table-striped" id="tableExportNoAction">
                                    <thead>
                                        <tr class="text-white" style="background-color: #051683;">
                                            <th class="text-center p-3" style="width: 50px">No</th>
                                            <th class="text-center p-3" style="width: 150px">Kode Produk</th>
                                            <th class="text-center p-3" style="width: 350px">Nama Produk</th>
                                            <th class="text-center p-3" style="width: 100px">Merk</th>
                                            <th class="text-center p-3" style="width: 80px">Qty</th>
                                            <th class="text-center p-3" style="width: 150px">Nama Petugas</th>
                                            <th class="text-center p-3" style="width: 150px">Dibuat Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php  
                                            $no = 1;
                                            $sql = "SELECT 
                                                        gmri.id_ganti_merk_in, 
                                                        gmri.created_by,
                                                        gmri.id_produk_reg,
                                                        gmri.qty,
                                                        gmri.nama_petugas,
                                                        STR_TO_DATE(gmri.created_date, '%d/%m/%Y, %H:%i:%s') AS created_date,
                                                        COALESCE(tpr.nama_produk, tpe.nama_produk) AS nama_produk,
                                                        COALESCE(tpr.kode_produk, tpe.kode_produk) AS kode_produk,
                                                        COALESCE(mr.nama_merk, mr_ecat.nama_merk) AS nama_merk,
                                                        uc.nama_user AS user_created 
                                                    FROM ganti_merk_reg_in AS gmri
                                                     LEFT JOIN $database2.user AS uc ON (gmri.created_by = uc.id_user)
                                                    LEFT JOIN tb_produk_reguler tpr ON (gmri.id_produk_reg = tpr.id_produk_reg)
                                                    LEFT JOIN tb_produk_ecat tpe ON (gmri.id_produk_reg = tpe.id_produk_ecat)
                                                    LEFT JOIN tb_merk mr ON (tpr.id_merk = mr.id_merk)
                                                    LEFT JOIN tb_merk mr_ecat ON (tpe.id_merk = mr.id_merk)
                                                    ORDER BY created_date DESC ";
                                            $query = mysqli_query($connect, $sql) OR DIE (mysqli_error($connect, $sql));
                                            while($data = mysqli_fetch_array($query)){
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td class="text-center"><?php echo $data['kode_produk']; ?></td>
                                            <td><?php echo $data['nama_produk']; ?></td>
                                            <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                                            <td class="text-end"><?php echo $data['qty']; ?></td>
                                            <td class="text-center"><?php echo $data['nama_petugas']; ?></td>
                                            <td class="text-center">
                                                <?php echo $data['user_created']; ?><br>
                                                <?php echo date('d/m/Y, H:i:s', strtotime($data['created_date'])); ?>
                                            </td>
                                        </tr> 
                                        <?php $no++; ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><!-- End Bordered Tabs -->
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
    $(document).ready(function() {
        var table = $('#tableAwal').DataTable({
            "lengthChange": false,
            "ordering": false,
            "autoWidth": false
        });
    });
    $(document).ready(function() {
        var table = $('#tableAkhir').DataTable({
            "lengthChange": false,
            "ordering": false,
            "autoWidth": false
        });
    });
</script>