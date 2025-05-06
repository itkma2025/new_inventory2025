<?php 
    require_once "akses.php" 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk</title>
    <?php include "page/head.php"; ?>
    <style>
        .custom-margin{
            margin-bottom: 2px !important;
        }
        .scrolly {
            font-size: 16px;
            max-height: 100%; /* Biarkan elemen <p> menyesuaikan diri dengan tinggi maksimum kartu */
            overflow-y: auto; /* Menambahkan scrollbar vertikal jika konten melebihi tinggi kartu */
            position: absolute; /* Biarkan elemen <p> diatur relatif ke kartu */
            top: 0; /* Atur elemen <p> mulai dari bagian atas kartu */
            bottom: 0; /* Pastikan elemen <p> mengisi hingga bagian bawah kartu */
            width: 100%; /* Pastikan elemen <p> memenuhi lebar kartu */
        }

        .card-custom {
            max-height: 300px; /* Tinggi maksimum kartu */
            overflow: auto; /* Sembunyikan konten yang melampaui tinggi maksimum */
            position: relative; /* Pastikan elemen di dalamnya diatur relatif ke kartu */
            text-align: justify;
            padding: 10px;
        }

        .mobile {
            display: none;
        }

        @media screen and (max-width: 600px) {
            .desktop {
               display: none;
            }

            .mobile {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php  
        if ($role == "Super Admin" || $role == "Manager Gudang" || $role == "Admin Penjualan" || $role == "Kepala Gudang" || $role == "Operator Gudang" || $role == "Admin Gudang") { 
            ?>
                <div class="container mt-4">
                    <?php
                        $key = "KM@2024?";
                        //tangkap URL dengan $_GET
                        $id = $_GET['id'];
                        $id_produk = decrypt($id, $key);
                        //mengambil nama gambar yang terkait
                        $sql = "SELECT 
                                    pr.kode_produk,
                                    pr.kode_katalog,
                                    pr.nama_produk,
                                    pr.satuan,
                                    pr.harga_produk,
                                    pr.gambar,
                                    pr.deskripsi,  
                                    DATE_FORMAT(pr.created_date, '%d/%m/%Y, %H:%i:%s') AS produk_created,  -- Format tanggal Indonesia
                                    pr.created_by,
                                    CASE 
                                        WHEN pr.updated_date = '0000-00-00 00:00:00' THEN '-'
                                        ELSE DATE_FORMAT(pr.updated_date, '%d/%m/%Y, %H:%i:%s')
                                    END AS produk_updated,
                                    pr.id_produk_reg as 'produk_id',  
                                    uc.nama_user as user_created, 
                                    uu.nama_user as user_updated,
                                    mr.nama_merk,
                                    kp.nama_kategori as kat_prod,
                                    kp.no_izin_edar,
                                    kj.nama_kategori as kat_penj,
                                    gr.nama_grade,
                                    lok.nama_lokasi,
                                    lok.nama_area,
                                    lok.no_lantai,
                                    lok.no_rak,
                                    spr.stock
                                FROM tb_produk_reguler as pr
                                LEFT JOIN $database2.user AS uc ON (pr.created_by = uc.id_user)
                                LEFT JOIN $database2.user AS uu ON (pr.updated_by = uu.id_user)
                                LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                                LEFT JOIN tb_kat_produk kp ON (pr.id_kat_produk = kp.id_kat_produk)
                                LEFT JOIN tb_kat_penjualan kj ON (pr.id_kat_penjualan = kj.id_kat_penjualan)
                                LEFT JOIN tb_produk_grade gr ON (pr.id_grade = gr.id_grade)
                                LEFT JOIN tb_lokasi_produk lok ON (pr.id_lokasi = lok.id_lokasi)
                                LEFT JOIN stock_produk_reguler spr ON (pr.id_produk_reg = spr.id_produk_reg)
                                WHERE pr.id_produk_reg ='$id_produk'";
                        $result = mysqli_query($connect, $sql);
                        $data = mysqli_fetch_array($result);
                        $img = "";
                        if ($data['gambar'] && file_exists("gambar/upload-produk-reg/" . $data['gambar'])) {
                            $img = "gambar/upload-produk-reg/" . $data['gambar'];
                        } else {
                            $img = "assets/img/no_img.jpg";
                        }
                    ?>
                    <div class="card">
                        <div class="card-header text-center fw-bold">
                            <h4>Detail Produk Reguler</h4>
                        </div>
                        <div class="card-body">
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-center">
                                        <?php  
                                            $sql_produk_reg = $connect->query("SELECT id_produk, qty_in, qty_out FROM kartu_stock_reg WHERE id_produk ='$id_produk'");
                                            $total_qty_in = 0;
                                            $total_qty_out = 0;
                                            $stock_tersedia = 0;
                                            $total_data_kartu = mysqli_num_rows($sql_produk_reg);
                                            if($total_data_kartu != 0) {
                                                while($data_stock_tersedia = mysqli_fetch_array($sql_produk_reg)){
                                                    $total_qty_in += $data_stock_tersedia['qty_in'];
                                                    $total_qty_out +=  $data_stock_tersedia['qty_out'];
                                                    $stock_tersedia = $total_qty_in - $total_qty_out;
                                                }
                                            }
                                        ?>
                                        <div class="card border" style="width: 250px;">
                                            <p class="text-center fw-bold mt-2" style="font-size: 20px;">Stock Barang</p>
                                            <p class="text-center" style="font-size: 18px;"><?php echo  $stock_tersedia ?></p>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center mt-1">
                                        <div class="card shadow-lg border" style="width: 500px; height: 500px;">
                                            <img src="<?php echo $img ?>" style="height: 100%; width: 100%;" class="img-fluid">
                                        </div>
                                    </div>
                                    <div class="col-md-12 border-top border-dark"></div>
                                    <p class="mt-2">Dibuat Oleh : <?php echo $data['user_created'] ?> (<?php echo $data['produk_created'] ?>)</p>
                                    <p>Diubah Oleh : <?php echo $data['user_updated'] ?> (<?php echo $data['produk_updated'] ?>)</p>
                                    
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-end mt-4">
                                        <?php  
                                            // Cek apakah sesi 'url' sudah ada
                                            if (isset($_SESSION['url'])) {
                                                // Hapus sesi 'url' yang sudah ada
                                                unset($_SESSION['url']);
                                            }
                                            $session_url = "";
                                            if ($role == "Driver") {
                                                $session_url = "driver/kartu-persediaan-barang.php?jenis_produk=reguler&id=" . $id;
                                            } else if ($role == "Finance") {
                                                header("Location:404.php");
                                            } else {
                                                $session_url = "kartu-persediaan-barang.php?jenis_produk=reguler&id=" . $id;
                                            }
                                            $_SESSION['url'] = $session_url;
                                            $get_url = $_SESSION['url'];
                                        ?>
                                        <a href="<?php echo $get_url . "&sort_data=semua_data" ?>" class="btn btn-primary desktop"><i class="bi bi-clipboard2-data-fill"></i> Lihat Kartu Stock</a>
                                    </div>
                                    <p class="mt-3 custom-margin" style="font-size: 16px;"><?php echo $data['nama_grade'] ?> / <?php echo $data['kat_prod'] ?> / <?php echo $data['kode_produk']; ?></p>
                                    <p class="fw-bold custom-margin" style="font-size: 19px;"><?php echo $data['nama_merk']; ?> / <?php echo $data['nama_produk']; ?> / <?php echo $data['no_izin_edar'] ?></p>
                                    <p class="custom-margin" style="font-size: 16px;">Kode Katalog : <?php echo $data['kode_katalog']; ?></p>
                                    <p class="fw-bold mt-4" style="font-size: 19px;">Rp. <?php echo number_format($data['harga_produk'],0,'.','.'); ?>,-</p>
                                    <p class="fw-bold mt-4" style="font-size: 19px;">Deskripsi Produk :</p>
                                    <div class="card-custom scrolly">
                                        <p class="custom-margin" style="font-size: 16px;"><?php echo $data['deskripsi'] ?></p>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th class="text-center p-2">Lokasi</th>
                                                    <th class="text-center p-2">Lantai</th>
                                                    <th class="text-center p-2">No Rak</th>
                                                    <th class="text-center p-2">Area</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-nowrap text-center"><?php echo $data['nama_lokasi'] ?></td>
                                                    <td class="text-nowrap text-center"><?php echo $data['no_lantai'] ?></td>
                                                    <td class="text-nowrap text-center"><?php echo $data['no_rak'] ?></td>
                                                    <td class="text-nowrap text-center"><?php echo $data['nama_area'] ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="<?php echo $get_url . "&sort_data=semua_data" ?>" class="btn btn-primary mobile"><i class="bi bi-clipboard2-data-fill"></i> Lihat Kartu Stock</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
        } else {
            // Path file log
            $logFile = 'log_user_ks.txt';
            
            // Periksa apakah file log ada, jika tidak, buat file log baru
            if (!file_exists($logFile)) {
                // Buat file log dengan header default
                file_put_contents($logFile, "=== Log Akses Ditolak ===\n", LOCK_EX);
            }

            // Logging Role dan Nama User yang Tidak Memiliki Akses
            $logMessage = date('Y-m-d H:i:s') . " - Mencoba masuk detail produk untuk User: $nama_user dengan Role: $role dari IP: {$_SERVER['REMOTE_ADDR']}\n";
            file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
            ?>
                <!-- Sweet Alert -->
                <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                <script src="assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Maaf Anda Tidak Memiliki Akses Fitur Ini",
                        icon: "error",
                    }).then(function() {
                        window.location.href = "data-produk-reg.php";
                    });
                    });
                </script>
            <?php
        }
    ?>
</body>
</html>