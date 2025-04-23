<?php
    require_once "../akses.php";
    require_once "../function/function-enkripsi.php";
    if (isset($_POST['id_status_kirim'])) {
        $id_status_kirim = htmlspecialchars($_POST['id_status_kirim']);
        $id_status_kirim_decrypt = decrypt($id_status_kirim, $key_spk);
        
        $no = 1;
        // Query untuk mengambil data
        $sql = "SELECT 
                    id_status_kirim_revisi,
                    id_komplain,
                    no_resi,
                    ongkir
                FROM revisi_status_kirim
                WHERE id_status_kirim_revisi = '$id_status_kirim_decrypt'";
        // Eksekusi query
        $query = $connect->query($sql);
        $data = mysqli_fetch_array($query);
        $no_resi = $data['no_resi'];
        $ongkir = $data['ongkir'];
        $id_status_kirim_revisi = encrypt($data['id_status_kirim_revisi'], $key_spk);
        $id_komplain = encrypt($data['id_komplain'], $key_spk);

        ?>
            <input type="hidden" class="form-control" name="id_status_kirim_revisi" value="<?php echo $id_status_kirim_revisi ?>" required>
            <input type="hidden" class="form-control" name="id_komplain" value="<?php echo $id_komplain ?>" required>
            <div class="mb-3">
                <label for="">No. Resi</label>
                <input type="text" class="form-control" name="no_resi" value="<?php echo $no_resi ?>" required>
            </div>
            <div class="mb-3">
                <label for="">Ongkir</label>
                <input type="text" class="form-control" name="ongkir" id="ongkir_edit" value="<?php echo number_format($ongkir,0,'.','.'); ?>" maxlength="12" required>
            </div>
        <?php
        
    }    
?>

<script>
    $(document).ready(function() {
        const ongkir = $('#ongkir_edit');

        ongkir.on('input', function() {
            formatNumber(this); // Kirim elemen input yang benar
        });

        function formatNumber(input) {
            var value = $(input).val().replace(/\D/g, ''); // Menghapus karakter non-digit
            value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Menambahkan pemisah ribuan dengan titik
            $(input).val(value); // Set nilai yang diformat kembali ke input
        }
    });
</script>