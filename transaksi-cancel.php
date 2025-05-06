<?php
$page  = 'transaksi';
$page2 = 'spk';
$page_nav  = 'cancel';
include "akses.php";
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
        <!--<div class="loader loader">-->
        <!--    <div class="loading">-->
        <!--        <img src="img/loading.gif" width="200px" height="auto">-->
        <!--    </div>-->
        <!--</div>-->
        <!-- ENd Loading -->
        <div class="pagetitle">
            <h1>Data SPK</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">SPK</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section>
            <div class="card">
                <div class="mt-3">
                    <!-- Tampilkan navbar spk -->
                    <?php include "page/navbar-spk.php" ?> 
                    <div class="card-body bg-body rounded mt-3">
                        <div class="card-body pt-3">
                            <div class="row mb-3">
                                <div class="col-sm-2">
                                    <form action="" method="GET">
                                        <select name="sort" class="form-select" id="select" aria-label="Default select example" onchange='if(this.value != 0) { this.form.submit(); }'>
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
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="table2">
                                    <thead>
                                        <tr class="text-white" style="background-color: navy;">
                                            <th class="text-center p-3" style="width: 30px">No</th>
                                            <th class="text-center p-3" style="width: 100px">No. SPK</th>
                                            <th class="text-center p-3" style="width: 150px">Tgl. SPK</th>
                                            <th class="text-center p-3" style="width: 250px">Nama Customer</th>
                                            <th class="text-center p-3" style="width: 150px">Alasan</th>
                                            <th class="text-center p-3" style="width: 150px">Posisi Transaksi</th>
                                            <th class="text-center p-3" style="width: 80px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include "koneksi.php";
                                        $no = 1;
                                        $filter = '';
                                        if (isset($_GET['sort'])) {
                                            if ($_GET['sort'] == "baru") {
                                                $filter = "ORDER BY SUBSTRING_INDEX(no_spk, '/', -1) DESC, 
                                                           CAST(SUBSTRING_INDEX(no_spk, '/', 1) AS UNSIGNED) DESC";
                                            } elseif ($_GET['sort'] == "lama") {
                                                $filter = "ORDER BY SUBSTRING_INDEX(no_spk, '/', -1) ASC, 
                                                           CAST(SUBSTRING_INDEX(no_spk, '/', 1) AS UNSIGNED) ASC";
                                            }
                                        }
                                        $sql = "SELECT 
                                                    id_spk_reg,
                                                    no_spk,
                                                    tgl_spk,
                                                    no_po,
                                                    menu_cancel,
                                                    user_cancel,
                                                    note,
                                                    nama_cs, 
                                                    alamat,
                                                    no_inv
                                                FROM (
                                                    SELECT 
                                                        sr.id_spk_reg,
                                                        sr.no_spk,
                                                        sr.tgl_spk,   
                                                        sr.no_po,
                                                        sr.menu_cancel,
                                                        sr.user_cancel,
                                                        sr.note,
                                                        cs.nama_cs, 
                                                        cs.alamat,
                                                        '' AS no_inv  
                                                    FROM spk_reg AS sr
                                                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                    WHERE sr.status_spk = 'Cancel Order' AND sr.id_inv = ''
                                                    UNION
                                                    SELECT 
                                                        MAX(sr.id_spk_reg) AS id_spk_reg,
                                                        GROUP_CONCAT(CONCAT(sr.no_spk, ', ') SEPARATOR '') AS no_spk,
                                                        MAX(sr.tgl_spk) AS tgl_spk,
                                                        MAX(sr.no_po) AS no_po,
                                                        MAX(sr.menu_cancel) AS menu_cancel,
                                                        MAX(sr.user_cancel) AS user_cancel,
                                                        MAX(sr.note) AS note,
                                                        MAX(cs.nama_cs) AS nama_cs, 
                                                        MAX(cs.alamat) AS alamat,
                                                        COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv
                                                    FROM spk_reg AS sr
                                                    LEFT JOIN tb_customer cs ON sr.id_customer = cs.id_cs
                                                    LEFT JOIN inv_nonppn nonppn ON sr.id_inv = nonppn.id_inv_nonppn
                                                    LEFT JOIN inv_ppn ppn ON sr.id_inv = ppn.id_inv_ppn
                                                    LEFT JOIN inv_bum bum ON sr.id_inv = bum.id_inv_bum
                                                    WHERE sr.status_spk = 'Cancel Order' AND sr.id_inv != ''
                                                    GROUP BY COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv)
                                                ) AS subquery
                                                $filter";
                                        $query = mysqli_query($connect, $sql);
                                        while ($data = mysqli_fetch_array($query)) {
                                            // Hilangkan tanda koma di bagian akhir data
                                            $no_spk_result = $data['no_spk'];
                                            // Menghilangkan tanda koma di akhir
                                            $no_spk_formatted = trim($no_spk_result, ', ');

                                            // Pisahkan data berdasarkan koma
                                            $no_spk_array = explode(', ', $no_spk_formatted);

                                            // Tentukan jumlah data yang diinginkan untuk perbarisannya
                                            $jumlah_data_per_baris = 2;

                                            // Inisialisasi variabel untuk menyimpan hasil
                                            $no_spk_final = '';

                                            // Loop melalui array data
                                            for ($i = 0; $i < count($no_spk_array); $i++) {
                                                // Tambahkan data ke hasil dengan tanda koma
                                                $no_spk_final .= $no_spk_array[$i];

                                                // Tambahkan <br> jika jumlah data mencapai batas tertentu dan bukan data terakhir
                                                if (($i + 1) % $jumlah_data_per_baris == 0 && $i < count($no_spk_array) - 1) {
                                                    $no_spk_final .= "<br>";
                                                } else {
                                                    // Tambahkan koma dan spasi setelah data, kecuali untuk data terakhir
                                                    $no_spk_final .= ($i < count($no_spk_array) - 1) ? ', ' : '';
                                                }
                                            }
                                        ?>
                                            <tr>
                                                <td class="text-center"><?php echo $no; ?></td>
                                                <td class="text-center text-nowrap">
                                                    <?php echo $no_spk_final ?><br>
                                                    <?php
                                                        if($data['no_inv'] != ''){
                                                            echo '<b>(' .$data['no_inv'] .')</b>';
                                                        }
                                                    ?>
                                                </td>
                                                <td class="text-center text-nowrap"><?php echo $data['tgl_spk'] ?></td>
                                                <td><?php echo $data['nama_cs'] ?></td>
                                                <td><?php echo $data['note'] ?></td>
                                                <td>
                                                    <?php echo $data['menu_cancel'] ?><br>
                                                    (<?php echo $data['user_cancel'] ?>)
                                                </td>
                                                <td class="text-center">
                                                    <a href="detail-transaksi-cancel.php?id=<?php echo base64_encode($data['id_spk_reg']) ?>" class="btn btn-primary btn-sm mb-2" title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                                                </td>
                                            </tr>
                                            <?php $no++ ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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