<?php
    $page  = 'transaksi';
    $page2 = 'sph';
    include "akses.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPH</title>
    <?php include "page/head.php"; ?>

    <style>
        .wrap-text {
            max-width: 300px; /* Contoh lebar maksimum */
            overflow: hidden; /* Sembunyikan teks yang melebihi max-width */
            white-space: pre-line; /* Tetapkan spasi putih dan pecah baris sesuai dengan teks */
            word-wrap: break-word; /* Pecah kata jika melebihi max-width */
        }
        @media (max-width: 767px) { /* Media query untuk tampilan mobile */
            .wrap-text {
                min-width: 400px; /* Contoh lebar maksimum */
                overflow: hidden; /* Sembunyikan teks yang melebihi max-width */
                white-space: pre-line; /* Tetapkan spasi putih dan pecah baris sesuai dengan teks */
                word-wrap: break-word; /* Pecah kata jika melebihi max-width */
            }
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
        <h1>Dashboard</h1>
        </div><!-- End Page Title -->

        <section class="pagetitle">
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <nav aria-label="breadcrumb">
                <ol class=" breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">SPH</li>
                </ol>
            </nav>
            <div class="card p-3">
                <div class="mb-3">
                    <?php  
                        if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan" ) { 
                            ?>
                               <a href="form-create-sph.php" class="btn btn-secondary"><i class="bi bi-file-earmark-text"></i> Buat SPH</a>
                            <?php
                        }
                    ?>
                </div>
                <!-- Pills Tabs -->
              <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">SPH Aktif</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">SPH Cancel</button>
                </li>
              </ul>
              <div class="tab-content pt-2" id="myTabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table2">
                            <thead>
                                <tr class="text-white" style="background-color: navy">
                                    <td class="text-center text-nowrap p-3">No</td>
                                    <td class="text-center text-nowrap p-3">No. SPH</td>
                                    <td class="text-center text-nowrap p-3">Tgl. SPH</td>
                                    <td class="text-center text-nowrap p-3">Customer</td>
                                    <td class="text-center text-nowrap p-3">Alamat</td>
                                    <td class="text-center text-nowrap p-3">Hal</td>
                                    <td class="text-center text-nowrap p-3">Aksi</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  
                                    include "koneksi.php";
                                    $no = 1;
                                    $sql_sph = "SELECT 
                                                    sph.id_sph, sph.no_sph, sph.tanggal, sph.id_cs, sph.perihal,
                                                    cs.nama_cs, cs.alamat
                                                FROM sph as sph
                                                LEFT JOIN tb_customer_sph cs ON (sph.id_cs = cs.id_cs)
                                                WHERE status_cancel = 0 ORDER BY sph.created_date ASC";
                                    $query_sph = mysqli_query($connect, $sql_sph);
                                    while($data = mysqli_fetch_array($query_sph)){
                                ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['no_sph'] ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data['tanggal'] ?></td>
                                    <td class="text-start text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                    <td class="text-start wrap-text"><?php echo $data['alamat'] ?></td>
                                    <td class="text-start wrap-text"><?php echo $data['perihal'] ?></td>
                                    <td class="text-center text-nowrap">
                                        <a href="tampil-data-sph.php?id=<?php echo encrypt($data['id_sph'], $key_global) ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i> Lihat Data
                                        </a>
                                        <?php  
                                        if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan" ) { 
                                                ?>
                                                 <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelSph" data-id= "<?php echo encrypt($data['id_sph'], $key_global) ?>" data-no= "<?php echo $data['no_sph'] ?>" data-cs= "<?php echo $data['nama_cs'] ?>" onclick="setSphId(this)">
                                                    <i class="bi bi-x-circle"> Cancel</i>
                                                </button>
                                                <?php
                                            }
                                        ?>
                                    </td>
                                </tr>
                                <?php $no++ ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="table3">
                                <thead>
                                    <tr class="text-white" style="background-color: navy">
                                        <td class="text-center text-nowrap p-3">No</td>
                                        <td class="text-center text-nowrap p-3">No. SPH</td>
                                        <td class="text-center text-nowrap p-3">Tgl. SPH</td>
                                        <td class="text-center text-nowrap p-3">Customer</td>
                                        <td class="text-center text-nowrap p-3">Alamat</td>
                                        <td class="text-center text-nowrap p-3">Hal</td>
                                        <td class="text-center text-nowrap p-3">Aksi</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php  
                                        include "koneksi.php";
                                        $no = 1;
                                        $sql_sph = "SELECT 
                                                            sph.id_sph, sph.no_sph, sph.tanggal, sph.id_cs, sph.perihal,
                                                            cs.nama_cs, cs.alamat
                                                        FROM sph as sph
                                                        LEFT JOIN tb_customer_sph cs ON (sph.id_cs = cs.id_cs)
                                                        WHERE status_cancel = 1 ORDER BY sph.created_date ASC";
                                        $query_sph = mysqli_query($connect, $sql_sph);
                                        while($data = mysqli_fetch_array($query_sph)){
                                    ?>
                                    <tr>
                                        <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                        <td class="text-center text-nowrap"><?php echo $data['no_sph'] ?></td>
                                        <td class="text-center text-nowrap"><?php echo $data['tanggal'] ?></td>
                                        <td class="text-start text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                        <td class="text-start wrap-text"><?php echo $data['alamat'] ?></td>
                                        <td class="text-start wrap-text"><?php echo $data['perihal'] ?></td>
                                        <td class="text-center text-nowrap">
                                            <a href="tampil-data-sph.php?id=<?php echo encrypt($data['id_sph'], $key_global) ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye"></i> Lihat Data
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $no++ ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
              </div><!-- End Pills Tabs -->
            </div> 
        </section>
    </main><!-- End #main -->
    <!-- Modal Cancel -->
    <div class="modal fade" id="cancelSph" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Konfirmasi Cancel</h1>
                </div>
                <div class="modal-body">
                    Apakah anda yakin ingin cancel No. SPH : <b><span id="noSph"></span></b><br><b><span id="namaCs"></span></b> ?
                    <form action="proses/proses-sph.php" method="POST">
                        <input type="hidden" name="id_sph" id="idSph">
                        <textarea class="form-control mt-3" name="alasan" placeholder="Silahkan masukan alasan cancel" required></textarea>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="cancel" class="btn btn-danger">
                                <i class="bi bi-eye"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>
</html>
<script>
    function setSphId(button) {
        var idSphValue = button.getAttribute('data-id');
        var namaCsValue = button.getAttribute('data-cs');
        var noSphValue = button.getAttribute('data-no');

        document.getElementById('idSph').value = idSphValue;
        document.getElementById('namaCs').innerText = namaCsValue;
        document.getElementById('noSph').innerText = noSphValue;
    }
</script>