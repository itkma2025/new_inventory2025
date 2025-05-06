<?php  
    require_once "akses.php";
?>
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav">
        <!-- Produk -->
        <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'produk') ? 'active-link' : ''; ?>" data-bs-target="#produk" data-bs-toggle="collapse" href="#">
              <i class="bi bi-box-seam-fill"></i><span>Data Produk</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="produk" class="nav-content collapse <?php echo (isset($page) && $page == 'produk') ? 'show' : ''; ?>" data-bs-parent="#produk">
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-produk') ? 'active' : ''; ?>" href="data-produk-reg.php">
                  <i class="bi bi-circle"></i><span>Produk</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-stock-reg') ? 'active' : ''; ?>" href="stock-produk-reg.php">
                  <i class="bi bi-circle"></i><span>Stok Produk Reguler</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-stock-ecat') ? 'active' : ''; ?>" href="stock-produk-ecat.php">
                  <i class="bi bi-circle"></i><span>Stok Produk Ecat</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-produk-set-marwa') ? 'active' : ''; ?>" href="data-produk-set-marwa.php">
                  <i class="bi bi-circle"></i><span>Produk Set Reguler</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-produk-set-ecat') ? 'active' : ''; ?>" href="data-produk-set-ecat.php">
                  <i class="bi bi-circle"></i><span>Produk Set Ecat</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-kat-prod') ? 'active' : ''; ?>" href="kategori-produk.php">
                  <i class="bi bi-circle"></i><span>Kategori Produk</span>
                </a>
              </li>
              <?php
              if ($user_role == "Super Admin" || $user_role == "Manager Gudang" || $user_role == "Admin Penjualan") {
              ?>
                <li>
                  <a class="<?php echo (isset($page2) && $page2 == 'data-kat-penj') ? 'active' : ''; ?>" href="kategori-penjualan.php">
                    <i class="bi bi-circle"></i><span>Kategori Penjualan</span>
                  </a>
                </li>
                <li>
                  <a class="<?php echo (isset($page2) && $page2 == 'data-merk') ? 'active' : ''; ?>" href="merk-produk.php">
                    <i class="bi bi-circle"></i><span>Merk Produk</span>
                  </a>
                </li>
                <li>
                  <a class="<?php echo (isset($page2) && $page2 == 'lokasi') ? 'active' : ''; ?>" href="lokasi-produk.php">
                    <i class="bi bi-circle"></i><span>Lokasi Produk</span>
                  </a>
                </li>
                <li>
                  <a class="<?php echo (isset($page2) && $page2 == 'grade') ? 'active' : ''; ?>" href="grade-produk.php">
                    <i class="bi bi-circle"></i><span>Grade Produk</span>
                  </a>
                </li>
              <?php
              }
              ?>
            </ul>
        </li>
        <!-- End Produk -->
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($page) && $page == 'scan-qr') ? 'active-link' : ''; ?>" href="scan-qr.php">
                <i class="bi bi-qr-code-scan"></i><span>Scan Barcode</span>
            </a>
        </li>
        <!-- End scan Nav -->

        <li class="nav-item">
            <a class="nav-link <?php echo (isset($page) && $page == 'history-input-stock') ? 'active-link' : ''; ?>" href="history-input-stock.php">
                <i class="bi bi-clock-history"></i><span>History Input Stock</span>
            </a>
        </li>
        <!-- End history Nav -->
    </ul>
</aside><!-- End Sidebar-->