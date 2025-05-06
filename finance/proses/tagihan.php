<?php  
    require_once "../../akses.php";

    if(isset($_POST['update_driver'])){
        $id_user = $_POST['id_user'];
        $tgl = date('d/m/Y');
        $id_tagihan = $_POST['id_tagihan'];
        $id_tagihan_encode = base64_encode($id_tagihan);

        $update_data = mysqli_query($connect, "UPDATE finance_tagihan SET id_driver = '$id_user', tgl_kirim = '$tgl' WHERE id_tagihan = '$id_tagihan'");

        if($update_data){
            ?>
            <!-- Sweet Alert -->
            <link rel="stylesheet" href="../assets/sweet-alert/dist/sweetalert2.min.css">
            <script src="../assets/sweet-alert/dist/sweetalert2.all.min.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Success",
                    text: "Data berhasil disimpan",
                    icon: "success",
                }).then(function() {
                    window.location.href = "../detail-bill.php?id=<?php echo $id_tagihan_encode ?>";
                });
                });
            </script>
            <?php
        }
    }

?>