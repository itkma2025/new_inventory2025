<?php
$page  = 'transaksi';
$page2 = 'spk';
include "akses.php";
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
    .custom-width {
      width: 50%;
      /* Atur lebar kolom menjadi 50% saat tampil pada mobile */
    }

    /* Gaya khusus untuk mengatur lebar kolom pada tampilan mobile */
    @media only screen and (max-width: 480px) {
      .wrap-text {
        overflow-wrap: break-word;
        word-wrap: break-word;
        max-width: 300px;
        /* Contoh lebar maksimum elemen */
      }
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
    <!-- SWEET ALERT -->
    <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {
                                              echo $_SESSION['info'];
                                            }
                                            unset($_SESSION['info']); ?>"></div>
      <!-- END SWEET ALERT -->
    <section>
      <div class="card shadow p-2">
        <div class="card-header text-center">
          <h5><strong>FORM SPK REGULER ORDER</strong></h5>
        </div>
        <form action="proses/proses-spk-reg.php" method="POST">
          <?php
            // UUID
            $uuid = generate_uuid();
            $year = date('y');
            $day = date('d');
            $month = date('m');
            $years = date('Y');
            include "koneksi.php";
            $sql  = mysqli_query($connect, "SELECT 
                                              CAST(MAX(CAST(SUBSTRING_INDEX(no_spk, '/', 1) AS UNSIGNED)) AS CHAR) AS maxID,
                                              STR_TO_DATE(tgl_spk, '%d/%m/%Y') AS tgl 
                                            FROM 
                                                spk_reg 
                                            WHERE 
                                                YEAR(STR_TO_DATE(tgl_spk, '%d/%m/%Y')) = '$years'
                                  ");
            $data = mysqli_fetch_array($sql);
            $kode = $data['maxID'];
            $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
            $ket1 = "/SPK/";
            $bln = $array_bln[date('n')];
            $ket2 = "/";
            $ket3 = date("Y");
            $urutkan = $kode; // Mengambil nilai maksimum langsung dari hasil query
            $urutkan++;
            $no_spk = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;
          ?>

          <div class="card-body">
            <div class="row mt-3">
              <div class="col-sm-6">
                <div class="card-body">
                  <div class="mt-3">
                    <label for="no_spk" class="form-label">No. SPK</label>
                    <input type="hidden" name="id_spk_reg" value="SPKREG-<?php echo $year ?><?php echo $month ?><?php echo $uuid ?><?php echo $day ?>">
                    <input type="text" class="form-control" id="no_spk" name="no_spk" value="<?php echo $no_spk ?>" readonly>
                  </div>
                  <div class="mt-3">
                    <label for="tgl_spk" class="form-label">Tanggal SPK</label>
                    <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl_spk" id="datetime-input" readonly>
                  </div>
                  <div class="mt-3">
                    <label for="no_po" class="form-label">NO. PO</label>
                    <input type="text" class="form-control" id="no_po" name="no_po">
                  </div>
                  <div class="mt-3">
                    <label for="tgl_pesan" class="form-label">Tanggal Pesanan</label>
                    <input type="text" style="background-color:white;" class="bg-white form-control" name="tgl_pesan" id="date" placeholder="dd/mm/yyyy" required>
                  </div>
                  <div class="mt-3">
                    <label for="order_via" class="form-label">Order Via</label>
                    <select class="selectize-js form-select" name="order_by" required>
                      <option value="">Pilih...</option>
                      <?php
                      include "koneksi.php";
                      $sql2 = "SELECT * FROM tb_orderby";
                      $query2 = mysqli_query($connect, $sql2);
                      while ($data2 = mysqli_fetch_array($query2)) {
                      ?>
                        <option value="<?php echo $data2['id_orderby'] ?>"><?php echo $data2['order_by'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card-body">
                  <div class="mt-3">
                    <label for="sales" class="form-label">Sales</label>
                    <select class="selectize-js form-select" name="sales" required>
                      <option value="">Pilih...</option>
                      <?php
                      include "koneksi.php";
                      $sql_sales = "SELECT * FROM tb_sales";
                      $query_sales = mysqli_query($connect, $sql_sales);
                      while ($data3 = mysqli_fetch_array($query_sales)) {
                      ?>
                        <option value="<?php echo $data3['id_sales'] ?>"><?php echo $data3['nama_sales'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="mt-3">
                    <label for="Pelanggan" class="form-label">Pelanggan</label>
                    <input type="hidden" class="form-control" id="id" name="id_cs">
                    <input type="text" class="form-control bg-white" id="cs" name="pelanggan" data-bs-toggle="modal" data-bs-target="#modalCs" readonly>
                  </div>
                  <div class="mt-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea type="text" class="form-control bg-white" id="alamat" name="alamat" rows="3" readonly></textarea>
                  </div>
                  <div class="mt-3">
                    <label for="note" class="form-label">Note</label>
                    <textarea type="text" class="form-control" id="note" name="note"></textarea>
                  </div>
                  <input type="hidden" name="id_user" value="<?php echo $_SESSION['tiket_id'] ?>">
                </div>
              </div>
              <div class="text-center mt-3">
                <button type="submit" name="simpan" class="btn btn-primary btn-md m-2"><i class="bx bx-save"></i> Simpan Data</button>
                <a href="spk-reg.php" class="btn btn-secondary m-2"><i class="bi bi-x-circle"></i> Cancel</a>
              </div>
            </div>
          </div>
        </form>
      </div>

      </div>
    </section>
  </main><!-- End #main -->

  <!-- Modal -->
  <div class="modal fade" id="modalCs" tabindex="-1">
      <div class="modal-dialog modal-xl">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Data Pelanggan</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <style>
                      .wrap-text {
                          max-width: 300px; /* Contoh lebar maksimum */
                          overflow: hidden; /* Sembunyikan teks yang melebihi max-width */
                          white-space: pre-line; /* Tetapkan spasi putih dan pecah baris sesuai dengan teks */
                          word-wrap: break-word; /* Pecah kata jika melebihi max-width */
                      }
                      @media (max-width: 767px) { /* Media query untuk tampilan mobile */
                          .wrap-text {
                              min-width: 400px; /* Contoh lebar maksimum */
                              overflow: hidden; /* Sembunyikan teks yang melebihi max-width */
                              white-space: pre-line; /* Tetapkan spasi putih dan pecah baris sesuai dengan teks */
                              word-wrap: break-word; /* Pecah kata jika melebihi max-width */
                          }
                      }
                  </style>
                  <div class="card p-3">
                      <div class="table-responsive">
                          <table class="table table-hover table-striped table-bordered" id="table2">
                            <thead>
                                <tr class="text-white" style="background-color: navy;">
                                  <td class="text-nowrap text-center">No</td>
                                  <td class="text-nowrap text-center" style="width: 400px;">Nama Customer</td>
                                  <td class="text-nowrap text-center" style="width: 600px;">Alamat Customer</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                include "koneksi.php";
                                $sql_cs = "SELECT id_cs, nama_cs, alamat, status_aktif FROM tb_customer WHERE status_aktif = '1' ORDER BY nama_cs ASC";
                                $query_cs = mysqli_query($connect, $sql_cs);
                                while ($data_cs = mysqli_fetch_array($query_cs)) {
                                ?>
                                <tr data-id="<?php echo $data_cs['id_cs'] ?>" data-nama="<?php echo $data_cs['nama_cs'] ?>" data-alamat="<?php echo $data_cs['alamat'] ?>" data-bs-dismiss="modal">
                                    <td class="text-center"><?php echo $no ?></td>
                                    <td><?php echo $data_cs['nama_cs'] ?></td>
                                    <td class="wrap-text"><?php echo $data_cs['alamat'] ?></td>
                                </tr>
                                <?php $no++ ?>
                                <?php } ?>
                            </tbody>
                          </table>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- End Large Modal-->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</html>
<!-- Generate UUID -->
<?php
function generate_uuid()
{
  return sprintf(
    '%04x%04x%04x',
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff)
  );
}
?>

<!-- Clock js -->
<script>
  function inputDateTime() {
    // Get current date and time
    let currentDate = new Date();

    // Format date and time as yyyy-mm-ddThh:mm:ss
    let year = currentDate.getFullYear();
    let month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
    let day = currentDate.getDate().toString().padStart(2, '0');
    let hours = currentDate.getHours();
    let minutes = currentDate.getMinutes().toString().padStart(2, '0');
    let seconds = currentDate.getSeconds().toString().padStart(2, '0');
    let formattedDateTime = `${day}/${month}/${year}, ${hours}:${minutes}`;

    // Set value of input field to current date and time
    document.getElementById("datetime-input").setAttribute('value', formattedDateTime);

  }
  // Call updateDateTime function every second
  setInterval(inputDateTime, 1000);
</script>

<!-- date picker with flatpick -->
<script type="text/javascript">
  flatpickr("#date", {
    dateFormat: "d/m/Y",
    defaultDate: new Date(),
  });

  flatpickr("#tgl_kirim", {
    dateFormat: "d/m/Y",
  });
</script>
<!-- end date picker -->

<!-- Script Select Data Supplier -->
<script>
  $(document).on('click', '#table2 tbody tr', function(e) {
    $('#id').val($(this).data('id'));
    $('#cs').val($(this).data('nama'));
    $('#alamat').val($(this).data('alamat'));
    $('#modalCs').modal('hide');
  });
</script>