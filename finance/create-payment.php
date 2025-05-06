<?php
  $page = 'invoice';
  $page2  = 'pembelian';
  require_once "../akses.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'page/head.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory KMA</title>
</head>
<body>
    <!-- nav header -->
    <?php include "page/nav-header.php" ?>
    <!-- end nav header -->

    <!-- sidebar  -->
    <?php include "page/sidebar.php"; ?>
    <!-- end sidebar --> 
    <main id="main" class="main">
        <section>
            <form action="proses/pembayaran-produk-lokal.php" method="POST">
                <div class="card p-3">
                    <?php
                        // Include file function-enkripsi.php
                        include "../function/function-enkripsi.php";

                        // Mendapatkan nilai 'inv_id' dari URL
                        $encodedIds = json_encode($_GET['inv_id']);

                        // Kunci enkripsi
                        $key = 'pembelian2024';

                        // Mendekripsi dan menampilkan setiap ID secara terpisah
                        // Pastikan untuk memeriksa apakah $encodedIds adalah string sebelum menggunakan explode
                        if(is_string($encodedIds)) {
                            // Mendekripsi dan menambahkan setiap ID terdekripsi ke dalam array
                            $decodedIds = array();
                            foreach (explode(',', $encodedIds) as $encodedId) {
                                // Mendekripsi setiap bagian
                                $decodedId = decrypt($encodedId, $key);
                                // Menambahkan ID terdekripsi ke dalam array
                                $decodedIds[] = $decodedId;
                            }
                            
                            // Menghapus karakter non-printable dan spasi ekstra di awal dan akhir setiap ID
                            $formattedIds = array_map(function($id) { return "'" . preg_replace('/[^\x20-\x7E]+/', '', trim($id)) . "'"; }, $decodedIds);
                            
                            // Menggabungkan kembali hasil dekripsi menjadi format yang diinginkan
                            $id_pembelian = implode(',', $formattedIds);

                            $id_tanpa_petik = str_replace("'", "", $id_pembelian);
                            
                            // Menampilkan hasil
                            // echo "Hasil gabungan: " . $id_pembelian;
                            // Output string tanpa tanda petik
                            // echo $tanpa_petik;
                            // Lakukan kueri SQL dengan menggunakan string yang telah didekripsi
                            $sql = "SELECT
                                        ipl.id_inv_pembelian, 
                                        ipl.no_trx,
                                        ipl.no_inv,
                                        ipl.kategori_pembelian,
                                        ipl.tgl_pembelian,
                                        ipl.tgl_tempo,
                                        ipl.total_pembelian,  
                                        sp.nama_sp
                                    FROM inv_pembelian_lokal AS ipl
                                    LEFT JOIN tb_supplier sp ON ipl.id_sp = sp.id_sp
                                    WHERE ipl.id_inv_pembelian IN ($id_pembelian)";
                            $query1 = $connect->query($sql);
                            $query2 = $connect->query($sql);
                            $total_data = mysqli_num_rows($query1);
                            
                            // Pengecekan apakah kueri SQL berhasil dijalankan
                            if ($query1) {
                                $grandTotal = 0;
                                while ($data_inv = mysqli_fetch_array($query1)) {
                                    $nama_sp = $data_inv['nama_sp']; 
                                    $total_pembelian = $data_inv['total_pembelian'];
                                    $grandTotal += $total_pembelian;
                                }
                            } else {
                                // Tampilkan pesan kesalahan jika kueri SQL gagal dijalankan
                                echo "Error: " . mysqli_error($connect);
                            }
                        } else {
                            echo "inv_id harus berupa string.";
                        }

                        // Kode untuk membuat no pembayaran
                        include "../function/uuid.php";
                        date_default_timezone_set('Asia/Jakarta');
                        $uuid = uuid();
                        $day = date('d');
                        $month = date('m');
                        $year  = date('Y');
                        $sql  = mysqli_query($connect, "SELECT max(no_pembayaran) as maxID, STR_TO_DATE(tgl_pembayaran, '%d/%m/%Y') AS tgl_pembayaran FROM finance_pembayaran_produk_lokal WHERE YEAR(STR_TO_DATE(tgl_pembayaran, '%d/%m/%Y')) = '$year'");
                        $data = mysqli_fetch_array($sql);

                        $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
                        $kode = $data['maxID'];
                        $ket1 = "/PAYMENT/";
                        $bln = $array_bln[date('n')];
                        $ket2 = "/";
                        $ket3 = date("Y");
                        $urutkan = (int)substr($kode, 0, 3);
                        $urutkan++;
                        $no_tagihan = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;
                        $id_pembayaran = "PAYMENT" . $year . "" . $month . "" . $uuid . "" . $day;

                        // encrypt id
                        $id_pembelian_encrypt = encrypt($id_tanpa_petik, $key);
                        $id_pembayaran_encrypt = encrypt($id_pembayaran, $key);
                        // Sanitisasi data untuk nilai $tanpa_petik
                        $id_pembelian_sanitized = htmlspecialchars($id_pembelian_encrypt, ENT_QUOTES, 'UTF-8');
                        // Sanitisasi data untuk nilai $id_pembayaran
                        $id_pembayaran_sanitized = htmlspecialchars($id_pembayaran_encrypt, ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="text-center mb-3">
                        <h5><b>Buat Pembayaran</b></h5>
                    </div>
                    <div class="row mb-1">
                        <input type="hidden" name="id_inv" value="<?php echo $id_pembelian_sanitized ?>">
                        <input type="hidden" name="id_pembayaran" value="<?php echo $id_pembayaran_sanitized ?>">
                        <div class="col-md-3 mb-3">
                            <label>No. Tagihan</label>
                            <input type="text" class="form-control bg-light" name="no_pembayaran" value="<?php echo $no_tagihan ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Supplier</label>
                            <input type="text" class="form-control" name="sp" value="<?php echo $nama_sp ?>" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Tgl. Pembayaran</label>
                            <input type="text" class="form-control" id="date" name="tgl_pembayaran">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Jenis Faktur Pembayaran</label>
                            <input type="text" class="form-control" name="jenis_faktur"  maxlength="25">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-2">
                                <p>Total Tagihan (Dipilih):</p>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">Rp</span>
                                    <input type="text" class="form-control" name="total_tagihan" id="totalTagihan" value="<?php echo number_format($grandTotal, 0,'.','.') ?>" aria-label="total_tagihan" readonly>
                                </div>      
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr class="text-white" style="background-color: navy;">
                                    <th class="text-center p-3 text-nowrap">No</th>
                                    <th class="text-center p-3 text-nowrap">No. Invoice</th>
                                    <th class="text-center p-3 text-nowrap">Nama Supplier</th>
                                    <th class="text-center p-3 text-nowrap">Tgl. Pembelian</th>
                                    <th class="text-center p-3 text-nowrap">Tgl. Tempo</th>
                                    <th class="text-center p-3 text-nowrap">Total Tagihan</th>
                                    <?php  
                                        if($total_data > 1){
                                            ?>
                                                <th class="text-center p-3 text-nowrap">Aksi</th>
                                            <?php
                                        }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  
                                    $no = 1;
                                    while ($data_trx = mysqli_fetch_array($query2)) {
                                        $id_inv = $data_trx['id_inv_pembelian'];
                                        $id_inv_encrypt = encrypt($id_inv, $key);
                                        $id_inv_encrypt = rtrim($id_inv_encrypt, '=');
                                        $no_inv = "";
                                        if($no_inv != ''){
                                            $no_inv = $data_trx['no_inv'];
                                        } else {
                                            $no_inv = 'Tidak ada';
                                        }
                                ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                    <td class="text-center text-nowrap"><?php echo $no_inv ?></td>
                                    <td class="text-nowrap"><?php echo $data_trx['nama_sp'] ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data_trx['tgl_pembelian'] ?></td>
                                    <td class="text-center text-nowrap"><?php echo $data_trx['tgl_tempo'] ?></td>
                                    <td class="text-end text-nowrap"><?php echo number_format($data_trx['total_pembelian'],0,'.','.'); ?></td>
                                    <?php  
                                        if($total_data > 1){
                                            ?>
                                                <td class="text-center">
                                                    <button class="btn btn-danger btn-sm" type="button" data-id="<?php echo $id_inv_encrypt; ?>" onclick="removeValue(this)">Hapus Data</button>
                                                </td>
                                            <?php
                                        }
                                    ?>
                                </tr>
                                <?php $no++ ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end">
                        <a href="finance-inv-pembelian.php?date_range=year" class="btn btn-secondary btn-md">Cancel</a>
                        <button class="btn btn-primary btn-md" id="simpanButton" name="simpan-payment">Simpan</button>
                    </div>
                </div>
            </form>      
        </section>
    </main>
    <?php include "page/footer.php" ?>
    <?php include "page/script.php" ?>
</body>
</html>
<script>
    function removeValue(button) {
        // Pastikan tombol yang ditekan memiliki atribut data-id
        var dataIdValue = button.getAttribute('data-id');

        if (dataIdValue !== null) {
            console.log('Data-id:', dataIdValue);

            // Mendapatkan URL saat ini
            var currentUrl = new URL(window.location.href);

            // Mendapatkan parameter 'inv_id' dari URL
            var invIdParam = currentUrl.searchParams.get('inv_id[]');

            if (invIdParam) {
                // Pecah nilai parameter menjadi array
                var invIdArray = invIdParam.split(',');

                // Hapus nilai yang sesuai dengan data-id
                var filteredInvIdArray = invIdArray.filter(value => value !== dataIdValue);

                // Atur kembali nilai parameter 'inv_id'
                currentUrl.searchParams.set('inv_id[]', filteredInvIdArray.join(','));

                // Mengganti URL dan reload halaman
                window.location.href = currentUrl.href;

                // Menampilkan URL yang sudah diubah di console (opsional)
                console.log('New URL:', currentUrl.href);
            } else {
                console.error('Parameter inv_id tidak ditemukan pada URL.');
            }
        } else {
            console.error('Atribut data-id tidak ditemukan pada tombol yang ditekan.');
        }
    }
</script>
<script type="text/javascript">
  flatpickr("#date", {
    dateFormat: "d/m/Y",
    defaultDate: new Date(),
  });
</script>
