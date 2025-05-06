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

  <style type="text/css">
    @media only screen and (max-width: 500px) {
      body {
        font-size: 10px;
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
    <section>
      <div class="container-fluid">
        <div class="card shadow p-2">
          <div class="card-header text-center">
            <h5><strong>INPUT PRODUK SPK</strong></h5>
          </div>
          <?php
          include "koneksi.php";
          $id_spk = base64_decode($_GET['id']);
          $sql = "SELECT sr.*, cs.nama_cs, cs.alamat, ordby.order_by, sl.nama_sales 
                    FROM spk_reg AS sr
                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                    JOIN tb_orderby ordby ON(sr.id_orderby = ordby.id_orderby)
                    JOIN tb_sales sl ON(sr.id_sales = sl.id_sales)
                    WHERE sr.id_spk_reg = '$id_spk'";
          $query = mysqli_query($connect, $sql);
          $data = mysqli_fetch_array($query);
          ?>
          <div class="card-body">
            <div class="row mt-3">
              <div class="col-sm-6">
                <div class="card-body p-3 border">
                  <div class="row">
                    <div class="col-5">
                      <p style="float: left;">No. SPK</p>
                      <p style="float: right;">:</p>
                    </div>
                    <div class="col-7">
                      <?php echo $data['no_spk'] ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-5">
                      <p style="float: left;">Tanggal SPK</p>
                      <p style="float: right;">:</p>
                    </div>
                    <div class="col-7">
                      <?php echo $data['tgl_spk'] ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-5">
                      <p style="float: left;">No. PO</p>
                      <p style="float: right;">:</p>
                    </div>
                    <div class="col-7">
                      <?php
                      if ($data['no_po'] != '') {
                        echo $data['no_po'];
                      } else {
                        echo '-';
                      }
                      ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-5">
                      <p style="float: left;">Tanggal Pesanan</p>
                      <p style="float: right;">:</p>
                    </div>
                    <div class="col-7">
                      <?php echo $data['tgl_pesanan'] ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-5">
                      <p style="float: left;">Order Via</p>
                      <p style="float: right;">:</p>
                    </div>
                    <div class="col-7">
                      <?php echo $data['order_by'] ?>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card-body p-3 border" style="min-height: 234px;">
                  <div class="row">
                    <div class="col-5">
                      <p style="float: left;">Sales</p>
                      <p style="float: right;">:</p>
                    </div>
                    <div class="col-7">
                      <?php echo $data['nama_sales'] ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-5">
                      <p style="float: left;">Pelanggan</p>
                      <p style="float: right;">:</p>
                    </div>
                    <div class="col-7">
                      <?php echo $data['nama_cs'] ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-5">
                      <p style="float: left;">Alamat</p>
                      <p style="float: right;">:</p>
                    </div>
                    <div class="col-7">
                      <?php echo $data['alamat'] ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-5">
                      <p style="float: left;">Note</p>
                      <p style="float: right;">:</p>
                    </div>
                    <div class="col-7">
                      <?php
                      if ($data['note'] != '') {
                        echo $data['note'];
                      } else {
                        echo '-';
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Butto Modal Dialog -->
        <div class="card shadow p-2">
          <div class="card-body mt-3">
            <div class="text-start">
              <a class="btn btn-primary btn-detail" data-spk="<?php echo $data['id_spk_reg'] ?>" data-bs-toggle="modal" data-bs-target="#modalBarang">
                <i class="bi bi-plus-circle"></i> Tambah Produk
              </a>
            </div>
          </div>
          <div class="container-fluid">
            <div class="card-body border">
              <div class="text-center">
                <div class="row">
                  <p></p>
                  <div class="col-sm-5">
                    <strong>Nama Produk</strong>
                  </div>
                  <div class="col-sm-2">
                    <strong>Merk</strong>
                  </div>
                  <div class="col-sm-2">
                    <strong>Harga</strong>
                  </div>
                  <div class="col-sm-2">
                    <strong>Stock Tersedia</strong>
                  </div>
                  <div class="col-sm-1">
                    <strong>Qty</strong>
                  </div>
                </div>
              </div>
            </div>
            <form action="proses/proses-produk-spk-reg.php" method="POST">
              <?php
              $id_spk_reg = $data['id_spk_reg'];
              $sql = "SELECT sr.*, tps.*, spr.stock, tpr.nama_produk, tpr.harga_produk, mr.* 
                                    FROM spk_reg AS sr
                                    JOIN tmp_produk_spk tps ON(sr.id_spk_reg = tps.id_spk)
                                    JOIN stock_produk_reguler spr ON(tps.id_produk = spr.id_produk_reg)
                                    JOIN tb_produk_reguler tpr ON(tps.id_produk = tpr.id_produk_reg)
                                    JOIN tb_merk mr ON (tpr.id_merk = mr.id_merk)
                                    WHERE sr.id_spk_reg = '$id_spk_reg' AND tps.status_tmp = '0'";
              $query = mysqli_query($connect, $sql);
              $isEmpty = true; // Tambahkan variabel pengecekan apakah data kosong
              while ($data = mysqli_fetch_array($query)) {
                $uuid = generate_uuid();
                $isEmpty = false; // Setel variabel pengecekan menjadi false jika ada data
              ?>
                <div class="card-body border p-2">
                  <div class="">
                    <div class="row">
                      <div class="col-sm-5">
                        <input type="hidden" name="id_tmp[]" id="id_<?php echo $data['id_tmp'] ?>" value="<?php echo $data['id_tmp'] ?>" readonly>
                        <input type="hidden" class="form-control" name="id_spk_reg[]" value="<?php echo $id_spk_reg ?>" readonly>
                        <input type="hidden" class="form-control" name="id_produk[]" value="<?php echo $data['id_produk'] ?>" readonly>
                        <input type="text" class="form-control bg-light" name="nama_produk" value="<?php echo $data['nama_produk'] ?>" readonly>
                      </div>
                      <div class="col-sm-2">
                        <input type="text" class="form-control bg-light text-center" name="merk" value="<?php echo $data['nama_merk'] ?>" readonly>
                      </div>
                      <div class="col-sm-2">
                        <input type="text" class="form-control bg-light text-end" name="harga[]" value="<?php echo number_format($data['harga_produk']) ?>" readonly>
                      </div>
                      <div class="col-sm-2">
                        <input type="text" class="form-control bg-light text-end" name="stock" id="stock_<?php echo $data['id_tmp'] ?>" value="<?php echo $data['stock'] ?>" readonly>
                      </div>
                      <div class="col-sm-1">
                        <input type="text" class="form-control text-end" name="qty[]" id="qtyInput_<?php echo $data['id_tmp'] ?>" oninput="checkStock('<?php echo $data['id_tmp'] ?>')" required>
                      </div>
                    </div>
                  </div>
                </div>

              <?php } ?>
              <?php if ($isEmpty) { // Cek apakah data kosong 
              ?>
              <?php } else { // Jika ada data, tampilkan tombol simpan 
              ?>
                <div class="card-body mt-3 text-end">
                  <button type="submit" class="btn btn-primary" name="simpan-tmp"> Simpan</button>
                </div>
              <?php } ?>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->
  <!-- Modal Barang -->
  <div class="modal fade" id="modalBarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
        <form method="post" action=""> <!-- Tambahkan form dengan method POST -->
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Data Barang</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered" id="table2">
                <thead>
                  <tr class="text-white" style="background-color: #051683;">
                    <td class="text-center p-3" style="width: 50px">No</td>
                    <td class="text-center p-3" style="width: 350px">Nama Produk</td>
                    <td class="text-center p-3" style="width: 100px">Merk</td>
                    <td class="text-center p-3" style="width: 100px">Stock</td>
                    <td class="text-center p-3" style="width: 100px">Aksi</td>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  include "koneksi.php";
                  $id = base64_decode($_GET['id']);
                  $selected_produk = [];
                  $id_spk = $id_spk_reg;
                  $no = 1;

                  // Mengambil data produk yang ada dalam tmp_produk_spk untuk id_spk yang sedang aktif
                  $query_selected_produk = mysqli_query($connect, "SELECT id_produk FROM tmp_produk_spk WHERE id_spk = '$id_spk'");
                  while ($selected_data = mysqli_fetch_array($query_selected_produk)) {
                    $selected_produk[] = $selected_data['id_produk'];
                  }

                  $sql = "SELECT pr.nama_produk, pr.id_merk, pr.harga_produk, mr.nama_merk, spr.stock, spr.id_produk_reg
                          FROM stock_produk_reguler AS spr
                          LEFT JOIN tb_produk_reguler AS pr ON spr.id_produk_reg = pr.id_produk_reg
                          LEFT JOIN tb_merk AS mr ON pr.id_merk = mr.id_merk
                          ORDER BY pr.nama_produk ASC";

                  $query = mysqli_query($connect, $sql);

                  while ($data = mysqli_fetch_array($query)) {
                    $id_produk = $data['id_produk_reg'];
                    $isChecked = in_array($id_produk, $selected_produk);
                    $isDisabled = false;

                    if ($data['stock'] == 0) {
                      $isDisabled = true; // Jika stock = 0, maka tombol pilih akan menjadi disabled
                    }
                  ?>
                    <tr>
                      <td class="text-center"><?php echo $no; ?></td>
                      <td><?php echo $data['nama_produk']; ?></td>
                      <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                      <td class="text-center"><?php echo number_format($data['stock']); ?></td>
                      <td class="text-center">
                        <button class="btn-pilih btn btn-primary btn-sm" data-id="<?php echo $id_produk; ?>" data-spk="<?php echo $id_spk; ?>" <?php echo ($isChecked || $isDisabled) ? 'disabled' : ''; ?>>Pilih</button>
                      </td>
                    </tr>
                    <?php $no++; ?>
                  <?php } ?>
                </tbody>




              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="refreshPage()">Close</button>
          </div>
        </form> <!-- Akhir dari form -->
      </div>
    </div>
  </div>
  <!-- End Modal -->


  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</html>

<!-- Generat UUID -->
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
<!-- End Generate UUID -->

<script>
  function refreshPage() {
    location.reload();
  }
</script>

<script>
  $(document).ready(function() {
    $('.btn-detail').click(function() {
      var idSpk = $(this).data('spk');
      $('#spk').text(idSpk);

      $('button.btn-pilih').attr('data-spk', idSpk);

      $('#modalBarang').modal('show');
    });

    $(document).on('click', '.btn-pilih', function(event) {
      event.preventDefault();
      event.stopPropagation();

      var id = $(this).data('id');
      var spk = $(this).attr('data-spk');

      saveData(id, spk);
    });

    function saveData(id, spk) {
      $.ajax({
        url: 'simpan-data-spk.php',
        type: 'POST',
        data: {
          id: id,
          spk: spk
        },
        success: function(response) {
          console.log('Data berhasil disimpan.');
          $('button[data-id="' + id + '"]').prop('disabled', true);
        },
        error: function(xhr, status, error) {
          console.error('Terjadi kesalahan saat menyimpan data:', error);
        }
      });
    }
  });
</script>

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

<!-- Kode Untuk Qty   -->
<script>
  function formatNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  function formatInputValue(value) {
    return value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  }

  function checkStock(inputId) {
    var stock = parseInt(document.getElementById('stock_' + inputId).value.replace(/,/g, '')); // Menggunakan ID yang sesuai untuk elemen stock
    var qtyInput = document.getElementById('qtyInput_' + inputId); // Menggunakan ID yang sesuai untuk elemen qtyInput
    var qty = qtyInput.value.replace(/,/g, '');

    qtyInput.value = formatInputValue(qty);

    if (parseInt(qty) > stock) {
      qtyInput.value = formatNumber(stock);
    }

    var simpanButton = document.getElementById('simpan');
    if (parseInt(qty) > 0) {
      simpanButton.disabled = false;
    } else {
      simpanButton.disabled = true;
    }
  }
</script>


<!-- <button class="btn btn-primary btn-sm" id="pilih" data-idprod="' . $data['id_produk_reg'] . '" data-namaprod="' . $data['nama_produk'] . '" data-merkprod="' . $data['nama_merk'] . '" data-stock="' . $data['stock'] . '" data-bs-dismiss="modal">Pilih</button> -->

<!-- Modal Barang -->