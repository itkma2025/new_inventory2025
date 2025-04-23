<?php
require_once "../akses.php";
require_once '../koneksi-ecat.php';
$id_user = decrypt($_SESSION['tiket_id'], $key_global);
if (isset($_POST['id_spk'])) {
    $id_spk = htmlspecialchars($_POST['id_spk']);
    $id_spk_decrypt = decrypt($id_spk, $key_gudang);

    $no = 1;
    // Query untuk mengambil data
    $sql = "SELECT 
                    tmp.id_tmp,
                    tmp.id_transaksi,
                    tmp.id_produk_ks,
                    tmp.id_spk_ks,
                    tmp.status_barang,
                    tmp.qty_ks,
                    tmp.keterangan_ks,
                    tmp.input_date,
                    tmp.input_by,
                    COALESCE(spk.no_spk, spk_ecat.no_spk_ecat, spk_pl.no_spk_pl) AS no_spk,
                    COALESCE(spk.tgl_spk, spk_ecat.tgl_spk_ecat, spk_pl.tgl_spk_pl) AS tgl_spk,
                    COALESCE(cs.nama_cs, cs_ecat.nama_perusahaan, cs_pl.nama_perusahaan) AS nama_cs,
                    COALESCE(tpr.nama_produk, tpe.nama_produk, tpsm.nama_set_marwa, tpse.nama_set_ecat) AS nama_produk,
                    COALESCE(tpr.id_merk, tpe.id_merk, tpsm.id_merk, tpse.id_merk) AS id_merk,
                    trx.created_date
                FROM tmp_kartu_stock AS tmp
                LEFT JOIN $db.spk_reg spk ON tmp.id_spk_ks = spk.id_spk_reg
                LEFT JOIN $db_ecat.tb_spk_ecat spk_ecat ON tmp.id_spk_ks = spk_ecat.id_spk_ecat
                LEFT JOIN $db_ecat.tb_spk_pl spk_pl  ON tmp.id_spk_ks = spk_pl.id_spk_pl
                LEFT JOIN $db.tb_customer cs ON spk.id_customer = cs.id_cs
                LEFT JOIN $db_ecat.tb_perusahaan cs_ecat ON spk_ecat.id_perusahaan = cs_ecat.id_perusahaan
                LEFT JOIN $db_ecat.tb_perusahaan cs_pl ON spk_pl.id_perusahaan = cs_pl.id_perusahaan
                LEFT JOIN $db.tb_produk_reguler tpr ON tmp.id_produk_ks = tpr.id_produk_reg
                LEFT JOIN $db.tb_produk_ecat tpe ON tmp.id_produk_ks = tpe.id_produk_ecat
                LEFT JOIN $db.tb_produk_set_marwa tpsm ON tmp.id_produk_ks = tpsm.id_set_marwa
                LEFT JOIN $db.tb_produk_set_ecat tpse ON tmp.id_produk_ks = tpse.id_set_ecat
                LEFT JOIN $db.transaksi_produk_reg trx ON tmp.id_transaksi = trx.id_transaksi
                WHERE tmp.id_spk_ks = '$id_spk_decrypt' AND input_by = '$id_user' ORDER BY trx.created_date ASC";

    // Eksekusi query
    $result = $connect->query($sql);

    // Mulai output HTML
    echo '<table class="table table-bordered table-striped" id="tableDetail">';
    echo '    <thead>';
    echo '        <tr>';
    echo '            <th class="text-center p-3 text-nowrap">No</th>';
    echo '            <th class="text-center p-3 text-nowrap" style="display:none;">id_trx</th>';
    echo '            <th class="text-center p-3 text-nowrap">No. SPK</th>';
    echo '            <th class="text-center p-3 text-nowrap">Nama Produk</th>';
    echo '            <th class="text-center p-3 text-nowrap">Merk</th>';
    echo '            <th class="text-center p-3 text-nowrap">Qty</th>';
    echo '            <th class="text-center p-3 text-nowrap">Aksi</th>';
    echo '        </tr>';
    echo '    </thead>';
    echo '    <tbody>';

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Debug output data
            $id_trx = $row['id_transaksi'];
            $id_trx_encrypt = encrypt($id_trx, $key_gudang);
            $id_merk = $row['id_merk'];
            $no_spk = $row['no_spk'];
            $nama_produk = $row['nama_produk'];
            $qty_ks = number_format($row['qty_ks'], 0, '.', '.');
            $input_date = date('d/m/Y H:i:s', strtotime($row['input_date']));
            $status_barang = ($row['status_barang'] == '1') ? "Keluar" : "Masuk";
            $keterangan_ks = $row['keterangan_ks'];

            // Menampilkan nama merk
            $merk = $connect->query("SELECT nama_merk FROM tb_merk WHERE id_merk = '$id_merk'");
            $nama_merk = '';
            while ($data_merk = mysqli_fetch_array($merk)) {
                $nama_merk = $data_merk['nama_merk'];
            }

            echo '<tr>';
            echo '      <td class="text-center text-nowrap p-2">' . $no . '</td>';
            echo '      <td class="text-center text-nowrap p-2" style="display:none;">' . htmlspecialchars($id_trx_encrypt) . '</td>';
            echo '      <td class="text-center text-nowrap p-2">' . htmlspecialchars($no_spk) . '</td>';
            echo '      <td class="text-start text-nowrap p-2">' . htmlspecialchars($nama_produk) . '</td>';
            echo '      <td class="text-center text-nowrap p-2">' . htmlspecialchars($nama_merk) . '</td>';
            echo '      <td class="text-center text-nowrap p-2">' . htmlspecialchars($qty_ks) . '</td>';
            echo '      <td class="text-center text-nowrap p-2">';
            if ($keterangan_ks != 1) {
                echo '          <button type="button" class="btn btn-warning btn-sm btn-edit">';
                echo '              <i class="bi bi-pencil-square"></i>';
                echo '          </button>';
                echo '          <button type="button" class="btn btn-secondary btn-sm btn-cancel d-none">';
                echo '              <i class="bi bi-x-circle"></i>';
                echo '          </button>';
            }
            echo '      </td>';
            echo '</tr>';

            $no++; // Increment nomor urut
        }
    } else {
        echo '<tr><td colspan="9" class="text-center">Data tidak ditemukan</td></tr>';
    }

    echo '    </tbody>';
    echo '</table>';
}


