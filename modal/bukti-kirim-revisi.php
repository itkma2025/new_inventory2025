<?php
include '../akses.php'; // Pastikan koneksi database tersedia
require_once __DIR__ . "/../function/function-enkripsi.php";
require_once __DIR__ . "/../function/format-tanggal.php"; 
require_once __DIR__ . "/../function/CSRFToken.php";

$csrf = new CSRFToken();
$token = $csrf->generateToken();
$_SESSION['csrf'] = $token;

if (isset($_POST['id'])) {
    $id = urldecode($_POST['id']);
    $id_komplain = decrypt($id, $key_spk); // Dekripsi ID 
    $id_komplain = mysqli_real_escape_string($connect, $id_komplain);
    $id_komplain_encrypt = encrypt($id_komplain, $key_global);
    $sql_bukti = "SELECT
                    ibt.id_komplain,     
                    ibt.bukti_satu, 
                    ibt.bukti_dua, 
                    ibt.bukti_tiga, 
                    ibt.lokasi,
                    ibt.created_date,
                    COALESCE(nonppn.ongkir, ppn.ongkir, bum.ongkir) AS ongkir,   
                    ip.id_komplain, 
                    ip.nama_penerima,
                    STR_TO_DATE(ip.tgl_terima, '%d/%m/%Y') AS tgl_terima,
                    ip.alamat, 
                    sk.jenis_pengiriman, 
                    sk.jenis_penerima, 
                    sk.dikirim_ekspedisi, 
                    sk.dikirim_driver,
                    sk.no_resi, 
                    STR_TO_DATE(sk.tgl_kirim, '%d/%m/%Y') AS tgl_kirim,
                    ex.nama_ekspedisi,
                    us.nama_user AS nama_user,
                    uc.nama_user AS user_created
                FROM inv_bukti_terima_revisi AS ibt
                LEFT JOIN inv_penerima_revisi ip ON (ibt.id_komplain = ip.id_komplain)
                LEFT JOIN inv_nonppn nonppn ON (ibt.id_komplain = nonppn.id_inv_nonppn)
                LEFT JOIN inv_ppn ppn ON (ibt.id_komplain = ppn.id_inv_ppn)
                LEFT JOIN inv_bum bum ON (ibt.id_komplain = bum.id_inv_bum)
                LEFT JOIN revisi_status_kirim sk ON (ibt.id_komplain = sk.id_komplain)
                LEFT JOIN ekspedisi ex ON (ex.id_ekspedisi = sk.dikirim_ekspedisi) 
                LEFT JOIN $database2.user us ON (sk.dikirim_driver = us.id_user)
                LEFT JOIN $database2.user uc ON (ibt.created_by = uc.id_user)
                WHERE ibt.id_komplain = '$id_komplain' GROUP BY ibt.id_komplain";
    $query_bukti = mysqli_query($connect, $sql_bukti);
    $data_bukti = mysqli_fetch_array($query_bukti);   
    if ($data_bukti) {
        $nama_driver = $data_bukti['nama_user'];
        $nama_driver = !empty($nama_driver) ? str_replace(' ', '_', $nama_driver) : '';
        $lokasi = $data_bukti['lokasi'];   
        $created_date = $data_bukti['created_date'];
        $jenis_penerima = $data_bukti['jenis_penerima'];
        $no_resi = $data_bukti['no_resi'];
        $gambar = $data_bukti['bukti_satu'];
        $encrypt_image = encrypt($gambar, $key_global);
        $view_image = urlencode($encrypt_image);
        $driver = urlencode($nama_driver);
        $path = "image-history-revisi.php?file=$view_image&&driver=$driver";
        $img = "";
        if ($gambar && file_exists("../gambar-revisi/bukti1/" . $gambar)) {
            $img = $path;
        } else if($gambar && file_exists("../gambar-revisi/bukti_kirim/" . $nama_driver . "/" . $gambar)){
            $img = $path;
        } else {
            $img = "assets/img/no_img.jpg";
        }
    ?>
        <div class="card mb-3 p-2">
            <div class="card-header text-center fw-bold fs-5 text-dark">
                Bukti Pengiriman Barang
            </div>
            <div class="row g-0 mt-3">
                <div class="col-md-5 container-img">
                    <a href="<?php echo $img; ?>" data-fancybox="gallery" data-width="1600" data-height="1200">
                        <img src="<?php echo $img; ?>" class="image img-fluid rounded img-preview" alt="..." id="buktiTerimaImg">
                    </a>
                    <?php  
                        ?>
                            <div class="card-body mt-2">
                                <?php  
                                    if($lokasi != ""){
                                        ?>
                                            <div class="text-center"><span class="text-dark fw-bold fs-6">Lokasi Upload</span></div>
                                            <p class="text-center text-wrap" style="text-align: justify;">
                                                <?php echo $lokasi ?>
                                            </p>
                                        <?php
                                    }
                                ?>
                                <div class="text-center"><span class="text-dark fw-bold fs-6">Tanggal Upload</span></div>
                                <p class="card-text text-center"><?php echo formatTanggalIndonesia($created_date) ?></p>

                                <div class="text-center"><span class="text-dark fw-bold fs-6">User Upload</span></div>
                                <p class="card-text text-center"><?php echo $data_bukti['user_created'] ?></p>
                            </div>
                        <?php
                    ?>
                </div>
                <div class="col-md-7">
                    <?php  
                        if($data_bukti['jenis_penerima'] == "Ekspedisi"){
                            ?>
                                <div class="card-header text-dark">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <?php  
                                                if ($data_bukti['nama_user'] != "") {
                                                    ?>
                                                        <tr>
                                                            <td class="text-nowrap" style="width:180px;">Nama Pengirim</td>
                                                            <td>:</td>
                                                            <td class="text-nowrap"><?php echo $data_bukti['nama_user'] ?></td>
                                                        </tr>
                                                    <?php
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Tanggal Pengiriman</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo formatTanggalIndonesia($data_bukti['tgl_kirim'])?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-header text-dark">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Jenis Pengiriman</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo $data_bukti['jenis_pengiriman']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Jenis Penerima</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo $data_bukti['jenis_penerima']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Nama Ekspedisi</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo $data_bukti['nama_ekspedisi']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Nominal Ongkir</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo number_format($data_bukti['ongkir'],0,'.','.'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">No Resi</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo $data_bukti['no_resi']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Tujuan Pengiriman</td>
                                                <td>:</td>
                                                <td class="text-wrap"><?php echo $data_bukti['alamat']; ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Nama Penerima Paket</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo $data_bukti['nama_penerima']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Tanggal Diterima</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo formatTanggalIndonesia($data_bukti['tgl_terima']); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php
                        } else if ($data_bukti['jenis_penerima'] == "Customer"){
                            ?>  
                                <div class="card-header text-dark">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <?php  
                                                if ($data_bukti['jenis_pengiriman'] != "Diambil Langsung"){
                                                    ?>
                                                        <tr>
                                                            <td class="text-nowrap" style="width:180px;">Nama Pengirim</td>
                                                            <td>:</td>
                                                            <td class="text-nowrap"><?php echo $data_bukti['nama_user'] ?></td>
                                                        </tr>
                                                    <?php
                                                }
                                            
                                            ?>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Tanggal Pengiriman</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo formatTanggalIndonesia($data_bukti['tgl_kirim'])?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-header text-dark">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Jenis Pengiriman</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo $data_bukti['jenis_pengiriman']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Jenis Penerima</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo $data_bukti['jenis_penerima']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Nama Penerima Paket</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo $data_bukti['nama_penerima']; ?></td>
                                            </tr>
                                            <?php  
                                                if ($data_bukti['jenis_pengiriman'] != "Diambil Langsung"){
                                                    ?>
                                                        <tr>
                                                            <td class="text-nowrap" style="width:180px;">Tujuan Pengiriman</td>
                                                            <td>:</td>
                                                            <td class="text-wrap"><?php echo $data_bukti['alamat']; ?></td>
                                                        </tr>
                                                    <?php
                                                }
                                            
                                            ?>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Tanggal Diterima</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo formatTanggalIndonesia($data_bukti['tgl_terima']); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php
                        } else if ($data_bukti['jenis_pengiriman'] == "Diambil Langsung"){
                            ?>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Nama Penerima</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo $data_bukti['nama_penerima']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-nowrap" style="width:180px;">Tanggal Diterima</td>
                                                <td>:</td>
                                                <td class="text-nowrap"><?php echo formatTanggalIndonesia($data_bukti['tgl_terima']); ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php
                        } else {
                            echo "Maaf data tidak di temukan";
                        }
                    ?>
                </div>
            </div> 
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
</script>











