<?php
    session_start();
    date_default_timezone_set('Asia/Jakarta');
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
                    <h5 class="card-title text-center pb-0 fs-4">Reset Password</h5>
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
                  <form action="proses/proses-ubah-password.php" class="row g-3" method="POST">
                    <?php  
                        $id_reset = base64_decode($_GET['id']);
                    ?>
                    <input type="hidden" name="id_reset" class="form-control" value="<?php echo $id_reset ?>">
                    <div class="col-12">
                      <label class="fw-bold">Password Baru</label>
                      <input type="password" name="password" class="form-control" placeholder="Masukan password baru" required>
                    </div>
                    <div class="col-12">
                      <label class="fw-bold">Konfirmasi Password</label>
                      <input type="password" name="konfirmasi_password" class="form-control" placeholder="Masukan konfirmasi password" required>
                    </div>
                    <div class="col-12 mt-5">
                      <button class="btn btn-primary w-100" type="submit" id="insert-btn" name="ubah-password" disabled>Ubah Password</button>
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

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var passwordInput = document.querySelector('input[name="password"]');
    var confirmPasswordInput = document.querySelector('input[name="konfirmasi_password"]');
    var resetButton = document.getElementById('insert-btn');

    function checkPasswordMatch() {
      var password = passwordInput.value;
      var confirmPassword = confirmPasswordInput.value;

      if (password === confirmPassword && password.trim() !== '') {
        resetButton.disabled = false; // Mengaktifkan tombol jika password sesuai
      } else {
        resetButton.disabled = true; // Menonaktifkan tombol jika password tidak sesuai atau kosong
      }
    }

    // Memantau perubahan input pada kedua password
    passwordInput.addEventListener('input', checkPasswordMatch);
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
  });
</script>




