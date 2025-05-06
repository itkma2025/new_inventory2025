<?php
require_once "akses.php";
$page  = 'transaksi';
$page2 = 'spk';
$page_nav  = 'dalam_proses';
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
           <!-- SWEET ALERT -->
            <?php
                if (isset($_SESSION['info'])) {
                    echo '<div class="info-data" data-infodata="' . $_SESSION['info'] . '"></div>';
                    unset($_SESSION['info']);
                }
            ?>
            <!-- END SWEET ALERT -->
            <div class="card">
                <div class="mt-4">
                    <!-- Tampilkan navbar spk -->
                    <?php include "page/navbar-spk.php" ?> 
                    <div class="card-body bg-body rounded mt-3">
                        <div class="card p-3 pt-3">
                            <div class="row mb-3">
                                <div class="col-12 col-md-2">
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
                                            <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                                            <th class="text-center p-3 text-nowrap">No. SPK</th>
                                            <th class="text-center p-3 text-nowrap">Tgl. SPK</th>
                                            <th class="text-center p-3 text-nowrap">No. PO</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Customer</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 300px">Note SPK</th>
                                            <th class="text-center p-3 text-nowrap">Status Validasi</th>
                                            <th class="text-center p-3 text-nowrap" style="width: 100px">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $filter = '';
                                        if (isset($_GET['sort'])) {
                                            if ($_GET['sort'] == "baru") {
                                                $filter = "ORDER BY tgl_spk DESC";
                                            } elseif ($_GET['sort'] == "lama") {
                                                $filter = "ORDER BY tgl_spk ASC";
                                            }
                                        }
                                        $sql = "SELECT 
                                                    sr.id_spk_reg, 
                                                    sr.tgl_spk, 
                                                    sr.no_spk, 
                                                    sr.no_po, 
                                                    sr.note, 
                                                    cs.nama_cs, 
                                                    cs.alamat
                                                FROM spk_reg AS sr
                                                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                WHERE status_spk = 'Dalam Proses'  $filter";
                                        $query = mysqli_query($connect, $sql);
                                        while ($data = mysqli_fetch_array($query)) {
                                            $id_spk = $data['id_spk_reg'];
                                            // Query untuk menghitung jumlah data dengan status ks = 2
                                            $trx_produk_status_ks = $connect->query("SELECT status_ks FROM transaksi_produk_reg WHERE id_spk = '$id_spk' AND status_ks = '2'");

                                            // Query untuk menghitung jumlah data produk
                                            $trx_produk = $connect->query("SELECT status_ks FROM transaksi_produk_reg WHERE id_spk = '$id_spk'");

                                            $total_status_ks_2 = mysqli_num_rows($trx_produk_status_ks);
                                            $total_produk = mysqli_num_rows($trx_produk);

                                            $text_status = "";
                                            $btn_color = "";
                                            if ($total_status_ks_2 == 0) {
                                                $text_status = "<b>Belum dilakukan validasi</b>";
                                                $btn_color = "btn-warning";
                                            } else if ($total_status_ks_2 == $total_produk) {
                                                $text_status = "<b>Sudah selesai validasi</b>";
                                                $btn_color = "btn-success";
                                            } else if ($total_status_ks_2 > 0) {
                                                $text_status = "<b>Sudah di validasi</b>";
                                                $btn_color = "btn-primary";
                                            }                                            
                                        ?>
                                            <tr>
                                                <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                                <td class="text-center text-nowrap"><?php echo $data['no_spk'] ?></td>
                                                <td class="text-center text-nowrap"><?php echo $data['tgl_spk'] ?></td>
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
                                                <td class="text-nowrap">
                                                    <?php
                                                        $note = $data['note'];

                                                        $items = explode("\n", trim($note));

                                                        if(!empty($note = $data['note'])){
                                                            foreach ($items as $notes) {
                                                                echo trim($notes) . '<br>';
                                                            }
                                                        }else{
                                                            echo 'Tidak Ada';
                                                        }
                                                    ?>
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    <button class="btn <?php echo $btn_color ?> btn-sm">
                                                        <?php echo $text_status ." (" . $total_status_ks_2 . "/" . $total_produk .")"?>
                                                </button>
                                                </td>
                                                <?php  
                                                    if ($role == "Super Admin" || $role == "Admin Gudang") {
                                                        ?>
                                                            <td class="text-center text-nowrap">
                                                                <a href="detail-produk-spk-reg-dalam-proses.php?id=<?php echo encrypt($data['id_spk_reg'], $key_spk) ?>" class="btn btn-primary btn-sm"  title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                                                                <button href="#" data-bs-toggle="modal" data-bs-target="#cancelModal" class="btn btn-danger btn-sm" title="Cancel Order" data-id="<?php echo encrypt($data['id_spk_reg'], $key_spk); ?>" data-nama="<?php echo $data['no_spk']; ?>" data-cs ="<?php echo $data['nama_cs'] ?>">
                                                                    <i class="bi bi-x-circle"></i>
                                                                </button>
                                                            </td>
                                                        <?php
                                                    } else {
                                                        ?>
                                                            <td class="text-center text-nowrap">
                                                                <a href="detail-produk-spk-reg-dalam-proses.php?id=<?php echo encrypt($data['id_spk_reg'], $key_spk) ?>" class="btn btn-primary btn-sm"  title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                                                            </td>
                                                        <?php
                                                    }
                                                ?>
                                                
                                                    <!-- Modal Cancel -->
                                                    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4><strong>Silahkan Isi Alasan</strong></h4>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="proses/proses-produk-spk-reg.php" method="POST">
                                                                    <p>Apakah Anda Yakin Ingin Cancel <br>No.SPK : <b id="no_spk"></b> (<b id="cs"></b>) ?</p>
                                                                    <div class="mb-3">
                                                                        <input type="hidden" name="id_spk" id="id_spk">
                                                                        <Label>Alasan Cancel</Label>
                                                                        <input type="text" class="form-control" name="alasan" required>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="submit" class="btn btn-primary" name="cancel-dalam-proses" id="cancel">Ya, Cancel Transaksi</button>
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </tr>
                                            <?php $no++ ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
<script>
    $('#cancelModal').on('show.bs.modal', function(event) {
        // Mendapatkan data dari tombol yang ditekan
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var cs = button.data('cs');
        
        var modal = $(this);
        var simpanBtn = modal.find('.modal-footer #cancel');
        var namaInput = modal.find('.modal-body #no_spk');
        var csInput = modal.find('.modal-body #cs');

        // Menampilkan data
        modal.find('.modal-body #id_spk').val(id);
        namaInput.text(nama);
        csInput.text(cs);
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