<?php  
    require_once "../akses.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk</title>
    <?php include "page/head.php"; ?>
</head>
<body>
    <?php
        //tangkap URL dengan $_GET
        $id = $_GET['id'];

        //mengambil nama gambar yang terkait
        $sql = "SELECT pr.*,
                pr.created_date as 'produk_created',
                pr.created_date as 'produk_updated',
                pr.id_produk_reg as 'produk_id',    
                uc.nama_user as user_created, 
                uu.nama_user as user_updated,
                mr.*,
                kp.*,
                kj.*,
                kp.nama_kategori as kat_prod,
                kj.nama_kategori as kat_penj,
                gr.*,
                lok.*,
                spr.*
                FROM tb_produk_reguler as pr
                LEFT JOIN user uc ON (pr.id_user = uc.id_user)
                LEFT JOIN user uu ON (pr.user_updated = uu.id_user)
                LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                LEFT JOIN tb_kat_produk kp ON (pr.id_kat_produk = kp.id_kat_produk)
                LEFT JOIN tb_kat_penjualan kj ON (pr.id_kat_penjualan = kj.id_kat_penjualan)
                LEFT JOIN tb_produk_grade gr ON (pr.id_grade = gr.id_grade)
                LEFT JOIN tb_lokasi_produk lok ON (pr.id_lokasi = lok.id_lokasi)
                LEFT JOIN stock_produk_reguler spr ON (pr.id_produk_reg = spr.id_produk_reg)
                WHERE pr.id_produk_reg ='$id' ORDER BY nama_produk ASC";
        $result = mysqli_query($connect, $sql);
        $data = mysqli_fetch_array($result);
        $img = $data['gambar'];
        $no_img = $data["gambar"] == "" ? "gambar/upload-marwa/no-image.png" : "gambar/upload-marwa/$img";
    ?>
        <div class="container">
            <div class="card">
                <div class="card-header text-center">
                    <h4><strong>Detail Produk Reguler</strong></h4>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-5">
                            <img alt="Gambar Produk" src="gambar/upload-marwa/<?php echo $img ?>" style="height: 100%; width: 100%;" class="img-fluid">
                        </div>
                        <div class="col-md-7">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <td>Kode Produk</td>
                                        <td><?php echo $data['kode_produk'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>No Izin Edar</td>
                                        <td><?php echo $data['no_izin_edar'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Nama Produk</td>
                                        <td><?php echo $data['nama_produk']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Merk Produk</td>
                                        <td><?php echo $data['nama_merk']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Harga Produk</td>
                                        <td>Rp.<?php echo number_format($data['harga_produk']) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Stock Produk</td>
                                        <td><?php echo $data['stock'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Kategori Produk</td>
                                        <td><?php echo $data['kat_prod'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Kategori Penjualan</td>
                                        <td><?php echo $data['kat_penj'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Kategori Penjualan</td>
                                        <td><?php echo $data['nama_grade'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Lokasi Produk</td>
                                        <td><?php echo $data['nama_lokasi'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>No. Lantai</td>
                                        <td><?php echo $data['no_lantai'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Area</td>
                                        <td><?php echo $data['nama_area'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>No. Rak</td>
                                        <td><?php echo $data['no_rak'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Dibuat Tanggal</td>
                                        <td><?php echo $data['produk_created'] ?></td>
                                    </tr>
                                    <tr>
                                        <td>Dibuat Oleh</td>
                                        <td><?php echo $data['user_created'] ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>
</html>