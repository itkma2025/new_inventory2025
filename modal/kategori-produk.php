<!-- Modal Kategori Produk -->
<div class="modal fade" id="modalkatprod" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Pilih Data</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="card p-3">
          <div class="card-body table-responsive mt-3">
              <table class="table table-bordered table-striped katProd" id="table3">
                  <thead>
                    <tr class="text-white" style="background-color: #051683;">
                        <td class="text-center p-3" style="width: 80px">No</td>
                        <td class="text-center p-3" style="width: 200px">Nama Kategori</td>
                        <td class="text-center p-3" style="width: 200px">Merk</td>
                        <td class="text-center p-3" style="width: 200px">Nomor Izin Edar</td>
                    </tr>
                  </thead>
                  <tbody>
                      <?php 
                          date_default_timezone_set('Asia/Jakarta');
                          include "koneksi.php";
                          $no = 1;
                          $sql = "SELECT * FROM tb_kat_produk AS tkp
                                  JOIN tb_merk AS m ON (tkp.id_merk = m.id_merk)
                                  ORDER BY nama_kategori ASC"; 
                          $query = mysqli_query($connect, $sql) OR DIE(mysqli_error($connect, $sql));
                          while($data = mysqli_fetch_array($query)){
                      ?>
                      <tr data-idkat="<?php echo $data['id_kat_produk']; ?>" data-namakatprod="<?php echo $data['nama_kategori']?> - <?php echo $data['nama_merk'] ?>" data-bs-dismiss="modal">
                        <td class="text-center"><?php echo $no;?></td>
                        <td class="text-center"><?php echo $data['nama_kategori']; ?></td>
                        <td class="text-center"><?php echo $data['nama_merk']; ?></td>
                        <td class="text-center"><?php echo $data['no_izin_edar']; ?></td>
                      </tr>
                      <?php $no++; ?>
                      <?php } ?>
                  </tbody>
              </table>
          </div>
      </div>
    </div>
  </div>
</div>
<!-- End Modal  -->