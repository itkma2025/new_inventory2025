<?php
$page = 'invoice';
$page2  = 'penjualan';
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
        <form action="proses/bill.php" method="POST">
                <div class="card p-3">
                    <?php  
                       if (isset($_GET['inv_id'])) {
                            $selectedInvIds = $_GET['inv_id'];
                            $totalNonppn = 0;
                            $totalPpn = 0;
                            $totalBum = 0;
                        
                            // Lakukan sesuatu dengan data yang dipilih
                            // Misalnya, tampilkan daftar ID SPK yang dipilih
                            foreach ($selectedInvIds as $invId) {
                                echo '<input type="hidden" name="id_inv[]" value="' . $invId . '">';
                                // Lakukan sesuatu dengan data yang dipilih
                                // Jika Anda ingin menggabungkan elemen-elemen dengan pemisah, gunakan implode seperti sebelumnya
                                // hasil dari implode adalah 'BUM-2402dfa7b610809202', 'BUM-2402cb47f1c3ee2302'
                                $formattedInvIds = implode("', '", $selectedInvIds);

                                // Tambahkan tanda kutip pada awal dan akhir string
                                $formattedInvIds = "'" . $formattedInvIds . "'";

                                // Gantikan koma dengan koma dan spasi
                                $formattedInvIds = str_replace(",", "', '", $formattedInvIds);

                                // Lakukan sesuatu dengan data yang dipilih yang telah digabungkan
                                // echo $formattedInvIds;
                                $sql = mysqli_query($connect, "SELECT DISTINCT
                                                                    sr.id_customer, sr.id_inv, cs.nama_cs, 
                                                                    nonppn.id_inv_nonppn, 
                                                                    nonppn.no_inv AS no_inv_nonppn, 
                                                                    nonppn.cs_inv AS cs_nonppn, 
                                                                    STR_TO_DATE(nonppn.tgl_tempo, '%d/%m/%Y') AS tgl_tempo_nonppn, 
                                                                    STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y') AS tgl_inv_nonppn,
                                                                    nonppn.total_inv AS total_inv_nonppn,
                        
                                                                    ppn.id_inv_ppn, 
                                                                    ppn.no_inv AS no_inv_ppn, 
                                                                    ppn.cs_inv AS cs_ppn, 
                                                                    STR_TO_DATE(ppn.tgl_tempo, '%d/%m/%Y') AS tgl_tempo_ppn, 
                                                                    STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y') AS tgl_inv_ppn,
                                                                    ppn.total_inv AS total_inv_ppn,
                        
                                                                    bum.id_inv_bum, 
                                                                    bum.no_inv AS no_inv_bum, 
                                                                    bum.cs_inv AS cs_bum, 
                                                                    STR_TO_DATE(bum.tgl_tempo, '%d/%m/%Y') AS tgl_tempo_bum, 
                                                                    STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y') AS tgl_inv_bum,
                                                                    bum.total_inv AS total_inv_bum
                                                            FROM spk_reg AS sr
                                                            JOIN tb_customer cs ON (sr.id_customer = cs.id_cs)
                                                            LEFT JOIN inv_nonppn nonppn ON (sr.id_inv = nonppn.id_inv_nonppn)
                                                            LEFT JOIN inv_ppn ppn ON (sr.id_inv = ppn.id_inv_ppn)
                                                            LEFT JOIN inv_bum bum ON (sr.id_inv = bum.id_inv_bum)
                                                            WHERE sr.id_inv IN($formattedInvIds)");
                                
                                // Pengecekan apakah data ditemukan
                                if ($sql) {
                                    while ($data_inv = mysqli_fetch_array($sql)) {
                                        $nama_cs = $data_inv['nama_cs']; 
                                        $totalNonppn += $data_inv['total_inv_nonppn'];
                                        $totalPpn += $data_inv['total_inv_ppn'];
                                        $totalBum += $data_inv['total_inv_bum'];
                                        $grandTotal = $totalNonppn + $totalPpn + $totalBum;
                                    }
                                } else {
                                    // Atau Anda bisa mengatur $nama_cs menjadi nilai default jika tidak ada data
                                    $nama_cs = "Tidak ada Customer yang dipilih";
                                }
                            }
                        } 
                    ?>
                    
                    <?php
                    include "koneksi.php";
                    $year  = date('Y');
                    $sql  = mysqli_query($connect, "SELECT  CAST(MAX(CAST(SUBSTRING_INDEX(no_tagihan, '/', 1) AS UNSIGNED)) AS CHAR) AS maxID, STR_TO_DATE(tgl_tagihan, '%d/%m/%Y') AS tgl_tagihan FROM finance_tagihan WHERE YEAR(STR_TO_DATE(tgl_tagihan, '%d/%m/%Y')) = '$year'");
                    $data = mysqli_fetch_array($sql);

                    $kode = $data['maxID'];
                    $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
                    $ket1 = "/BILL/";
                    $bln = $array_bln[date('n')];
                    $ket2 = "/";
                    $ket3 = date("Y");
                    $urutkan = $kode; // Mengambil nilai maksimum langsung dari hasil query
                    $urutkan++;
                    $no_tagihan = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;

                    // Create id tagihan
                    $uuid = uuid();
                    $day = date('d');
                    $month = date('m');
                    $year = date('y');
                    $id_tagihan = "BILL" . $year . "" . $month . "" . $uuid . "" . $day ;
                    ?>
                    <div class="text-center mb-3">
                        <h5><b>Buat Tagihan</b></h5>
                    </div>
                    <div class="row mb-1">
                        <input type="hidden" class="form-control bg-light" name="id_tagihan" value="<?php echo $id_tagihan ?>" readonly>
                        <div class="col-md-3 mb-3">
                            <label>No. Tagihan</label>
                            <input type="text" class="form-control bg-light" name="no_tagihan" value="<?php echo $no_tagihan ?>" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Customer</label>
                            <input type="text" class="form-control" name="cs" value="<?php echo $nama_cs ?>" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Tgl. Tagihan</label>
                            <input type="text" class="form-control" id="date" name="tgl_tagihan" id="tgl_tagihan">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Jenis Faktur Tagihan</label>
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
                                <?php  
                                    $no = 1;
                                    $sql = mysqli_query($connect, "SELECT DISTINCT
                                                                    sr.id_customer, sr.id_inv, cs.nama_cs,
                                                                    nonppn.id_inv_nonppn, 
                                                                    nonppn.no_inv AS no_inv_nonppn, 
                                                                    nonppn.cs_inv AS cs_nonppn, 
                                                                    COALESCE(nonppn.tgl_tempo, ppn.tgl_tempo, bum.tgl_tempo) AS tgl_tempo,
                                                                    COALESCE(STR_TO_DATE(nonppn.tgl_tempo, '%d/%m/%Y'), STR_TO_DATE(ppn.tgl_tempo, '%d/%m/%Y'), STR_TO_DATE(bum.tgl_tempo, '%d/%m/%Y')) AS tgl_tempo_converted,
                                                                    STR_TO_DATE(nonppn.tgl_inv, '%d/%m/%Y') AS tgl_inv_nonppn,
                                                                    nonppn.total_inv AS total_inv_nonppn,

                                                                    ppn.id_inv_ppn, 
                                                                    ppn.no_inv AS no_inv_ppn, 
                                                                    ppn.cs_inv AS cs_ppn, 
                                                                    STR_TO_DATE(ppn.tgl_inv, '%d/%m/%Y') AS tgl_inv_ppn,
                                                                    ppn.total_inv AS total_inv_ppn,

                                                                    bum.id_inv_bum, 
                                                                    bum.no_inv AS no_inv_bum, 
                                                                    bum.cs_inv AS cs_bum, 
                                                                    STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y') AS tgl_inv_bum,
                                                                    bum.total_inv AS total_inv_bum
                                                                FROM spk_reg AS sr
                                                                JOIN tb_customer cs ON (sr.id_customer = cs.id_cs)
                                                                LEFT JOIN inv_nonppn nonppn ON (sr.id_inv = nonppn.id_inv_nonppn)
                                                                LEFT JOIN inv_ppn ppn ON (sr.id_inv = ppn.id_inv_ppn)
                                                                LEFT JOIN inv_bum bum ON (sr.id_inv = bum.id_inv_bum)
                                                                WHERE sr.id_inv IN($formattedInvIds) ORDER BY nonppn.no_inv, bum.no_inv, ppn.no_inv");
                                    $total_data = mysqli_num_rows($sql);
                                ?>
                                <tr class="text-white" style="background-color: navy;">
                                    <th class="text-center p-3 text-nowrap">No</th>
                                    <th class="text-center p-3 text-nowrap">No. Invoice</th>
                                    <th class="text-center p-3 text-nowrap">Customer Invoice</th>
                                    <th class="text-center p-3 text-nowrap">Tgl. Invoice</th>
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
                                    while ($data = mysqli_fetch_array($sql)) {
                                ?>
                                <tr>
                                    <td class="text-center text-nowrap"><?php echo $no; ?></td>
                                    <td class="text-center text-nowrap">
                                    <?php
                                        if (!empty($data['no_inv_nonppn'])) {
                                            echo $data['no_inv_nonppn'];
                                        } elseif (!empty($data['no_inv_ppn'])) {
                                            echo $data['no_inv_ppn'];
                                        } elseif (!empty($data['no_inv_bum'])) {
                                            echo $data['no_inv_bum'];
                                        }
                                    ?>
                                    </td>
                                    <td class="text-nowrap">
                                    <?php
                                        if (!empty($data['cs_nonppn'])) {
                                            echo $data['cs_nonppn'];
                                        } elseif (!empty($data['cs_ppn'])) {
                                            echo $data['cs_ppn'];
                                        } elseif (!empty($data['cs_bum'])) {
                                            echo $data['cs_bum'];
                                        }
                                    ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?php
                                            if (!empty($data['tgl_inv_nonppn'])) {
                                                echo date('d/m/Y',strtotime($data['tgl_inv_nonppn']));
                                            } elseif (!empty($data['tgl_inv_ppn'])) {
                                                echo date('d/m/Y',strtotime($data['tgl_inv_ppn']));
                                            } elseif (!empty($data['tgl_inv_bum'])) {
                                                echo date('d/m/Y',strtotime($data['tgl_inv_bum']));
                                            } else {
                                                echo '0000-00-00';
                                            }
                                        ?>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <?php
                                            // untuk di server ubah menjadi != '0000-00-00'
                                            if ($data['tgl_tempo'] != '') {
                                                echo date('d/m/Y',strtotime($data['tgl_tempo_converted']));
                                            } else {
                                                echo "Tidak Ada Tempo";
                                            }
                            
                                        ?>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <?php
                                            if (!empty($data['total_inv_nonppn'])) {
                                                echo number_format($data['total_inv_nonppn']);
                                            } elseif (!empty($data['total_inv_ppn'])) {
                                                echo number_format($data['total_inv_ppn']);
                                            } elseif (!empty($data['total_inv_bum'])) {
                                                echo number_format($data['total_inv_bum']);
                                            }
                                        ?>
                                    </td>
                                    <?php  
                                        if($total_data > 1){
                                            ?>
                                                <td class="text-center">
                                                    <button class="btn btn-danger btn-sm" type="button" data-id="<?php echo $data['id_inv']; ?>" onclick="removeValue(this)">Hapus Data</button>
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
                        <a href="finance-inv.php?date_range=monthly" class="btn btn-secondary btn-md">Cancel</a>
                        <button class="btn btn-primary btn-md" id="simpanButton" name="simpan-bill">Simpan</button>
                    </div>
                </div>
            </form>
        </section>
    </main>
    <?php include "page/footer.php" ?>
    <?php include "page/script.php" ?>
</body>
</html>
<?php  
    function uuid() {
        $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s%s', str_split(bin2hex($data), 4));
    }
?>
<script>
    function removeValue(button) {
        // Pastikan tombol yang ditekan memiliki atribut data-id
        var dataIdValue = button.getAttribute('data-id');

        if (dataIdValue !== null) {
            // console.log('Data-id:', dataIdValue);

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
                // console.log('New URL:', currentUrl.href);
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
