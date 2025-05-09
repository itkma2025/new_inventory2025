<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link <?php if ($page == 'dashboard') { echo 'active-link'; } ?>" href="dashboard.php">
        <i class="bi bi-grid"></i><span>Dasboard</span>
      </a>
    </li>
    <!-- End Dashboard Nav -->
   
    <!-- List Invoice -->
    <li class="nav-item">
      <?php  
          require_once __DIR__. '/../query/inv-baru.php'; 
          require_once __DIR__. "/../query/menunggu-verif-invoice.php";
          require_once __DIR__ . "/../query/badge-inv-baru.php";
          require_once __DIR__ . "/../query/badge-menunggu-verif.php";
          require_once __DIR__ . "/../query/badge-inv-revisi.php";

          // Kode untukmenampilkan total data invoice
          $total_data = $total_reg + $total_ecat + $total_pl;
          $total_data_verif = $total_verif_reg + $total_verif_ecat + $total_verif_pl;
          $grandTotal = $total_data + $total_data_verif;

          // Kode untuk menampilkan total data invoice revisi
          $total_data_revisi = $total_rev_reg;
      ?>
      <a class="nav-link collapsed <?php echo (isset($page) && $page == 'list-inv') ? 'active-link' : ''; ?>" data-bs-target="#barang-masuk" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-ruled-fill"></i><span>List Invoice</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="barang-masuk" class="nav-content collapse <?php echo (isset($page) && $page == 'list-inv') ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
        <li>
          <a class="<?php echo (isset($page2) && $page2 == 'list-inv-reg') ? 'active' : ''; ?>" href="list-invoice.php">
            <i class="bi bi-circle"></i>
            <span>
              List Invoice Reguler
                <span class="badge text-bg-secondary" id="totalInv"></span> 
            </span>
          </a>
          <a class="<?php echo (isset($page2) && $page2 == 'list-inv-rev') ? 'active' : ''; ?>" href="list-invoice-revisi.php">
            <i class="bi bi-circle"></i>
            <span>
              List Invoice Revisi
              <span class="badge text-bg-secondary" id="totalInvRev"></span> 
            </span>
          </a>
        </li>
      </ul>
      <script>
          let badgeTotalData = "<?php echo $grandTotal ?>";
          let badgeTotalDataRev = "<?php echo $total_data_revisi ?>";

          if(badgeTotalData != 0){
            document.getElementById('totalInv').textContent = badgeTotalData;
          } else {
            document.getElementById('totalInv').classList.add('d-none');
          }

          if(badgeTotalDataRev != 0){
            document.getElementById('totalInvRev').textContent = badgeTotalDataRev;
          } else {
            document.getElementById('totalInvRev').classList.add('d-none');
          }
      </script>
    </li>
    <!-- End List Invoice -->

    <!-- List Tagihan -->
    <li class="nav-item">
      <?php 
        include 'query/list-tagihan.php'; 
        $query_total_data_tagihan = $connect->query($sql_tagihan . " GROUP BY bill.no_tagihan");
        $total_data_tagihan = mysqli_num_rows($query_total_data_tagihan);
      ?>
      <a class="nav-link collapsed <?php if ($page == 'list-tagihan') { echo 'active-link'; } ?>" href="list-tagihan.php">
        <i class="bi bi-file-check"></i>
        <span>
          List Tagihan &nbsp;
          <?php  
            if ($total_data_tagihan != 0){
              ?>
                <span class="badge text-bg-secondary"><?php echo $total_data_tagihan; ?></span> 
              <?php
            }
          ?>
        </span>
      </a>
    </li>
    <!-- End List Tagihan -->

    <!-- History Inv-->
    <li class="nav-item">
      <a class="nav-link collapsed <?php if ($page == 'hist-inv') { echo 'active-link'; } ?>" href="history-invoice.php">
        <i class="bi bi-bar-chart"></i><span>History Invoice</span>
      </a>
    </li>
    <!-- End History Inv -->

    <!-- History Tagihan -->
    <li class="nav-item">
      <a class="nav-link collapsed <?php if ($page == 'hist-tagihan') { echo 'active-link'; } ?>" href="history-tagihan.php">
        <i class="bi bi-bar-chart"></i><span>History Tagihan</span>
      </a>
    </li>
    <!-- End History Tagihan -->

    <li class="nav-item">
        <a class="nav-link collapsed <?php echo (isset($page) && $page == 'scan-qr') ? 'active-link' : ''; ?>" href="scan-qr.php">
            <i class="bi bi-qr-code-scan"></i><span>Scan Barcode</span>
        </a>
    </li>
    <!-- End scan Nav -->

    <li class="nav-item">
        <a class="nav-link collapsed <?php echo (isset($page) && $page == 'history-input-stock') ? 'active-link' : ''; ?>" href="history-input-stock.php">
            <i class="bi bi-clock-history"></i><span>History Input Stock</span>
        </a>
    </li>
  </ul>
</aside><!-- End Sidebar-->