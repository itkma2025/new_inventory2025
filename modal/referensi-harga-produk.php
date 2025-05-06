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
<div class="modal fade" id="referensiHargaProduk" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Referensi Harga Produk</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="referensiHargaProdukShow"></div> <!-- Tempat data akan dimuat -->
            </div>
        </div>
    </div>
</div>