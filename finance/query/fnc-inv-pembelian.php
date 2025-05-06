<?php  
    require_once "../akses.php";
    include "../function/hitung-selisih-hari.php";
    $no = 1;
    $sort_option ="";
    $today = date('d/m/Y');
    $startWeek = date('d/m/Y', strtotime("-1 week"));
    $endWeek = date('d/m/Y', strtotime("now"));
    $thisWeekStart= date('d/m/Y',strtotime('last sunday'));
    $thisWeekEnd= date('d/m/Y',strtotime('next sunday'));
    $thisMonth = date('m');

    // Kode Khusus Untuk Last Mont
    // Dapatkan tanggal saat ini
    $tanggalSaatIni = new DateTime();

    // Set tanggal ke awal bulan
    $tanggalSaatIni->setDate($tanggalSaatIni->format('Y'), $tanggalSaatIni->format('m'), 1);

    // Kurangkan satu bulan dari tanggal saat ini
    $tanggalSaatIni->modify('-1 month');

    // Dapatkan bulan dalam format numerik (dengan angka nol di depan jika berlaku)
    $lastMonth = $tanggalSaatIni->format('m');

    // Tampilkan nilai bulan sebelumnya
    $thisYear = date('Y');
    $lastYear = date("Y",strtotime("-1 year"));
    if(isset($_GET['date_range']))
      {
        if($_GET['date_range'] == "today")
        {
            $sort_option = "DATE(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) = CURDATE()";
        }

        elseif($_GET['date_range'] == "weekly")
        {
            $sort_option = "
                            WEEK(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) = WEEK(CURDATE())
                            AND YEAR(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) = YEAR(CURDATE())
                          ";
        }

        elseif($_GET['date_range'] == "monthly")
        {

            $sort_option = "
                            MONTH(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) = MONTH(CURDATE())
                            AND YEAR(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) = YEAR(CURDATE())
                          "; 
            
        }

        elseif($_GET['date_range'] == "lastMonth")
        {
            $sort_option = " 
                            MONTH(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                            AND YEAR(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                            ";  
        }

        elseif($_GET['date_range'] == "year")
        {
            $sort_option = "YEAR(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) = YEAR(CURDATE())";
        }

        elseif($_GET['date_range'] == "lastyear")
        {
            $sort_option = "YEAR(STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y')) = YEAR(CURDATE()) - 1";
        } 

        elseif($_GET['date_range'] == "pilihTanggal")
        {
          if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
            $dt1 = $_GET["start_date"];
            $dt2 = $_GET["end_date"];
            $format_dt1 = date('d/m/Y', strtotime($dt1));
            $format_dt2 = date('d/m/Y', strtotime($dt2));
            $sort_option .= "STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y') BETWEEN STR_TO_DATE('$format_dt1', '%d/%m/%Y') AND STR_TO_DATE('$format_dt2', '%d/%m/%Y')";
          }
        } 

    }

    if(isset($_GET['status_bayar'])){
      if($_GET['status_bayar'] == "Belum Bayar"){
        $sort_option .= "AND status_pembayaran = '0'";
      }else if ($_GET['status_bayar'] == "Sudah Bayar"){
        $sort_option .= "AND status_pembayaran = '1'";
      }
    }

    if(isset($_GET['jenis_inv'])){
      if($_GET['jenis_inv'] == "nonppn"){
        $sort_option .= "AND jenis_inv = 'nonppn'";
      }else if ($_GET['jenis_inv'] == "ppn"){
        $sort_option .= "AND jenis_inv = 'ppn'";
      }else if ($_GET['jenis_inv'] == "bum"){
        $sort_option .= "AND jenis_inv = 'bum'";
      }
    }


    if(isset($_GET['status_tagihan'])){
      if($_GET['status_tagihan'] == "Belum Dibuat"){
        $sort_option .= "AND status_tagihan = '0'";
      }else if ($_GET['status_tagihan'] == "Sudah Dibuat"){
        $sort_option .= "AND status_tagihan = '1'";
      }
    }

    
    $sql = "SELECT 
                ipl.id_inv_pembelian,
                ipl.id_pembayaran,
                ipl.no_trx,
                ipl.no_inv,
                ipl.jenis_trx,
                ipl.tgl_pembelian,
                STR_TO_DATE(ipl.tgl_pembelian, '%d/%m/%Y') AS tgl_pembelian_convert,
                ipl.tgl_tempo,
                STR_TO_DATE(ipl.tgl_tempo, '%d/%m/%Y') AS tgl_tempo_convert,
                sp.nama_sp,
                ipl.total_pembelian,
                ipl.status_pembelian,
                ipl.status_pembayaran
            FROM inv_pembelian_lokal AS ipl
            LEFT JOIN tb_supplier sp ON ipl.id_sp = sp.id_sp
            WHERE $sort_option AND ipl.id_pembayaran = '' AND ipl.status_pembelian = '1' ORDER BY tgl_pembelian ASC";
  // Tambahkan ORDER BY setelah klausa WHERE
  $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));

?>