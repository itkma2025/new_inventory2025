<?php
require_once "akses.php";
$page = 'produk';
$page2 = 'data-produk-set-ecat';
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
    .table-striped tr.highlight-row td {
        background-color: blue !important;
        color: white !important;
    }
  </style>
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
      <!-- SWEET ALERT -->
      <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
      <!-- END SWEET ALERT -->
      <div class="container-fluid">
        <div class="card">
          <div class="card-body p-3">
            <?php
            $id = $_GET['detail-id'];
            $key = "KM@2024?SET";
            $decrypt_id = decrypt($id, $key);
            $encrypt_id = encrypt($decrypt_id, $key);
            $sql = "SELECT 
                      tbsm.id_set_ecat,
                      tbsm.kode_set_ecat,
                      tbsm.nama_set_ecat,
                      tbsm.harga_set_ecat,
                      lok.nama_lokasi,
                      lok.no_lantai,
                      lok.nama_area,
                      lok.no_rak
                    FROM tb_produk_set_ecat AS tbsm 
                    LEFT JOIN tb_lokasi_produk AS lok ON (tbsm.id_lokasi = lok.id_lokasi)
                    WHERE id_set_ecat = '$decrypt_id'";
            $query = mysqli_query($connect, $sql) or die(mysqli_error($connect, $sql));
            $data = mysqli_fetch_array($query);
            $cek_data = mysqli_num_rows($query);
            ?>
            <?php  
              if($cek_data > 0){
                ?>
                  <div class="row">
                    <div class="col-sm-8">
                      <p>Kode set : <?php echo $data['kode_set_ecat']; ?></p>
                      <p>Nama set : <?php echo $data['nama_set_ecat']; ?></p>
                      <?php
                      include "koneksi.php";
                      $grand_total = 0;
                      $sql_data = "SELECT 
                                    ipsm.id_isi_set_ecat, 
                                    ipsm.id_set_ecat, 
                                    ipsm.qty, 
                                    COALESCE(tpr.harga_produk , tpe.harga_produk) AS harga_produk
                                  FROM isi_produk_set_ecat ipsm
                                  LEFT JOIN tb_produk_reguler tpr ON (ipsm.id_produk = tpr.id_produk_reg)
                                  LEFT JOIN tb_produk_ecat tpe ON (ipsm.id_produk = tpe.id_produk_ecat)
                                  WHERE ipsm.id_set_ecat = '$decrypt_id'";
                      $query_data = mysqli_query($connect, $sql_data) or die(mysqli_error($connect, $sql_data));
                      while ($row = mysqli_fetch_array($query_data)) {
                        $id_isi_set = $row['id_isi_set_ecat'];
                        $id_set_ecat = $row['id_set_ecat'];
                        $harga = $row['harga_produk'];
                        $qty = $row['qty'];
                        $jumlah = $qty * $harga;
                        $grand_total += $jumlah;
                      ?>
                      <?php } ?>
                      <p>Harga Modal : <?php echo number_format($grand_total, 0, '.', '.'); ?></p>
                      <p>Harga Jual : <?php echo number_format($data['harga_set_ecat'], 0, '.', '.'); ?></p>
                      <p>Lokasi : <?php echo $data['nama_lokasi']; ?> / <?php echo $data['no_lantai']; ?> / <?php echo $data['nama_area']; ?> / <?php echo $data['no_rak']; ?></p>
                    </div>
                    <div class="col-sm-4 text-end">
                      <a href="tambah-isi-produk-set-ecat.php?id-set=<?php echo $encrypt_id ?>" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah produk</a>
                      <a href="data-produk-set-ecat.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr class="text-white" style="background-color: #051683;">
                          <th class="text-center p-3 text-nowrap" style="width: 50px">No</th>
                          <th class="text-center p-3 text-nowrap" style="width: 150px">Kode Produk</th>
                          <th class="text-center p-3 text-nowrap" style="width: 450px">Nama Produk</th>
                          <th class="text-center p-3 text-nowrap" style="width: 150px">Merk</th>
                          <th class="text-center p-3 text-nowrap" style="width: 150px">No. AKL</th>
                          <th class="text-center p-3 text-nowrap" style="width: 50px">Qty</th>
                          <th class="text-center p-3 text-nowrap" style="width: 100px">Harga Satuan (Rp)</th>
                          <th class="text-center p-3 text-nowrap" style="width: 80px">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>   
                        <?php  
                          $no = 1;
                          $sql_isi = " SELECT DISTINCT
                                          ipse.id_isi_set_ecat, 
                                          ipse.id_set_ecat, 
                                          ipse.id_produk, 
                                          ipse.qty, 
                                          COALESCE(tpr.kode_produk,  tpe.kode_produk) AS kode_produk, 
                                          COALESCE(tpr.nama_produk,  tpe.nama_produk) AS nama_produk, 
                                          COALESCE(mr_reg.nama_merk,  mr_ecat.nama_merk) AS nama_merk, 
                                          COALESCE(akl_reg.no_izin_edar, akl_ecat.no_izin_edar) AS akl, 
                                          COALESCE(tpr.harga_produk, tpe.harga_produk) AS harga_produk
                                        FROM isi_produk_set_ecat ipse
                                        LEFT JOIN tb_produk_reguler tpr ON (ipse.id_produk = tpr.id_produk_reg)
                                        LEFT JOIN tb_produk_ecat tpe ON (ipse.id_produk = tpe.id_produk_ecat)
                                        LEFT JOIN tb_merk mr_reg ON (mr_reg.id_merk = tpr.id_merk)
                                        LEFT JOIN tb_merk mr_ecat ON (mr_ecat.id_merk = tpe.id_merk)
                                        LEFT JOIN tb_kat_produk akl_reg ON (tpr.id_kat_produk = akl_reg.id_kat_produk)
                                        LEFT JOIN tb_kat_produk akl_ecat ON (tpe.id_kat_produk = akl_ecat.id_kat_produk)
                                        WHERE ipse.id_set_ecat = '$decrypt_id'";
                          $query_isi = mysqli_query($connect, $sql_isi) or die(mysqli_error($connect, $sql_isi));
                          while($data_produk = mysqli_fetch_array($query_isi)){
                            $kode_produk = $data_produk['kode_produk'];
                            $nama_merk = $data_produk['nama_merk'];
                            $akl = $data_produk['akl'];
                            $harga = $data_produk['harga_produk'];
                            $qty = $data_produk['qty'];
                            $nama_produk = $data_produk['nama_produk'];    
                            $id_isi_set_marwa = $data_produk['id_isi_set_ecat'];  
                            $encrypt_id_isi = encrypt($id_isi_set_marwa, $key);
                            $non_akl_class = "";
                            if (empty($akl) || $akl == '--') {
                                $non_akl_class = "highlight-row";
                            }   
                        ?>
                        <tr class="<?php echo $non_akl_class; ?>">
                          <td class="text-center text-nowrap"><?php echo $no; ?></td>
                          <td class="text-nowrap"><?php echo $kode_produk; ?></td>
                          <td class="text-nowrap"><?php echo $nama_produk; ?></td>
                          <td class="text-nowrap text-center"><?php echo $nama_merk; ?></td>
                          <td class="text-nowrap text-center">
                              <?php  
                                if(!empty($akl) && $akl != '--'){
                                  echo "AKL-" . $akl;
                                } else {
                                  echo "Tidak Ada No. AKL";
                                }
                              ?>
                          </td>
                          <td class="text-end text-nowrap"><?php echo $qty; ?></td>
                          <td class="text-end text-nowrap"><?php echo number_format($harga,0,'.','.') ?></td>
                          <td class="text-center text-nowrap">
                            <a href="edit-isi-produk-set-ecat.php?edit-id=<?php echo $encrypt_id_isi ?>" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                            <a href="proses/proses-produk-set-ecat.php?hapus-isi-set=<?php echo $encrypt_id_isi ?>&kode=<?php echo $encrypt_id ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                          </td>
                        </tr>
                        <?php $no++; ?>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                <?php
              }else{
                ?>
                  <script>
                      // Mengarahkan pengguna ke halaman 404.php
                      // window.location.replace("404.php");
                  </script>
                <?php
              }
            
            ?>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</html>