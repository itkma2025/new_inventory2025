<?php
require_once "../akses.php";
$id_user = decrypt($_SESSION['tiket_id'], $key_global);
$page = 'history-input-stock';
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
        th {
            background-color: navy !important;
            color: white !important;
        }

        .modal-open-backdrop {
            pointer-events: none;
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

        <div class="pagetitle text-center">
            <h1>History Input Stock</h1>
        </div><!-- End Page Title -->

        <section>
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="card">
                <div class="card-body mt-3">
                    <!-- Bordered Tabs -->
                    <ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="onproses-tab" data-bs-toggle="tab" data-bs-target="#bordered-onproses" type="button" role="tab" aria-controls="onproses" aria-selected="true">On Proses</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#bordered-history" type="button" role="tab" aria-controls="history" aria-selected="false" tabindex="-1">History</button>
                        </li>
                    </ul>
                    <div class="tab-content pt-2" id="borderedTabContent">
                        <div class="tab-pane fade active show" id="bordered-onproses" role="tabpanel" aria-labelledby="onproses-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="table2">
                                    <thead>
                                        <tr>
                                            <th class="text-center p-3 text-nowrap" style="width: 80px">No</th>
                                            <th class="text-center p-3 text-nowrap">No. SPK</th>
                                            <th class="text-center p-3 text-nowrap">Tgl. SPK</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 600px">Nama Customer</th>
                                            <th class="text-center p-3 text-nowrap">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        require_once "query/on-proses-ks.php"; 
                                        require_once "../function/function-enkripsi.php";
                                        while ($data_on_proses = mysqli_fetch_array($tmp_grouping)) {
                                            $id_spk = $data_on_proses['id_spk_ks'];
                                        ?>
                                            <tr>
                                                <td class="text-nowrap text-center"><?php echo $no; ?></td>
                                                <td class="text-nowrap text-center"><?php echo $data_on_proses['no_spk']; ?></td>
                                                <td class="text-nowrap text-center"><?php echo $data_on_proses['tgl_spk']; ?></td>
                                                <td class="text-nowrap"><?php echo $data_on_proses['nama_cs']; ?></td>
                                                <td class="text-nowrap text-center">
                                                    <button type="button" class="btn btn-primary btn-sm btn-detail" data-bs-toggle="modal" data-bs-target="#detailProduk" data-id="<?php echo encrypt($id_spk, $key_gudang) ?>">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php $no++ ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="bordered-history" role="tabpanel" aria-labelledby="history-tab">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="tableExport">
                                    <thead>
                                        <tr>
                                            <th class="text-center p-3 text-nowrap">No</th>
                                            <th class="text-center p-3 text-nowrap">Nama Produk / Nama Set</th>
                                            <th class="text-center p-3 text-nowrap">Qty</th>
                                            <th class="text-center p-3 text-nowrap">Tgl. Input</th>
                                            <th class="text-center p-3 text-nowrap">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        require_once "query/history-input-stock.php";
                                        while ($data_history = mysqli_fetch_array($user_history_input_stock)) {
                                            $status = ($data_history['status_barang'] == 0) ? "Masuk" : "Keluar";
                                            $qty = "";
                                            if ($status == "Masuk") {
                                                $qty = $data_history['qty_in'];
                                            } else if ($status == "Keluar") {
                                                $qty = $data_history['qty_out'];
                                            }
                                        ?>
                                            <tr>
                                                <td class="text-nowrap text-center"><?php echo $no; ?></td>
                                                <td class="text-nowrap"><?php echo $data_history['nama_produk']; ?></td>
                                                <td class="text-nowrap text-end"><?php echo $qty; ?></td>
                                                <td class="text-nowrap text-center"><?php echo date('d/m/Y', strtotime($data_history['created_date'])); ?></td>
                                                <td class="text-nowrap text-center"><?php echo $status; ?></td>
                                            </tr>
                                            <?php $no++ ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!-- End Bordered Tabs -->

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

<!-- Modal Detail -->
<div class="modal fade" id="detailProduk" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Detail Produk</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <div id="detailProdukBody"></div> <!-- Tempat data tabel akan dimuat -->
                </div>
                <div id="result"></div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('.btn-detail').click(function() {
            var id_spk = $(this).data('id');
            $.ajax({
                type: 'POST',
                url: '../ajax/detail-produk-tmp-ks.php', // Pastikan file PHP ini benar
                data: {
                    id_spk: id_spk
                },
                success: function(response) {
                    console.log("Raw Response:", response);
                    $('#detailProdukBody').html(response); // Isi modal body dengan HTML dari response
                    $('#detailProduk').modal('show'); // Tampilkan modal
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        });
    });
</script>