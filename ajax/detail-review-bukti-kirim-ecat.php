<?php
require_once __DIR__ .  '/../akses.php';
require_once __DIR__ .  '/../koneksi-ecat.php';
require_once __DIR__ . "/../function/function-enkripsi.php";
require_once __DIR__ . "/../function/format-tanggal.php";


if (isset($_POST['id'])) {
    $id_inv = decrypt($_POST['id'], $key_global); // Dekripsi ID 
    $id_inv = mysqli_real_escape_string($connect, $id_inv);

    $sql_bukti = "SELECT 
                    ibt.bukti_terima, 
                    ibt.lokasi,
                    ibt.created_date,
                    ecat.ongkir,   
                    ip.id_inv_ecat, 
                    ip.nama_penerima,
                    ip.tgl_terima,
                    ip.alamat, 
                    sk.jenis_pengiriman, 
                    sk.jenis_penerima, 
                    sk.id_ekspedisi, 
                    sk.id_driver,
                    sk.no_resi, 
                    sk.tgl_kirim AS tgl_kirim,
                    ex.nama_ekspedisi,
                    us.nama_user
                FROM inv_bukti_terima AS ibt
                LEFT JOIN inv_penerima ip ON (ibt.id_inv_ecat = ip.id_inv_ecat)
                LEFT JOIN inv_ecat ecat ON (ibt.id_inv_ecat = ecat.id_inv_ecat)
                LEFT JOIN status_kirim sk ON (ibt.id_inv_ecat = sk.id_inv_ecat)
                LEFT JOIN $db.ekspedisi ex ON (ex.id_ekspedisi = sk.id_ekspedisi) 
                LEFT JOIN $database2.user us ON (sk.id_driver = us.id_user)
                WHERE ibt.id_inv_ecat = '$id_inv'";
    $query_bukti = mysqli_query($connect_ecat, $sql_bukti);
    $data_bukti = mysqli_fetch_array($query_bukti); 
    if ($data_bukti) {
        $nama_driver = $data_bukti['nama_user'];
        $nama_driver = !empty($nama_driver) ? str_replace(' ', '_', $nama_driver) : '';
        $lokasi = $data_bukti['lokasi'];  
        $created_date = date("d-m-Y H:i:s", strtotime($data_bukti['created_date']));
        $gambar = $data_bukti['bukti_terima'];
        $gambar_encrypt = encrypt($gambar, $key_global);
        $gambar_encode_url = urlencode($gambar_encrypt);
        $jenis_penerima = $data_bukti['jenis_penerima'];
        $no_resi = $data_bukti['no_resi'];
        // Get encrypt di project ecat
        $response = file_get_contents('http://localhost:8082/aes.php?action=encrypt&data=' . urlencode($gambar));
        $result = json_decode($response, true);
        $view_image = urlencode($result['result']);
        $driver = urlencode($nama_driver);
        $path = "image-history.php?file=$gambar_encode_url&&driver=$driver";
        $path_ecat = "http://localhost:8082/image-history-ecat.php?file=$view_image";
        $img = "";
        if(!empty($nama_driver)){
            if($gambar && file_exists("../gambar/bukti_kirim/" . $nama_driver . "/" . $gambar)){
                $img = $path;
            } else {
                $img = $path;
                // $img = "assets/img/no_img.jpg";
            }
        } else {
            if ($gambar) {
                $img = $path_ecat ;
            } else {
                $img = "assets/img/no_img.jpg";
            }
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
                        if($data_bukti['jenis_pengiriman'] == "Diambil Langsung"){
                            ?>
                                <div class="text-center"><span class="text-dark fw-bold fs-6">Tanggal Upload</span></div>
                                <p class="card-text text-center"><?php echo $created_date ?></p>
                            <?php
                        } else if($data_bukti['jenis_penerima'] == "Ekspedisi" || $data_bukti['jenis_penerima'] == "Customer"){
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
                                </div>
                            <?php
                        } else {
                            echo "Maaf data tidak ditemukan";
                        }
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
                                                <td class="text-nowrap"><?php echo $data_bukti['ongkir']; ?></td>
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
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    <div>
                        <a href="proses/review-ecat.php?id=<?php echo urlencode($_POST['id']) ?>&&action=approval" class="btn btn-primary btn-md"><i class="bi bi-check-circle"></i> Approve</a>
                        <a href="proses/review-ecat.php?id=<?php echo urlencode($_POST['id']) ?>&&action=reject" class="btn btn-danger btn-md reject-data"><i class="bi bi-x-circle"></i> Reject</a>
                    </div>
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

<script>
    $(".reject-data").on("click", function (e) {
    e.preventDefault();
    var getLink = $(this).attr("href");

    // **SOLUSI: Tutup Modal Bootstrap sebelum menampilkan SweetAlert**
    $(".modal").modal("hide");

    setTimeout(() => {
        Swal.fire({
            title: "Anda yakin ingin reject?",
            text: "Masukkan alasan untuk menolak data ini:",
            icon: "warning",
            input: "textarea",
            inputPlaceholder: "Tulis alasan di sini...",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonColor: "#EB5406",
            cancelButtonColor: "#437C17",
            confirmButtonText: "Ya, Reject Data",
            cancelButtonText: "Batal",
            allowOutsideClick: false,
            inputValidator: (value) => {
                if (!value) {
                    return "Anda harus mengisi alasan!";
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var alasan = encodeURIComponent(result.value);
                window.location.href = getLink + "&alasan=" + alasan;
            }
        });
    }, 300); // **Tunggu 300ms setelah modal ditutup**
});

</script>






