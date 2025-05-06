<?php
include "akses.php";
$page  = 'transaksi';
$page2 = 'spk';
$page_nav = 'diterima'; 
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

    <style>
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
    <div class="table-responsive" id="filteredDataBum">
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
        <table class="table table-bordered table-striped" id="filter_bum">
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
                include "koneksi.php";
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
                        LEFT JOIN $database2.user AS us ON (sk.dikirim_driver = us.id_user)
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
                            <a href="detail-produk-dikirim.php?jenis=bum&&id=<?php echo encrypt($data['id_inv_bum'], $key_global) ?>" class="btn btn-primary btn-sm mb-2" title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                        </td>
                    </tr>
                    <?php $no++ ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include "page/script.php" ?>
    <script>
        // Fungsi untuk mengirim permintaan AJAX
        function filterDataBum() {
            var sortValue = document.getElementById('select_bum').value;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById('filteredDataBum').innerHTML = this.responseText;

                    // Inisialisasi ulang DataTable setelah mengganti isi tabel
                    filter_bum();
                }
            };

            xhttp.open('GET', 'filter-data-bum-dikirim.php?sort=' + sortValue, true);
            xhttp.send();
        }
    </script>
</body>

</html>