<?php
require_once "../akses.php";
$page = 'spk';
$page_nav = 'diterima'; 
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
        table{
            padding: 0 !important;
        }
        @media (max-width: 767px) {

            /* Tambahkan aturan CSS khusus untuk tampilan mobile di bawah 767px */
            .col-12.col-md-2 {
                /* Contoh: Mengatur tinggi elemen select pada tampilan mobile */
                height: 50px;
            }
        }
        .btn.active {
            background-color: black;
            color: white;
            border-color: 1px solid white;
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
            <h1>Data SPK</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">SPK</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section>
            <!-- SWEET ALERT -->
            <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
            <!-- END SWEET ALERT -->
            <div class="card">
                <div class="mt-4">
                    <!-- Tampilkan navbar spk -->
                    <?php include "page/navbar-spk.php" ?> 
                    <div class="card-body bg-body rounded mt-3">
                        <button class="btn btn-outline-dark mb-3" id="btnNonPpn">
                            Invoice Non PPN &nbsp;
                            <?php if ($total_inv_nonppn_diterima != 0) {
                                echo '<span class="badge text-bg-secondary">' . $total_inv_nonppn_diterima . '</span>';
                            } ?>
                        </button>

                        <button class="btn btn-outline-dark mb-3" id="btnPpn">
                            Invoice PPN &nbsp;
                            <?php if ($total_inv_ppn_diterima != 0) {
                                echo '<span class="badge text-bg-secondary">' . $total_inv_ppn_diterima . '</span>';
                            } ?>
                        </button>

                        <button class="btn btn-outline-dark mb-3" id="btnBum">
                            Invoice BUM &nbsp;
                            <?php if ($total_inv_bum_diterima != 0) {
                                echo '<span class="badge text-bg-secondary">' . $total_inv_bum_diterima . '</span>';
                            } ?>
                        </button>
                        <!-- Data Nonppn -->
                        <div class="d-none" id="nonppn">
                            <div class="table-responsive m-2" id="filteredDataNonPpn">
                                <form id="invoiceForm" name="proses" method="POST">
                                    <div class="row mb-3 mt-4">
                                        <div class="col-md-2">
                                            <form action="" method="GET">
                                                <select name="sort" class="form-select" id="select" aria-label="Default select example" onchange="filterDataNonPpn()">
                                                    <option value="baru" <?php if (isset($_GET['sort']) && $_GET['sort'] == "baru") {
                                                                                echo "selected";
                                                                            } ?>>Paling Baru</option>
                                                    <option value="lama" <?php if (isset($_GET['sort']) && $_GET['sort'] == "lama") {
                                                                                echo "selected";
                                                                            } ?>>Paling Lama</option>
                                                </select>

                                            </form>
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped" id="table2">
                                        <thead>
                                            <tr class="text-white" style="background-color: navy;">
                                                <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">No. Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Tgl. Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 150px">No. PO</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Customer</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Kat. Inv</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Note Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Jenis Pengiriman</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 80px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $filter = '';
                                            if (isset($_GET['sort'])) {
                                                if ($_GET['sort'] == "baru") {
                                                    $filter = "ORDER BY no_inv DESC";
                                                } elseif ($_GET['sort'] == "lama") {
                                                    $filter = "ORDER BY no_inv ASC";
                                                }
                                            }
                                            $sql = "SELECT 
                                                        nonppn.id_inv_nonppn, 
                                                        nonppn.no_inv, 
                                                        nonppn.tgl_inv, 
                                                        nonppn.kategori_inv, 
                                                        nonppn.note_inv, 
                                                        sr.no_po, 
                                                        cs.nama_cs, 
                                                        sk.jenis_pengiriman,
                                                        sk.dikirim_driver,
                                                        sk.dikirim_ekspedisi,
                                                        us.nama_user,
                                                        ex.nama_ekspedisi
                                                    FROM inv_nonppn AS nonppn
                                                    LEFT JOIN spk_reg sr ON(nonppn.id_inv_nonppn = sr.id_inv)
                                                    LEFT JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                    LEFT JOIN status_kirim sk ON(nonppn.id_inv_nonppn = sk.id_inv)
                                                    LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                                                    LEFT JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                    WHERE status_transaksi = 'Diterima' GROUP BY no_inv  $filter";
                                            $query = mysqli_query($connect, $sql);
                                            while ($data = mysqli_fetch_array($query)) {
                                            ?>
                                                <tr>
                                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['no_inv'] ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['tgl_inv'] ?></td>
                                                    <td class="text-center text-nowrap">
                                                        <?php 
                                                            if(!empty($data['no_po'])){
                                                                echo $data['no_po'];
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['kategori_inv'] ?></td>
                                                    <td>
                                                        <?php
                                                            $note = $data['note_inv'];

                                                            $items = explode("\n", trim($note));

                                                            if(!empty($note)){
                                                                foreach ($items as $notes) {
                                                                    echo trim($notes) . '<br>';
                                                                }
                                                            }else{
                                                                echo 'Tidak Ada';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <?php 
                                                            if($data['jenis_pengiriman'] == "Driver"){
                                                                echo $data['jenis_pengiriman']."<br>";
                                                                echo "(".$data['nama_user'].")";
                                                            } else if($data['jenis_pengiriman'] == "Ekspedisi"){
                                                                echo $data['jenis_pengiriman']."<br>";
                                                                echo "(".$data['nama_ekspedisi'].")";
                                                            } else {
                                                                echo $data['jenis_pengiriman']."<br>";
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <a href="detail-produk-diterima.php?jenis=nonppn&&id=<?php echo encrypt($data['id_inv_nonppn'], $key_global) ?>" class="btn btn-primary btn-sm mb-2" title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                                                    </td>
                                                </tr>
                                                <?php $no++ ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                        <!-- Data PPN -->
                        <div class="d-none" id="ppn">
                            <div class="table-responsive m-2" id="filteredDataPpn">
                                <form id="invoiceForm" name="proses" method="POST">
                                    <div class="row mb-3 mt-4">
                                        <div class="col-md-2">
                                            <form action="" method="GET">
                                                <select name="sort" class="form-select" id="select_ppn" aria-label="Default select example" onchange="filterDataPpn()">
                                                    <option value="baru" <?php if (isset($_GET['sort']) && $_GET['sort'] == "baru") {
                                                                                echo "selected";
                                                                            } ?>>Paling Baru</option>
                                                    <option value="lama" <?php if (isset($_GET['sort']) && $_GET['sort'] == "lama") {
                                                                                echo "selected";
                                                                            } ?>>Paling Lama</option>
                                                </select>

                                            </form>
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped" id="table3">
                                        <thead>
                                            <tr class="text-white" style="background-color: navy;">
                                                <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">No. Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Tgl. Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 150px">No. PO</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Customer</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Kat. Inv</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Note Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Jenis Pengiriman</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 80px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $filter = '';
                                            if (isset($_GET['sort'])) {
                                                if ($_GET['sort'] == "baru") {
                                                    $filter = "ORDER BY no_inv DESC";
                                                } elseif ($_GET['sort'] == "lama") {
                                                    $filter = "ORDER BY no_inv ASC";
                                                }
                                            }
                                            $sql = "SELECT 
                                                        ppn.id_inv_ppn, 
                                                        ppn.no_inv, 
                                                        ppn.tgl_inv, 
                                                        ppn.kategori_inv, 
                                                        ppn.note_inv, 
                                                        sr.no_po, 
                                                        cs.nama_cs, 
                                                        sk.jenis_pengiriman,
                                                        sk.dikirim_driver,
                                                        sk.dikirim_ekspedisi,
                                                        us.nama_user,
                                                        ex.nama_ekspedisi
                                                    FROM inv_ppn AS ppn
                                                    LEFT JOIN spk_reg sr ON(ppn.id_inv_ppn = sr.id_inv)
                                                    LEFT JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                    LEFT JOIN status_kirim sk ON(ppn.id_inv_ppn = sk.id_inv)
                                                    LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                                                    LEFT JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                    WHERE status_transaksi = 'Diterima' GROUP BY no_inv  $filter";
                                            $query = mysqli_query($connect, $sql);
                                            while ($data = mysqli_fetch_array($query)) {
                                            ?>
                                                <tr>
                                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['no_inv'] ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['tgl_inv'] ?></td>
                                                    <td class="text-center text-nowrap">
                                                        <?php 
                                                            if(!empty($data['no_po'])){
                                                                echo $data['no_po'];
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['kategori_inv'] ?></td>
                                                    <td>
                                                        <?php
                                                            $note = $data['note_inv'];

                                                            $items = explode("\n", trim($note));

                                                            if(!empty($note)){
                                                                foreach ($items as $notes) {
                                                                    echo trim($notes) . '<br>';
                                                                }
                                                            }else{
                                                                echo 'Tidak Ada';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <?php 
                                                            if($data['jenis_pengiriman'] == "Driver"){
                                                                echo $data['jenis_pengiriman']."<br>";
                                                                echo "(".$data['nama_user'].")";
                                                            } else if($data['jenis_pengiriman'] == "Ekspedisi"){
                                                                echo $data['jenis_pengiriman']."<br>";
                                                                echo "(".$data['nama_ekspedisi'].")";
                                                            } else {
                                                                echo $data['jenis_pengiriman']."<br>";
                                                            }
                                                        
                                                        ?>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <a href="detail-produk-diterima.php?jenis=ppn&&id=<?php echo encrypt($data['id_inv_ppn'], $key_global) ?>" class="btn btn-primary btn-sm mb-2" title="Lihat Produk"><i class="bi bi-eye-fill"></i> </a>
                                                    </td>
                                                </tr>
                                                <?php $no++ ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                        <!-- Data BUM -->
                        <div class="d-none" id="bum">
                            <div class="table-responsive m-2" id="filteredDataBum">
                                <form id="invoiceForm" name="proses" method="POST">
                                    <div class="row mb-3 mt-4">
                                        <div class="col-md-2">
                                            <form action="" method="GET">
                                                <select name="sort" class="form-select" id="select_bum" aria-label="Default select example" onchange="filterDataBum()">
                                                    <option value="baru" <?php if (isset($_GET['sort']) && $_GET['sort'] == "baru") {
                                                                                echo "selected";
                                                                            } ?>>Paling Baru</option>
                                                    <option value="lama" <?php if (isset($_GET['sort']) && $_GET['sort'] == "lama") {
                                                                                echo "selected";
                                                                            } ?>>Paling Lama</option>
                                                </select>

                                            </form>
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-striped" id="table4">
                                        <thead>
                                            <tr class="text-white" style="background-color: navy;">
                                                <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">No. Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Tgl. Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 150px">No. PO</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Customer</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Kat. Inv</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 250px">Note Invoice</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 100px">Jenis Pengiriman</th>
                                                <th class="text-center p-3 text-nowrap" style="width: 80px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            $filter = '';
                                            if (isset($_GET['sort'])) {
                                                if ($_GET['sort'] == "baru") {
                                                    $filter = "ORDER BY no_inv DESC";
                                                } elseif ($_GET['sort'] == "lama") {
                                                    $filter = "ORDER BY no_inv ASC";
                                                }
                                            }
                                            $sql = "SELECT 
                                                        bum.id_inv_bum, 
                                                        bum.no_inv, 
                                                        bum.tgl_inv, 
                                                        bum.kategori_inv, 
                                                        bum.note_inv, 
                                                        sr.no_po, 
                                                        cs.nama_cs, 
                                                        sk.jenis_pengiriman,
                                                        sk.dikirim_driver,
                                                        sk.dikirim_ekspedisi,
                                                        us.nama_user,
                                                        ex.nama_ekspedisi
                                                    FROM inv_bum AS bum
                                                    LEFT JOIN spk_reg sr ON(bum.id_inv_bum = sr.id_inv)
                                                    LEFT JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                    LEFT JOIN status_kirim sk ON(bum.id_inv_bum = sk.id_inv)
                                                    LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                                                    LEFT JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                                                    WHERE status_transaksi = 'Diterima' GROUP BY no_inv $filter";
                                            $query = mysqli_query($connect, $sql);
                                            while ($data = mysqli_fetch_array($query)) {
                                            ?>
                                                <tr>
                                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['no_inv'] ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['tgl_inv'] ?></td>
                                                    <td class="text-center text-nowrap">
                                                        <?php 
                                                            if(!empty($data['no_po'])){
                                                                echo $data['no_po'];
                                                            } else {
                                                                echo '-';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                                                    <td class="text-center text-nowrap"><?php echo $data['kategori_inv'] ?></td>
                                                    <td>
                                                        <?php
                                                            $note = $data['note_inv'];

                                                            $items = explode("\n", trim($note));

                                                            if(!empty($note)){
                                                                foreach ($items as $notes) {
                                                                    echo trim($notes) . '<br>';
                                                                }
                                                            }else{
                                                                echo 'Tidak Ada';
                                                            }
                                                        ?>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <?php 
                                                            if($data['jenis_pengiriman'] == "Driver"){
                                                                echo $data['jenis_pengiriman']."<br>";
                                                                echo "(".$data['nama_user'].")";
                                                            } else if($data['jenis_pengiriman'] == "Ekspedisi"){
                                                                echo $data['jenis_pengiriman']."<br>";
                                                                echo "(".$data['nama_ekspedisi'].")";
                                                            } else {
                                                                echo $data['jenis_pengiriman']."<br>";
                                                            }
                                                        
                                                        ?>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <a href="detail-produk-diterima.php?jenis=bum&&id=<?php echo encrypt($data['id_inv_bum'], $key_global) ?>" class="btn btn-primary btn-sm mb-2" title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                                                    </td>
                                                </tr>
                                                <?php $no++ ?>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Diterima -->
                    <!-- ================================================ -->
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
<!-- Script untuk mengatur hide and show div -->
<script>
    // Ambil elemen tombol dan div
    const buttons = {
        btnNonPpn: document.getElementById('btnNonPpn'),
        btnPpn: document.getElementById('btnPpn'),
        btnBum: document.getElementById('btnBum')
    };

    const divs = {
        nonppn: document.getElementById('nonppn'),
        ppn: document.getElementById('ppn'),
        bum: document.getElementById('bum')
    };

    // Fungsi untuk menyembunyikan semua div dan menghapus kelas 'active' dari semua tombol
    function resetButtonsAndDivs() {
        Object.values(divs).forEach(div => div.classList.add('d-none'));
        Object.values(buttons).forEach(button => button.classList.remove('active'));
    }

    // Fungsi untuk menampilkan div yang sesuai dan mengaktifkan tombol yang sesuai
    function showDiv(button, div) {
        resetButtonsAndDivs(); // Reset semua div dan tombol
        div.classList.remove('d-none'); // Tampilkan div yang sesuai
        div.classList.add('d-block');
        button.classList.add('active'); // Aktifkan tombol yang diklik
    }

    // Tambahkan event listener ke setiap tombol
    buttons.btnNonPpn.addEventListener('click', function () {
        showDiv(buttons.btnNonPpn, divs.nonppn);
    });

    buttons.btnPpn.addEventListener('click', function () {
        showDiv(buttons.btnPpn, divs.ppn);
    });

    buttons.btnBum.addEventListener('click', function () {
        showDiv(buttons.btnBum, divs.bum);
    });
</script>

<script>
    $(document).ready(function() {
        $("#select").change(function() {
            var open = $(this).data("isopen");
            if (open) {
                window.location.href = $(this).val();
            }
            //set isopen to opposite so next time when user clicks select box
            //it won't trigger this event
            $(this).data("isopen", !open);
        });
    });
</script>

<script>
    // Filter Non PPN
    // Fungsi untuk mengirim permintaan AJAX
    function filterDataNonPpn() {
        // Ambil nilai filter dari elemen select
        var sortValue = document.getElementById('select').value;

        // Buat objek XMLHttpRequest
        var xhttp = new XMLHttpRequest();

        // Atur callback function untuk menangani perubahan status permintaan
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Update elemen filteredData dengan hasil filter yang diterima dari server
                document.getElementById('filteredDataNonPpn').innerHTML = this.responseText;
                // Inisialisasi ulang DataTable setelah mengganti isi tabel
                filter_nonppn();
            }
        };

        // Buat permintaan GET ke file PHP yang akan memproses filter
        xhttp.open('GET', 'filter-data-nonppn-diterima.php?sort=' + sortValue, true);
        xhttp.send();
    }

    // Filter BUM
    // Fungsi untuk mengirim permintaan AJAX
    function filterDataPpn() {
        // Ambil nilai filter dari elemen select
        var sortValue = document.getElementById('select_ppn').value;

        // Buat objek XMLHttpRequest
        var xhttp = new XMLHttpRequest();

        // Atur callback function untuk menangani perubahan status permintaan
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Update elemen filteredData dengan hasil filter yang diterima dari server
                document.getElementById('filteredDataPpn').innerHTML = this.responseText;

                // Inisialisasi ulang DataTable setelah mengganti isi tabel
                filter_ppn();
            }
        };

        // Buat permintaan GET ke file PHP yang akan memproses filter
        xhttp.open('GET', 'filter-data-ppn-diterima.php?sort=' + sortValue, true);
        xhttp.send();
    }

    // Filter BUM
    // Fungsi untuk mengirim permintaan AJAX
    function filterDataBum() {
        // Ambil nilai filter dari elemen select
        var sortValue = document.getElementById('select_bum').value;

        // Buat objek XMLHttpRequest
        var xhttp = new XMLHttpRequest();

        // Atur callback function untuk menangani perubahan status permintaan
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                // Update elemen filteredData dengan hasil filter yang diterima dari server
                document.getElementById('filteredDataBum').innerHTML = this.responseText;

                // Inisialisasi ulang DataTable setelah mengganti isi tabel
                filter_bum();
            }
        };

        // Buat permintaan GET ke file PHP yang akan memproses filter
        xhttp.open('GET', 'filter-data-bum-diterima.php?sort=' + sortValue, true);
        xhttp.send();
    }
</script>
