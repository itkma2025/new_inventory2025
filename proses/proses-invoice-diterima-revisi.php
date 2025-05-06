<!DOCTYPE html>
<html lang="en">

<body>
    <?php
    include "../akses.php";
    if (isset($_POST['diterima_ekspedisi'])) {
        $connect->begin_transaction();
        try {
            $uuid = generate_uuid();
            $year = date('y');
            $day = date('d');
            $month = date('m');
            $id_inv_penerima = "PNMR" . $year . "" . $month . "" . $uuid . "" . $day;
            $id_inv = $_POST['id_inv'];
            $jenis_inv = $_POST['jenis_inv'];
            $id_komplain_encode = $_POST['id_komplain'];
            $id_komplain = base64_decode($_POST['id_komplain']);
            $alamat = $_POST['alamat'];
            $nama_penerima = $_POST['nama_penerima'];
            $tgl = $_POST['tgl'];
            // Query 1
            $query_diterima = mysqli_query($connect, "INSERT INTO inv_penerima_revisi (id_inv_penerima_revisi, id_komplain, nama_penerima, alamat, tgl_terima) VALUES ('$id_inv_penerima', '$id_komplain', '$nama_penerima', '$alamat', '$tgl')");

            $query_update_inv = '';
            if ($jenis_inv == 'nonppn') {
                $query_update_inv = mysqli_query($connect, "UPDATE inv_nonppn SET status_transaksi = 'Komplain' WHERE id_inv_nonppn = '$id_inv'");
            } else if ($jenis_inv == 'ppn') {
                $query_update_inv = mysqli_query($connect, "UPDATE inv_ppn SET status_transaksi = 'Komplain' WHERE id_inv_ppn = '$id_inv'");
            } else if ($jenis_inv == 'bum') {
                $query_update_inv = mysqli_query($connect, "UPDATE inv_bum SET status_transaksi = 'Komplain' WHERE id_inv_bum = '$id_inv'");
            }

            $query_update_inv_komplain = mysqli_query($connect, "UPDATE inv_komplain SET status_komplain = '0' WHERE id_komplain = '$id_komplain'");

            $query_update_inv_revisi = mysqli_query($connect, "UPDATE inv_revisi SET status_pengiriman = '1',  status_trx_komplain = '0', status_trx_selesai = '0' WHERE id_inv = '$id_inv'");

            $query_update_revisi_status_kirim = mysqli_query($connect, "UPDATE revisi_status_kirim SET status_kirim = '1'  WHERE id_komplain = '$id_komplain'");

            if ($query_diterima && $query_update_inv && $query_update_inv_komplain && $query_update_inv_revisi && $query_update_revisi_status_kirim) {
                // Commit transaksi
                $connect->commit();
                if ($jenis_inv == 'nonppn') {
                    header("Location:../detail-komplain-revisi-nonppn.php?id=$id_komplain_encode");
                } else if ($jenis_inv == 'ppn') {
                    header("Location:../detail-komplain-revisi-ppn.php?id=$id_komplain_encode");
                } else if ($jenis_inv == 'bum') {
                    header("Location:../detail-komplain-revisi-bum.php?id=$id_komplain_encode");
                }
            }
        } catch (Exception $e) {
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
                        window.location.href = "../detail-komplain-revisi.php?id=$id_komplain_encode";
                    });
                });
            </script>
    <?php
        }
    }

    function generate_uuid()
    {
        $data = openssl_random_pseudo_bytes(16);
        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s%s', str_split(bin2hex($data), 4));
    }
    ?>
</body>

</html>