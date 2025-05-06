<!-- Modal Lokasi -->
<div class="modal fade" id="modalLokasi" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5">Pilih Data</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="card p-3">
          <div class="card-body table-responsive mt-3">
              <table class="table table-bordered table-striped" id="table2">
                  <thead>
                    <tr class="text-white" style="background-color: #051683;">
                        <td class="text-center p-3" style="width: 80px">No</td>
                        <td class="text-center p-3" style="width: 200px">Lokasi</td>
                        <td class="text-center p-3" style="width: 200px">No. Lantai</td>
                        <td class="text-center p-3" style="width: 300px">Area</td>
                        <td class="text-center p-3" style="width: 150px">No. Rak</td>
                    </tr>
                  </thead>
                  <tbody>
                      <?php 
                          date_default_timezone_set('Asia/Jakarta');
                          include "koneksi.php";
                          $no = 1;
                          $sql = "SELECT 
                                    lp.id_lokasi,  
                                    lp.nama_lokasi,  
                                    lp.no_lantai,  
                                    lp.nama_area,  
                                    lp.no_rak
                                  FROM tb_lokasi_produk as lp
                                  ORDER BY lp.nama_lokasi ASC";
                          $query = mysqli_query($connect, $sql) OR DIE(mysqli_error($connect, $sql));
                          while($data = mysqli_fetch_array($query)){
                      ?>
                      <tr data-id="<?php echo $data['id_lokasi']; ?>" data-nama="<?php echo $data['nama_lokasi']; ?>" data-lantai="<?php echo $data['no_lantai']?>" data-area="<?php echo $data['nama_area']?>" data-rak="<?php echo $data['no_rak']; ?>" data-bs-dismiss="modal">
                        <td class="text-center"><?php echo $no;?></td>
                        <td class="text-center"><?php echo $data['nama_lokasi']; ?></td>
                        <td class="text-center"><?php echo $data['no_lantai']; ?></td>
                        <td class="text-center"><?php echo $data['nama_area']; ?></td>
                        <td class="text-center"><?php echo $data['no_rak']; ?></td>
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