<!-- Kode untuk role akses -->
<?php
include "akses.php";

// Mendapatkan protokol (http atau https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

// Mendapatkan path URI
// $uri = $_SERVER['REQUEST_URI'];

// Mendapatkan nama host
$host = $_SERVER['HTTP_HOST'];

// untuk localhost
$url = $protocol . "://" . $host . "/test-inventory";

// untuk hosting
// $url = $protocol . "://" . $host;

// Sidebar inclusion based on role
if (isset($user_role)) {
  if ($user_role == "Pimpinan") {
      include "sidebar-pimpinan.php";
  } else if ($user_role == "Operator Gudang") {
      include "sidebar-personil-gudang.php";
  } else if ($user_role == "Kepala Gudang") {
     include "sidebar-kepala-gudang.php";
  } else if ($user_role == "Driver" ||  $user_role == "Finance") { 
      header("Location:404.php");
  } else {
    ?>
      <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav">
          <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'dashboard') ? 'active-link' : ''; ?>" href="dashboard.php">
              <i class="bi bi-grid"></i><span>Dasboard</span>
            </a>
          </li>
          <!-- End Dashboard Nav -->
    
          <!-- Supplier & Customer -->
          <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'spcs') ? 'active-link' : ''; ?>" data-bs-target="#suppliercs" data-bs-toggle="collapse" href="#">
              <i class="bi bi-truck"></i><span>Supplier & Customer</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="suppliercs" class="nav-content collapse <?php echo (isset($page) && $page == 'spcs') ? 'show' : ''; ?>" data-bs-parent="#suppliercs">
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-sp') ? 'active' : ''; ?>" href="data-supplier.php">
                  <i class="bi bi-circle"></i><span>Supplier</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-sp') ? 'active' : ''; ?>" href="https://localhost/test-supplier/?url=<?php echo $url ?>">
                  <i class="bi bi-circle"></i><span>Supplier New</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-cs') ? 'active' : ''; ?>" href="data-customer.php">
                  <i class="bi bi-circle"></i><span>Customer</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-cs-sph') ? 'active' : ''; ?>" href="data-customer-sph.php">
                  <i class="bi bi-circle"></i><span>Customer SPH</span>
                </a>
              </li>
            </ul>
          </li>
          <!-- End Supplier & Customer -->
    
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
          <!-- Produk  Masuk -->
          <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'br-masuk') ? 'active-link' : ''; ?>" data-bs-target="#barang-masuk" data-bs-toggle="collapse" href="#">
              <i class="bi bi-file-ruled-fill"></i><span>Produk Masuk</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="barang-masuk" class="nav-content collapse <?php echo (isset($page) && $page == 'br-masuk') ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
              <li>
                <?php
                if ($user_role == "Super Admin" || $user_role == "Manager Gudang" || $user_role == "Admin Penjualan") {
                ?>
                  <a class="<?php echo (isset($page2) && $page2 == 'br-masuk-import') ? 'active' : ''; ?>" href="barang-masuk-reg-import.php">
                    <i class="bi bi-circle"></i><span>Import</span>
                  </a>
                <?php
                }
                ?>
                <a class="<?php echo (isset($page2) && $page2 == 'br-masuk-tambahan') ? 'active' : ''; ?>" href="barang-masuk-tambahan.php">
                  <i class="bi bi-circle"></i><span>Tambahan</span>
                </a>
                <a class="<?php echo (isset($page2) && $page2 == 'br-masuk-set-reg') ? 'active' : ''; ?>" href="barang-masuk-set-reg.php?date_range=year">
                  <i class="bi bi-circle"></i><span>Produk Set Reguler</span>
                </a>
                <a class="<?php echo (isset($page2) && $page2 == 'br-masuk-set-ecat') ? 'active' : ''; ?>" href="barang-masuk-set-ecat.php?date_range=year">
                  <i class="bi bi-circle"></i><span>Produk Set E-Cat</span>
                </a>
              </li>
            </ul>
          </li>
          <!-- End Produk  Masuk Nav -->
    
          <!-- Produk  Keluar -->
          <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'br-keluar') ? 'active-link' : ''; ?>" href="barang-keluar-reg.php">
              <i class="bi bi-file-ruled"></i><span>Produk Keluar</span>
            </a>
          </li>
          <!-- End Produk  Keluar Nav -->
    
          <!-- Perubahan Merk -->
          <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'perubahan-merk') ? 'active-link' : ''; ?>" href="ganti-merk-reg.php">
              <i class="bi bi-arrow-left-right"></i><span>Perubahan Merk</span>
            </a>
          </li>
          <!-- End Perubahan Merk -->
    
          <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'transaksi') ? 'active-link' : ''; ?>" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-bar-chart"></i><span>Transaksi</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse <?php echo (isset($page) && $page == 'transaksi') ? 'show' : ''; ?>" data-bs-parent="#sidebar-nav">
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'spk') ? 'active' : ''; ?>" href="spk-reg.php?sort=baru">
                  <i class="bi bi-circle"></i><span>SPK</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'sph') ? 'active' : ''; ?>" href="sph.php">
                  <i class="bi bi-circle"></i><span>SPH</span>
                </a>
              </li>
              <?php
              if ($user_role == "Super Admin" || $user_role == "Manager Gudang" || $user_role == "Admin Penjualan") {
              ?>
                <li>
                  <a class="<?php echo (isset($page2) && $page2 == 'list-cmp') ? 'active' : ''; ?>" href="invoice-komplain.php?date_range=year">
                    <i class="bi bi-circle"></i><span>Invoice Komplain</span>
                  </a>
                </li>
                <li>
                  <a class="<?php echo (isset($page2) && $page2 == 'list-review') ? 'active' : ''; ?>" href="review-bukti-kirim.php?sort=baru">
                    <i class="bi bi-circle"></i><span>Review Bukti Kirim</span>
                  </a>
                </li>
              <?php
              }
              ?>
            </ul>
          </li><!-- End Forms Nav -->
    
          <?php
          if ($user_role == "Super Admin" || $user_role == "Manager Gudang" || $user_role == "Admin Penjualan") {
          ?>
            <!-- Sales -->
            <li class="nav-item">
              <a class="nav-link collapsed <?php echo (isset($page) && $page == 'sales') ? 'active-link' : ''; ?>" href="data-sales.php">
                <i class="bi bi-people"></i><span>Sales</span>
              </a>
            </li>
            <!-- Emd Sales -->
          <?php
          }
          ?>
    
          <?php
          if ($user_role == "Super Admin" || $user_role == "Manager Gudang" || $user_role == "Admin Penjualan") {
          ?>
            <!-- Ekspedisi -->
            <li class="nav-item">
              <a class="nav-link collapsed <?php echo (isset($page) && $page == 'ekspedisi') ? 'active-link' : ''; ?>" href="data-ekspedisi.php">
                <i class="ri-truck-line"></i><span>Ekspedisi</span>
              </a>
            </li>
            <!-- End Ekspedisi -->
          <?php
          }
          ?>
    
    
          <!-- Keterangan -->
          <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'keterangan') ? 'active-link' : ''; ?>" data-bs-target="#keterangan" data-bs-toggle="collapse" href="#">
              <i class="bi bi-bookmarks"></i><span>Keterangan</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="keterangan" class="nav-content collapse <?php echo (isset($page) && $page == 'keterangan') ? 'show' : ''; ?>" data-bs-parent="#keterangan">
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'ket-in') ? 'active' : ''; ?>" href="keterangan-in.php">
                  <i class="bi bi-circle"></i><span>Produk Masuk</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'ket-out') ? 'active' : ''; ?>" href="keterangan-out.php">
                  <i class="bi bi-circle"></i><span>Produk Keluar</span>
                </a>
              </li>
              <li>
                <a class="<?php echo (isset($page2) && $page2 == 'cashback') ? 'active' : ''; ?>" href="keterangan-cashback.php">
                  <i class="bi bi-circle"></i><span>Cashback</span>
                </a>
              </li>
            </ul>
          </li>
          <!-- End Keterangan -->
    
    
    
          <!-- End Pajak di Gunggung -->
          <!-- Order By -->
          <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'orderby') ? 'active-link' : ''; ?>" href="data-orderby.php">
              <i class="bi bi-dropbox"></i><span>Order By</span>
            </a>
          </li>
          <!-- End Order By -->
    
          <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'scan-qr') ? 'active-link' : ''; ?>" href="scan-qr.php">
              <i class="bi bi-qr-code-scan"></i><span>Scan QR Code</span>
            </a>
          </li>
          <!-- End scan Nav -->
    
          <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'history-input-stock') ? 'active-link' : ''; ?>" href="history-input-stock.php">
              <i class="bi bi-clock-history"></i><span>History Input Stock</span>
            </a>
          </li>
          <?php
          if ($role == "Super Admin") { ?>
            <li class="nav-heading">Pages</li>
            <!-- Stock Digital -->
            <li class="nav-item">
              <a class="nav-link collapsed <?php echo (isset($page) && $page == 'stock-digital') ? 'active-link' : ''; ?>" data-bs-target="#stock-digital" data-bs-toggle="collapse" href="#">
                <i class="bi bi-box-seam"></i><span>Kartu Stock Digital</span><i class="bi bi-chevron-down ms-auto"></i>
              </a>
              <ul id="stock-digital" class="nav-content collapse <?php echo (isset($page) && $page == 'stock-digital') ? 'show' : ''; ?>" data-bs-parent="#stock-digital">
                <li>
                  <a class="<?php echo (isset($page2) && $page2 == 'prod-satuan') ? 'active' : ''; ?>" href="data-kartu-stock-produk-satuan.php?jenis=reguler">
                    <i class="bi bi-circle"></i><span>Produk Satuan</span>
                  </a>
                </li>
                <li>
                  <a class="<?php echo (isset($page2) && $page2 == 'prod-set') ? 'active' : ''; ?>" href="data-kartu-stock-produk-set.php?jenis=set-reg">
                    <i class="bi bi-circle"></i><span>Produk Set</span>
                  </a>
                </li>
              </ul>
            </li>
    
            <!-- End Kartu Stock Digital-->
            <li class="nav-item">
              <a class="nav-link collapsed <?php echo (isset($page) && $page == 'data-user') ? 'active-link' : ''; ?>" href="data-user.php">
                <i class="bi bi-person"></i>
                <span>Data User</span>
              </a>
            </li><!-- End Data User Page Nav -->
    
            <li class="nav-item">
              <a class="nav-link collapsed <?php echo (isset($page) && $page == 'role-user') ? 'active-link' : ''; ?>" href="data-user-role.php">
                <i class="bi bi-arrows-fullscreen"></i>
                <span>Role User</span>
              </a>
            </li><!-- End Role User Page Nav -->
    
            <!-- History user -->
            <li class="nav-item">
              <a class="nav-link collapsed <?php echo (isset($page) && $page == 'history-user') ? 'active-link' : ''; ?>" href="data-user-history.php">
                <i class="bi bi-clock-history"></i>
                <span>History User</span>
              </a>
            </li>
            <!-- End History User Page Nav -->
          <?php } ?>
        </ul>
      </aside>
      <!-- End Sidebar-->
    <?php
  }
} 
?>