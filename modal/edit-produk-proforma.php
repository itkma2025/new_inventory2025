<?php
    $action = "";
    if ($_GET['jenis'] == 'nonppn') {
        $action = "proses/proses-invoice-nonppn.php";
    } else if ($_GET['jenis'] == 'ppn') {
        $action = "proses/proses-invoice-ppn.php";
    } else if ($_GET['jenis'] == 'bum') {
        $action = "proses/proses-invoice-bum.php";
    } else {
        ?>
            <script type="text/javascript">
                window.location.href = "../404.php";
            </script>
        <?php
    }
?>
<!-- Modal Edit-->
<div class="modal fade" id="modalEditProduk" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 text-dark" id="exampleModalLabel">Edit Data Produk</h1>
            </div>
            <div class="modal-body">
                <div id="editDataProdukShow"></div> <!-- Tempat data akan dimuat -->
            </div>
        </div>
    </div>
</div>