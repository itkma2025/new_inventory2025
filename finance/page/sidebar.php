<?php  
  require_once "../akses.php";
  require_once __DIR__ . "/../cek-role.php";
?>
<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link <?php if ($page == 'dashboard') { echo 'active-link'; } ?>" href="dashboard.php">
        <i class="bi bi-grid"></i><span>Dasboard</span>
      </a>
    </li>
    <!-- End Dashboard Nav -->

    <!-- Data SPK -->
    <li class="nav-item">
      <a class="nav-link <?php if ($page == 'data-sp') { echo 'active-link'; } ?>" href="data-supplier.php">
        <i class="bi bi-truck"></i><span>Data Supplier</span>
      </a>
    </li>
    <!-- End Data SPK -->

    <!-- Data Bank -->
    <li class="nav-item">
      <a class="nav-link collapsed <?php echo (isset($page) && $page == 'bank') ? 'active-link' : ''; ?>" data-bs-target="#bank" data-bs-toggle="collapse" href="#">
        <i class="bi bi-bank"></i><span>Data Bank</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="bank" class="nav-content collapse <?php echo (isset($page) && $page == 'bank') ? 'show' : ''; ?>" data-bs-parent="#bank">
        <li>
          <a class="<?php if ($page2 == 'bank-master') { echo 'active'; } ?>" href="data-bank.php">
            <i class="bi bi-circle"></i><span>Bank</span>
          </a>
        </li>
        <li>
          <a class="<?php if ($page2 == 'bank-pt') { echo 'active'; } ?>" href="data-bank-pt.php">
            <i class="bi bi-circle"></i><span>Perusahaan</span> 
          </a>
        </li>
        <li>
          <a class="<?php if ($page2 == 'bank-cs') { echo 'active'; } ?>" href="data-bank-cs.php">
            <i class="bi bi-circle"></i><span>Customer</span>
          </a>
        </li>
        <li>
          <a class="<?php if ($page2 == 'bank-sp') { echo 'active'; } ?>" href="data-bank-sp.php">
            <i class="bi bi-circle"></i><span>Supplier</span>
          </a>
        </li>
      </ul>
    </li>
    <!-- End Data Bank -->

    <!-- Data SPK -->
    <li class="nav-item">
      <a class="nav-link <?php echo (isset($page) && $page == 'spk') ? 'active-link' : ''; ?>" href="spk-reg.php">
        <i class="bi bi-bar-chart"></i><span>Data SPK</span>
      </a>
    </li>
    <!-- End Data SPK -->

    <!-- Data Pembelian -->
    <li class="nav-item">
      <a class="nav-link <?php echo (isset($page) && $page == 'pembelian') ? 'active-link' : ''; ?>" href="data-pembelian.php?date_range=year">
        <i class="bi bi-bar-chart"></i><span>Data Pembelian</span>
      </a>
    </li>
    <!-- End Data Pembelian -->
    
    <!-- Invoice -->
    <li class="nav-item">
      <a class="nav-link <?php echo (isset($page) && $page == 'invoice') ? 'active-link' : ''; ?>" data-bs-target="#invoice" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-text"></i><span>Data Invoice</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="invoice" class="nav-content collapse <?php echo (isset($page) && $page == 'invoice') ? 'show' : ''; ?>" data-bs-parent="#invoice">
        <li class="nav-item">
          <a class="<?php if ($page2 == 'penjualan') { echo 'active'; } ?>" href="finance-inv.php?date_range=monthly">
            <i class="bi bi-circle"></i><span>Penjualan</span>
          </a>
        </li>
        <li>
          <a class="<?php if ($page2 == 'pembelian') { echo 'active'; } ?>" href="finance-inv-pembelian.php?date_range=year">
            <i class="bi bi-circle"></i><span>Pembelian</span>
          </a>
        </li>
      </ul>
    </li>

    <li class="nav-item">
      <a class="nav-link <?php echo (isset($page) && $page == 'list-tagihan') ? 'active-link' : ''; ?>" data-bs-target="#tagihan" data-bs-toggle="collapse" href="#">
        <i class="bi bi-file-earmark-text"></i><span>List Tagihan</span><i class="bi bi-chevron-down ms-auto"></i>
      </a>
      <ul id="tagihan" class="nav-content collapse <?php echo (isset($page) && $page == 'list-tagihan') ? 'show' : ''; ?>" data-bs-parent="#tagihan">
        <li>
          <a class="<?php if ($page2 == 'tagihan-penjualan') { echo 'active'; } ?>" href="list-tagihan-penjualan.php?date_range=year">
            <i class="bi bi-circle"></i><span>Penjualan</span>
          </a>
        </li>
        <li>
          <a class="<?php if ($page2 == 'tagihan-pembelian') { echo 'active'; } ?>" href="list-tagihan-pembelian.php?date_range=year">
            <i class="bi bi-circle"></i><span>Pembelian</span>
          </a>
        </li>
        <li>
          <a class="<?php if ($page2 == 'tagihan-refund') { echo 'active'; } ?>" href="list-refund-dana.php?date_range=year"">
            <i class="bi bi-circle"></i><span>Refund</span>
          </a>
        </li>
        <li>
          <a class="<?php if ($page2 == 'tagihan-cb') { echo 'active'; } ?>" href="list-cashback.php?sort_data=tahun_ini"">
            <i class="bi bi-circle"></i><span>Cashback</span>
          </a>
        </li>
      </ul>
    </li>

    <!-- Transaksi Customer -->
    <li class="nav-item">
      <a class="nav-link <?php echo (isset($page) && $page == 'list-cs') ? 'active-link' : ''; ?>" href="finance-customer.php?sort_data=bulan_ini">
        <i class="bi bi-cash-stack"></i><span>Transaksi Customer</span>
      </a>
    </li>
    <!-- End Transaksi Customer -->

    <!-- Invoice Komplain -->
    <li class="nav-item">
      <a class="nav-link <?php echo (isset($page) && $page == 'list-komplain') ? 'active-link' : ''; ?>" href="invoice-komplain.php?date_range=year">
        <i class="bi bi-clipboard-x"></i><span>Invoice Komplain</span>
      </a>
    </li>
    <!-- End Invoice Komplain -->
  </ul>
</aside><!-- End Sidebar-->