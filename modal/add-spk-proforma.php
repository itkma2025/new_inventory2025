<div class="modal fade" id="addSpk" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header text-center text-dark"><b>Tambah SPK Baru</b></div>
                    <div class="card-body mt-3">
                        <div class="table-responsive">
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
                                $no = 1;
                                $sql = "SELECT id_spk_reg FROM spk_reg WHERE id_inv = '$id_inv'";
                                $query = mysqli_query($connect, $sql);
                                $totalData = mysqli_num_rows($query);
                            ?>
                            <form action="<?php echo $action ?>" method="POST">
                                <table class="table table-bordered table-striped" id="table2">
                                    <thead>
                                        <tr class="text-white" style="background-color: navy;">
                                            <th class="text-center p-3" style="width: 20px">Pilih</th>
                                            <th class="text-center p-3" style="width: 30px">No</th>
                                            <th class="text-center p-3" style="width: 150px">No. SPK</th>
                                            <th class="text-center p-3" style="width: 150px">Tgl. SPK</th>
                                            <th class="text-center p-3" style="width: 150px">No. PO</th>
                                            <th class="text-center p-3" style="width: 200px">Nama Customer</th>
                                            <th class="text-center p-3" style="width: 150px">Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include "koneksi.php";
                                        $no = 1;
                                        $sql_inv = "SELECT  
                                                        sr.id_spk_reg,
                                                        sr.no_spk,
                                                        sr.tgl_spk,
                                                        sr.no_po,
                                                        sr.note, 
                                                        cs.nama_cs, 
                                                        cs.alamat
                                                    FROM spk_reg AS sr
                                                    JOIN tb_customer cs ON(sr.id_customer = cs.id_cs)
                                                    WHERE status_spk = 'Siap Kirim' AND id_cs = '$id_cs'";
                                        $query_inv = mysqli_query($connect, $sql_inv);
                                        while ($data_inv = mysqli_fetch_array($query_inv)) {
                                        ?>
                                            <tr>
                                                <input type="hidden" name="id_inv" value="<?php echo encrypt($id_inv, $key_global) ?>">
                                                <td class="text-center"><input type="checkbox" name="id_spk[]" value="<?php echo encrypt($data_inv['id_spk_reg'], $key_global) ?>"></td>
                                                <td class="text-center"><?php echo $no; ?></td>
                                                <td><?php echo $data_inv['no_spk'] ?></td>
                                                <td><?php echo $data_inv['tgl_spk'] ?></td>
                                                <td><?php echo $data_inv['no_po'] ?></td>
                                                <td><?php echo $data_inv['nama_cs'] ?></td>
                                                <td><?php echo $data_inv['note'] ?></td>
                                            </tr>
                                            <?php $no++ ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Close</button>
                                    <button type="submit" class="btn btn-primary" name="add-spk" id="add" disabled><i class="bi bi-plus-circle"></i> Add SPK</button>
                                </div>
                            </form>
                            <script>
                                // Mendapatkan checkbox SPK
                                const spkCheckboxes = document.querySelectorAll('input[name="id_spk[]"]');

                                // Mendapatkan tombol
                                const addButton = document.getElementById("add");


                                // Mendapatkan jumlah total checkbox yang dipilih
                                function getSelectedCheckboxCount() {
                                    let count = 0;
                                    spkCheckboxes.forEach(function(checkbox) {
                                        if (checkbox.checked) {
                                            count++;
                                        }
                                    });
                                    return count;
                                }

                                // Fungsi untuk mengaktifkan/menonaktifkan tombol berdasarkan jumlah checkbox yang dipilih
                                function toggleButton() {
                                    if (getSelectedCheckboxCount() > 0) {
                                        addButton.disabled = false;
                                    } else {
                                        addButton.disabled = true;
                                    }
                                }

                                // Event listener untuk setiap checkbox
                                spkCheckboxes.forEach(function(checkbox) {
                                    checkbox.addEventListener('change', toggleButton);
                                });

                                // Event listener untuk setiap checkbox
                                spkCheckboxes.forEach(function(checkbox) {
                                    checkbox.addEventListener('change', function() {
                                        // console.log("Total Data: " + <?php echo $totalData; ?>);
                                        // console.log("Total Checkbox: " + getSelectedCheckboxCount());

                                        const totalData = <?php echo $totalData; ?>;
                                        const maxAllowed = 10;

                                        if (totalData + getSelectedCheckboxCount() > maxAllowed) {
                                            const message = "Data Anda saat ini: " + totalData + " Anda hanya bisa menambahkan " + (maxAllowed - totalData) + " data.";
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Data melebihi batasan maksimum',
                                                text: message,
                                                didOpen: () => {
                                                    // Mengatur ulang semua checkbox menjadi tidak dipilih
                                                    spkCheckboxes.forEach(function(checkbox) {
                                                        checkbox.checked = false;
                                                    });
                                                }
                                            });
                                        }
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>