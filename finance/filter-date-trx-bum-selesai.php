<?php
require_once "../akses.php";
require_once "../function/function-enkripsi.php";
$page  = 'transaksi';
$page2 = 'spk';
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
    <div class="table-responsive" id="filteredDataBUM">
        <table class="table table-bordered table-striped" id="tablebumfilter">
            <thead>
                <tr class="text-white" style="background-color: navy;">
                    <th class="text-center p-3 text-nowrap" style="width: 30px">No</th>
                    <th class="text-center p-3 text-nowrap" style="width: 150px">No. Invoice</th>
                    <th class="text-center p-3 text-nowrap" style="width: 150px">Tgl. Invoice</th>
                    <th class="text-center p-3 text-nowrap" style="width: 250px">Nama Customer</th>
                    <th class="text-center p-3 text-nowrap" style="width: 100px">Kat. Inv</th>
                    <th class="text-center p-3 text-nowrap" style="width: 100px">Total Invoice</th>
                    <th class="text-center p-3 text-nowrap" style="width: 100px">Status Pembayaran</th>
                    <th class="text-center p-3 text-nowrap" style="width: 150px">Jenis Pengiriman</th>
                    <th class="text-center p-3 text-nowrap" style="width: 80px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                $start_date = $_GET['start_date_bum']; // Tanggal awal rentang
                $end_date = $_GET['end_date_bum'];// Tanggal akhir rentang
                $sql = "SELECT  
                            bum.id_inv_bum,
                            bum.no_inv, 
                            STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y') AS tgl_inv,
                            bum.cs_inv, 
                            bum.tgl_tempo, 
                            bum.sp_disc, 
                            bum.note_inv, 
                            bum.kategori_inv, 
                            bum.ongkir, 
                            bum.total_inv, 
                            bum.status_transaksi, 
                            sr.id_inv, 
                            sr.id_customer, 
                            sr.no_po, 
                            cs.nama_cs, cs.alamat, 
                            fn.status_pembayaran, fn.id_inv,
                            sk.jenis_pengiriman,
                            us.nama_user AS nama_driver,
                            ex.nama_ekspedisi,
                            ip.nama_penerima,
                            kmpl.id_komplain
                        FROM inv_bum AS bum
                        LEFT JOIN spk_reg sr ON(bum.id_inv_bum = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        JOIN finance fn ON (fn.id_inv = bum.id_inv_bum)
                        LEFT JOIN status_kirim sk ON (bum.id_inv_bum = sk.id_inv)
                        LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                        LEFT JOIN ekspedisi ex ON (sk.dikirim_ekspedisi = ex.id_ekspedisi)
                        LEFT JOIN inv_penerima ip ON (bum.id_inv_bum = ip.id_inv)
                        LEFT JOIN inv_komplain kmpl ON (bum.id_inv_bum = kmpl.id_inv)
                        WHERE (bum.status_transaksi = 'Transaksi Selesai' OR bum.status_transaksi = 'Komplain Selesai')
                            AND STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y') >= STR_TO_DATE('$start_date', '%d/%m/%Y') 
                            AND STR_TO_DATE(bum.tgl_inv, '%d/%m/%Y') <= STR_TO_DATE('$end_date', '%d/%m/%Y')
                        GROUP BY no_inv ORDER BY no_inv";
                $query = mysqli_query($connect, $sql);
                while ($data = mysqli_fetch_array($query)) {
                    $status_trx = $data['status_transaksi'];
                    $id_komplain = $data['id_komplain'];
                    $status_pembayaran = $data['status_pembayaran'] == 0 ? 'Belum Bayar' : 'Sudah Bayar';
                        $jenis_pengiriman =  $data['jenis_pengiriman'];
                        $nama_pengiriman = [
                            'Driver' => $data['nama_driver'],
                            'Ekspedisi' => $data['nama_ekspedisi'],
                            'Diambil Langsung' => $data['nama_penerima']
                        ][$jenis_pengiriman];               
                ?>
                    <tr>
                        <td class="text-center text-nowrap"><?php echo $no; ?></td>
                        <td class="text-nowrap text-center">
                            <?php echo $data['no_inv'] ?><br>
                            <?php  
                                if (!empty($data['no_po'])){
                                    echo "(<b>" . $data['no_po'] . "</b>)";
                                }
                            ?>
                        </td>
                        <td class="text-nowrap text-center"><?php echo date('d/m/Y',strtotime($data['tgl_inv'])) ?></td>
                        <td class="text-nowrap"><?php echo $data['nama_cs'] ?></td>
                        <td class="text-nowrap text-center"><?php echo $data['kategori_inv'] ?></td>
                        <td class="text-nowrap text-end"><?php echo number_format($data['total_inv'])?></td>
                        <td class="text-nowrap text-center"><?php echo $status_pembayaran; ?></td>
                        <td class="text-nowrap text-center">
                            <?php   
                                echo $jenis_pengiriman . '<br>';
                                echo "(" .$nama_pengiriman . ")";
                            ?>
                        </td>
                        <td class="text-center text-nowrap">
                            <?php  
                                if($status_trx == 'Komplain Selesai'){
                                    ?>
                                        <a href="detail-produk-selesai-revisi-bum.php?jenis=bum&&id=<?php echo base64_encode($data['id_komplain']) ?>" class="btn btn-primary btn-sm mb-2"><i class="bi bi-eye-fill"></i></a>
                                    <?php
                                } else {
                                    ?>
                                        <a href="detail-produk-selesai.php?jenis=bum&&id=<?php echo encrypt($data['id_inv_bum'], $key_global) ?>" class="btn btn-primary btn-sm mb-2" title="Lihat Produk"><i class="bi bi-eye-fill"></i></a>
                                    <?php
                                }
                            ?>
                        </td>
                    </tr> 
                    <?php $no++ ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include "page/script.php" ?>
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            var table = $('#table9').DataTable({
                "lengthChange": false,
                "ordering": false,
                "autoWidth": false
            });
        });

        function filterDataBUM() {
            // Ambil nilai filter dari elemen select
            var dateRangeBumValue = document.getElementById('dateRangeBUM').value;
            var startDate = document.getElementById('start_date_bum').value;
            var endDate = document.getElementById('end_date_bum').value;

            // Buat objek XMLHttpRequest
            var xhttp = new XMLHttpRequest();

            // Atur callback function untuk menangani perubahan status permintaan
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Update elemen filteredData dengan hasil filter yang selesai dari server
                    document.getElementById('filteredDataBUM').innerHTML = this.responseText;
                    // Inisialisasi ulang DataTable setelah mengganti isi tabel
                    $('#table9').DataTable({
                        "lengthChange": false,
                        "ordering": false,
                        "autoWidth": false
                    });

                    flatpickr("#start_date_bum", {
                        dateFormat: "d/m/Y",
                        onClose: function(selectedDates, dateStr, instance) {
                        // Ambil tanggal awal yang dipilih
                        var startDate = selectedDates[0];

                        // Perbarui batas tanggal maksimal pada pemilih tanggal akhir
                        var endDateInput = document.getElementById("end_date");
                        var endDatePicker = flatpickr("#end_date_bum", {
                            dateFormat: "d/m/Y",
                            minDate: startDate,
                            maxDate: new Date(startDate.getTime() + 30 * 24 * 60 * 60 * 1000)
                        });

                        // Jika tanggal akhir saat ini berada di bawah tanggal awal, hapus nilainya
                        var endDate = endDatePicker.selectedDates[0];
                        if (endDate < startDate) {
                            endDateInput.value = "";
                        }
                        }
                    });
                }
            };

            // Buat permintaan GET ke file PHP yang akan memproses filter
            var url = 'filter-date-trx-bum-selesai.php?start_date_bum=' + startDate + '&end_date_bum=' + endDate ;
            xhttp.open('GET', url, true);
            xhttp.send();
            // Tambahkan kode berikut untuk menjaga collapse tetap terbuka setelah button cari data diklik
            $('#bum').collapse('show');
        }
    </script>
</body>
</html>