$link_detail = '';
$link_update = '';
if ($role == "Driver") {
    $link_detail = '../ajax/detail-produk-tmp-ks.php';
    $link_update = '../ajax/update-tmp-ks.php';
} else {
    $link_detail = 'ajax/detail-produk-tmp-ks.php';
    $link_update = 'ajax/update-tmp-ks.php';
}

?>
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables dengan AJAX untuk mengambil data dari server
        const table = $("#tableDetail").DataTable({
            lengthChange: false,
            autoWidth: false,
            language: {
                searchPlaceholder: "Cari data",
                search: "",
            },
        });

        // Simpan data asli untuk setiap baris
        let originalData = {};

        // Fungsi untuk format angka dengan pemisah titik
        function formatNumber(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Fungsi untuk menghapus pemisah ribuan
        function removeFormatting(number) {
            return number.replace(/\./g, '');
        }

        // Fungsi untuk menyembunyikan tombol edit pada baris lain
        function disableOtherEditButtons(excludedRow) {
            $('#tableDetail').find('.btn-edit').each(function() {
                const tr = $(this).closest('tr');
                if (tr[0] !== excludedRow[0]) {
                    $(this).hide(); // Menyembunyikan tombol edit
                }
            });
        }

        // Fungsi untuk mengaktifkan kembali tombol edit
        function enableAllEditButtons() {
            $('#tableDetail').find('.btn-edit').show();
        }

        // Fungsi untuk mengembalikan data asli dan status tombol
        function revertRow(tr) {
            const id_spk = "<?php echo $id_spk ?>"; // Ambil id_tmp dari atribut data-id
            console.log(id_spk);

            // Lakukan AJAX request untuk mendapatkan data asli dari database
            $.ajax({
                type: 'POST',
                url: '<?php echo $link_detail ?>', // Pastikan file PHP ini benar
                data: {
                    id_spk: id_spk
                },
                success: function(response) {
                    console.log("Raw Response:", response);
                    $('#detailProdukBody').html(response); // Isi modal body dengan HTML dari response
                    $('#detailProduk').modal('show'); // Tampilkan modal
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                }
            });
        }
        // Event listener untuk tombol edit
        $('#tableDetail').on('click', '.btn-edit', function() {
            const tr = $(this).closest('tr');
            const row = table.row(tr);
            const rowData = row.data();

            // Jika ada baris yang sedang diedit, batalkan perubahan pada baris tersebut
            if (Object.keys(originalData).length) {
                const previousTr = $('#tableDetail').find('tr').filter(function() {
                    return originalData[$(this).attr('data-id')];
                }).first();
                revertRow(previousTr);
            }

            // Simpan data asli sebelum mengedit
            originalData[tr.attr('data-id')] = rowData;

            // Nonaktifkan tombol edit pada baris lain
            disableOtherEditButtons(tr);

            // Ambil id_trx_encrypt dari kolom hidden (kolom pertama)
            const id_trx_encrypt = tr.find('td').eq(1).text();

            // Ambil nilai qty dari kolom qty (misalnya kolom ke-5)
            const qtyCell = tr.find('td').eq(5); // Index 5 adalah kolom qty
            const qtyValue = qtyCell.text();

            // Ubah kolom qty menjadi input dengan format ribuan
            qtyCell.html(`
                <div style="display: flex; justify-content: center; align-items: center; height: 100%;">
                    <input type="text" class="form-control text-center" value="${formatNumber(qtyValue)}" style="width: 120px; text-align: center;">
                </div>
            `);

            // Ubah tombol Edit menjadi Save dan tampilkan tombol Cancel
            $(this).html('<i class="bi bi-save"></i>')
                .removeClass('btn-warning')
                .addClass('btn-primary')
                .removeClass('btn-edit')
                .addClass('btn-save');
            tr.find('.btn-cancel').removeClass('d-none');

            // Format input saat mengetik
            qtyCell.find('input').on('input', function() {
                let value = $(this).val();
                value = removeFormatting(value);
                $(this).val(formatNumber(value));
            });
        });

        // Event listener untuk tombol save
        $('#tableDetail').on('click', '.btn-save', function() {
            const tr = $(this).closest('tr');
            const row = table.row(tr);

            // Simpan referensi ke tombol yang diklik
            const saveButton = $(this);

            // Ambil nilai baru dari input qty
            const qtyCell = tr.find('td').eq(5);
            let newQty = qtyCell.find('input').val();

            // Hapus format ribuan sebelum mengirim ke server
            newQty = removeFormatting(newQty);

            // Ambil id_trx_encrypt dari kolom hidden
            const id_trx_encrypt = tr.find('td').eq(1).text(); // Kolom hidden (index 0)

            // Simpan status modal sebelum reload
            const isModalOpen = $('#ubahStatus').hasClass('show');

            // Kirim data yang sudah diubah ke server
            $.ajax({
                url: '<?php echo $link_update ?>', // Endpoint untuk memperbarui data
                type: 'POST',
                data: {
                    id: id_trx_encrypt, // Kirim ID transaksi terenkripsi
                    qty: newQty // Kirim nilai qty yang baru
                },
                success: function(response) {
                    // Handle sukses
                    // Memperbarui tabel dengan data baru
                    qtyCell.html(formatNumber(newQty)); // Kembalikan nilai baru ke tabel tanpa input

                    // Ubah tombol Save kembali menjadi Edit
                    saveButton.html('<i class="bi bi-pencil-square"></i>')
                        .removeClass('btn-primary')
                        .addClass('btn-warning')
                        .removeClass('btn-save')
                        .addClass('btn-edit');
                    tr.find('.btn-cancel').addClass('d-none');

                    // Update originalData dengan nilai baru
                    originalData[tr.attr('data-id')] = table.row(tr).data();

                    // Tampilkan kembali semua tombol Edit
                    enableAllEditButtons();

                    // Reload hanya data tabel tanpa menutup modal
                    table.ajax.reload(null, false); // false agar tidak mereset paging

                    // Jika modal sebelumnya terbuka, buka kembali modal
                    if (isModalOpen) {
                        $('#ubahStatus').modal('show');
                    }

                    // Menyembunyikan tombol jika keterangan_ks adalah 1
                    if (response.keterangan_ks === '1') {
                        tr.find('.btn-edit').addClass('d-none');
                        tr.find('.btn-cancel').addClass('d-none');
                    }
                    var data = JSON.parse(response);
                    if (data.status === 'success') {
                        $('#result').html("ID transaksi setelah dekripsi: " + data.id_trx);
                    } else {
                        $('#result').html("Error updating data.");
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(error);
                    // Tampilkan alert jika gagal
                    alert('Gagal memperbarui data. Silakan coba lagi.');
                }
            });
        });

        // Event listener untuk tombol cancel
        $('#tableDetail').on('click', '.btn-cancel', function() {
            const tr = $(this).closest('tr');
            revertRow(tr);
        });
    });
</script>