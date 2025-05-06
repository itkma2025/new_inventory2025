<?php
require_once "akses.php";
$page  = 'transaksi';
$page2 = 'spk';
require_once "function/class-spk.php";
require_once "function/function-enkripsi.php";
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

        .warning-color {
            --bs-tooltip-bg: var(--bs-warning);
            /* Menggunakan warna danger dari Bootstrap */
            --bs-tooltip-color: var(--bs-dark);
            /* Tetap putih untuk teks */
        }

        .danger-color {
            --bs-tooltip-bg: var(--bs-danger);
            /* Menggunakan warna danger dari Bootstrap */
            --bs-tooltip-color: var(--bs-white);
            /* Tetap putih untuk teks */
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
            <!-- SWEET ALERT -->
            <?php
                if (isset($_SESSION['info'])) {
                    echo '<div class="info-data" data-infodata="' . $_SESSION['info'] . '"></div>';
                    unset($_SESSION['info']);
                }
            ?>
            <!-- END SWEET ALERT -->
            <div class="card shadow p-2">
                <div class="card-header text-center">
                    <h5><strong>DETAIL SPK</strong></h5>
                </div>
                <?php
                $id_spk = decrypt($_GET['id'], $key_spk);
                $sql = "SELECT 
                                sr.id_spk_reg,
                                sr.no_spk,
                                sr.tgl_spk,
                                sr.no_po,
                                sr.tgl_pesanan,
                                sr.petugas,
                                sr.note,
                                cs.nama_cs, 
                                cs.alamat, 
                                ordby.order_by, 
                                sl.nama_sales 
                            FROM spk_reg AS sr
                            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                            JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                            JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                            WHERE sr.id_spk_reg = '$id_spk'";
                $query = mysqli_query($connect, $sql);
                $data = mysqli_fetch_array($query);
                $petugas = $data['petugas'];
                ?>
                <!-- Kode untuk menampilkan data -->
                <?php
                $year = date('y');
                $day = date('d');
                $month = date('m');
                $no = 1;
                $total_spk = 0;
                $sql_trx = "SELECT
                                    sr.id_spk_reg,
                                    sr.id_inv,
                                    trx.id_transaksi,
                                    trx.id_produk,
                                    trx.qty,
                                    trx.status_ks,
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
                                WHERE sr.id_spk_reg = '$id_spk' ORDER BY trx.created_date ASC";
                $trx_produk_reg = mysqli_query($connect, $sql_trx);
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
                                    <p style="float: left;">Tgl. Pesanan</p>
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
                        <div class="text-start mb-3">
                            <a href="spk-dalam-proses.php?sort=baru" class="btn btn-warning btn-detail mb-3">
                                <i class="bi bi-arrow-left"></i> Halaman Sebelumnya
                            </a>
                            <?php
                            if ($role == "Super Admin" || $role == "Admin Gudang") {
                                ?>
                                    <input type="hidden" name="id_spk_reg" value="<?php echo $id_spk ?>">
                                    <?php
                                    $sql_cek_empty = mysqli_query($connect, "SELECT 
                                                                                id_spk,
                                                                                id_transaksi, 
                                                                                status_ks,
                                                                                created_date
                                                                            FROM transaksi_produk_reg
                                                                            WHERE id_spk = '$id_spk' ORDER BY created_date ASC");
                                    $data_cek_empty = mysqli_num_rows($sql_cek_empty);
                                    $status_ks_validasi = false;
                                    while ($cek_status_ks = mysqli_fetch_array($sql_cek_empty)) {
                                        $tampil_status_ks =  $cek_status_ks['status_ks'];
                                        // Cek apakah status_ks tidak sama dengan 2
                                        if ($tampil_status_ks != 2) {
                                            $status_ks_validasi = true;  // Set variabel ke true jika ada perbedaan
                                        }
                                    }

                                    // Jika ada perbedaan status_ks, maka button siap kirim di disabled
                                    $disabled = "";
                                    if ($status_ks_validasi) {
                                        $disabled = "disabled";
                                    } else {
                                        $disabled = "enabled";
                                    }

                                    ?>
                                    <?php
                                    if ($data_cek_empty != 0) {
                                    ?>
                                        <!-- Button validasi KS -->
                                        <button type="button" class="btn btn-secondary mb-3" id="validasi-ks" data-id="<?php echo encrypt($id_spk, $key_spk) ?>"><i class="bi bi-clipboard-check"></i> Validasi Kartu Stock</button>
                                        <!-- Button siap kirim -->
                                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#siapKirim" <?php echo $disabled ?>><i class="bi bi-send"></i> Siap Kirim</button>
                                    <?php
                                    } else {
                                        $update_spk = mysqli_query($connect, "UPDATE spk_reg SET status_spk = 'Belum Diproses' WHERE id_spk_reg = '$id_spk'");
                                    }
                                    ?>
                                <?php
                            }

                            ?>
                        </div>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr class="text-white" style="background-color: #051683;">
                                    <th class="text-center p-3" style="width:20px">No</th>
                                    <th class="text-center p-3 text-nowrap" style="width:350px">Nama Produk</th>
                                    <th class="text-center p-3 text-nowrap" style="width:80px">Qty Order</th>
                                    <th class="text-center p-3 text-nowrap" style="width:80px">Satuan</th>
                                    <th class="text-center p-3 text-nowrap" style="width:80px">Merk</th>
                                    <th class="text-center p-3 text-nowrap" style="width:100px">Harga</th>
                                    <th class="text-center p-3 text-nowrap" style="width:80px">Status KS</th>
                                    <th class="text-center p-3 text-nowrap" style="width:150px">Petugas Input KS</th>
                                    <?php
                                    if ($role == "Super Admin" || $role == "Admin Gudang") {
                                    ?>
                                        <th class="text-center p-3 text-nowrap" style="width:80px">Aksi</th>
                                    <?php
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($data_trx = mysqli_fetch_array($trx_produk_reg)) {
                                    $trx_id = $data_trx['id_transaksi'];
                                    $namaProduk = $data_trx['nama_produk'];
                                    $id_produk = $data_trx['id_produk'];
                                    $satuan = $data_trx['satuan'];
                                    $nama_merk = $data_trx['merk_produk'];
                                    $harga = $data_trx['harga_produk'];
                                    $sub_total =  $harga * $data_trx['qty'];
                                    $total_spk += $sub_total;
                                    $satuan_produk = '';
                                    $id_produk_substr = substr($id_produk, 0, 2);
                                    if ($id_produk_substr == 'BR') {
                                        $satuan_produk = $satuan;
                                    } else {
                                        $satuan_produk = 'Set';
                                    }

                                    $status_ks = "";
                                    if ($data_trx['status_ks'] == "0") {
                                        $status_ks = '  <i class="bi bi-x-circle-fill fs-5 text-danger"
                                                                data-bs-toggle="tooltip"  data-bs-placement="right"
                                                                data-bs-custom-class="warning-color"
                                                                data-bs-title="Kartu Stock Belum Di Input">
                                                            </i>';
                                    } else  if ($data_trx['status_ks'] == "1") {
                                        $status_ks = ' <i class="bi bi-x-circle-fill fs-5 text-danger"
                                                                data-bs-toggle="tooltip"  data-bs-placement="right"
                                                                data-bs-custom-class="danger-color"
                                                                data-bs-title="Qty Tidak Sesuai">
                                                            </i>';
                                    } else  if ($data_trx['status_ks'] == "2") {
                                        $status_ks = '<i class="bi bi-check-circle-fill fs-5" style="color: green;"></i>';
                                    }

                                    // Kode untuk menampilkan input date dan input by pada temp kartu stock
                                    $temp_kartu_stock = $connect->query("SELECT 
                                                                                tmp_ks.id_spk_ks,
                                                                                tmp_ks.input_date, 
                                                                                us.nama_user
                                                                            FROM tmp_kartu_stock AS tmp_ks
                                                                            LEFT JOIN $database2.user AS us ON (tmp_ks.created_by = us.id_user)
                                                                            WHERE tmp_ks.id_transaksi = '$trx_id'");
                                    while ($data_temp_ks = mysqli_fetch_array($temp_kartu_stock)) {
                                        $input_by = $data_temp_ks['nama_user'];
                                        $input_date = date('d/m/Y H:i:s', strtotime($data_temp_ks['input_date']));
                                    }
                                ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no; ?></td>
                                        <td class="text-nowrap"><?php echo $namaProduk ?></td>
                                        <td class="text-center"><?php echo number_format($data_trx['qty']) ?></td>
                                        <td class="text-center text-nowrap"><?php echo $satuan_produk ?></td>
                                        <td class="text-center"><?php echo $nama_merk ?></td>
                                        <td class="text-end"><?php echo number_format($harga) ?></td>
                                        <td class="text-center text-nowrap"><?php echo $status_ks ?></td>
                                        <td class="text-center text-nowrap">
                                            <?php
                                            if ($data_trx['status_ks'] != "0") {
                                                echo $input_by . '<br>';
                                                echo $input_date;
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </td>
                                        <?php
                                        if ($role == "Super Admin" || $role == "Admin Gudang") {
                                        ?>
                                            <td class="text-center">
                                                <a href="proses/proses-produk-spk-reg.php?hapus_trx=<?php echo encrypt($data_trx['id_transaksi'], $key_spk) ?> && id_spk=<?php echo encrypt($data_trx['id_spk_reg'], $key_spk) ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                                            </td>
                                        <?php
                                        }
                                        ?>
                                    </tr>
                                    <?php $no++; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php
                        $update_total = mysqli_query($connect, "UPDATE spk_reg SET total_spk = '$total_spk' WHERE id_spk_reg = '$id_spk'");
                        ?>
                    </div>
                </div>
            </div>
            <div id="result">

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

<!-- Modal Trx Selesai -->
<div class="modal fade" id="siapKirim" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="proses/proses-produk-spk-reg.php" method="POST">
                <div class="modal-body">
                    <?php  
                         $disabled = "";
                         if ($status_ks_validasi) {
                             $disabled = "disabled";
                         } else {
                             $disabled = "enabled";
                         }
                    ?>
                    <input type="hidden" name="id_spk_reg" value="<?php echo encrypt($id_spk, $key_spk) ?>">
                    <input type="hidden" name="status_validasi" value="<?php echo encrypt($disabled, $key_spk) ?>">
                    <h5>Apakah anda yakin pesanan ini siap kirim ?</h5>
                    <div class="mt-3">
                        <label><b>Nama Petugas</b></label>
                        <?php
                        // Kode untuk menampilkan input date dan input by pada temp kartu stock
                        $temp_petugas = $connect->query("SELECT 
                                                            tmp_ks.id_spk_ks,
                                                            tmp_ks.input_date, 
                                                            us.nama_user
                                                        FROM tmp_kartu_stock AS tmp_ks
                                                        LEFT JOIN $database2.user AS us ON (tmp_ks.input_by = us.id_user)
                                                        WHERE tmp_ks.id_spk_ks = '$id_spk' GROUP BY us.nama_user");

                        $nama_petugas_array = [];
                        while ($data_temp_petugas = mysqli_fetch_array($temp_petugas)) {
                            $nama_petugas_array[] = trim($data_temp_petugas['nama_user']);
                        }

                        // Gabungkan nama petugas dengan koma
                        $nama_petugas_string = implode(', ', $nama_petugas_array);
                        echo '<input type="text" class="form-control" id="petugas" name="petugas" value="' . htmlspecialchars($nama_petugas_string) . '" readonly >';
                        ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="siap-kirim"><i class="bi bi-send"></i> Siap Kirim</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetInput()">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Modal Trx Selesai -->

<script>
    function refreshPage() {
        location.reload();
    }
</script>

<!-- Add JavaScript function to reset the input value -->
<script>
    function resetInput() {
        // Set the value of the input field to an empty string
        document.getElementById("petugas").value = "";
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

<script>
    $(document).ready(function() {
        $('#validasi-ks').click(function() {
            var id_spk = $(this).data('id');

            $.ajax({
                type: 'POST',
                url: 'ajax/cek-ks.php', // Pastikan file PHP ini sesuai
                data: {
                    id_spk: id_spk
                },
                success: function(response) {
                    console.log("Success");
                    location.reload();
                    try {
                        var data = JSON.parse(response);
                        if (data.id_spk) {
                            console.log("ID SPK:", data.id_spk);
                        } else if (data.error) {
                            console.log("Error:", data.error);
                        }
                    } catch (e) {
                        console.error("Parsing Error:", e);
                        console.log("Raw Response:", response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        });
    });
</script>