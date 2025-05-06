<?php
    $page  = 'transaksi';
    $page2 = 'spk';
    include "akses.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Create SPH</title>
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
        <div class="loader loader">
            <div class="loading">
                <img src="img/loading.gif" width="200px" height="auto">
            </div>
        </div>
        <!-- ENd Loading -->

        <section class="pagetitle">
            <form action="proses/proses-sph.php" method="POST" enctype="multipart/form-data">
                <div class="card p-3">
                    <div class="card-header text-center">
                        <h5>Form Surat Penawaran Harga</h5>
                    </div>
                    <div class="row">
                        <?php  
                            $month = date('m');

                            include "koneksi.php";
                            $thn  = date('Y');
                            $sql  = mysqli_query($connect, "SELECT max(no_sph) as maxID, STR_TO_DATE(tanggal, '%d/%m/%Y') AS tgl FROM sph WHERE YEAR(STR_TO_DATE(tanggal, '%d/%m/%Y')) = '$thn'");
                            $data = mysqli_fetch_array($sql);
                  
                            $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
                            $kode = $data['maxID'];
                            $ket1 = "/SPK/";
                            $bln = $array_bln[date('n')];
                            $ket2 = "/";
                            $ket3 = date("Y");
                            $urutkan = (int)substr($kode, 0, 3);
                            $urutkan++;
                            $no_sph = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;
                        ?>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>No. SPH</label>
                                <input type="text" class="form-control" name="no_sph" value="<?php echo $no_sph ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Tanggal</label>
                                <input type="text" class="form-control" name="tgl" id="date" required>
                            </div>
                            <div class="mb-3">
                                <label>U.P</label>
                                <input type="text" class="form-control" name="up" required>
                            </div>
                            <div class="mb-3">
                                <label>Customer SPH</label>
                                <input type="hidden" class="form-control" id="id" name="id_cs">
                                <input type="text" class="form-control" name="cs" id="cs" data-bs-toggle="modal" data-bs-target="#modalCs" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Alamat</label>
                                <textarea name="alamat" id="alamat" class="form-control" cols="30" style="max-height: 100px; min-height: 100px;" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>TTD Oleh</label>
                                <input type="text" class="form-control" name="ttd" required>
                            </div>
                            <div class="mb-3">
                                <label>Jabatan</label>
                                <input type="text" class="form-control" name="jabatan" required>
                            </div>
                            <div class="mb-3">
                                <label>Perihal</label>
                                <input type="text" class="form-control" name="perihal" required>
                            </div>
                            <div class="mb-3">
                                <label>Notes</label>
                                <textarea class="form-control" name="note" cols="30" style="max-height: 100px; min-height: 100px;"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 mt-3 text-center">
                        <button type="submit" name="simpan-sph" class="btn btn-primary">Simpan</button>
                        <a href="sph.php" class="btn btn-secondary">Batal</a>
                    </div>
                </div>
            </form>
        </section>
    </main><!-- End #main -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>
</html>

<!-- Modal -->
<div class="modal fade" id="modalCs" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
                <div class="card p-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered" id="table2">
                        <thead>
                            <tr class="text-white" style="background-color: navy;">
                            <td class="col-4 text-nowrap">Nama Customer</td>
                            <td class="col-6 text-nowrap">Alamat Customer</td>
                            <td class="col-2 text-nowrap">Telepon</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "koneksi.php";
                            $sql_cs = "SELECT * FROM tb_customer_sph";
                            $query_cs = mysqli_query($connect, $sql_cs);
                            while ($data_cs = mysqli_fetch_array($query_cs)) {
                            ?>
                            <tr data-id="<?php echo $data_cs['id_cs'] ?>" data-nama="<?php echo $data_cs['nama_cs'] ?>" data-alamat="<?php echo $data_cs['alamat'] ?>" data-bs-dismiss="modal">
                                <td><?php echo $data_cs['nama_cs'] ?></td>
                                <td class="wrap-text"><?php echo $data_cs['alamat'] ?></td>
                                <td class="text-nowrap"><?php echo $data_cs['no_telp'] ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Large Modal-->

<!-- date picker with flatpick -->
<script type="text/javascript">
  flatpickr("#date", {
    dateFormat: "d/m/Y",
  });
</script>
<!-- end date picker -->

<script>
  $(document).on('click', '#table2 tbody tr', function(e) {
    $('#id').val($(this).data('id'));
    $('#cs').val($(this).data('nama'));
    $('#alamat').val($(this).data('alamat'));
    $('#modalCs').modal('hide');
  });
</script>