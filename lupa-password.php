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
                    <h5 class="card-title text-center pb-0 fs-4">Lupa Password</h5>
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
                  <form action="proses/proses-lupa-password.php" class="row g-3" method="POST">
                    <div class="col-12">
                      <!-- <label for="yourName" class="form-label">Username atau Password</label> -->
                      <input type="hidden" name="otp" value="<?php echo $generatedOTP ?>" class="form-control">
                      <input type="text" name="cek_data" class="form-control" placeholder="Masukan Username atau Email" required>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-danger w-100" type="submit" id="insert-btn" name="reset" disabled>Reset Password</button>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Sudah punya akun? <a href="login.php">Login</a></p>
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
  document.addEventListener('DOMContentLoaded', function () {
    var cekDataInput = document.querySelector('input[name="cek_data"]');
    var resetButton = document.getElementById('insert-btn');

    cekDataInput.addEventListener('input', function () {
      // Memeriksa apakah nilai input tidak kosong
      if (cekDataInput.value.trim() !== '') {
        resetButton.disabled = false; // Mengaktifkan tombol
      } else {
        resetButton.disabled = true; // Menonaktifkan tombol jika input kosong
      }
    });
  });
</script>




