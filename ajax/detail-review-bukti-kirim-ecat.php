<?php
require_once __DIR__ .  '/../akses.php';
require_once __DIR__ .  '/../koneksi-ecat.php';
require_once __DIR__ . "/../function/function-enkripsi.php";
require_once __DIR__ . "/../function/format-tanggal.php";
require_once __DIR__ . "/../function/CSRFToken.php";

$csrf = new CSRFToken();
$token = $csrf->generateToken();
$_SESSION['csrf'] = $token;

if (isset($_POST['id'])) {
    $id = urldecode($_POST['id']);
    $id_inv = decrypt($id, $key_global); // Dekripsi ID 
    $id_inv = mysqli_real_escape_string($connect, $id_inv);
    $id_inv_encrypt = encrypt($id_inv, $key_global);
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
                    us.nama_user AS nama_user,
                    uc.nama_user AS user_created
                FROM inv_bukti_terima AS ibt
                LEFT JOIN inv_penerima ip ON (ibt.id_inv_ecat = ip.id_inv_ecat)
                LEFT JOIN inv_ecat ecat ON (ibt.id_inv_ecat = ecat.id_inv_ecat)
                LEFT JOIN status_kirim sk ON (ibt.id_inv_ecat = sk.id_inv_ecat)
                LEFT JOIN $db.ekspedisi ex ON (ex.id_ekspedisi = sk.id_ekspedisi) 
                LEFT JOIN $database2.user us ON (sk.id_driver = us.id_user)
                 LEFT JOIN $database2.user uc ON (ibt.created_by = uc.id_user)
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
            if($gambar && file_exists("../gambar/bukti_kirim/ecat/" . $nama_driver . "/" . $gambar)){
                $img = $path;
            } else {
                $img = "assets/img/no_img.jpg";
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
                                <p class="card-text text-center"><?php echo $created_date ?></p>
                                
                                <div class="text-center"><span class="text-dark fw-bold fs-6">User Upload</span></div>
                                <p class="card-text text-center"><?php echo $data_bukti['user_created'] ?></p>
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

                                    <div class="text-center"><span class="text-dark fw-bold fs-6">User Upload</span></div>
                                        <p class="card-text text-center"><?php echo $data_bukti['user_created'] ?></p>
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
                        <a href="proses/review-ecat.php?id=<?php echo urlencode($id_inv_encrypt) ?>&&action=approval&&token=<?php echo $_SESSION['csrf'] ?>" class="btn btn-primary btn-md approval-btn" onclick="disableButtons(this)">
                            <i class="bi bi-check-circle"></i> Approve
                        </a>

                        <a href="proses/review-ecat.php?id=<?php echo urlencode($id_inv_encrypt) ?>&&action=reject&&token=<?php echo $_SESSION['csrf'] ?>" class="btn btn-danger btn-md reject-data" onclick="disableButtons(this)">
                            <i class="bi bi-x-circle"></i> Reject
                        </a>
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

        $(".modal").modal("hide");

        setTimeout(() => {
            let selectedJenis = null;
            let alasanReject = "";

            // STEP 1 - Pilih Jenis Reject
            function showStep1() {
                Swal.fire({
                    title: "Pilih Jenis Reject",
                    html: `
                        <div class="text-start">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenisReject" id="gambar" value="1" ${selectedJenis === "1" ? "checked" : ""}>
                                <label class="form-check-label" for="gambar">Gambar</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenisReject" id="data" value="2" ${selectedJenis === "2" ? "checked" : ""}>
                                <label class="form-check-label" for="data">Data</label>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: "Next",
                    cancelButtonText: "Batal",
                    preConfirm: () => {
                        const selected = document.querySelector('input[name="jenisReject"]:checked');
                        if (!selected) {
                            Swal.showValidationMessage("Silakan pilih jenis reject terlebih dahulu!");
                            return false;
                        }
                        selectedJenis = selected.value;
                        return true;
                    }
                }).then((step1) => {
                    if (step1.isConfirmed) {
                        showStep2();
                    }
                });
            }

            // STEP 2 - Masukkan Alasan
            function showStep2() {
                Swal.fire({
                    title: "Masukkan Alasan Reject",
                    html: `
                        <textarea id="textareaAlasan" placeholder="Masukkan alasan di sini..." class="form-control">${alasanReject}</textarea>
                    `,
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: "Submit",
                    denyButtonText: "Kembali",
                    cancelButtonText: "Batal",
                    preConfirm: () => {
                        const alasan = document.getElementById("textareaAlasan").value.trim();
                        if (!alasan) {
                            Swal.showValidationMessage("Alasan tidak boleh kosong!");
                            return false;
                        }
                        alasanReject = alasan;
                        return true;
                    }
                }).then((step2) => {
                    if (step2.isDenied) {
                        const textareaValue = document.getElementById("textareaAlasan").value.trim();
                        if (textareaValue) {
                            alasanReject = textareaValue;
                        }
                        showStep1();
                    } else if (step2.isConfirmed) {
                        const alasan = encodeURIComponent(alasanReject);
                        const jenis = encodeURIComponent(selectedJenis);
                        window.location.href = getLink + `&jenis=${jenis}&alasan=${alasan}`;
                    }
                });
            }

            // Mulai dari step 1
            showStep1();

        }, 300);
    });
</script>

<!-- Kode untuk disable button -->
<script>
    function disableButtons(btn) {
        // Nonaktifkan tombol yang diklik
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

        // Optional: nonaktifkan semua tombol lain juga
        document.querySelectorAll('a.btn').forEach(function(button) {
            button.classList.add('disabled');
            button.style.pointerEvents = 'none';
        });
    }
</script>






