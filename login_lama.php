<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <link href="assets/img/logo-kma.png" rel="icon">
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/style-login.css">
</head>

<body>
  <div class="background">
    <div class="shape"></div>
    <div class="shape"></div>
  </div>
  <!-- SWEET ALERT -->
  <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {
                                          echo $_SESSION['info'];
                                        }
                                        unset($_SESSION['info']); ?>"></div>
  <!-- END SWEET ALERT -->
  <form action="cek-login.php" method="POST">
    <?php
    if (isset($_GET["gagal"])) { ?>
      <div class="alert alert-danger d-none" role="alert">
        Username atau password salah. Silakan coba lagi.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php unset($_GET["gagal"]);
    } ?>

    <script>
      if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href.split('?')[0]);
      }
    </script>
    <h3>Login</h3>
    <label for="username">Username</label>
    <input type="text" name="username" placeholder="Masukan username" id="username">

    <label for="password">Password</label>
    <input type="password" name="password" class="form-password" placeholder="Masukan password">

    <div class="password-wrapper">
      <input type="checkbox" class="form-check-input me-2 form-checkbox" id="show-password">
      <label style="font-size: 18px;" for="show-password">Lihat Password</label>
    </div>

    <button name="login">Log In</button>
  </form>
</body>

</html>

<script>
  var checkbox = document.getElementById('show-password');
  var password = document.querySelector('.form-password');

  checkbox.addEventListener('change', function() {
    if (password.type === 'password') {
      password.type = 'text';
    } else {
      password.type = 'password';
    }
  });
</script>