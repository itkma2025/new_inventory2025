<?php
require_once "akses.php";
$page  = 'transaksi';
$page2 = 'spk';
$page_nav  = 'proforma';
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
                <tr class="text-white text-nowrap" style="background-color: navy;">
                    <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                    <th class="text-center p-3 text-nowrap" style="width: 150px">No. Invoice</th>
                    <th class="text-center p-3 text-nowrap" style="width: 150px">Tgl. Invoice</th>
                    <th class="text-center p-3 text-nowrap" style="width: 150px">No. PO</th>
                    <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Customer</th>
                    <th class="text-center p-3 text-nowrap" style="width: 100px">Kat. Inv</th>
                    <th class="text-center p-3 text-nowrap" style="width: 100px">Note</th>
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
                            sr.id_inv, 
                            sr.id_customer, 
                            sr.no_po, 
                            cs.nama_cs, 
                            cs.alamat
                        FROM inv_bum AS bum
                        LEFT JOIN spk_reg sr ON(bum.id_inv_bum = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        WHERE status_transaksi = 'Belum Dikirim' GROUP BY no_inv  $filter";
                $query = mysqli_query($connect, $sql);
                while ($data = mysqli_fetch_array($query)) {
                ?>
                    <tr>
                        <td class="text-center text-nowrap"><?php echo $no; ?></td>
                        <td class="text-center text-nowrap"><?php echo $data['no_inv'] ?></td>
                        <td class="text-center text-nowrap"><?php echo $data['tgl_inv'] ?></td>
                        <td class="text-center text-nowrap">
                            <?php
                            if (!empty($data['no_po'])) {
                                echo $data['no_po'];
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                        <td class="text-center text-nowrap"><?php echo $data['kategori_inv'] ?></td>
                        <td class="text-nowrap">
                            <?php
                            $note = $data['note_inv'];

                            $items = explode("\n", trim($note));

                            if (!empty($note)) {
                                foreach ($items as $notes) {
                                    echo trim($notes) . '<br>';
                                }
                            } else {
                                echo 'Tidak Ada';
                            }
                            ?>
                        </td>
                        <td class="text-center text-nowrap">
                            <a href="detail-produk-proforma.php?jenis=bum&&id=<?php echo encrypt($data['id_inv_bum'], $key_global) ?>" class="btn btn-primary btn-sm" title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                            <button data-bs-toggle="modal" data-bs-target="#cancelModalBum" class="btn btn-danger btn-sm" title="Cancel Order" data-id="<?php echo encrypt($data['id_inv_bum'], $key_global); ?>" data-noinv="<?php echo $data['no_inv']; ?>" data-cs="<?php echo $data['nama_cs'] ?>">
                                <i class="bi bi-x-circle"></i>
                            </button>
                        </td>
                    </tr>
                    <div class="modal fade" id="cancelModalBum" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4><strong>Silahkan Isi Alasan</strong></h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="proses/proses-cancel-inv.php" method="POST">
                                        <p>Apakah Anda Yakin Ingin Cancel <br>No.Invoice : <b id="no_inv"></b> (<b id="cs"></b>) ?</p>
                                        <div class="mb-3">
                                            <input type="hidden" name="id_inv" id="id_inv">
                                            <Label>Alasan Cancel</Label>
                                            <input type="text" class="form-control" name="alasan" required>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" name="cancel-inv-bum" id="cancel">Ya, Cancel Transaksi</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $no++ ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include "page/script.php" ?>
</body>
</html>