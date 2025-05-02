<?php
require_once "../akses.php";
$jenis_produk = $_GET['jenis_produk'];
$page = '';
$page2 = '';
if ($jenis_produk == 'reguler' || $jenis_produk == 'ecat') {
    $page = 'produk';
    $page2 = 'data-produk';
} else if ($jenis_produk == 'set_reg') {
    $page = 'produk';
    $page2 = 'data-produk-set-marwa';
} else if ($jenis_produk == 'set_ecat') {
    $page = 'produk';
    $page2 = 'data-produk-set-ecat';
}
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
        p {
            margin-bottom: 0px !important;
        }

        .btn-sm {
            padding-top: 0px !important;
            padding-bottom: 0px !important;
            padding-left: 6px !important;
            padding-right: 6px !important;
        }

        td {
            text-align: left;
            padding: 2px;
        }

        th {
            text-align: center;
            padding: 4px;
            color: white;
        }

        .card-body {
            padding: 0 20px 0px 10px;
        }

        .dropdown-item-list.active {
            background-color: #6c757d;
            color: white;
        }

        .margin-left {
            margin-left: 10.255em !important;
        }

        .desktop-none {
            display: none;
        }

        @media only screen and (max-width: 480px) {
            .mobile-none {
                display: none;
            }

            h4 {
                font-size: 14pt;
            }

            h5 {
                font-size: 12pt;
            }

            label {
                font-size: 12pt;
            }

            .mobile-font {
                font-size: 12pt;
            }

            .desktop-none {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include 'page/sidebar.php'; ?>
    <!-- end sidebar -->

    <main id="main" class="main">
        <!-- SWEET ALERT -->
        <?php
        if (isset($_SESSION['info'])) {
            echo '<div class="info-data" data-infodata="' . $_SESSION['info'] . '" style="overvlow:hidden !important;"></div>';
            unset($_SESSION['info']);
        }
        ?>
        <!-- END SWEET ALERT -->
        <section>
            <?php
            // menampilkan url         
            $url = str_replace('driver/', '', $_SESSION['url']);
            $get_sort = $_GET['sort_data'];
            $jenis_produk = $_GET['jenis_produk'];
            require_once "../function/function-enkripsi.php";
            $key = "";
            //tangkap URL dengan $_GET
            $id = $_GET['id'];
            $data_produk = "";
            $del = "";
            $jenis_del = "";
            if ($jenis_produk == 'reguler') {
                $key = "KM@2024?";
                $id_produk = decrypt($id, $key);
                require_once "query/kartu-persediaan-barang-reg.php";
                $data_produk = mysqli_fetch_array($query_produk);
                $del = "proses/proses-kartu-stock.php?id_kartu_stock=";
                $jenis_del = "&&jenis_produk=reguler&&id_produk=";
            } else if ($jenis_produk == 'ecat') {
                $key = "KM@2024?";
                $id_produk = decrypt($id, $key);
                require_once "query/kartu-persediaan-barang-ecat.php";
                $data_produk = mysqli_fetch_array($query_produk);
                $del = "proses/proses-kartu-stock-ecat.php?id_kartu_stock=";
                $jenis_del = "&&jenis_produk=ecat&&id_produk=";
            } else if ($jenis_produk == 'set_reg') {
                $key = "KM@2024?SET";
                $id_produk = decrypt($id, $key);
                require_once "query/kartu-persediaan-barang-set-reg.php";
                $data_produk = mysqli_fetch_array($query_produk);
                $del = "proses/proses-kartu-stock-set-reg.php?id_kartu_stock=";
                $jenis_del = "&&jenis_produk=set_reg&&id_produk=";
            } else if ($jenis_produk == 'set_ecat') {
                $key = "KM@2024?SET";
                $id_produk = decrypt($id, $key);
                require_once "query/kartu-persediaan-barang-set-ecat.php";
                $data_produk = mysqli_fetch_array($query_produk);
                $del = "proses/proses-kartu-stock-set-ecat.php?id_kartu_stock=";
                $jenis_del = "&&jenis_produk=set_ecat&&id_produk=";
            } else {
            ?>
                <script>
                    // Mengarahkan pengguna ke halaman 404.php
                    window.location.replace("404.php");
                </script>
            <?php
            }
            ?>
            <div class="card">
                <div class="card-header text-center text-dark fw-bold">
                    <h4>PT. Karsa Mandiri Alkesindo</h4>
                    <h5>Kartu Persediaan Barang</h5>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        <div class="col-sm-6 mb-3">
                            <div class="card-body border mb-3 desktop-none">
                                <div class="col mb-2">
                                    <label class="fw-bold">Nama Produk :</label>
                                    <p class="mobile-font"><?php echo $data_produk['nama_produk'] ?></p>
                                </div>
                                <div class="col mb-2">
                                    <label class="fw-bold">Kategori Produk :</label>
                                    <p class="mobile-font"><?php echo $data_produk['kat_prod'] ?></p>
                                </div>
                            </div>
                            <div class="card-body border">
                                <div class="table-responsive">
                                    <table>
                                        <tr class="mobile-none">
                                            <td class="text-nowrap" style="width: 40%">Nama</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 58%"><?php echo $data_produk['nama_produk'] ?></td>
                                        </tr>
                                        <tr class="mobile-none">
                                            <td class="text-nowrap" style="width: 40%">Kategori</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 58%"><?php echo $data_produk['kat_prod'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 40%">No Izin Edar</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 58%"><?php echo $data_produk['no_izin_edar'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 40%">Merk</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 58%"><?php echo $data_produk['nama_merk'] ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="card-body border">
                                <div class="table-responsive">
                                    <table>
                                        <tr>
                                            <td class="text-nowrap" style="width: 40%">Lokasi</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 58%"><?php echo $data_produk['nama_lokasi'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 40%">No. Lantai</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 58%"><?php echo $data_produk['no_lantai'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 40%">Area</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 58%"><?php echo $data_produk['nama_area'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-nowrap" style="width: 40%">No. Rak</td>
                                            <td class="text-nowrap" style="width: 2%">:</td>
                                            <td class="text-nowrap" style="width: 58%"><?php echo $data_produk['no_rak'] ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Menampilkan data limit 1 -->
                <?php
                $update_stock = mysqli_fetch_array($query_produk_limit_stock);
                $update_in = mysqli_fetch_array($query_produk_limit_in);
                $update_out = mysqli_fetch_array($query_produk_limit_out);
                $total_qty_in = 0;
                $total_qty_out = 0;
                $stock_tersedia = 0;
                $total_data_kartu = mysqli_num_rows($query_total_stock);
                if ($total_data_kartu != 0) {
                    while ($data_stock_tersedia = mysqli_fetch_array($query_total_stock)) {
                        $total_qty_in += $data_stock_tersedia['qty_in'];
                        $total_qty_out +=  $data_stock_tersedia['qty_out'];
                        $stock_tersedia = $total_qty_in - $total_qty_out;
                    }
                }
                ?>
                <div class="d-flex justify-content-center p-2">
                    <div class="card border" style="width: 280px;">
                        <p class="text-center fw-bold mt-2" style="font-size: 20px;">Total Stock</p>
                        <p class="text-center mt-3" style="font-size: 18px;"><?php echo $stock_tersedia ?></p>
                        <?php
                        if (!empty($update_stock['created_by']) && !empty($update_stock['created_date'])) {
                        ?>
                            <p class="text-center mb-3 mt-3" style="font-size: 16px;">
                                Update Terakhir:<br>
                                <?php echo $update_stock['created_by'] ?><br>
                                (<?php echo date('d/m/Y H:i:s', strtotime($update_stock['created_date'])) ?>)
                            </p>
                        <?php
                        } else {
                        ?>
                            <br>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="d-flex justify-content-evenly border-top p-2 flex-wrap">
                    <div class="card border mt-4" style="width: 280px;">
                        <p class="text-center fw-bold mt-3" style="font-size: 20px;">Total Barang Masuk</p>
                        <p class="text-center mt-3" style="font-size: 18px;" id="totalIn"></p>
                        <?php
                        if (!empty($update_in['created_by']) && !empty($update_in['created_date'])) {
                        ?>
                            <p class="text-center mb-3 mt-3" style="font-size: 16px;">
                                Update Terakhir:<br>
                                <?php echo $update_in['created_by'] ?><br>
                                (<?php echo date('d/m/Y H:i:s', strtotime($update_in['created_date'])) ?>)
                            </p>
                        <?php
                        } else {
                        ?>
                            <br>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="card border mt-4" style="width: 280px;">
                        <p class="text-center fw-bold mt-3" style="font-size: 20px;">Total Barang Keluar</p>
                        <p class="text-center mt-3" style="font-size: 18px;" id="totalOut"></p>
                        <?php
                        if (!empty($update_out['created_by']) && !empty($update_out['created_date'])) {
                        ?>
                            <p class="text-center mb-3 mt-3" style="font-size: 16px;">
                                Update Terakhir:<br>
                                <?php echo $update_out['created_by'] ?><br>
                                (<?php echo date('d/m/Y H:i:s', strtotime($update_out['created_date'])) ?>)
                            </p>
                        <?php
                        } else {
                        ?>
                            <br>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="d-flex justify-content-start border-top p-2 flex-wrap">
                    <div class="ms-3" style="width: 300px;">
                        <p class="fw-bold mt-3" style="font-size: 18px;">Filter Tanggal Submit :</p>
                        <div class="dropdown">
                            <button id="dropdownButton" class="btn btn-secondary dropdown-toggle form-control" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pilih Filter
                            </button>
                            <ul class="dropdown-menu form-control p-3">
                                <button type="button" class="btn btn-outline-secondary form-control mb-2 dropdown-item-list" id="hari_ini">Hari Ini</button>
                                <button type="button" class="btn btn-outline-secondary form-control mb-2 dropdown-item-list" id="minggu_ini">Minggu Ini</a>
                                    <button type="button" class="btn btn-outline-secondary form-control mb-2 dropdown-item-list" id="bulan_ini">Bulan Ini</button>
                                    <button type="button" class="btn btn-outline-secondary form-control mb-2 dropdown-item-list" id="bulan_kemarin">Bulan Kemarin</button>
                                    <button type="button" class="btn btn-outline-secondary form-control mb-2 dropdown-item-list" id="tahun_kemarin">Tahun Kemarin</button>
                                    <li id="dateRangePicker" class="date-range-picker">
                                        <input type="text" id="dateRange" class="form-control mb-2 text-center" placeholder="<?php echo $placeholder_input_date ?>">
                                        <!-- <button type="button" class="btn btn-primary form-control" id="applyDateRange">Apply</button> -->
                                    </li>
                                    <button type="button" class="btn btn-danger form-control" id="reset">Reset</button>
                            </ul>
                        </div>
                    </div>
                    <div class="ms-3" style="width: 300px;">
                        <p class="fw-bold mt-3" style="font-size: 18px;">Filter User Submit :</p>
                        <select id="filter_petugas" class="form-select" multiple>
                            <option value="">Pilih...</option>
                            <?php
                            while ($data_petugas = mysqli_fetch_array($query_produk_petugas)) {
                            ?>
                                <option value="<?php echo $data_petugas['created_by'] ?>"><?php echo $data_petugas['created_by'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="d-flex justify-content-start p-2 flex-wrap">
                    <div class="ms-3 mb-3" style="width: 300px;">
                        <button id="add-data-btn" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#add">
                            <i class="bi bi-plus-circle"></i> Tambah Data
                        </button>
                    </div>
                    <div class="ms-3" style="width: 300px;">
                        <a href="scan-qr.php" class="btn btn-warning w-100"><i class="bi bi-qr-code-scan"></i> Scan QR Code</a>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tableKartuStock">
                            <thead>
                                <tr style="background-color:navy;">
                                    <th class="text-center text-nowrap p-3" style="width: 5%;">No</th>
                                    <th class="text-center text-nowrap p-3" style="width: 20%;">Jenis Barang Masuk / Keluar</th>
                                    <th class="text-center text-nowrap p-3" style="width: 10%;">Masuk</th>
                                    <th class="text-center text-nowrap p-3" style="width: 10%;">Keluar</th>
                                    <?php
                                    if ($get_sort == 'semua_data') {
                                    ?>
                                        <th class="text-center text-nowrap p-3" style="width: 10%;">Jumlah Akhir</th>
                                    <?php
                                    }
                                    ?>
                                    <th class="text-center text-nowrap p-3" style="width: 13%;">Nama Petugas Input</th>
                                    <th class="text-center text-nowrap p-3" style="width: 22%;">Keterangan</th>
                                    <th class="text-center text-nowrap p-3" style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                date_default_timezone_set('Asia/Jakarta');
                                require_once "../function/uuid.php";
                                $uuid = uuid();
                                $year = date('y');
                                $day = date('d');
                                $month = date('m');
                                $id_history = "HIS-KS-" . $year . $month . $uuid . $day;
                                $no = 1;
                                // Inisialisasi total masuk dan total keluar
                                $totalMasuk = 0;
                                $totalKeluar = 0;
                                while ($data_kartu_stock =  mysqli_fetch_array($query_produk2)) {
                                    $id_kartu_stock = $data_kartu_stock['id_kartu_stock'];
                                    $id_kartu_stock_encrypt = encrypt($id_kartu_stock, $key);
                                    $jenis_barang_masuk = $data_kartu_stock['ket_in'];
                                    $jenis_barang_keluar = $data_kartu_stock['ket_out'];
                                    $keterangan = $data_kartu_stock['keterangan'];
                                    $qty_in = $data_kartu_stock['qty_in'];
                                    $qty_out = $data_kartu_stock['qty_out'];
                                    $created_by = $data_kartu_stock['created_by'];
                                    $created_date = $data_kartu_stock['created_date'];

                                    $jenis_br_in_out = "";
                                    if ($jenis_barang_masuk != '') {
                                        $jenis_br_in_out = $jenis_barang_masuk;
                                    } else {
                                        $jenis_br_in_out = $jenis_barang_keluar;
                                    }

                                    // Menghitung jumlah akhir
                                    $totalMasuk += $qty_in;
                                    $totalKeluar += $qty_out;
                                    $jumlah_akhir = $totalMasuk - $totalKeluar;

                                    $edit_data = "";
                                    $in = "";
                                    if ($qty_in == 0) {
                                        $in = '<td class="text-start" style="background-color: #ecf2f9"></td>';
                                    } else {
                                        $in = '<td class="text-start fw-bold">' . $qty_in . '</td>';
                                        $edit_data = '<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editIn" title="Edit Data" data-id="' . $id_kartu_stock_encrypt . '" data-keterangan="' . $keterangan . '" data-qty="' . $qty_in . '"><i class="bi bi-pencil"></i></button>';
                                    }

                                    $out = "";
                                    if ($qty_out == 0) {
                                        $out = '<td style="background-color: #ecf2f9"></td>';
                                    } else {
                                        $out = '<td class="fw-bold">' . $qty_out . '</td>';
                                        $edit_data = '<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editOut" title="Edit Data" data-id="' . $id_kartu_stock_encrypt . '" data-keterangan="' . $keterangan . '" data-qty="' . $qty_out . '"><i class="bi bi-pencil"></i></button>';
                                    }

                                    $qty_hapus = ($qty_in == 0) ? $qty_out : $qty_in;
                                ?>
                                    <tr>
                                        <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                        <td> <?php echo $jenis_br_in_out ?></td>
                                        <?php echo $in ?>
                                        <?php echo $out ?>
                                        <?php
                                        if ($get_sort == 'semua_data') {
                                        ?>
                                            <td class="text-center"><?php echo  $jumlah_akhir; ?></td>
                                        <?php
                                        }
                                        ?>
                                        <td class="text-center text-nowrap">
                                            <?php echo  $created_by; ?><br>
                                            (<?php echo date('d/m/Y H:i:s', strtotime($created_date)); ?>)

                                        </td>
                                        <td class="text-nowrap"><?php echo $keterangan; ?></td>
                                        <td class="text-center text-nowrap">
                                            <?php
                                            if ($jenis_br_in_out != "Penjualan") {
                                            ?>
                                                <?php echo $edit_data; ?>
                                                <a href="<?php echo $del; ?><?php echo $id_kartu_stock_encrypt ?><?php echo $jenis_del; ?><?php echo $id ?>&&id_history=<?php echo $id_history ?>&&qty=<?php echo $qty_hapus ?>" class="btn btn-danger btn-sm delete-data" title="Hapus Data"><i class="bi bi-trash"></i></a>
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
            </div>

            <?php include "page/script.php"; ?>
            <?php include "modal/tambah-data-persediaan-barang.php" ?>
        </section>
        <!-- End Footer -->
        <?php include "modal/edit-data-in-persediaan-barang.php" ?>
    </main><!-- End #main -->
    <!-- Footer -->
    <?php include "page/footer.php" ?>
</body>

</html>
<?php include "modal/edit-data-out-persediaan-barang.php" ?>
<script>
    var totalBarangIn = "<?php echo $totalMasuk ?>";
    var totalBarangOut = "<?php echo $totalKeluar ?>";
    var totalStock = totalBarangIn - totalBarangOut;

    // Menggunakan jQuery untuk memilih elemen dengan id 'totalIn' dan menetapkan teksnya
    $('#totalIn').text(totalBarangIn);
    $('#totalOut').text(totalBarangOut);
    $('#totalStock').text(totalStock);
</script>
<script>
    $(document).ready(function() {
        var sortData = "<?php echo $get_sort ?>";
        var table = $("#tableExport").DataTable();

        // Menambahkan ekstensi DataTables untuk menyaring baris berdasarkan nilai pada kolom "created_by"
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var selectedItem = $("#filter_petugas").val();
            if (sortData == 'semua_data') {
                var createdBy = data[5]; // Sesuaikan indeks kolom dengan kolom "created_by"
            } else {
                var createdBy = data[4]; // Sesuaikan indeks kolom dengan kolom "created_by"
            }


            // Jika tidak ada item yang dipilih, tampilkan semua baris
            if (!selectedItem || selectedItem.length === 0) {
                return true;
            }

            // Periksa apakah nilai "created_by" ada dalam nilai yang dipilih
            if (selectedItem.includes(createdBy)) {
                return true;
            }

            return false;
        });

        // Inisialisasi Selectize setelah DataTables selesai memuat
        $("#filter_petugas").selectize({
            plugins: ["remove_button"],
            delimiter: ",",
            persist: false,
            create: function(input) {
                return {
                    value: input,
                    text: input,
                };
            },
            onChange: function(value) {
                // Perbarui filter DataTables setelah nilai dipilih
                table.draw();
            },
            onDropdownOpen: function($dropdown) {
                // Jangan lakukan apa pun ketika dropdown dibuka
                // (kode yang dijalankan setelah pemilihan data)
            }
        });

        // Gambar tabel pada saat memuat halaman
        table.draw();
    });
</script>

<!-- Filter date range -->
<script>
    $(document).ready(function() {
        var sortData = "<?php echo $get_sort ?>";
        // console.log(sortData);
        if (sortData == 'hari_ini') {
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Hari Ini');
            $("#hari_ini").addClass("active");
            $("#hari_ini").prop('disabled', true);
        } else if (sortData == 'minggu_ini') {
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Minggu Ini');
            $("#minggu_ini").addClass("active");
            $("#minggu_ini").prop('disabled', true);
        } else if (sortData == 'bulan_ini') {
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Bulan Ini');
            $("#bulan_ini").addClass("active");
            $("#bulan_ini").prop('disabled', true);
        } else if (sortData == 'bulan_kemarin') {
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Bulan Kemarin');
            $("#bulan_kemarin").addClass("active");
            $("#bulan_kemarin").prop('disabled', true);
        } else if (sortData == 'tahun_kemarin') {
            // Update text of dropdown button with the selected text
            $("#dropdownButton").text('Tahun Kemarin');
            $("#tahun_kemarin").addClass("active");
            $("#tahun_kemarin").prop('disabled', true);
        } else if (sortData == 'date_range') {
            // Function to update dropdown text with selected date range
            var textDate = "<?php echo $text_date_dropdown ?>";
            $("#dropdownButton").text(textDate);

        }
    });
</script>

<script>
    $(document).ready(function() {
        $("#hari_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=hari_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#minggu_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=minggu_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#bulan_ini").click(function() {
            var url = "<?php echo $url; ?>&sort_data=bulan_ini"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#bulan_kemarin").click(function() {
            var url = "<?php echo $url; ?>&sort_data=bulan_kemarin"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#tahun_kemarin").click(function() {
            var url = "<?php echo $url; ?>&sort_data=tahun_kemarin"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });
    $(document).ready(function() {
        $("#reset").click(function() {
            var url = "<?php echo $url; ?>&sort_data=semua_data"; // Menggabungkan $url dengan string "&sort_data=today"
            window.location.href = url;
        });
    });

    // Filter untuk date range
    $(document).ready(function() {
        var dateRangePicker = flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "d/m/Y",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 1) {
                    var startDate = selectedDates[0];
                    var today = new Date(); // Mendapatkan tanggal hari ini
                    today.setHours(0, 0, 0, 0); // Reset waktu pada tanggal hari ini

                    // Hitung tanggal maksimal 90 hari dari startDate
                    var maxEndDate = new Date(startDate.getTime() + 90 * 24 * 60 * 60 * 1000);

                    // Jika maxEndDate melebihi hari ini, set maxDate menjadi hari ini
                    if (maxEndDate > today) {
                        instance.set('maxDate', today);
                    } else {
                        // Jika masih dalam rentang, set maxDate ke 90 hari dari startDate
                        instance.set('maxDate', maxEndDate);
                    }

                    // Setel minDate ke tanggal mulai
                    instance.set('minDate', startDate);
                } else {
                    // Reset batas tanggal setelah memilih rentang tanggal
                    instance.set('minDate', null);
                    instance.set('maxDate', null);
                    redirectToUrl(); // Redirect ke URL setelah memilih rentang tanggal
                }
            }
        });



        // Function to check if a string is a valid date
        function isValidDate(dateString) {
            // Regular expression untuk memeriksa format dd/mm/yyyy
            var datePattern = /^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/;
            
            // Periksa apakah dateString sesuai dengan pola
            if (!datePattern.test(dateString)) {
                return false;
            }

            // Pisahkan string tanggal berdasarkan "/"
            var parts = dateString.split('/');
            var day = parseInt(parts[0], 10);
            var month = parseInt(parts[1], 10) - 1; // Bulan di JavaScript Date mulai dari 0
            var year = parseInt(parts[2], 10);

            // Buat objek tanggal dengan format yang tepat
            var date = new Date(year, month, day);

            // Periksa validitas dengan mencocokkan kembali nilai hari, bulan, dan tahun
            return (
                date.getFullYear() === year &&
                date.getMonth() === month &&
                date.getDate() === day
            );
        }

        // Function to redirect to URL with selected date range
        function redirectToUrl() {
            var selectedDates = dateRangePicker.selectedDates;
            if (selectedDates.length === 2) {
                var formattedStartDate = selectedDates[0].toLocaleDateString('en-GB');
                var formattedEndDate = selectedDates[1].toLocaleDateString('en-GB');

                // Check if the selected dates are valid
                if (!isValidDate(formattedStartDate) || !isValidDate(formattedEndDate)) {
                    window.location.href = '404.php';
                    return;
                }

                var key = "<?php echo $key ?>";

                // Melakukan request AJAX untuk memanggil file PHP
                $.ajax({
                    url: '../ajax/function-enkripsi.php',
                    type: 'POST',
                    data: {
                        formattedStartDate: formattedStartDate,
                        formattedEndDate: formattedEndDate,
                        key: key
                    },
                    success: function(response) {
                        // response sudah berupa objek JavaScript jika tipe konten respons adalah JSON
                        var encryptedStartDate = response.startDate;
                        var encryptedEndDate = response.endDate;

                        var url = "<?php echo $url; ?>&sort_data=date_range&start_date=" + encodeURIComponent(encryptedStartDate) + "&end_date=" + encodeURIComponent(encryptedEndDate);
                        window.location.href = url;
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error(error);
                    }
                });
            }
        }

    });
</script>
<script>
    $(document).ready(function() {
        $.ajax({
            url: '../ajax/get-token-csrf.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#csrf_token').val(response.token_csrf);
                $('#expiryDate').attr('data-expiry', response.token_exp);
                $.getScript('../assets/js/expired-token.js');
            },
            error: function() {
                // Debugging
                console.error('AJAX request failed');
            }
        });
    });
</script>