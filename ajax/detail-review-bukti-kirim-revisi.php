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
    echo $id_komplain = decrypt($id, $key_global); // Dekripsi ID 
    $id_komplain = mysqli_real_escape_string($connect, $id_komplain);
    $id_komplain_encrypt = encrypt($id_komplain, $key_global);
    $sql_bukti = "SELECT 
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
                WHERE ibt.id_komplain = '$id_komplain'";
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
            <div class="card-footer">
                <div class="d-flex justify-content-center">
                    <div>
                    <a href="proses/review-revisi.php?id=<?php echo urlencode($id_komplain_encrypt) ?>&&action=approval&&token=<?php echo $_SESSION['csrf'] ?>" class="btn btn-primary btn-md approval-btn" onclick="disableButtons(this)">
                            <i class="bi bi-check-circle"></i> Approve
                        </a>

                        <a href="proses/review-revisi.php?id=<?php echo urlencode($id_komplain_encrypt) ?>&&action=reject&&token=<?php echo $_SESSION['csrf'] ?>" class="btn btn-danger btn-md reject-data" onclick="disableButtons(this)">
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









