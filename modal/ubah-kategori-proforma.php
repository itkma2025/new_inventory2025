  <!-- Modal Kategori Inv -->
  <div class="modal fade" id="ubahKat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Ubah Kategori Invoice</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Kategori Invoice Saat Ini</label>
                    <input type="text" class="form-control bg-light" name="id_inv" value="<?php echo $kat_inv ?>" readonly>
                </div>
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
               <form action="<?php echo $action ?>" method="POST">
                    <input type="hidden" name="id_inv" value="<?php echo encrypt($id_inv, $key_global) ?>" readonly>
                    <?php  
                        if ($kat_inv == 'Spesial Diskon'){
                            ?>
                                <div class="mb-3" id="spdisc_edit">
                                    <label>Spesial Diskon (%)</label>
                                    <input type="text" class="form-control" name="spdisc_edit" value="<?php echo $sp_disc ?>" oninput="formatDiscount(this)" required>
                                </div>
                            <?php
                        }
                    ?>
                    <div class="mb-3 mt-3" style="border-top: 1px solid black;"></div>
                    <div class="mb-3">
                        <label>Ubah Kategori Invoice</label>
                        <select name="kat_inv" class="form-select" id="kat_inv" onchange="toggleDiscount()">
                            <option value="">Pilih...</option>
                            <?php
                            $kategori_inv = $data_inv['kategori_inv'];
                            $pilihan_sisa = array('Reguler', 'Diskon', 'Spesial Diskon');
                            foreach ($pilihan_sisa as $pilihan) {
                                if ($pilihan != $kategori_inv) {
                                    echo '<option value="' . $pilihan . '">' . $pilihan . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="spdisc_div" style="display: none;">
                        <label>Spesial Diskon (%)</label>
                        <input type="text" class="form-control" name="spdisc" oninput="formatDiscount(this)">
                    </div>
                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="ubah-kategori">Update Kategori</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>

                <script>
                    function toggleDiscount() {
                        var selectValue = document.getElementById('kat_inv').value;
                        var discountDiv = document.getElementById('spdisc_div');
                        
                        if (selectValue === 'Spesial Diskon') {
                            discountDiv.style.display = 'block';  // Tampilkan jika pilihannya adalah 'Spesial Diskon'
                        } else {
                            discountDiv.style.display = 'none';  // Sembunyikan untuk pilihan lainnya
                        }
                    }
                </script>
            </div>
        </div>
    </div>
</div>
<!-- End Modal --> 