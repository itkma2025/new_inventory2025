<?php
function tampilkanStatusBerlaku($data, $tanggal_sekarang, $sisa_tahun, $sisa_bulan, $sisa_hari)
{
    ob_start(); // Mulai penangkapan output

    if ($data['berlaku_sampai'] == '') {
        echo '<td class="text-center text-nowrap">Tanggal Berlaku Tidak Ada</td>';
    } else if ($data['tanggal_berlaku_sampai'] < $tanggal_sekarang) {
        echo '<td class="text-center text-nowrap text-white" style="background-color: red;">
                Expired <br>
                (Lewat ' . $sisa_hari . ' Hari)
              </td>';
    } else if ($sisa_tahun == '0' && $sisa_bulan == '0' && $sisa_hari == '0') {
        echo '<td class="text-center text-nowrap text-white" style="background-color: red;">
                Expired <br>
                (' . $sisa_hari . ' Hari)
              </td>';
    } else if ($sisa_tahun == '0' && $sisa_bulan == '0') {
        if ($sisa_hari <= 20) {
            echo '<td class="text-center text-nowrap" style="background-color: orange;">
                    Urgent <br>
                    (' . $sisa_hari . ' Hari)
                  </td>';
        } else {
            echo '<td class="text-center text-nowrap" style="background-color: yellow;">
                    Darurat <br>
                    (' . $sisa_hari . ' Hari)
                  </td>';
        }
    } else if ($sisa_tahun == '0' && $sisa_hari == '0') {
        echo '<td class="text-center text-nowrap" style="background-color: yellow;">
                Darurat <br>
                (' . $sisa_bulan . ' Bulan)
              </td>';
    } else if ($sisa_bulan == '0') {
        echo '<td class="text-center text-nowrap text-white" style="background-color: green;">
                Masih Aman <br>
                (' . $sisa_tahun . ' Tahun ' . $sisa_hari . ' Hari)
              </td>';
    } else if ($sisa_tahun != '0' && $sisa_bulan != '0' && $sisa_hari != '0') {
        echo '<td class="text-center text-nowrap text-white" style="background-color: green;">
                Masih Aman <br>
                (' . $sisa_tahun . ' Tahun ' . $sisa_bulan . ' Bulan ' . $sisa_hari . ' Hari)
              </td>';
    } else if ($sisa_tahun != '0' && $sisa_bulan != '0' && $sisa_hari == '0') {
        echo '<td class="text-center text-nowrap text-white" style="background-color: green;">
                Masih Aman <br>
                (' . $sisa_tahun . ' Tahun ' . $sisa_bulan . ' Bulan)
              </td>';
    } else {
        if ($sisa_bulan == 1 && $sisa_hari > 10) {
            echo '<td class="text-center text-nowrap text-white" style="background-color: green;">
                    Masih Aman <br>
                    (' . $sisa_bulan . ' Bulan ' . $sisa_hari . ' Hari)
                  </td>';
        } else if ($sisa_bulan == 1 && $sisa_hari < 10) {
            echo '<td class="text-center text-nowrap" style="background-color: yellow;">
                    Darurat <br>
                    (' . $sisa_bulan . ' Bulan ' . $sisa_hari . ' Hari)
                  </td>';
        } else if ($sisa_bulan > 1 && $sisa_hari > 0) {
            echo '<td class="text-center text-nowrap text-white" style="background-color: green;">
                    Masih Aman <br>
                    (' . $sisa_bulan . ' Bulan ' . $sisa_hari . ' Hari)
                  </td>';
        }
    }

    return ob_get_clean(); // Kembalikan hasil output
}
?>