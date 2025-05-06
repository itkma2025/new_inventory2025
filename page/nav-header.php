<?php  
  require_once "akses.php";
  require_once "function/error_log.php";
  $current_domain = 'https://' . $_SERVER['HTTP_HOST'] . "/test-inventory";

?>
<style>
  a{
    cursor: pointer;
  }

  .dropdown-custom{
    min-width: 400px;
    max-height: 500px; 
    overflow-y: auto;
  }

  .display-none{
    display: none;
  }
  @media (max-width: 420px) {
    .dropdown-custom{
      min-width: 380px;
      max-height: 500px; 
      overflow-y: auto;
    }
  }
  @media (max-width: 380px) {
    .dropdown-custom{
      min-width: 330px;
      max-height: 500px; 
      overflow-y: auto;
    }
  }
</style>
<!-- ======= Header ======= -->
<header id="header" class="header fixed-top d-flex align-items-center">
<div class="d-flex align-items-center justify-content-between">
  <a href="#" class="logo d-flex align-items-center">
    <img src="assets/img/logo-kma.png" alt="" style="width: 80px; height: auto;">
    <span class="d-none d-lg-block" style="text-decoration: none;">PT.KMA</span>
  </a>
  <i class="bi bi-list toggle-sidebar-btn"></i>
</div><!-- End Logo -->

<nav class="header-nav ms-auto">
  <ul class="d-flex align-items-center">
    <li class="nav-item d-block d-lg-none">
    </li><!-- End Search Icon-->
    <a class="nav-link nav-profile d-flex align-items-center pe-4">
      Sisa sesi: <span class="p-2" id="countdown"></span>
    </a><!-- End Profile Iamge Icon -->
    <?php include "./akses_domain.php"; ?>
    <?php  
      if ($role != "Operator Gudang") {
        ?>
          <!-- Notification Nav -->
          <li class="nav-item dropdown">
            <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-bell"></i>
                <span id="notification-badge" class="badge bg-primary badge-number"></span>
            </a><!-- End Notification Icon -->
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications p-3 dropdown-custom">
              <!-- Data notifikasi Hari Ini akan dimuat di sini -->
              <i class="fw-bold" style="color:#666666; font-size:14px">Today</i>
              <div id="notification-dropdown-today"></div>
              <div id="no-data-today" class="text-center fw-bold" style="font-size: 14px;">Tidak Ada Notifikasi Baru</div>
              <div class="text-center mb-3"><a id="load-more-today" style="color:blue;">View More <i class="bi bi-caret-down-fill"></i></a></div>
              <div><hr class="dropdown-divider mb-2"></div>
              <i class="fw-bold" style="color:#666666; font-size:14px">Earlier</i>
              <div id="notification-dropdown-earlier"></div>
              <div class="text-center mb-3"><a id="load-more-earlier" style="color:blue;">View More <i class="bi bi-caret-down-fill"></i></a></div>
            </ul><!-- End Notification Dropdown Items -->
          </li>
          <!-- End Notification Nav -->
        <?php
      }
    ?>
    <li class="nav-item dropdown pe-3">
      <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
        <img src="assets/img/user.jpg" alt="Profile" class="rounded-circle">
        <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo ucfirst($nama_user); ?></span>
      </a><!-- End Profile Iamge Icon -->

      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
        <li class="dropdown-header">
          <h6><?php echo ucfirst($nama_user); ?></h6>
          <span><?php echo $role; ?></span>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>

        <li>
          <a class="dropdown-item d-flex align-items-center" href="https://test-user-account.mandirialkesindo.co.id?url=<?php echo $current_domain ?>">
            <i class="bi bi-person"></i>
            <span>Profil</span>
          </a>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <a class="dropdown-item d-flex align-items-center" href="logout.php">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sign Out</span>
          </a>
        </li>

      </ul><!-- End Profile Dropdown Items -->
    </li><!-- End Profile Nav -->

  </ul>
</nav><!-- End Icons Navigation -->

</header>
<!-- End Header -->
<?php  
    include "notifikasi.php"
?>

