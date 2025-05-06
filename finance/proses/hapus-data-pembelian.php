<?php  
    require_once "../../akses.php";
    
    if(isset($_POST['hapus'])){
        $id_inv = htmlspecialchars($_POST['id_inv']);
        $id_inv_decrypt = decrypt($id_inv, $key_finance);
        $status_delete = 1;
        $user = $_SESSION['tiket_id'];
        $updated_date = date("d/m/Y, H:i:s");

        // Update finance
        $stmt = $connect->prepare("UPDATE inv_pembelian_lokal SET status_delete = ?, deleted_date = ?, deleted_by = ? WHERE id_inv_pembelian = ?");
        $stmt->bind_param("isss", $status_delete, $updated_date, $user, $id_inv_decrypt);
        $update_finance = $stmt->execute();

        if($update_finance){
            $_SESSION['info'] = "Dihapus";
            header("Location:../data-pembelian.php?date_range=year");
        }
    }

?>