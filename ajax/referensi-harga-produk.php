<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// Hubungkan Koneksi
require_once "../akses.php";

// Penghubung Library
require_once '../assets/vendor/autoload.php';

// Library sanitasi input data
require_once "../function/sanitasi_input.php";

// Function Encrypt dan Decrypt
require_once "../function/function-enkripsi.php";

if (isset($_POST['id'])) {
    // Sanitasi seluruh $_POST
    $sanitasi_post = sanitizeInput($_POST);
    $id_produk = decrypt($sanitasi_post['id'], $key_global);
    $id_produk_encrypt = $sanitasi_post['id'];
    $id_cs = decrypt($sanitasi_post['cs'], $key_global);
    $no_inv = decrypt($sanitasi_post['noinv'], $key_global);

    $no = 1;
    $trx_produk_ref = $connect->query("SELECT 
                                trx.id_spk, 
                                COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv,
                                COALESCE(nonppn.tgl_inv, ppn.tgl_inv, bum.tgl_inv) AS tgl_inv,
                                COALESCE(nonppn.kategori_inv, ppn.kategori_inv, bum.kategori_inv) AS kategori_inv,
                                cs.id_cs,
                                cs.nama_cs,
                                COALESCE(nonppn.cs_inv, ppn.cs_inv, bum.cs_inv) AS cs_inv,
                                trx.id_produk,
                                COALESCE(pr_reg.nama_produk, pr_ecat.nama_produk, set_mrw.nama_set_marwa, set_ecat.nama_set_ecat) AS nama_produk,
                                trx.harga,
                                trx.qty,
                                trx.disc,
                                COALESCE(nonppn.sp_disc, ppn.sp_disc, bum.sp_disc) AS sp_disc,
                                trx.created_date
                            FROM `transaksi_produk_reg` AS trx
                            LEFT JOIN spk_reg spk ON (trx.id_spk = spk.id_spk_reg)
                            LEFT JOIN inv_nonppn nonppn ON (spk.id_inv = nonppn.id_inv_nonppn)
                            LEFT JOIN inv_ppn ppn ON (spk.id_inv = ppn.id_inv_ppn)
                            LEFT JOIN inv_bum bum ON (spk.id_inv = bum.id_inv_bum)
                            LEFT JOIN tb_customer cs ON (spk.id_customer = cs.id_cs)
                            LEFT JOIN tb_produk_reguler pr_reg ON (trx.id_produk = pr_reg.id_produk_reg)
                            LEFT JOIN tb_produk_ecat pr_ecat ON (trx.id_produk = pr_ecat.id_produk_ecat)
                            LEFT JOIN tb_produk_set_marwa set_mrw ON (trx.id_produk = set_mrw.id_set_marwa)
                            LEFT JOIN tb_produk_set_ecat set_ecat ON (trx.id_produk = set_ecat.id_set_ecat)
                            WHERE cs.id_cs = '$id_cs' AND trx.id_produk = '$id_produk' AND  COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) != '$no_inv'
                            ORDER BY trx.created_date ASC LIMIT 10");

    ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr class="text-center text-white" style="background-color: navy">
                    <th class="p-3 text-nowrap">No</th>
                    <th class="p-3 text-nowrap">No. Invoice</th>
                    <th class="p-3 text-nowrap" style="width: 250px;">Nama CS Invoice</th> 
                    <th class="p-3 text-nowrap">Kategori Invoice</th> 
                    <th class="p-3 text-nowrap">Qty Pembelian</th> 
                    <th class="p-3 text-nowrap">Harga Awal</th> 
                    <th class="p-3 text-nowrap">
                        Harga Akhir <br>
                        (Jika Ada Diskon)
                    </th> 
                    <th class="p-3 text-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($trx_produk_ref) > 0): ?>
                    <?php while ($data_prd_reg = mysqli_fetch_array($trx_produk_ref)): ?>
                        <?php
                            $kategori_inv = $data_prd_reg['kategori_inv'];
                            $harga = $data_prd_reg['harga'];
                            $diskon = $data_prd_reg['disc'];
                            $sp_disc = $data_prd_reg['sp_disc'];
                            $tampil_kategori = "";
                            $hasil_diskon = "";
                            if ($kategori_inv == 'Reguler') {
                                $tampil_kategori = $kategori_inv;
                                $hasil_diskon = $harga;
                            } elseif ($kategori_inv == 'Diskon') {
                                $tampil_kategori = $kategori_inv . " (" . $diskon . ")";
                                $hasil_diskon = $harga * (1 - $diskon / 100);
                            } elseif ($kategori_inv == 'Spesial Diskon') {
                                $tampil_kategori = $kategori_inv . " (" . $sp_disc . ")";
                                $hasil_diskon = $harga * (1 - $sp_disc / 100);
                            }
                        ?>
                        <tr>
                            <td class="text-center text-nowrap"><?php echo $no++; ?></td>
                            <td class="text-center text-nowrap">
                                <?php echo $data_prd_reg['no_inv']; ?><br>
                                (<?php echo $data_prd_reg['tgl_inv']; ?>)
                            </td>
                            <td class="text-start text-nowrap"><?php echo $data_prd_reg['cs_inv']; ?></td>
                            <td class="text-center text-nowrap"><?php echo $tampil_kategori; ?></td>
                            <td class="text-center text-nowrap"><?php echo $data_prd_reg['qty']; ?></td>
                            <td class="text-end text-nowrap"><?php echo number_format($data_prd_reg['harga'], 0, '.', '.'); ?></td>
                            <td class="text-end text-nowrap"><?php echo number_format($hasil_diskon, 0, '.', '.'); ?></td>
                            <td class="text-center text-nowrap">
                                <button class="btn btn-primary btn-sm pilih-harga" data-harga="<?php echo $harga; ?>" data-index="<?php echo $id_produk_encrypt; ?>">
                                    <i class="bi bi-check-circle"></i> Pilih
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada transaksi sebelumnya.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).on('click', '.pilih-harga', function() {
            var harga = $(this).data('harga');
            var index = $(this).data('index');
            $('input.harga_produk[data-index="' + index + '"]').val(new Intl.NumberFormat('id-ID').format(harga));
            $('#referensiHargaProduk').modal('hide');
        });
    </script>
    <?php
}
?>
