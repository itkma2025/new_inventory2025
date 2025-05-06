<?php
  $page = 'dashboard';
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
</head>

<body>
  <!-- nav header -->
  <?php include "page/nav-header.php" ?>
  <!-- end nav header -->

  <!-- sidebar  -->
  <?php include "page/sidebar.php"; ?>
  <!-- end sidebar -->

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section dashboard">
        
        <?php  
            // echo "<pre>";
            // print_r($_SERVER);
            // echo "</pre>";
            // $leak = [];

            // for ($i = 0; $i < 100000; $i++) {
            //     $leak[] = str_repeat('leak', 1000); // simulasikan data besar
            //     if ($i % 10000 == 0) {
            //         echo "Usage: " . memory_get_usage(true) . " bytes\n";
            //     }
            // }


            $leak = [];

            for ($i = 0; $i < 100000; $i++) {
                $leak[] = str_repeat('leak', 1000);

                if ($i % 10000 == 0) {
                    echo "Usage: " . memory_get_usage(true) . " bytes\n";

                    // Bersihkan
                    unset($leak);
                    $leak = [];
                    gc_collect_cycles(); // Garbage Collector manual
                }
            }

         ?>

<?php

// PHP Native
// function tambah_native($a, $b) {
//     return $a + $b;
// }

// // PHP OOP
// class Calculator {
//     public function tambah($a, $b) {
//         return $a + $b;
//     }
// }

// // Pengujian
// $start_native = microtime(true);
// for ($i = 0; $i < 100000000; $i++) {
//     tambah_native(1, 2);
// }
// $end_native = microtime(true);
// $time_native = $end_native - $start_native;

// $start_oop = microtime(true);
// $calc = new Calculator();
// for ($i = 0; $i < 100000000; $i++) {
//     $calc->tambah(1, 2);
// }
// $end_oop = microtime(true);
// $time_oop = $end_oop - $start_oop;

// echo "PHP Native: " . number_format($time_native, 4) . " detik\n";
// echo "PHP OOP: " . number_format($time_oop, 4) . " detik\n";

?>


    </section>
  </main><!-- End #main -->
  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <?php include "page/script.php"; ?>
</body>
</html>
<script>
  // Fungsi untuk memeriksa status reload dan melakukan reload jika belum dilakukan sebelumnya
  function checkAndReload() {
      // Periksa apakah status reload telah diset sebelumnya
      var statusReload = sessionStorage.getItem('status_reload');
      
      // Jika status reload belum ada atau bernilai 0, lakukan reload
      if (!statusReload || statusReload === "0") {
          // Memperbarui status reload
          sessionStorage.setItem('status_reload', '1');
          // Memuat ulang halaman
          location.reload(true);
      }
  }
  // Panggil fungsi checkAndReload saat halaman dimuat
  checkAndReload();

  // window.onload = function() {
  //   var audio = new Audio('assets/sound/notifikasi.mp3');
  //   audio.autoplay = true;
  // };
</script>