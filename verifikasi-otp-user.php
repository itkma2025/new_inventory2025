<?php
    session_start();
    date_default_timezone_set('Asia/Jakarta');
    $generatedOTP = generateOTP();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Lupa Password</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <?php include "page/head.php"; ?>
</head>

<body>
  <main>
    <div class="container">
        <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="pt-2 pb-2">
                                    <h5 class="card-title text-center pb-0 fs-4">Konfirmasi Email</h5>
                                </div>
                                <?php  
                                    include "koneksi.php";
                                    $id_verifikasi = base64_decode($_GET['id']);
                                    $id_verifikasi_encode = base64_encode($id_verifikasi);
                                    $cek_data_verifikasi = $connect->query("SELECT 
                                                                                uv.id_user, uv.email, uv.otp, uv.expired, us.verifikasi 
                                                                            FROM user_verifikasi uv
                                                                            LEFT JOIN user us ON us.id_user = uv.id_user
                                                                            WHERE uv.id_verifikasi = '$id_verifikasi'");
                                    $data = mysqli_fetch_array($cek_data_verifikasi);
                                    $cek_data = mysqli_num_rows($cek_data_verifikasi);

                                    if($cek_data == 0){
                                        $_SESSION['alert'] = 'Akun sudah diverifikasi, silahkan tunggu aktivasi dari admin';
                                        header('Location:login.php');
                                    } else {
                                        echo '  <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                                    <strong>Verivikasi telah dikirim, silahkan verifikasi akun melalui email anda</strong>
                                                </div>';
                                    }
                                ?>
                                <div class="col-12">
                                    <p class="small mb-0" id="resend">Tidak mendapatkan Email ? <a href="registrasi-user.php"> Coba registrasi ulang</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
  </main><!-- End #main -->
  <?php include "page/script.php" ?>
  <?php include "page/validation-input.php" ?>
</body>
</html>

<!-- Generate OTP -->
<?php
function generateOTP() {
    // Panjang OTP
    $otpLength = 6;

    // Menghasilkan OTP acak dengan panjang yang ditentukan
    $otp = '';
    for ($i = 0; $i < $otpLength; $i++) {
        $otp .= rand(0, 9);
    }

    return $otp;
}

// Contoh penggunaan
// $generatedOTP = generateOTP();
?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var otpInput = document.querySelector('input[name="otp"]');
    var verifikasiButton = document.getElementById('insert-btn');
    var countdownExpired = false; // Flag untuk menandakan apakah waktu telah habis

    otpInput.addEventListener('input', function () {
      // Memeriksa apakah waktu belum habis dan nilai input tidak kosong
      if (!countdownExpired && otpInput.value.trim().length >= 6) {
        verifikasiButton.disabled = false; // Mengaktifkan tombol
      } else {
        verifikasiButton.disabled = true; // Menonaktifkan tombol jika input kosong atau waktu habis
      }
    });

    // Waktu target (waktu yang Anda berikan)
    var targetTime = new Date('<?php echo $expired ?>').getTime();
    // console.log("Nilai targetTime: ", targetTime);

    // Memperbarui countdown setiap 1 detik
    var countdownInterval = setInterval(function () {
      // Waktu sekarang
      var now = new Date().getTime();

      // Selisih waktu antara sekarang dan waktu target
      var timeDifference = targetTime - now;

      // Jika waktu sudah habis, hentikan interval countdown
      if (timeDifference <= 0) {
        clearInterval(countdownInterval);
        // Ubah display menjadi block saat waktu habis
        document.getElementById('resend').style.display = 'block';
        document.getElementById('countdown').style.display = 'none';
        verifikasiButton.disabled = true; // Menonaktifkan tombol jika input kosong atau waktu habis
        countdownExpired = true; // Set flag waktu habis menjadi true
      } else {
        // Menghitung sisa waktu dalam menit dan detik
        var minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

        // Menampilkan countdown di elemen dengan id "countdown"
        document.getElementById('countdown').innerHTML = minutes + ' menit ' + seconds + ' detik ';
      }
    }, 1000); // Mengupdate setiap 1 detik
  });
</script>




