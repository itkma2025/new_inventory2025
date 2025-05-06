<?php
$page = 'bank';
$page2 = 'bank-cs';
include 'akses.php';
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
            <h1>Data Bank Customer</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">Data Bank Customer</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section>
             <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="card p-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table2">
                        <thead>
                            <tr class="text-white" style="background-color: navy;">
                                <th class="text-center text-nowrap p-3" style="width: 100px;">No</th>
                                <th class="text-center text-nowrap p-3" style="width: 350px;">Nama Customer</th>
                                <th class="text-center text-nowrap p-3" style="width: 150px;">Nama Bank</th>
                                <th class="text-center text-nowrap p-3" style="width: 250px;">No. Rekening</th>
                                <th class="text-center text-nowrap p-3" style="width: 350px;">Atas Nama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                                include "koneksi.php";
                                $no = 1;
                                $sql_bank = "SELECT 
                                                csb.id_bank_cs, csb.id_bank, csb.no_rekening, csb.atas_nama,
                                                bk.nama_bank, cs.nama_cs
                                            FROM bank_cs AS csb
                                            LEFT JOIN bank bk ON (csb.id_bank = bk.id_bank)
                                            LEFT JOIN tb_customer cs ON (cs.id_cs = csb.id_cs)
                                            ORDER BY cs.nama_cs ASC";
                                $query_bank = mysqli_query($connect, $sql_bank);
                                while($data_bank = mysqli_fetch_array($query_bank)){
                                    $id_bank_cs = $data_bank['id_bank_cs'];
                            ?>
                            <tr>
                                <td class="text-nowrap text-center"><?php echo $no; ?></td>
                                <td class="text-nowrap"><?php echo $data_bank['nama_cs'] ?></td>
                                <td class="text-nowrap text-center"><?php echo $data_bank['nama_bank'] ?></td>
                                <td class="text-nowrap text-center"><?php echo $data_bank['no_rekening'] ?></td>
                                <td class="text-nowrap"><?php echo $data_bank['atas_nama'] ?></td>
                            </tr>
                            <?php $no++ ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main><!-- End #main -->

     <!-- Modal Edit-->
     <div class="modal fade animate__animated animate__flipInX" id="edit" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Data Bank Customer</h1>
                </div>
                <div class="modal-body">
                    <form action="proses/bank-cs.php" method="post">
                        <input type="hidden" id="id" name="id_bank_cs">
                        <div class="mb-3">
                            <label>Nama Bank</label>
                            <input type="hidden" class="form-control" id="idBank" name="id_bank">
                            <input type="text" class="form-control" id="namaBank" data-bs-toggle="modal" data-bs-target="#bankEdit" readonly>
                        </div>
                        <div class="mb-3">
                            <label>No. Rekening</label>
                            <input type="text" name="no_rekening" id="numberOnly" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Atas Nama</label>
                            <input type="text" name="atas_nama" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" id="btnCloseEdit" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary" name="edit" id="edit" disabled>Ubah Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bankEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Pilih Bank</h1>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="table3">
                            <thead>
                                <tr class="text-white" style="background-color: navy;">
                                    <th class="text-center text-nowrap p-3" style="width: 100px;">No</th>
                                    <th class="text-center text-nowrap p-3" style="width: 700px;">Nama Bank</th>
                                    <th class="text-center text-nowrap p-3" style="width: 200px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  
                                    include "koneksi.php";
                                    $no = 1;
                                    $sql_bank = "SELECT id_bank, nama_bank, logo FROM bank ORDER BY nama_bank ASC";
                                    $query_bank = mysqli_query($connect, $sql_bank);
                                    while($data_bank = mysqli_fetch_array($query_bank)){
                                        $id_bank = $data_bank['id_bank'];
                                        $nama_bank = $data_bank['nama_bank'];
                                ?>
                                <tr>
                                    <td class="text-nowrap text-center"><?php echo $no; ?></td>
                                    <td class="text-nowrap"><?php echo $data_bank['nama_bank'] ?></td>
                                    <td class="text-nowrap text-center">
                                      <button id="pilih" class="btn btn-primary btn-sm" data-id="<?php echo $data_bank['id_bank']; ?>" data-bank="<?php echo $data_bank['nama_bank']; ?>">Pilih</button> 
                                    </td>
                                </tr>
                                <?php $no++ ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"> Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Edit -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
  <script src="assets/js/input-number-only.js"></script>

</body>
</html>
<script>
    $('#edit').on('shown.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var id_bank = button.data('id-bank');
        var bank = button.data('bank');
        var rek = button.data('rek');
        var an = button.data('an');

        var modal = $(this);
        var editBtn = modal.find('.modal-footer #edit');
        var idInput = modal.find('.modal-body #id');
        var idBankInput = modal.find('.modal-body #idBank');
        var namaBank = modal.find('.modal-body #namaBank');
        var noRekInput = modal.find('.modal-body input[name="no_rekening"]');
        var atasNamaInput = modal.find('.modal-body input[name="atas_nama"]');

        // Isi nilai-nilai input di dalam modal
        idInput.val(id);
        idBankInput.val(id_bank);
        namaBank.val(bank);
        noRekInput.val(rek);
        atasNamaInput.val(an);

        // Simpan nilai awal
        var originalValues = {
            namaBank: namaBank.val(),
            noRek: noRekInput.val(),
            atasNama: atasNamaInput.val()
        };

        // Menambahkan elemen input ke dalam array inputFields
        var inputFields = [namaBank, noRekInput, atasNamaInput];

        // Menambahkan event listener untuk setiap input
        inputFields.forEach(function (field) {
            field.on('input', function () {
                var currentValues = {
                    namaBank: namaBank.val(),
                    noRek: noRekInput.val(),
                    atasNama: atasNamaInput.val()
                };

                // Memeriksa apakah terjadi perubahan pada nilai-nilai input
                if (
                    currentValues.namaBank !== originalValues.namaBank ||
                    currentValues.noRek !== originalValues.noRek ||
                    currentValues.atasNama !== originalValues.atasNama
                ) {
                    editBtn.prop('disabled', false);
                } else {
                    editBtn.prop('disabled', true);
                }
            });
        });

        // Event listener untuk mereset form
        modal.find('form').on('reset', function () {
            editBtn.prop('disabled', true);
            // Set nilai awal kembali setelah form direset
            originalValues = {
                namaBank: namaBank.val(),
                noRek: noRekInput.val(),
                atasNama: atasNamaInput.val()
            };
        });
    });


    // =================================================
    // Kode untu multiple pop up tanpa close pupup awal
    $('#cekBank').on('show.bs.modal', function () {
        $('#edit').modal('hide');
    });

    $('#bankEdit').on('show.bs.modal', function () {
        // Check if edit is open, if yes, hide it
        if ($('#edit').hasClass('show')) {
            $('#edit').modal('hide');
        }
    });

    $('#edit').on('hide.bs.modal', function (e) {
        // Prevent #edit from closing
        e.preventDefault();
    });

    $('#bankEdit').on('hidden.bs.modal', function () {
        // Show edit when bankEdit is hidden
        $('#edit').modal('show');
    });

    document.getElementById('btnCloseEdit').addEventListener('click', function() {
      // Reload halaman
      location.reload();
    });

    // select lokasi
    $(document).on('click', '#pilih', function (e) {
      var namaBank = $(this).data('bank');
      var idBank = $(this).data('id');
      // Trigger event input setelah mengubah nilai
      $('#namaBank').val(namaBank).trigger('input'); 
      $('#idBank').val(idBank).trigger('input'); 
      $('#bankEdit').modal('hide');
    });
</script>