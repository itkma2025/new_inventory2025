<style>
    @media (max-width: 576px) {
        .nav-tabs .nav-item {
            width: 100%;
        }

        .nav-tabs .nav-link {
            text-align: left;
            width: 100%;
        }
    }
</style>
<ul class="nav nav-tabs d-flex ms-3 me-3 justify-content-between text-center" role="tablist" id="myTab" role="tablist">
    <!-- Belum Diproses -->
    <li class="nav-item flex-fill" role="presentation">
        <?php
        $sql_belum_diproses = " SELECT sr.*, cs.nama_cs, cs.alamat
                    FROM spk_reg AS sr
                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                    WHERE status_spk = 'Belum Diproses'";
        $query_belum_diproses = mysqli_query($connect, $sql_belum_diproses);
        $total_data_belum_diproses = mysqli_num_rows($query_belum_diproses);
        ?>
        <div class="p-3">
            <a class="nav-link position-relative <?php echo (isset($page_nav) && $page_nav == 'belum_diproses') ? 'active' : ''; ?>" href="spk-reg.php">
                <i class="bi bi-cart3" style="color: #6C757D"></i> Belum Diproses 
                <?php 
                    if ($total_data_belum_diproses != 0) {
                        echo '  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                    ' . $total_data_belum_diproses . '
                                </span>';
                    }
                ?>
            </a>
        </div>
    </li>
    
    <!-- Dalam proses -->
    <li class="nav-item flex-fill" role="presentation">
        <?php
        $sql_dalam_proses = " SELECT sr.*, cs.nama_cs, cs.alamat
                                FROM spk_reg AS sr
                                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                WHERE status_spk = 'Dalam Proses'";
        $query_dalam_proses = mysqli_query($connect, $sql_dalam_proses);
        $total_data_dalam_proses = mysqli_num_rows($query_dalam_proses);
        ?>
        <div class="p-3">
            <a class="nav-link position-relative <?php echo (isset($page_nav) && $page_nav == 'dalam_proses') ? 'active' : ''; ?>" href="spk-dalam-proses.php?sort=baru">
                <i class="bi bi-arrow-repeat" style="color: #6C757D"></i> Dalam Proses 
                <?php 
                    if ($total_data_dalam_proses != 0) {
                        echo '  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                    ' . $total_data_dalam_proses . '
                                </span>';
                    }
                ?>
            </a>
        </div>
    </li>
    
    <!-- Siap Kirim -->
    <li class="nav-item flex-fill" role="presentation">
        <?php
        include "koneksi.php";
        $sql_siap_kirim = " SELECT sr.*, cs.nama_cs, cs.alamat
                            FROM spk_reg AS sr
                            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                            WHERE status_spk = 'Siap Kirim'";
        $query_siap_kirim = mysqli_query($connect, $sql_siap_kirim);
        $total_data_siap_kirim = mysqli_num_rows($query_siap_kirim);
        ?>
        <div class="p-3">
            <a class="nav-link position-relative <?php echo (isset($page_nav) && $page_nav == 'siap_kirim') ? 'active' : ''; ?>" href="spk-siap-kirim.php?sort=baru">
                <i class="bi bi-box-seam-fill" style="color: #6C757D"></i> Siap Kirim
                <?php 
                    if ($total_data_siap_kirim != 0) {
                        echo '  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                    ' . $total_data_siap_kirim . '
                                </span>';
                    }
                ?>
            </a>
        </div>
    </li>

    <!-- Proforma Invoice -->
    <li class="nav-item flex-fill" role="presentation">
        <?php
        $sql_inv = "SELECT nonppn.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
                        FROM inv_nonppn AS nonppn
                        LEFT JOIN spk_reg sr ON(nonppn.id_inv_nonppn = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        WHERE status_transaksi = 'Belum Dikirim' GROUP BY no_inv";
        $query_inv = mysqli_query($connect, $sql_inv);
        $total_inv_nonppn = mysqli_num_rows($query_inv);
        ?>
        <?php
        $sql_inv_ppn = "SELECT ppn.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
                        FROM inv_ppn AS ppn
                        LEFT JOIN spk_reg sr ON(ppn.id_inv_ppn = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        WHERE status_transaksi = 'Belum Dikirim' GROUP BY no_inv";
        $query_inv_ppn = mysqli_query($connect, $sql_inv_ppn);
        $total_inv_ppn = mysqli_num_rows($query_inv_ppn);
        $hasil = $total_inv_nonppn + $total_inv_ppn;
        ?>
        <?php
        $sql_inv_bum = "SELECT bum.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
                        FROM inv_bum AS bum
                        LEFT JOIN spk_reg sr ON(bum.id_inv_bum = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        WHERE status_transaksi = 'Belum Dikirim' GROUP BY no_inv";
        $query_inv_bum = mysqli_query($connect, $sql_inv_bum);
        $total_inv_bum = mysqli_num_rows($query_inv_bum);
        $hasil = $total_inv_nonppn + $total_inv_ppn + $total_inv_bum;
        ?>
        <div class="p-3">
            <a class="nav-link position-relative <?php echo (isset($page_nav) && $page_nav == 'proforma') ? 'active' : ''; ?>" href="invoice-reguler.php?sort=baru">
                <i class="bi bi-receipt" style="color: #6C757D"></i> Proforma Invoice 
                <?php 
                    if ($hasil != 0) {
                        echo '  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                    ' . $hasil . '
                                </span>';
                    }
                ?>
            </a>
        </div>
    </li>
    
    <!-- Dikirim -->
    <li class="nav-item flex-fill" role="presentation">
        <?php
        $sql_inv_dikirim = "SELECT nonppn.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
                        FROM inv_nonppn AS nonppn
                        LEFT JOIN spk_reg sr ON(nonppn.id_inv_nonppn = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        WHERE status_transaksi = 'Dikirim' GROUP BY no_inv";
        $query_inv_dikirim = mysqli_query($connect, $sql_inv_dikirim);
        $total_inv_nonppn_dikirim = mysqli_num_rows($query_inv_dikirim);
        ?>
        <?php
        $sql_inv_ppn_dikirim = "SELECT ppn.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
                        FROM inv_ppn AS ppn
                        LEFT JOIN spk_reg sr ON(ppn.id_inv_ppn = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        WHERE status_transaksi = 'Dikirim' GROUP BY no_inv";
        $query_inv_ppn_dikirim = mysqli_query($connect, $sql_inv_ppn_dikirim);
        $total_inv_ppn_dikirim = mysqli_num_rows($query_inv_ppn_dikirim);
        ?>
        <?php
        $sql_inv_bum_dikirim = "SELECT bum.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
                        FROM inv_bum AS bum
                        LEFT JOIN spk_reg sr ON(bum.id_inv_bum = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        WHERE status_transaksi = 'Dikirim' GROUP BY no_inv";
        $query_inv_bum_dikirim = mysqli_query($connect, $sql_inv_bum_dikirim);
        $total_inv_bum_dikirim = mysqli_num_rows($query_inv_bum_dikirim);
        $hasil_dikirim = $total_inv_nonppn_dikirim + $total_inv_ppn_dikirim + $total_inv_bum_dikirim;
        ?>
        <div class="p-3">
            <a class="nav-link position-relative <?php echo (isset($page_nav) && $page_nav == 'dikirim') ? 'active' : ''; ?>" href="invoice-reguler-dikirim.php?sort=baru">
                <i class="bi bi-truck" style="color: #6C757D"></i> Dikirim 
                <?php 
                    if ($hasil_dikirim != 0) {
                        echo '  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                    ' . $hasil_dikirim . '
                                </span>';
                    }
                ?>
            </a>
        </div>
    </li>

    <!-- Diterima -->
    <li class="nav-item flex-fill" role="presentation">
        <?php
        $sql_inv_diterima = "SELECT nonppn.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
                        FROM inv_nonppn AS nonppn
                        LEFT JOIN spk_reg sr ON(nonppn.id_inv_nonppn = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        WHERE status_transaksi = 'diterima' GROUP BY no_inv";
        $query_inv_diterima = mysqli_query($connect, $sql_inv_diterima);
        $total_inv_nonppn_diterima = mysqli_num_rows($query_inv_diterima);
        ?>
        <?php
        $sql_inv_ppn_diterima = "SELECT ppn.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
                        FROM inv_ppn AS ppn
                        LEFT JOIN spk_reg sr ON(ppn.id_inv_ppn = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        WHERE status_transaksi = 'diterima' GROUP BY no_inv";
        $query_inv_ppn_diterima = mysqli_query($connect, $sql_inv_ppn_diterima);
        $total_inv_ppn_diterima = mysqli_num_rows($query_inv_ppn_diterima);
        ?>
        <?php
        $sql_inv_bum_diterima = "SELECT bum.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
                        FROM inv_bum AS bum
                        LEFT JOIN spk_reg sr ON(bum.id_inv_bum = sr.id_inv)
                        JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                        WHERE status_transaksi = 'diterima' GROUP BY no_inv";
        $query_inv_bum_diterima = mysqli_query($connect, $sql_inv_bum_diterima);
        $total_inv_bum_diterima = mysqli_num_rows($query_inv_bum_diterima);
        $hasil_diterima = $total_inv_nonppn_diterima + $total_inv_ppn_diterima + $total_inv_bum_diterima;
        ?>
        <div class="p-3">
            <a class="nav-link position-relative <?php echo (isset($page_nav) && $page_nav == 'diterima') ? 'active' : ''; ?>" href="invoice-reguler-diterima.php?sort=baru">
                <i class="bi bi-check2-circle" style="color: #6C757D "></i> Diterima 
                <?php 
                    if ($hasil_diterima != 0) {
                        echo '  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                    ' . $hasil_diterima . '
                                </span>';
                    }
                ?>
            </a>
        </div>
    </li>

    <!-- Transaksi Selesai -->
    <li class="nav-item flex-fill" role="presentation">
        <?php
        $sql_inv_selesai = "SELECT nonppn.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
            FROM inv_nonppn AS nonppn
            LEFT JOIN spk_reg sr ON(nonppn.id_inv_nonppn = sr.id_inv)
            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
            WHERE status_transaksi = 'Transaksi Selesai' GROUP BY no_inv";
        $query_inv_selesai = mysqli_query($connect, $sql_inv_selesai);
        $total_inv_nonppn_selesai = mysqli_num_rows($query_inv_selesai);
        ?>
        <?php
        $sql_inv_ppn_selesai = "SELECT ppn.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
            FROM inv_ppn AS ppn
            LEFT JOIN spk_reg sr ON(ppn.id_inv_ppn = sr.id_inv)
            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
            WHERE status_transaksi = 'Transaksi Selesai' GROUP BY no_inv";
        $query_inv_ppn_selesai = mysqli_query($connect, $sql_inv_ppn_selesai);
        $total_inv_ppn_selesai = mysqli_num_rows($query_inv_ppn_selesai);
        ?>
        <?php
        $sql_inv_bum_selesai = "SELECT bum.*, sr.id_inv, sr.id_customer, sr.no_po, cs.nama_cs, cs.alamat
            FROM inv_bum AS bum
            LEFT JOIN spk_reg sr ON(bum.id_inv_bum = sr.id_inv)
            JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
            WHERE status_transaksi = 'Transaksi Selesai' GROUP BY no_inv";
        $query_inv_bum_selesai = mysqli_query($connect, $sql_inv_bum_selesai);
        $total_inv_bum_selesai = mysqli_num_rows($query_inv_bum_selesai);
        $hasil_selesai = $total_inv_nonppn_selesai + $total_inv_ppn_selesai + $total_inv_bum_selesai;
        ?>
        <div class="p-3">
            <a class="nav-link position-relative <?php echo (isset($page_nav) && $page_nav == 'selesai') ? 'active' : ''; ?>" href="invoice-reguler-selesai.php">
                <i class="bi bi-clipboard-check" style="color: #6C757D"></i> Transaksi Selesai 
                <?php 
                    if ($hasil_selesai != 0) {
                        echo '  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                    ' . $hasil_selesai . '
                                </span>';
                    }
                ?>
            </a>
        </div>
    </li>

    <!-- Transaksi cancel -->
    <li class="nav-item flex-fill" role="presentation">
        <?php
            $sql_cancel = " SELECT 
                                no_spk,
                                no_inv
                            FROM (
                                SELECT 
                                    sr.no_spk,
                                    '' AS no_inv  
                                FROM spk_reg AS sr
                                JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                WHERE sr.status_spk = 'Cancel Order' AND sr.id_inv = ''
                                UNION
                                SELECT 
                                    GROUP_CONCAT(CONCAT(sr.no_spk, ', ') SEPARATOR '') AS no_spk,
                                    COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv) AS no_inv
                                FROM spk_reg AS sr
                                LEFT JOIN tb_customer cs ON sr.id_customer = cs.id_cs
                                LEFT JOIN inv_nonppn nonppn ON sr.id_inv = nonppn.id_inv_nonppn
                                LEFT JOIN inv_ppn ppn ON sr.id_inv = ppn.id_inv_ppn
                                LEFT JOIN inv_bum bum ON sr.id_inv = bum.id_inv_bum
                                WHERE sr.status_spk = 'Cancel Order' AND sr.id_inv != ''
                                GROUP BY COALESCE(nonppn.no_inv, ppn.no_inv, bum.no_inv)
                            ) AS subquery";
            $query_cancel = mysqli_query($connect, $sql_cancel);
            $total_query_cancel = mysqli_num_rows($query_cancel);
        ?>
        <div class="p-3">
            <a class="nav-link position-relative <?php echo (isset($page_nav) && $page_nav == 'cancel') ? 'active' : ''; ?>" href="transaksi-cancel.php?sort=baru">
                <i class="bi bi-x-circle-fill" style="color: red;"></i> Transaksi Cancel 
                <?php 
                    if ($total_query_cancel != 0) {
                        echo ' <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary">
                                    ' . $total_query_cancel . '
                                </span>';
                    }
                ?>
            </a>
        </div>
    </li>
</ul>