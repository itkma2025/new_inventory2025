<?php
require_once '../akses.php'; // Sesuaikan dengan file koneksi database Anda
$key = "Fin@nce2024?";

if(isset($_POST['id_cs'])) {
    $id_cs = $_POST['id_cs'];
    $id_cs_decrypt = decrypt($id_cs, $key);
    $no = 1;
    $sql_bank = "SELECT 
                    csb.id_bank_cs, csb.id_bank, csb.no_rekening, csb.atas_nama,
                    bk.nama_bank, cs.nama_cs
                FROM bank_cs AS csb
                LEFT JOIN bank bk ON (csb.id_bank = bk.id_bank)
                LEFT JOIN tb_customer cs ON (cs.id_cs = csb.id_cs)
                WHERE cs.id_cs = '$id_cs_decrypt'
                ORDER BY cs.nama_cs ASC";
    $query_bank = mysqli_query($connect, $sql_bank);
    ?>
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="table2">
            <thead>
                <tr class="text-white" style="background-color: navy;">
                    <th class="text-center text-nowrap p-3" style="width: 100px;">No</th>
                    <th class="text-center text-nowrap p-3" style="width: 350px;">Nama Customer</th>
                    <th class="text-center text-nowrap p-3" style="width: 150px;">Nama Bank</th>
                    <th class="text-center text-nowrap p-3" style="width: 250px;">No. Rekening</th>
                    <th class="text-center text-nowrap p-3" style="width: 350px;">Atas Nama</th>
                    <th class="text-center text-nowrap p-3" style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php
                while($data_bank = mysqli_fetch_array($query_bank)) {
                    $id_bank_cs = $data_bank['id_bank_cs'];
                    ?>
                    <tr>
                        <td class="text-nowrap text-center"><?php echo $no; ?></td>
                        <td class="text-nowrap"><?php echo $data_bank['nama_cs']; ?></td>
                        <td class="text-nowrap text-center"><?php echo $data_bank['nama_bank']; ?></td>
                        <td class="text-nowrap text-center"><?php echo $data_bank['no_rekening']; ?></td>
                        <td class="text-nowrap"><?php echo $data_bank['atas_nama']; ?></td>
                        <td class="text-nowrap text-center">
                            <button type="button" id="pilih" class="btn btn-primary btn-sm" data-id="<?php echo $id_bank_cs; ?>" data-id-bank="<?php echo $data_bank['id_bank']; ?>" data-bank="<?php echo $data_bank['nama_bank']; ?>" data-rek="<?php echo $data_bank['no_rekening']; ?>" data-an="<?php echo $data_bank['atas_nama']; ?>">
                                Pilih
                            </button>
                        </td>
                    </tr>
                    <?php
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>