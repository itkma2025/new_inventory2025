<?php  
require_once "../../akses.php";
$nama_user =  $_SESSION['tiket_nama'];

// Kunci enkripsi
$key = 'pembelian2024';

if(isset($_POST['simpan-payment'])){
    $id_inv_pembelian =  mysqli_real_escape_string($connect, $_POST['id_inv']);
    $id_inv_pembelian_decrypt = decrypt($id_inv_pembelian, $key);
    $id_pembayaran = mysqli_real_escape_string($connect, $_POST['id_pembayaran']);
    $id_pembayaran_decrypt = decrypt($id_pembayaran, $key);
    $total_tagihan = mysqli_real_escape_string($connect, str_replace('.', '', $_POST['total_tagihan'])); // Menghapus tanda ribuan (,)
    $total_tagihan = intval($total_tagihan); // Mengubah string harga menjadi integer
    $no_pembayaran = mysqli_real_escape_string($connect, $_POST['no_pembayaran']);
    $tgl_pembayaran = mysqli_real_escape_string($connect, $_POST['tgl_pembayaran']);
    $jenis_faktur = mysqli_real_escape_string($connect, $_POST['jenis_faktur']);

    // Ubah format id invoice menjadi array agar bisa update multiple
    $id_inv_pembelian_formatted = implode("', '", (array)$id_inv_pembelian_decrypt);
 
    // Tambahkan tanda kutip pada awal dan akhir string
    $id_inv_pembelian_formatted = "'" . $id_inv_pembelian_formatted . "'";

    // Gantikan koma dengan koma dan spasi
    $id_inv_pembelian_formatted = str_replace(",", "', '", $id_inv_pembelian_formatted);

    // Sanitasi data untuk mencegah XSS
    $id_pembayaran_decrypt = htmlspecialchars($id_pembayaran_decrypt);
    $total_tagihan = htmlspecialchars($total_tagihan);
    $no_pembayaran = htmlspecialchars($no_pembayaran);
    $tgl_pembayaran = htmlspecialchars($tgl_pembayaran);
    $jenis_faktur = htmlspecialchars($jenis_faktur);

    // echo $id_inv_pembelian_formatted;
    
    // Begin transaction
    mysqli_begin_transaction($connect);

    try{
        // Simpan data ke finance pembayaran produk lokal
        $simpan_pembayaran = mysqli_query($connect, "INSERT IGNORE INTO finance_pembayaran_produk_lokal (id_pembayaran, no_pembayaran, tgl_pembayaran, jenis_faktur, total_tagihan, created_by) VALUES ('$id_pembayaran_decrypt', '$no_pembayaran', '$tgl_pembayaran', '$jenis_faktur', '$total_tagihan', '$nama_user')");

        //Update id pembayaran pada invoice pembelian lokal
          
        $update_inv_pembelian = mysqli_query($connect, "UPDATE inv_pembelian_lokal SET id_pembayaran = '$id_pembayaran_decrypt', status_pembayaran = 1  WHERE id_inv_pembelian IN($id_inv_pembelian_formatted)");
        if (!$simpan_pembayaran) {
            throw new Exception("Error updating data");
        }
        
        //Commit the transaction
        mysqli_commit($connect);
        $_SESSION['info'] = 'No pembayaran berhasil dibuat';
        //Redirect to the invoice page
        header("Location:../finance-inv-pembelian.php?date_range=year");
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
                        window.location.href = "../finance-inv-pembelian.php?date_range=year";
                    });
                    });
                </script>
            <?php
    } 
} else if(isset($_POST['ubah-jenis-faktur'])){
    $id_bill = $_POST['id_bill'];
    $id_bill_encode = base64_encode($id_bill);
    $jenis_faktur = htmlspecialchars($_POST['jenis_faktur']);
    $cs_tagihan = htmlspecialchars($_POST['cs']);
    $tgl_tagihan = htmlspecialchars($_POST['tgl']);
    $update = mysqli_query($connect, "UPDATE finance_tagihan SET tgl_tagihan = '$tgl_tagihan', cs_tagihan = '$cs_tagihan', jenis_faktur = '$jenis_faktur' WHERE id_tagihan = '$id_bill'");
    if($update){
        ?>
            <!-- Sweet Alert -->
            <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
            <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Success",
                    text: "Data berhasil diubah",
                    icon: "success",
                }).then(function() {
                    window.location.href = "../detail-bill.php?id=<?php echo $id_bill_encode ?>";
                });
                });
            </script>
        <?php
    }
}
?>