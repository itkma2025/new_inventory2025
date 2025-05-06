<?php  
    require_once "../../akses.php";
    $key = "Fin@nce2024?";

    if(isset($_POST['hapus_refund'])){
        $id_refund = htmlspecialchars($_POST['id_refund']);
        $id_refund_decrypt = decrypt($id_refund, $key);
        $id_finance = htmlspecialchars($_POST['id_finance']);
        $id_finance_decrypt = decrypt($id_finance, $key);

        echo "Decrypted ID Finance: " . $id_finance_decrypt . "<br>";
        echo "Decrypted ID Refund: " . $id_refund_decrypt . "<br>";

        // Mulai transaksi
        mysqli_begin_transaction($connect);

        try {
           // Hapus data refund
           $stmt = $connect->prepare("DELETE FROM finance_bayar_refund WHERE id_refund = ?");
           $stmt->bind_param("s", $id_refund_decrypt);
           $stmt_delete_refund = $stmt->execute();

           // Hapus bukti refund
           $stmt = $connect->prepare("DELETE FROM finance_bukti_tf_refund WHERE id_refund = ?");
           $stmt->bind_param("s", $id_refund_decrypt);
           $stmt_delete_bukti_refund = $stmt->execute();
          
           // Update status refund
           $stmt = $connect->prepare("UPDATE finance_refund SET status_refund = '3' WHERE id_refund = ?");
           $stmt->bind_param("s", $id_refund_decrypt);
           $stmt_update_status_refund = $stmt->execute();
           
           // Update finance
           $stmt = $connect->prepare("UPDATE finance SET id_tagihan = '' WHERE id_finance = ?");
           $stmt->bind_param("s", $id_finance_decrypt);
           $stmt_update_finance = $stmt->execute();

           // Hapus Finance Bayar
           $stmt = $connect->prepare("DELETE FROM finance_bayar WHERE id_finance = ?");
           $stmt->bind_param("s", $id_finance_decrypt);
           $hapus_finance_bayar = $stmt->execute();

           // Hapus Bukti Tf
           $stmt = $connect->prepare("DELETE FROM finance_bukti_tf WHERE id_finance = ?");
           $stmt->bind_param("s", $id_finance_decrypt);
           $hapus_bukti_tf = $stmt->execute();

           // Jika semua query berhasil
           if ($stmt_delete_refund && $stmt_delete_bukti_refund && $stmt_update_status_refund && $stmt_update_finance && $hapus_finance_bayar && $hapus_bukti_tf) { 
               // Ambil data bukti refund untuk dihapus
               $stmt_tampil_data_bukti = $connect->prepare("SELECT bukti_tf, path FROM finance_bukti_tf_refund WHERE id_refund = ?");
               $stmt_tampil_data_bukti->bind_param("s", $id_refund_decrypt);
               $stmt_tampil_data_bukti->execute();
               $result = $stmt_tampil_data_bukti->get_result();

                while($data_bukti = $result->fetch_assoc()){
                   $path = $data_bukti['path'] . $data_bukti['bukti_tf'];
                   unlink($path);
                }
               // Jika tidak terjadi kesalahan, commit transaksi
               mysqli_commit($connect);
               $_SESSION['info'] = "Disimpan";
               header("Location:../list-refund-dana.php?date_range=year");
               exit();
           } else {
               throw new Exception("Error updating data");
           }
        } catch (Exception $e) {
            // Jika terjadi kesalahan, rollback transaksi
            mysqli_rollback($connect);
            // $error_message = "Gagal saat proses data: " . $e->getMessage();
             // echo $error_message;
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../list-refund-dana.php?date_range=year");
            exit();
        }
    }
?>
