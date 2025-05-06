<?php
$page  = 'transaksi';
$page2 = 'spk';
include "akses.php";
include "function/class-spk.php";
include "query/notifikasi.php";
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

    <style type="text/css">
        table {
            border-collapse: collapse;
            width: 100%;
        }

        @media only screen and (max-width: 500px) {
            body {
                font-size: 14px;
            }

            .mobile {
                display: none;
            }

            .mobile-text {
                text-align: left !important;
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
        <section>
            <div class="card shadow p-2">
                <div class="card-header text-center">
                    <h5><strong>DETAIL SPK</strong></h5>
                </div>
                <?php
                include "koneksi.php";
                $id_spk = base64_decode($_GET['id']);
                $sql = "SELECT sr.*, cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                    FROM spk_reg AS sr
                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                    JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                    JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                    WHERE sr.id_spk_reg = '$id_spk'";
                $query = mysqli_query($connect, $sql);
                $data = mysqli_fetch_array($query);
                $petugas = $data['petugas'];
                ?>
                <!-- Proses update untuk notifikasi -->
                <?php  
                    if (isset($_GET['status'])) {
                        $status_notif = $_GET['status'];
                        $update_status_notif = $connect->query("UPDATE spk_reg SET status_notif = '$status_notif' WHERE id_spk_reg = '$id_spk'");
                    }
                ?>
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div class="card-body p-3 border">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">No. SPK</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['no_spk'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl. SPK</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['tgl_spk'] ?>
                                </div>
                            </div>
                            <?php
                               if ($data['no_po'] != '') {
                                    echo '
                                    <div class="row">
                                        <div class="col-5">
                                            <p style="float: left;">No. PO</p>
                                            <p style="float: right;">:</p>
                                        </div>
                                        <div class="col-7">
                                            ' . $data['no_po'] . '
                                        </div>
                                    </div>';
                                }
                            ?>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Tgl Pesanan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['tgl_pesanan'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Order Via</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['order_by'] ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card-body p-3 border">
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Sales</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['nama_sales'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Pelanggan</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['nama_cs'] ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-5">
                                    <p style="float: left;">Alamat</p>
                                    <p style="float: right;">:</p>
                                </div>
                                <div class="col-7">
                                    <?php echo $data['alamat'] ?>
                                </div>
                            </div>
                            <?php
                                $note = $data['note'];

                                $items = explode("\n", trim($note));
                                if (!empty($note)) {
                                    echo '
                                        <div class="row mt-2">
                                            <div class="col-5">
                                                <p style="float: left;">Note</p>
                                                <p style="float: right;">:</p>
                                            </div>
                                            <div class="col-7">
                                    ';

                                    foreach ($items as $notes) {
                                        echo trim($notes) . '<br>';
                                    }

                                    echo '
                                            </div>
                                        </div>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tampil data -->
            <div class="card shadow">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <form action="proses/proses-produk-spk-reg.php" method="POST">
                            <div class="text-start mb-3">
                                <a href="spk-siap-kirim.php?sort=baru" class="btn btn-warning btn-detail">
                                    <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                                </a>
                                <?php
                                $id_spk = base64_decode($_GET['id']);
                                ?>
                            </div>
                            <button type="button" class="btn btn-secondary p-2">Nama Petugas : <?php echo $petugas ?></button>
                            <table class="table table-striped table-bordered" id="table2">
                                <thead>
                                    <tr class="text-white" style="background-color: #051683;">
                                        <th class="text-center p-3 text-nowrap" style="width:20px">No</th>
                                        <th class="text-center p-3 text-nowrap" style="width:300px">Nama Produk</th>
                                        <th class="text-center p-3 text-nowrap" style="width:100px">Merk</th>
                                        <th class="text-center p-3 text-nowrap" style="width:80px">Qty Order</th>
                                        <th class="text-center p-3 text-nowrap" style="width:100px">Satuan</th>
                                        <th class="text-center p-3 text-nowrap" style="width:100px">Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include "koneksi.php";
                                    $year = date('y');
                                    $day = date('d');
                                    $month = date('m');
                                    $id_spk_decode = base64_decode($_GET['id']);
                                    $no = 1;
                                    $sql_trx = "SELECT
                                                    sr.id_spk_reg,
                                                    sr.id_inv,
                                                    trx.id_transaksi,
                                                    trx.id_produk,
                                                    trx.qty,
                                                    trx.created_date,
                                                    spr.stock, 
                                                    COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                                                    COALESCE(tpr.satuan, tpe.satuan) AS satuan,
                                                    COALESCE(tpr.harga_produk,  tpe.harga_produk, tpsm.harga_set_marwa, tpse.harga_set_ecat) AS harga_produk,
                                                    COALESCE(mr_produk.nama_merk, mr_produk_ecat.nama_merk, mr_set.nama_merk, mr_set_ecat.nama_merk) AS merk_produk -- Nama merk untuk produk reguler
                                                FROM  transaksi_produk_reg AS trx
                                                LEFT JOIN spk_reg sr ON sr.id_spk_reg = trx.id_spk
                                                LEFT JOIN stock_produk_reguler spr ON trx.id_produk = spr.id_produk_reg
                                                LEFT JOIN stock_produk_ecat spe ON trx.id_produk = spe.id_produk_ecat
                                                LEFT JOIN tb_produk_reguler tpr ON trx.id_produk = tpr.id_produk_reg
                                                LEFT JOIN tb_produk_ecat tpe ON trx.id_produk = tpe.id_produk_ecat
                                                LEFT JOIN tb_produk_set_marwa tpsm ON trx.id_produk = tpsm.id_set_marwa
                                                LEFT JOIN tb_produk_set_ecat tpse ON trx.id_produk = tpse.id_set_ecat
                                                LEFT JOIN tb_merk mr_produk ON tpr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
                                                LEFT JOIN tb_merk mr_produk_ecat ON tpe.id_merk = mr_produk_ecat.id_merk -- JOIN untuk produk reguler
                                                LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
                                                LEFT JOIN tb_merk mr_set_ecat ON tpse.id_merk = mr_set_ecat.id_merk -- JOIN untuk produk set
                                                WHERE sr.id_spk_reg = '$id_spk_decode' ORDER BY trx.created_date ASC";
                                    $trx_produk_reg = mysqli_query($connect, $sql_trx);
                                    while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                                        $namaProduk = $data_trx['nama_produk'];
                                        $id_produk = $data_trx['id_produk'];
                                        $satuan = $data_trx['satuan'];
                                        $nama_merk = $data_trx['merk_produk'];
                                        $harga = $data_trx['harga_produk'];
                                        $satuan_produk = '';
                                        $id_produk_substr = substr($id_produk, 0, 2);
                                        if ($id_produk_substr == 'BR') {
                                            $satuan_produk = $satuan;
                                        } else {
                                            $satuan_produk = 'Set';
                                        }
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $no; ?></td>
                                            <td class="text-nowrap"><?php echo $namaProduk ?></td>
                                            <td class="text-center"><?php echo $nama_merk ?></td>
                                            <td class="text-end"><?php echo number_format($data_trx['qty']) ?></td>
                                            <td class="text-center text-nowrap"><?php echo $satuan_produk ?></td>
                                            <td class="text-end"><?php echo number_format($harga) ?></td>
                                        </tr>
                                        <?php $no++; ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </form>
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

<!-- Generat UUID -->
<?php
function generate_uuid()
{
    return sprintf(
        '%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}
?>
<!-- End Generate UUID -->

<script>
    function refreshPage() {
        location.reload();
    }
</script>

<!-- Kode untuk modal cancel SPK -->
<script>
    $(document).ready(function() {
        $('.btn-detail').click(function() {
            var idSpk = $(this).data('spk');
            $('#spk').text(idSpk);

            $('button.btn-pilih').attr('data-spk', idSpk);

            $('#modalBarang').modal('show');
        });

        $(document).on('click', '.btn-pilih', function(event) {
            event.preventDefault();
            event.stopPropagation();

            var id = $(this).data('id');
            var spk = $(this).attr('data-spk');

            saveData(id, spk);
        });

        function saveData(id, spk) {
            $.ajax({
                url: 'simpan-data-spk.php',
                type: 'POST',
                data: {
                    id: id,
                    spk: spk
                },
                success: function(response) {
                    console.log('Data berhasil disimpan.');
                    $('button[data-id="' + id + '"]').prop('disabled', true);
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan saat menyimpan data:', error);
                }
            });
        }
    });
</script>

<!-- Update status notofikasi spk siap kirim -->
<script>
    function updateStatus(status) {
        var id = "<?php echo $id_spk ?>"; // Menggunakan id dari URL yang sudah di-encode
        $.ajax({
            url: 'update-status.php',
            type: 'POST',
            data: { id: id, status: status },
            success: function(response) {
                if (response == "success") {
                    // Update UI without reloading the page
                    document.getElementById('status_notif').innerText = status;
                } else {
                    alert('Gagal mengupdate status');
                }
            }
        });
    }
</script>

<!-- Kode Untuk Qty   -->
<script>
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function formatInputValue(value) {
        return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function checkStock(inputId) {
        var stock = parseInt(document.getElementById('stock_' + inputId).value.replace(/,/g, '')); // Menggunakan ID yang sesuai untuk elemen stock
        var qtyInput = document.getElementById('qtyInput_' + inputId); // Menggunakan ID yang sesuai untuk elemen qtyInput
        var qty = qtyInput.value.replace(/,/g, '');

        qtyInput.value = formatInputValue(qty);

        if (parseInt(qty) > stock) {
            qtyInput.value = formatNumber(stock);
        }

        var simpanButton = document.getElementById('simpan');
        if (parseInt(qty) > 0) {
            simpanButton.disabled = false;
        } else {
            simpanButton.disabled = true;
        }
    }
</script>

<!-- Fungsi menonaktifkan kerboard enter -->
<script>
    document.addEventListener("keydown", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            document.getElementById("simpan-data").click();
        }
    });
</script>