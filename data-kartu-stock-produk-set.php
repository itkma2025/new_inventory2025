<?php  
    include "akses.php";
    $page = "stock-digital";
    $page2 = "prod-set";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "page/head.php"?>
    <style>
        /* Reset margin dan padding pada tabel dan pembungkusnya */
        .dataTables_wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }

        #tableExport {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important; /* Pastikan tabel mengambil 100% lebar */
        }

        p{
            margin-bottom: 0px !important;
        }

        th{
            background-color: navy !important;
            color: white;
            text-align: center !important;
            font-size: 16px !important;
        }

        td{
            font-size: 15px !important;
        }

        .btn-sm{
            padding-top: 0px !important;
            padding-bottom: 0px !important;
            padding-left: 6px !important;
            padding-right: 6px !important;
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
        <!-- End Loading -->
        <section>
            <div class="card pe-2">
                <div class="card-header text-center text-dark ms-2">
                    <h4 class="fw-bold">Data Kartu Stock Produk</h4>
                    <div>
                        <h6>PT. Karsa Mandiri Alkesindo</h6> 
                    </div>
                </div>
                <?php  
                    require_once "function/function-enkripsi.php";
                    $key = "";
                    $jenis_produk = $_GET['jenis'];
                    $active_reg = "";
                    $active_ecat = "";
                    if($jenis_produk == "set-reg"){
                        $active_reg = "active";
                        require_once "query/data-kartu-stock-produk-set-reg.php";
                    } else if($jenis_produk == "set-ecat"){
                        $active_ecat = "active";
                        require_once "query/data-kartu-stock-produk-set-ecat.php";
                    } else {
                        ?>
                            <script>
                                // Mengarahkan pengguna ke halaman 404.php
                                window.location.replace("404.php");
                            </script>
                        <?php
                    }
                ?>
                <div class="d-flex justify-content-around flex-wrap mt-3">
                    <div class="card p-4" style="min-width: 320px;">
                        <div class="mb-3">
                            <label for="kat_produk">Pilih kategori produk :</label>
                            <select name="kat_produk" id="kat_produk" class="form-select" multiple>
                                <option value="">Pilih...</option>
                                <?php  
                                    while($data_kategori_produk = mysqli_fetch_array($query_kategori_produk)){
                                ?>
                                    <option value="<?php echo $data_kategori_produk['nama_kategori'] ?>"><?php echo $data_kategori_produk['nama_kategori'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <dib class="border p-3">
                            <div class="d-flex justify-content-evenly align-items-center">
                                <div class="text-center"><i class="bi bi-grid" style="color: blue; font-size: 50px;"></i></div>
                                <div class="text-center">
                                    <p class="fw-bold text-center" id="total_data_kat_produk"></p>
                                    <p class="text-center">Tersedia</p>
                                </div>
                            </div>
                        </dib>
                    </div>
                    <div class="card p-4" style="min-width: 320px;">
                        <div class="mb-3">
                            <label for="lokasi">Pilih lokasi :</label>
                            <select name="lokasi" id="lokasi" class="form-select" multiple>
                                <option value="">Pilih...</option>
                                <?php  
                                    while($data_nama_lokasi = mysqli_fetch_array($query_nama_lokasi)){
                                ?>
                                    <option value="<?php echo $data_nama_lokasi['nama_lokasi'] ?>"><?php echo $data_nama_lokasi['nama_lokasi'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <dib class="border p-3">
                            <div class="d-flex justify-content-evenly align-items-center">
                                <div class="text-center"><i class="bi bi-geo-alt" style="color: blue; font-size: 50px;"></i></div>
                                <div class="text-center">
                                    <p class="fw-bold text-center" id="total_data_lokasi"></p>
                                    <p class="text-center">Tersedia</p>
                                </div>
                            </div>
                        </dib>
                    </div>
                    <div class="card p-4" style="min-width: 320px;">
                        <div class="mb-3">
                            <label for="lantai">Pilih lantai :</label>
                            <select name="lantai" id="lantai" class="form-select" multiple>
                                <option value="">Pilih lantai...</option>
                                <?php  
                                    while($data_no_lantai = mysqli_fetch_array($query_no_lantai)){
                                ?>
                                    <option value="<?php echo $data_no_lantai['no_lantai'] ?>"><?php echo $data_no_lantai['no_lantai'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <dib class="border p-3">
                            <div class="d-flex justify-content-evenly align-items-center">
                                <div class="text-center"><i class="bi bi-map" style="color: blue; font-size: 50px;"></i></div>
                                <div class="text-center">
                                    <p class="fw-bold text-center" id="total_data_lantai"></p>
                                    <p class="text-center">Tersedia</p>
                                </div>
                            </div>
                        </dib>
                    </div>
                    <div class="card p-4" style="min-width: 320px;">
                        <div class="mb-3">
                            <label for="lev_stock">Pilih level stock :</label>
                            <select name="lev_stock" id="lev_stock" class="form-select" multiple>
                                <option value="">Pilih level stock...</option>
                                <option value="Low">Low</option>
                                <option value="Very Low">Very Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Very High">Very High</option>
                            </select>
                        </div>
                        <dib class="border p-3">
                            <div class="d-flex justify-content-evenly align-items-center">
                                <div class="text-center"><i class="bi bi-graph-down-arrow" style="color: blue; font-size: 50px;"></i></div>
                                <div class="text-center">
                                    <p class="fw-bold text-center" id="total_data_lev_stock"></p>
                                    <p class="text-center">Tersedia</p>
                                </div>
                            </div>
                        </dib>
                    </div>
                </div>
                <div class="mt-3" style="margin-left: 12px;">
                    <a href="data-kartu-stock-produk-set.php?jenis=set-reg" class="btn btn-outline-success mobile load-page <?php echo $active_reg ?>"><i class="bi bi-box-seam"></i> Produk Reguler</a>
                    <a href="data-kartu-stock-produk-set.php?jenis=set-ecat" class="btn btn-outline-success mobile load-page <?php echo $active_ecat ?>"><i class="bi bi-box-seam-fill"></i> Produk E-Catalog</a>
                </div>
                <div id="info"></div>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-striped" id="tableExport">
                        <thead>
                            <tr>
                                <th class="p-3 text-nowrap">No</th>
                                <th class="p-3 text-nowrap">Kode Produk</th>
                                <th class="p-3 text-nowrap">Nama Produk</th>
                                <th class="p-3 text-nowrap">Merk</th>
                                <th class="p-3 text-nowrap">Kategori Produk</th>
                                <th class="p-3 text-nowrap">Lokasi</th>
                                <th class="p-3 text-nowrap">Lantai</th>
                                <th class="p-3 text-nowrap">Rak</th>
                                <th class="p-3 text-nowrap">Stock</th>
                                <th class="p-3 text-nowrap">Level Stock</th>
                                <th class="p-3 text-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                include "function/class-function.php";
                                $no = 1;
                                // Inisialisasi total masuk dan total keluar
                                while ($data_ready_stock =  mysqli_fetch_array($query_produk)){
                                    $id_kartu_stock = $data_ready_stock['id_kartu_stock'];
                                    $id_kartu_stock_encrypt = encrypt($id_kartu_stock, $key);
                                    $qty_in = $data_ready_stock['total_in'];
                                    $qty_out = $data_ready_stock['total_out'];

                                    // Menghitung jumlah akhir
                                    $jumlah_akhir = $qty_in - $qty_out;
                                    $stockData = StockStatus::getStatus($jumlah_akhir, $data_ready_stock['min_stock_ready'], $data_ready_stock['max_stock_ready']);
                            ?>
                            <tr>
                                <td class="text-nowrap text-center"><?php echo $no; ?></td>
                                <td class="text-nowrap text-center"><?php echo $data_ready_stock['kode_produk'] ?></td>
                                <td class="text-nowrap"><?php echo $data_ready_stock['nama_produk'] ?></td>
                                <td class="text-nowrap text-center"><?php echo $data_ready_stock['nama_merk'] ?></td>
                                <td class="text-nowrap text-center"><?php echo $data_ready_stock['nama_kategori'] ?></td>
                                <td class="text-nowrap text-end"><?php echo $data_ready_stock['nama_lokasi'] ?></td>
                                <td class="text-nowrap text-center"><?php echo $data_ready_stock['no_lantai'] ?></td>
                                <td class="text-nowrap text-end"><?php echo $data_ready_stock['no_rak'] ?></td>
                                <?php echo "<td class='text-end text-nowrap " . $stockData['textColor'] . "' style='background-color: " . $stockData['backgroundColor'] . "'>" . $stockData['formattedStock'] . "</td>"; ?>
                                <?php echo "<td class='text-end text-nowrap'>" . $stockData['status'] . "</td>"; ?>
                                <td class="text-nowrap text-center">
                                    <button class="btn btn-primary btn-sm"><i class="bi bi-eye-fill"></i></button>
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
    <!-- Footer -->
    <?php include "page/footer.php" ?>
    <!-- End Footer -->
    <?php include "page/script.php"; ?>
</body>
</html>
<script>
    // Multiple Filter kategori produk
    $(document).ready(function() {
        var table = $("#tableExport").DataTable();

        // Menambahkan ekstensi DataTables untuk menyaring baris berdasarkan nilai pada kolom "created_by"
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var selectedItem = $("#kat_produk").val();
            var dataFilter = data[4];
            
            // Jika tidak ada item yang dipilih, tampilkan semua baris
            if (!selectedItem || selectedItem.length === 0) {
                return true;
            }

            // Periksa apakah nilai "created_by" ada dalam nilai yang dipilih
            if (selectedItem.includes(dataFilter)) {
                return true;
            }

            return false;
        });

        // Inisialisasi Selectize setelah DataTables selesai memuat
        $("#kat_produk").selectize({
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
                
                // Update info jumlah data yang ditampilkan
                updateInfo();
            },
        });

        // Gambar tabel pada saat memuat halaman
        table.draw();

        // Fungsi untuk mengupdate informasi jumlah data yang ditampilkan
        function updateInfo() {
            var info = table.page.info();
            var recordsDisplay = info.recordsDisplay;
            $("#total_data_kat_produk").text(recordsDisplay + " Produk");
        }

        // Memanggil updateInfo() saat halaman dimuat pertama kali
        updateInfo();
    });


    // Multiple Filter nama lokasi produk
    $(document).ready(function() {
        var table = $("#tableExport").DataTable();

        // Menambahkan ekstensi DataTables untuk menyaring baris berdasarkan nilai pada kolom "created_by"
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var selectedItem = $("#lokasi").val();
            var dataFilter = data[5];
               
            // Jika tidak ada item yang dipilih, tampilkan semua baris
            if (!selectedItem || selectedItem.length === 0) {
                return true;
            }

            // Periksa apakah nilai "created_by" ada dalam nilai yang dipilih
            if (selectedItem.includes(dataFilter)) {
                return true;
            }

            return false;
        });

        // Inisialisasi Selectize setelah DataTables selesai memuat
        $("#lokasi").selectize({
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

                 // Update info jumlah data yang ditampilkan
                 updateInfo();
            },
            onDropdownOpen: function($dropdown) {
                // Jangan lakukan apa pun ketika dropdown dibuka
                // (kode yang dijalankan setelah pemilihan data)
            }
        });

        // Gambar tabel pada saat memuat halaman
        table.draw();

        // Fungsi untuk mengupdate informasi jumlah data yang ditampilkan
        function updateInfo() {
            var info = table.page.info();
            var recordsDisplay = info.recordsDisplay;
            $("#total_data_lokasi").text(recordsDisplay + " Produk");
        }

        // Memanggil updateInfo() saat halaman dimuat pertama kali
        updateInfo();
    });

    // Multiple Filter nama lokasi produk
    $(document).ready(function() {
        var table = $("#tableExport").DataTable();

        // Menambahkan ekstensi DataTables untuk menyaring baris berdasarkan nilai pada kolom "created_by"
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var selectedItem = $("#lantai").val();
            var dataFilter = data[6];
               
            // Jika tidak ada item yang dipilih, tampilkan semua baris
            if (!selectedItem || selectedItem.length === 0) {
                return true;
            }

            // Periksa apakah nilai "created_by" ada dalam nilai yang dipilih
            if (selectedItem.includes(dataFilter)) {
                return true;
            }

            return false;
        });

        // Inisialisasi Selectize setelah DataTables selesai memuat
        $("#lantai").selectize({
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

                // Memanggil updateInfo() saat halaman dimuat pertama kali
                updateInfo();
            },
            onDropdownOpen: function($dropdown) {
                // Jangan lakukan apa pun ketika dropdown dibuka
                // (kode yang dijalankan setelah pemilihan data)
            }
        });

        // Gambar tabel pada saat memuat halaman
        table.draw();

        // Fungsi untuk mengupdate informasi jumlah data yang ditampilkan
        function updateInfo() {
            var info = table.page.info();
            var recordsDisplay = info.recordsDisplay;
            $("#total_data_lantai").text(recordsDisplay + " Produk");
        }

        // Memanggil updateInfo() saat halaman dimuat pertama kali
        updateInfo();
    });

    $(document).ready(function() {
        var table = $("#tableExport").DataTable();

        // Menambahkan ekstensi DataTables untuk menyaring baris berdasarkan nilai pada kolom "created_by"
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var selectedItem = $("#lev_stock").val();
            var dataFilter = data[9];
               
            // Jika tidak ada item yang dipilih, tampilkan semua baris
            if (!selectedItem || selectedItem.length === 0) {
                return true;
            }

            // Periksa apakah nilai "created_by" ada dalam nilai yang dipilih
            if (selectedItem.includes(dataFilter)) {
                return true;
            }

            return false;
        });

        // Inisialisasi Selectize setelah DataTables selesai memuat
        $("#lev_stock").selectize({
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

                // Memanggil updateInfo() saat halaman dimuat pertama kali
                updateInfo();
            },
            onDropdownOpen: function($dropdown) {
                // Jangan lakukan apa pun ketika dropdown dibuka
                // (kode yang dijalankan setelah pemilihan data)
            }
        });

        // Gambar tabel pada saat memuat halaman
        table.draw();

        // Fungsi untuk mengupdate informasi jumlah data yang ditampilkan
        function updateInfo() {
            var info = table.page.info();
            var recordsDisplay = info.recordsDisplay;
            $("#total_data_lev_stock").text(recordsDisplay + " Produk");
        }

        // Memanggil updateInfo() saat halaman dimuat pertama kali
        updateInfo();
    });
</script>