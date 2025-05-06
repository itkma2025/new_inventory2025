<?php  
    require_once "akses.php";
?>
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav">
        <li class="nav-item">
        <a class="nav-link <?php echo (isset($page) && $page == 'dashboard') ? 'active-link' : ''; ?>" href="dashboard.php">
            <i class="bi bi-grid"></i><span>Dasboard</span>
        </a>
        </li>
        <!-- End Dashboard Nav -->

        <!-- Produk -->
        <li class="nav-item">
        <a class="nav-link collapsed <?php echo (isset($page) && $page == 'produk') ? 'active-link' : ''; ?>"" data-bs-target="#produk" data-bs-toggle="collapse" href="#">
            <i class="bi bi-box-seam-fill"></i><span>Data Produk</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="produk" class="nav-content collapse " data-bs-parent="#produk">
            <li>
            <a class="<?php echo (isset($page2) && $page2 == 'data-produk') ? 'active-link' : ''; ?>" href="data-produk-reg.php">
                <i class="bi bi-circle"></i><span>Produk</span>
            </a>
            </li>
            <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-stock-reg') ? 'active-link' : ''; ?>" href="stock-produk-reg.php">
                <i class="bi bi-circle"></i><span>Stok Produk Reguler</span>
                </a>
            </li>
            <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-stock-ecat') ? 'active-link' : ''; ?>" href="stock-produk-ecat.php">
                <i class="bi bi-circle"></i><span>Stok Produk Ecat</span>
                </a>
            </li>
            <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-produk-set-marwa') ? 'active-link' : ''; ?>" href="data-produk-set-marwa.php">
                    <i class="bi bi-circle"></i><span>Produk Set Reguler</span>
                </a>
            </li>
            <li>
                <a class="<?php echo (isset($page2) && $page2 == 'data-kat-prod') ? 'active-link' : ''; ?>" href="kategori-produk.php">
                    <i class="bi bi-circle"></i><span>Kategori Produk</span>
                </a>
            </li>
            <li>
                <a class="<?php echo (isset($page2) && $page2 == 'lokasi') ? 'active-link' : ''; ?>" href="lokasi-produk.php">
                    <i class="bi bi-circle"></i><span>Lokasi Produk</span>
                </a>
            </li>
        </ul>
        </li>
        <!-- End Produk -->
        <!-- Produk  Masuk -->
        <li class="nav-item">
            <a class="nav-link collapsed <?php if ($page == 'br-masuk') { echo 'active-link'; } ?>" data-bs-target="#barang-masuk" data-bs-toggle="collapse" href="#">
                <i class="bi bi-file-ruled-fill"></i><span>Produk  Masuk</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="barang-masuk" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                    <a class="<?php echo (isset($page2) && $page2 == 'br-masuk-import') ? 'active-link' : ''; ?>" href="barang-masuk-reg-import.php">
                        <i class="bi bi-circle"></i><span>Import</span> 
                    </a>
                    <a class=<?php echo (isset($page2) && $page2 == 'br-masuk-reg') ? 'active-link' : ''; ?>" href="barang-masuk-set-reg.php?date_range=year">
                        <i class="bi bi-circle"></i><span>Produk Set Reguler</span>
                    </a>
                </li>
            </ul>
        </li>
        <!-- End Produk  Masuk Nav -->

        <li class="nav-item">
            <a class="nav-link <?php echo (isset($page) && $page == 'br-keluar') ? 'active-link' : ''; ?>"  href="barang-keluar-reg.php">
                <i class="bi bi-file-ruled"></i><span>Produk  Keluar</span>
            </a>
        </li>
        <!-- End Produk  Keluar Nav -->

        <li class="nav-item">
            <a class="nav-link collapsed <?php echo (isset($page) && $page == 'transaksi') ? 'active-link' : ''; ?>" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
                <i class="bi bi-bar-chart"></i><span>Transaksi</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
                <li>
                <a class="<?php echo (isset($page2) && $page2 == 'spk') ? 'active-link' : ''; ?>" href="spk-reg.php?sort=baru">
                    <i class="bi bi-circle"></i><span>SPK</span>
                </a>
                </li>
                <li>
                <a class="<?php echo (isset($page2) && $page2 == 'sph') ? 'active-link' : ''; ?>" href="sph.php">
                    <i class="bi bi-circle"></i><span>SPH</span>
                </a>
                </li>
                <?php  
                if ($data['role'] == "Super Admin" || $data['role'] == "Manager Gudang" || $data['role'] == "Admin Penjualan") { 
                    ?>
                    <li>
                        <a class="<?php echo (isset($page2) && $page2 == 'list-cmp') ? 'active-link' : ''; ?>" href="invoice-komplain.php?date_range=year">
                        <i class="bi bi-circle"></i><span>Invoice Komplain</span>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </li><!-- End Forms Nav -->

        <!-- Data Bank -->
        <li class="nav-item">
        <a class="nav-link <?php echo (isset($page) && $page == 'bank') ? 'active-link' : ''; ?>" data-bs-target="#bank" data-bs-toggle="collapse" href="#">
            <i class="bi bi-bank"></i><span>Data Bank</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="bank" class="nav-content collapse " data-bs-parent="#bank">
            <li>
            <a class="<?php echo (isset($page2) && $page2 == 'bank-pt') ? 'active-link' : ''; ?>" href="data-bank-pt.php">
                <i class="bi bi-circle"></i><span>Perusahaan</span>
            </a>
            </li>
            <li>
            <a class="<?php echo (isset($page2) && $page2 == 'bank-cs') ? 'active-link' : ''; ?>" href="data-bank-cs.php">
                <i class="bi bi-circle"></i><span>Customer</span>
            </a>
            </li>
            <li>
            <a class="<?php echo (isset($page2) && $page2 == 'bank-sp') ? 'active-link' : ''; ?>" href="data-bank-sp.php">
                <i class="bi bi-circle"></i><span>Supplier</span>
            </a>
            </li>
        </ul>
        </li>
        <!-- End Data Bank -->

        <!-- Data Invoice penjualan -->
        <li class="nav-item">
        <a class="nav-link <?php echo (isset($page) && $page == 'finance') ? 'active-link' : ''; ?>" href="finance-inv.php?date_range=monthly">
            <i class="bi bi-cash-stack"></i><span>Invoice Penjualan</span>
        </a>
        </li>

        <li class="nav-item">
            <a class="nav-link <?php echo (isset($page) && $page == 'list-tagihan') ? 'active-link' : ''; ?>" data-bs-target="#tagihan" data-bs-toggle="collapse" href="#">
                <i class="bi bi-file-earmark-text"></i><span>List Tagihan</span><i class="bi bi-chevron-down ms-auto"></i>
            </a>
            <ul id="tagihan" class="nav-content collapse " data-bs-parent="#tagihan">
                <li>
                <a class="<?php echo (isset($page2) && $page2 == 'tagihan-penjualan') ? 'active-link' : ''; ?>" href="list-tagihan-penjualan.php?date_range=year">
                    <i class="bi bi-circle"></i><span>Penjualan</span>
                </a>
                </li>
                <li>
                <a class="<?php echo (isset($page2) && $page2 == 'tagihan-pembelian') ? 'active-link' : ''; ?>" href="list-tagihan-pembelian.php">
                    <i class="bi bi-circle"></i><span>Pembelian</span>
                </a>
                </li>
            </ul>
        </li>
        <!-- End Invoice Penjualan -->

        <!-- Transaksi Per Customer -->
        <li class="nav-item">
        <a class="nav-link <?php echo (isset($page) && $page == 'list-cs') ? 'active-link' : ''; ?>" href="finance-customer.php?date_range=monthly">
            <i class="bi bi-cash-stack"></i><span>Transaksi Customer</span>
        </a>
        </li>
        <!-- End  -->
        <?php
        if ($data['role'] == "Super Admin") { ?>
            <li class="nav-heading">Pages</li>
            <li class="nav-item">
                <a class="nav-link collapsed <?php echo (isset($page) && $page == 'data-user') ? 'active-link' : ''; ?>" href="data-user.php">
                    <i class="bi bi-person"></i>
                    <span>Data User</span>
                </a>
            </li>
            <!-- End Data User Page Nav -->

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
</aside><!-- End Sidebar-->