<?php  
     if ($_GET['jenis'] == 'nonppn') {
            $sql_cb_nonppn = $connect->query("SELECT status_cb, jenis_cb FROM cashback_nonppn WHERE id_inv = '$id_inv'");
            $data_cb_nonppn =  mysqli_fetch_array($sql_cb_nonppn);
            $status_cb = $data_cb_nonppn['status_cb'] ?? '';
            $jenis_cb = $data_cb_nonppn['jenis_cb'] ?? '';
            
        
            // Pecah data berdasarkan koma
            $jenisCbArray = explode(",", $jenis_cb);
        
            // Gabungkan data dengan tanda petik
            $result_jenis_cb = "'" . implode("','", $jenisCbArray) . "'";
            
            // Menampilkan keterangan cashback
            $ket_cb = $connect->query("SELECT ket_cashback FROM keterangan_cashback WHERE id_ket_cashback IN ($result_jenis_cb)");
      } else if ($_GET['jenis'] == 'ppn') {
            $sql_cb_ppn = $connect->query("SELECT status_cb, jenis_cb FROM cashback_ppn WHERE id_inv = '$id_inv'");
            $data_cb_ppn =  mysqli_fetch_array($sql_cb_ppn);
            $status_cb = $data_cb_ppn['status_cb'] ?? '';
            $jenis_cb = $data_cb_ppn['jenis_cb'] ?? '';

            // Pecah data berdasarkan koma
            $jenisCbArray = explode(",", $jenis_cb);

            // Gabungkan data dengan tanda petik
            $result_jenis_cb = "'" . implode("','", $jenisCbArray) . "'";
            
            // Menampilkan keterangan cashback
            $ket_cb = $connect->query("SELECT ket_cashback FROM keterangan_cashback WHERE id_ket_cashback IN ($result_jenis_cb)");
      } else if ($_GET['jenis'] == 'bum') {
            $sql_cb_bum = $connect->query("SELECT status_cb, jenis_cb FROM cashback_bum WHERE id_inv = '$id_inv'");
            $data_cb_bum =  mysqli_fetch_array($sql_cb_bum);
            $status_cb = $data_cb_bum['status_cb'] ?? '';
            $jenis_cb = $data_cb_bum['jenis_cb'] ?? '';

            // Pecah data berdasarkan koma
            $jenisCbArray = explode(",", $jenis_cb);

            // Gabungkan data dengan tanda petik
            $result_jenis_cb = "'" . implode("','", $jenisCbArray) . "'";

            // Menampilkan keterangan cashback
            $ket_cb = $connect->query("SELECT ket_cashback FROM keterangan_cashback WHERE id_ket_cashback IN ($result_jenis_cb)");
      } else {
          ?>
              <script type="text/javascript">
                  window.location.href = "../404.php";
              </script>
          <?php
      }
    
?>