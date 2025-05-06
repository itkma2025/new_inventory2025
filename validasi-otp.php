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
  <style>
    .otp-input-container {
      display: flex;
      justify-content: space-between;
    }

    .otp-input {
      width: 40px;
      height: 40px;
      text-align: center;
      font-size: 18px;
      border: 1px solid #ccc;
      border-radius: 5px;
      margin-right: 10px;
    }
  </style>
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
                                <h5 class="card-title text-center pb-0 fs-4">Konfirmasi OTP</h5>
                                </div>
                                <?php  
                                    // Check if alert session is set
                                    if (isset($_SESSION['alert'])) {
                                    $alertType = $_SESSION['alert'];

                                    // Display alert based on the session value
                                    echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                                            <strong>'.$alertType.'</strong>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                          </div>';

                                    // Clear the alert session after displaying it
                                    unset($_SESSION['alert']);
                                    }
                                ?>
                                <form action="proses/proses-validasi-otp.php" class="row g-3" method="POST">
                                    <?php  
                                        include "koneksi.php";
                                        $id_reset = base64_decode($_GET['id']);
                                        $id_reset_encode = base64_encode($id_reset);
                                        $cek_data_reset = $connect->query("SELECT id_user, email, otp, expired FROM reset_password WHERE id_reset = '$id_reset'");
                                        $data = mysqli_fetch_array($cek_data_reset);
                                        $email = $data['email'];
                                        $otp = $data['otp'];
                                        $expired = $data['expired'];
                                    ?>
                                    <div class="col-12 text-center">
                                        <input type="hidden" name="id_reset" value="<?php echo $id_reset ?>">
                                        <input type="hidden" name="otp" id="hiddenOTP" class="form-control" maxlength="6" placeholder="Masukan OTP" required>
                                        <!-- Create 6 input boxes for OTP -->
                                        <input type="text" class="otp-input" maxlength="1" id="otp1" oninput="moveToNext(this, 'otp2')">
                                        <input type="text" class="otp-input" maxlength="1" id="otp2" data-prev-input="otp1" oninput="moveToNext(this, 'otp3')">
                                        <input type="text" class="otp-input" maxlength="1" id="otp3" data-prev-input="otp2" oninput="moveToNext(this, 'otp4')">
                                        <input type="text" class="otp-input" maxlength="1" id="otp4" data-prev-input="otp3" oninput="moveToNext(this, 'otp5')">
                                        <input type="text" class="otp-input" maxlength="1" id="otp5" data-prev-input="otp4" oninput="moveToNext(this, 'otp6')">
                                        <input type="text" class="otp-input" maxlength="1" id="otp6" data-prev-input="otp5" oninput="combineOTP()">
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-secondary w-100" type="submit" id="insert-btn" name="cek-validasi" disabled>Submit</button>
                                    </div>
                                    <div class="col-12">
                                        <p id="countdown" style="display: block;"></p>
                                        <p class="small mb-0" id="resend" style="display: none;">Tidak mendapatkan OTP ? <a href="proses/proses-resend-otp.php?otp=<?php echo base64_encode($generatedOTP) ?>&&id_reset=<?php echo $id_reset_encode ?>"> Kirim ulang OTP</a></p>
                                    </div>
                                </form>
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
 

    
 
</script>

<script>
  // Function to move to the next input box when a digit is entered
  function moveToNext(currentInput, nextInputId) {
    const maxLength = parseInt(currentInput.getAttribute('maxlength'));
    const currentLength = currentInput.value.length;

    if (currentLength >= maxLength) {
      const nextInput = document.getElementById(nextInputId);

      if (nextInput) {
        nextInput.focus();
      }
    }
  }
</script>
<script>
  function combineOTP() {
  // Menggabungkan nilai dari setiap kotak menjadi satu string OTP
  var otpValue = '';
  for (var i = 1; i <= 6; i++) {
    var inputId = 'otp' + i;
    var inputElement = document.getElementById(inputId);
    otpValue += inputElement.value;
  }

  // Memasukkan nilai string OTP ke input tersembunyi (jika diperlukan)
  document.getElementById('hiddenOTP').value = otpValue;

  // Menghitung jumlah karakter dalam string OTP
  var jumlahKarakter = otpValue.length;
  var resetButton = document.getElementById('insert-btn');
  var countdownExpired = false; // Flag untuk menandakan apakah waktu telah habis

  // Melakukan sesuatu dengan jumlah karakter, misalnya menampilkan di console
  // console.log("Jumlah karakter OTP:", jumlahKarakter);

  // Memeriksa apakah waktu belum habis dan nilai input 6 digit
  if (!countdownExpired && jumlahKarakter >= 6) {
    resetButton.disabled = false; // Mengaktifkan tombol
  } else {
    resetButton.disabled = true; // Menonaktifkan tombol jika input kosong atau waktu habis
  }

  // Waktu target (waktu yang Anda berikan)
  var targetTime = new Date('<?php echo $expired ?>').getTime();

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
      resetButton.disabled = true; // Menonaktifkan tombol jika input kosong atau waktu habis
      countdownExpired = true; // Set flag waktu habis menjadi true
    } else {
      // Menghitung sisa waktu dalam menit dan detik
      var minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

      // Menampilkan countdown di elemen dengan id "countdown"
      document.getElementById('countdown').innerHTML = minutes + ' menit ' + seconds + ' detik ';
    }
  }, 1000); // Mengupdate setiap 1 detik
}

// Memanggil combineOTP setelah mendapatkan nilai OTP
combineOTP();

</script>

<script>
 function handleBackspace(inputElement) {
    inputElement.addEventListener('keydown', function(event) {
      if (event.key === 'Backspace' && this.value.length === 0) {
        event.preventDefault();

        const prevInputId = this.getAttribute('data-prev-input');

        if (prevInputId) {
          const prevInput = document.getElementById(prevInputId);

          if (prevInput) {
            prevInput.focus();
          }
        }
      }
    });
  }

  // Pasang event listener untuk setiap input
  const inputIds = ['otp1', 'otp2', 'otp3', 'otp4', 'otp5', 'otp6'];
  for (let i = 0; i < inputIds.length; i++) {
    const currentInput = document.getElementById(inputIds[i]);
    const nextInputId = i < inputIds.length - 1 ? inputIds[i + 1] : null;
    const prevInputId = i > 0 ? inputIds[i - 1] : null;

    if (currentInput) {
      if (nextInputId) {
        currentInput.setAttribute('data-next-input', nextInputId);
      }
      if (prevInputId) {
        currentInput.setAttribute('data-prev-input', prevInputId);
      }

      currentInput.addEventListener('input', function() {
        moveToNext(this, this.getAttribute('data-next-input'));
        combineOTP();
      });

      handleBackspace(currentInput);
    }
  }
</script>


