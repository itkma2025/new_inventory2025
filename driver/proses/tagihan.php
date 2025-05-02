<?php
session_start();
include "../koneksi.php";
include "../page/resize-image.php";

if(isset($_POST['update-tagihan'])){
    $connect->begin_transaction();
    try{
        $img_uuid = img_uuid();
        $year = date('y');
        $day = date('d');
        $month = date('m');
        $id_tagihan = $_POST['id_tagihan'];
        $nama_penerima = $_POST['nama_penerima'];
        $tgl = $_POST['tgl'];
        $file1_name = $_FILES['fileku1']['name'];
        $file1_tmp = $_FILES['fileku1']['tmp_name'];
        $file1_destination = "../../gambar/bukti-tagihan/" . $file1_name;

         // Pindahkan file bukti terima ke lokasi tujuan
         move_uploaded_file($file1_tmp, $file1_destination);

         $new_file1_name = "Bukti_Terima". $year . "" . $month . "" . $img_uuid . "" . $day . ".jpg";
         if($file1_name != ''){
            // Kompres dan ubah ukuran gambar bukti terima 1
            $compressed_file1_destination = "../../gambar/bukti-tagihan/$new_file1_name";
            compressAndResizeImage($file1_destination, $compressed_file1_destination, 500, 500, 100);
            unlink($file1_destination);

            $update_tagihan = mysqli_query($connect, "UPDATE finance_tagihan SET nama_penerima = ' $nama_penerima', tgl_terima = '$tgl', bukti_terima = '$new_file1_name' WHERE id_tagihan = '$id_tagihan'");
            if ($update_tagihan) {
                // Commit transaksi
                $connect->commit();
                ?>
                <!-- Sweet Alert -->
                <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
                <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Sukses",
                            text: "Data berhasil disimpan",
                            icon: "success",
                        }).then(function() {
                            window.location.href = "../list-tagihan.php";
                        });
                    });
                </script>
                <?php
            }
        }
    }catch (Exception $e){
        // Rollback transaksi jika terjadi exception
        $connect->rollback();
        $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();
        ?>
        <!-- Sweet Alert -->
        <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
        <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Error!",
                text: "<?php echo $error_message; ?>",
                icon: "error",
            }).then(function() {
                window.location.href = "../list-tagihan.php";
            });
            });
        </script>
        <?php
    }
   

}

function img_uuid() {
    $data = openssl_random_pseudo_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s', str_split(bin2hex($data), 4));
}
?>