<?php
require_once __DIR__ . '/../akses.php'; // Pastikan koneksi database tersedia
require_once __DIR__ . '/../koneksi-ecat.php';
require_once __DIR__ . "/../function/function-enkripsi.php";
require_once __DIR__ . "/../function/format-tanggal.php";

if (isset($_POST['id'])) {
    $id_inv = decrypt($_POST['id'], $key_global); // Dekripsi ID 
    $id_inv = mysqli_real_escape_string($connect, $id_inv);
    $no = 1;
    $sql = $connect_ecat->query("
                                    SELECT DISTINCT
                                        COALESCE(ecat.no_inv_ecat) AS no_inv,
                                        COALESCE(ecat.satker_inv) AS satker,
                                        hibt.bukti_terima, 
                                        hibt.lokasi, 
                                        sk.jenis_pengiriman,
                                        us.nama_user AS nama_driver,
                                        uc.nama_user AS user_created,
                                        ua.nama_user AS user_approval,
                                        ip.nama_penerima,
                                        ex.nama_ekspedisi,
                                        hibt.approval, 
                                        hibt.alasan,
                                        hibt.approval_date, 
                                        hibt.created_date
                                    FROM history_inv_bukti_terima AS hibt
                                    LEFT JOIN inv_ecat AS ecat ON hibt.id_inv = ecat.id_inv_ecat
                                    LEFT JOIN status_kirim sk ON hibt.id_inv = sk.id_inv_ecat
                                    LEFT JOIN inv_penerima ip ON hibt.id_inv = ip.id_inv_ecat
                                    LEFT JOIN ekspedisi ex ON sk.id_ekspedisi = ex.id_ekspedisi
                                    LEFT JOIN $database2.user AS us ON sk.id_driver = us.id_user
                                    LEFT JOIN $database2.user AS uc ON hibt.created_by = uc.id_user
                                    LEFT JOIN $database2.user AS ua ON hibt.approval_by = ua.id_user
                                    WHERE hibt.id_inv = '$id_inv'
                                    ORDER BY hibt.approval_date DESC
                                ");

    if($sql){
        ?>
            <style>
                .inline-div {
                    display: inline;
                }
            </style>
            <div class="w-50 bg-transparent">
                <table class="table table-borderless">
                    <tr>
                        <td class="fw-bold col-3">No. Invoice</td>
                        <td>:</td>
                        <td><span class="fw-bold" id="noInv"></span></td>
                    </tr>
                    <tr>
                        <td class="fw-bold col-3">Satker</td>
                        <td>:</td>
                        <td><span class="fw-bold" id="satker"></span></div></td>
                    </tr>
                </table>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="text-center text-nowrap p-2">No</th>
                            <th class="text-center text-nowrap p-2">Jenis Pengiriman</th>
                            <th class="text-center text-nowrap p-2">Diupload Oleh</th>
                            <th class="text-center text-nowrap p-2">Dilakukan Oleh</th>
                            <th class="text-center text-nowrap p-2">Status</th>
                            <th class="text-center text-nowrap p-2">Alasan</th>
                            <th class="text-center text-nowrap p-2">Bukti Kirim</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  
                             while($data = mysqli_fetch_assoc($sql)){ 
                                    $no_inv = $data['no_inv'];
                                    $satker = $data['satker'];
                                    $diupload_oleh = !empty($data['nama_driver']) ? $data['nama_driver'] : $data['user_created'];
                                    $pengirim = '';
                                    if (!empty($data['nama_driver'])) {
                                        $pengirim = $data['nama_driver'];
                                    } elseif (!empty($data['nama_ekspedisi'])) {
                                        $pengirim = $data['nama_ekspedisi'];
                                    } else {
                                        $pengirim = $data['nama_penerima'];
                                    }                                    
                                    $approval = $data['approval'];
                                    $status = '';
                                    $alasan = '';
                                    if($approval == '1'){
                                        $status = '<span class="badge bg-danger">Reject</span>';
                                        $alasan = $data['alasan'];
                                    } else if($approval == '2'){
                                        $status = '<span class="badge bg-success">Approved</span>';
                                        $alasan = '-';
                                    }
                                    $nama_driver = $data['nama_driver'];
                                    $nama_driver = !empty($nama_driver) ? str_replace(' ', '_', $nama_driver) : '';
                                    $lokasi = $data['lokasi'];
                                    $created_date = $data['created_date'];
                                    $gambar = basename($data['bukti_terima']);
                                    // Get encrypt di project ecat
                                    $response = file_get_contents('http://localhost:8082/aes.php?action=encrypt&data=' . urlencode($gambar));
                                    $result = json_decode($response, true);
                                    $view_image = urlencode($result['result']);
                                    $path = "http://localhost:8082/image-history-ecat.php?file=$view_image";
                                    $img = "";
                                    if ($gambar) {
                                        $img = $path ;
                                    } else {
                                        $img = "assets/img/no_img.jpg";
                                    }
                                ?>
                        <tr>
                            <td class="text-center">
                                <?php echo $no; ?><br>
                            </td>
                            <td class="text-nowrap text-center">
                                <div><?php echo $data['jenis_pengiriman'] ?></div>
                                <div>(<?php echo ucfirst($pengirim) ?>)</div>
                            </td>
                            <td class="text-center text-nowrap">
                                <div><?php echo ucfirst($diupload_oleh) ?></div>
                                <div><?php echo date('d/m/Y H:i:s', strtotime($data['created_date'])) ?></div>
                            </td>
                            <td class="text-center text-nowrap">
                                <div><?php echo ucfirst($data['user_approval']) ?></div>
                                <div><?php echo date('d/m/Y H:i:s', strtotime($data['approval_date'])) ?></div>
                            </td>
                            <td class="text-center"><?php echo $status; ?></td>
                            <td class="text-center"><?php echo $alasan; ?></td>
                            <td class="text-center">
                                <a href="<?php echo $img; ?>" data-fancybox="gallery" data-width="1600" data-height="1200">
                                    <img src="<?php echo $img; ?>" class="image img-fluid rounded" alt="..." id="buktiTerimaImg" width="50px" height="50px">
                                </a>
                            </td>
                        </tr>
                        <?php $no++; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div> 
        <?php
    } else {
        echo "<div class='alert alert-danger text-center'>Data tidak ditemukan.</div>";
    }
}
?>
<script>
    function reloadPage() {
        location.reload(); // Reload halaman
    }

    var noInv = "<?php echo $no_inv ?>";
    var satker = "<?php echo $satker ?>";

    $('#noInv').text(noInv);
    $('#satker').text(satker);

</script>






