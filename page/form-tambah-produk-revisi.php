<?php
    $no = 1;
    $sql = "SELECT DISTINCT
                nonppn.id_inv_nonppn AS id_inv,
                STR_TO_DATE(ik.tgl_komplain, '%d/%m/%Y') AS tanggal,
                ik.id_komplain,
                tpk.id_tmp,
                tpk.id_produk,
                tpk.nama_produk,
                tpk.harga,
                tpk.qty,
                tpk.disc,
                tpk.total_harga,
                tpk.status_tmp,
                spr.stock,
                COALESCE(mr_produk.nama_merk, mr_set.nama_merk) AS merk
            FROM inv_komplain AS ik 
            LEFT JOIN inv_nonppn nonppn ON ik.id_inv = nonppn.id_inv_nonppn
            LEFT JOIN tmp_produk_komplain tpk ON nonppn.id_inv_nonppn = tpk.id_inv
            LEFT JOIN stock_produk_reguler spr ON tpk.id_produk = spr.id_produk_reg
            LEFT JOIN tb_produk_reguler pr ON tpk.id_produk = pr.id_produk_reg
            LEFT JOIN tb_produk_set_marwa tpsm ON tpk.id_produk = tpsm.id_set_marwa
            LEFT JOIN tb_merk mr_produk ON pr.id_merk = mr_produk.id_merk -- JOIN untuk produk reguler
            LEFT JOIN tb_merk mr_set ON tpsm.id_merk = mr_set.id_merk -- JOIN untuk produk set
            WHERE (nonppn.id_inv_nonppn = '$id_inv') AND tpk.status_tmp = '0'";
    $query = mysqli_query($connect, $sql);
    $totalRows = mysqli_num_rows($query);
    if ($totalRows != 0) {
?>
<div class="card">
    <br>
    <h5 class="text-center">Tambahan Produk Revisi</h5>
    <div class="card-body p-2 card-mobile">
        <div class="row p-1">
            <div class="col-sm-1 mb-2">
                <input type="text" class="form-control text-center mobile"
                    style="border: none;" value="No" readonly>
            </div>
            <div class="col-sm-3 mb-2">
                <input type="text" class="form-control text-center" style="border: none;"
                    value="Nama Produk">
            </div>
            <div class="col-sm-1 mb-2">
                <input type="text" class="form-control text-center mobile-text"
                    style="border: none;" value="Satuan" readonly>
            </div>
            <div class="col-sm-1 mb-2">
                <input type="text" class="form-control text-center mobile-text"
                    style="border: none;" value="Merk" readonly>
            </div>
            <div class="col-sm-2 mb-2">
                <input type="text" class="form-control text-center mobile-text"
                    style="border: none;" value="Harga">
            </div>
            <div class="col-sm-1 mb-2">
                <input type="text" class="form-control text-center mobile-text"
                    style="border: none;" value="Stock" readonly>
            </div>
            <div class="col-sm-1 mb-2">
                <input type="text" class="form-control text-center mobile-text"
                    style="border: none;" value="Qty" readonly>
            </div>
            <div class="col-sm-1 mb-2">
                <input type="text" class="form-control text-center mobile-text"
                    style="border: none;" value="Diskon" readonly>
            </div>
            <div class="col-sm-1 mb-2 text-center">
                <input type="text" class="form-control text-center mobile-text"
                    style="border: none;" value="Aksi" readonly>
            </div>
        </div>
    </div>
    <?php
        } else {
        }

    while ($data = mysqli_fetch_array($query)) {
        $id_inv = $data['id_inv'];
        $satuan = detailSpkFnc::getSatuan($data['id_produk']);  
        // $uuid = generate_uuid();
        $isEmpty = false; // Setel variabel pengecekan menjadi false jika ada data
    ?>
    <form action="proses/produk-tmp-revisi-nonppn.php" method="POST"
        enctype="multipart/form-data">
        <div class="card-body p-2">
            <div class="row p-1">
                <div class="col-sm-1 mb-2">
                    <input type="text" class="form-control text-center bg-light mobile"
                        value="<?php echo $no; ?>" readonly>
                    <?php $no++ ?>
                </div>
                <div class="col-sm-3 mb-2">
                    <label class="form-control mobile-text fw-bold label-mobile"
                        style="border: none;">Nama Produk</label>
                    <input type="hidden" name="id_komplain" value="<?php echo encrypt($id, $key_spk) ?>" readonly>
                    <input type="hidden" name="id_tmp[]" id="id_<?php echo $data['id_tmp'] ?>" value="<?php echo $data['id_tmp'] ?>" readonly>
                    <input type="hidden" class="form-control" name="id_produk_tmp[]" value="<?php echo $data['id_produk'] ?>" readonly>
                    <input type="text" class="form-control" name="nama_produk[]" value="<?php echo $data['nama_produk']; ?>">
                </div>
                <div class="col-sm-1 mb-2">
                    <label class="form-control mobile-text fw-bold label-mobile" style="border: none;">Satuan</label>
                    <input type="text" class="form-control bg-light text-center mobile-text" value="<?php echo $satuan; ?>" readonly>
                </div>
                <div class="col-sm-1 mb-2">
                    <label class="form-control mobile-text fw-bold label-mobile" style="border: none;">Merk</label>
                    <input type="text" class="form-control bg-light text-center mobile-text" value="<?php echo $data['merk'] ?>" readonly>
                </div>
                <div class="col-sm-2 mb-2">
                    <label class="form-control mobile-text fw-bold label-mobile" style="border: none;">Harga</label>
                    <input type="text" class="form-control text-end mobile-text" name="harga[]" value="<?php echo number_format($data['harga']) ?>" oninput="formatNumberHarga(this)">
                </div>
                <div class="col-sm-1 mb-2">
                    <label class="form-control mobile-text fw-bold label-mobile" style="border: none;">Stock</label>
                    <input type="text" class="form-control bg-light text-end mobile-text" name="stock[]" id="stock_<?php echo $data['id_tmp'] ?>" value="<?php echo number_format($data['stock']) ?>" readonly>
                </div>
                <div class="col-sm-1 mb-2">
                    <label class="form-control mobile-text fw-bold label-mobile" style="border: none;">Qty</label>
                    <input type="text" class="form-control text-end mobile-text" name="qty_tmp[]" id="qtyInput_<?php echo $data['id_tmp'] ?>" oninput="checkStock('<?php echo $data['id_tmp'] ?>')" required>
                </div>
                <div class="col-sm-1 mb-2">
                    <label class="form-control mobile-text fw-bold label-mobile" style="border: none;">Diskon</label>
                    <input type="text" class="form-control text-end mobile-text" name="disc[]" oninput="validasiDiskon(this)" required>
                </div>
                <div class="col-sm-1 mb-2 text-center">
                    <a href="proses/produk-tmp-revisi-nonppn.php?hapus_tmp=<?php echo encrypt($data['id_tmp'], $key_spk) ?>&&id_komplain=<?php echo encrypt($id, $key_spk) ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="card-body mt-3 text-end">
            <?php  
                if ($totalRows != 0) {
                    echo '<button type="submit" class="btn btn-primary" name="simpan-tmp" id="simpan-data"><i class="bi bi-save"></i> Simpan</button>';
                }
            ?>
        </div>
    </form>
</div>

<!-- Format Number Harga -->
<script>
    function formatNumberHarga(input) {
        // Mengambil nilai input
        var inputValue = input.value;

        // Menghapus semua karakter kecuali angka dan tanda koma (,)
        var cleanedValue = inputValue.replace(/[^0-9,]/g, '');

        // Menghapus tanda koma (,) tambahan yang mungkin ada
        cleanedValue = cleanedValue.replace(/,/g, '');

        // Mengubah nilai input menjadi format angka yang sesuai
        var formattedValue = Number(cleanedValue).toLocaleString('en-US');

        // Memasukkan kembali nilai yang telah diformat ke dalam input
        input.value = formattedValue;
    }
</script>

<!-- Kode Diskon -->
<script>
    function validasiDiskon(input) {
        // Hapus karakter selain angka, titik (.), dan tanda persen (%)
        input.value = input.value.replace(/[^0-9.%]/g, '');

        // Hapus tanda persen (%) yang ada di akhir input
        if (input.value.endsWith('%')) {
            input.value = input.value.slice(0, -1);
        }

        // Pisahkan angka sebelum dan sesudah titik
        var parts = input.value.split('.');
        var angkaDepan = parts[0] || ""; // Bagian sebelum titik atau string kosong jika tidak ada
        var angkaBelakang = parts[1] || ""; // Bagian setelah titik atau string kosong jika tidak ada

        // Hanya tambahkan titik dan angka desimal jika angkaBelakang ada
        if (angkaBelakang) {
            // Format ulang nilai diskon dengan satu angka desimal
            if (angkaBelakang.length > 1) {
                angkaBelakang = angkaBelakang.substring(0, 1);
            }
            input.value = angkaDepan + "." + angkaBelakang;
        }

        // Konversi input ke dalam format angka dengan satu angka desimal
        var nilaiDiskon = parseFloat(input.value);

        // Batasi nilai diskon maksimum menjadi 100
        if (!isNaN(nilaiDiskon) && nilaiDiskon > 100) {
            input.value = "100";
        }
    }
</script>