<?php  
    require_once "akses.php";
?>
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav">
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