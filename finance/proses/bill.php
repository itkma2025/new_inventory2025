<?php  
require_once "../../akses.php";

if(isset($_POST['simpan-bill'])){
    $id_inv = $_POST['id_inv'];
    $id_tagihan = htmlspecialchars($_POST['id_tagihan']);;
    $total_tagihan = str_replace('.', '', $_POST['total_tagihan']); // Menghapus tanda ribuan (,)
    $total_tagihan = intval($total_tagihan); // Mengubah string harga menjadi integer
    $no_tagihan = mysqli_real_escape_string($connect, $_POST['no_tagihan']);
    $tgl_tagihan = mysqli_real_escape_string($connect, $_POST['tgl_tagihan']);
    $cs_tagihan = mysqli_real_escape_string($connect, $_POST['cs']);
    $jenis_faktur = mysqli_real_escape_string($connect, $_POST['jenis_faktur']);

    
    foreach($id_inv as $id_inv_array){
        $id_inv_escape[] = mysqli_real_escape_string($connect, $id_inv_array);
    }
    // Begin transaction
    mysqli_begin_transaction($connect);

    try{
       
        $id_inv_count = count($id_inv_escape);
        for ($i = 0; $i < $id_inv_count; $i++){

            $sql_tagihan = mysqli_query($connect, "INSERT IGNORE INTO finance_tagihan (id_tagihan, no_tagihan, tgl_tagihan, cs_tagihan, jenis_faktur, total_tagihan) VALUES ('$id_tagihan','$no_tagihan', '$tgl_tagihan', '$cs_tagihan', '$jenis_faktur', '$total_tagihan')");

            $id_inv_array = $id_inv_escape[$i];
            $formattedInvIds = implode("', '", (array)$id_inv_array);

            // Tambahkan tanda kutip pada awal dan akhir string
            $formattedInvIds = "'" . $formattedInvIds . "'";

            // Gantikan koma dengan koma dan spasi
            $formattedInvIds = str_replace(",", "', '", $formattedInvIds);

            // Lakukan sesuatu dengan data yang dipilih yang telah digabungkan
            // echo $formattedInvIds;

            $sql_finance = mysqli_query($connect, "UPDATE finance SET id_tagihan = '$id_tagihan', status_tagihan = 1  WHERE id_inv IN($formattedInvIds)");
            if (!$sql_finance && !$sql_tagihan) {
                throw new Exception("Error updating data");
            }
        }
        // Commit the transaction
        mysqli_commit($connect);
        // $_SESSION['info'] = 'Disimpan';
        $_SESSION['info'] = 'No tagihan berhasil dibuat';
        // Redirect to the invoice page
        header("Location:../finance-inv.php?date_range=monthly");
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        mysqli_rollback($connect);
        // Handle the error (e.g., display an error message)
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
                    window.location.href = "../finance-inv.php?date_range=monthly";
                });
                });
            </script>
            <?php
    } 
} else if(isset($_POST['ubah-jenis-faktur'])){
    $id_bill = $_POST['id_bill'];
    $id_bill_decrypt = decrypt($id_bill, $key_finance);
    $jenis_faktur = htmlspecialchars($_POST['jenis_faktur']);
    $cs_tagihan = htmlspecialchars($_POST['cs']);
    $tgl_tagihan = htmlspecialchars($_POST['tgl']);
    $update = mysqli_query($connect, "UPDATE finance_tagihan SET tgl_tagihan = '$tgl_tagihan', cs_tagihan = '$cs_tagihan', jenis_faktur = '$jenis_faktur' WHERE id_tagihan = '$id_bill_decrypt'");
    if($update){
        $_SESSION['info'] = 'Diupdate';
        // Redirect to the invoice page
        header("Location:../detail-bill.php?id=$id_bill");
    } else {
        $_SESSION['info'] = 'Data Gagal Diupdate';
        // Redirect to the invoice page
        header("Location:../detail-bill.php?id=$id_bill");
    }
}
?>