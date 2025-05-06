<?php  
    require_once "../akses.php";
    require_once "../function/function-enkripsi.php";

    if(isset($_POST['ubah-data-ks'])){
        $id_trx = htmlspecialchars(decrypt($_POST['id_trx'], $key_gudang));
        $qty =  htmlspecialchars($_POST['qty']);

        // Melakukan pengecekan untuk menentukan keterangan KS
        $cek_spk = $connect->query("SELECT id_spk, id_produk, qty FROM transaksi_produk_reg WHERE id_transaksi = '$id_trx'");
        $data_spk = mysqli_fetch_array($cek_spk);
        $trx_spk_id = $data_spk['id_spk'];
        $trx_id_produk = $data_spk['id_produk'];
        $trx_qty = $data_spk['qty'];


        $keterangan_ks = "";
        if($qty == $trx_qty){
            $keterangan_ks = "1";
        } else {
            $keterangan_ks = "0";
        }

        $stmt = $connect->prepare("UPDATE tmp_kartu_stock SET qty_ks = ?, keterangan_ks = ? WHERE id_transaksi = ?");
        $stmt->bind_param("iis", $qty, $keterangan_ks, $id_trx);
        $update_tmp = $stmt->execute();

        if($update_tmp){
            $_SESSION['info'] = 'Disimpan';
            header("Location:../history-input-stock.php");
            exit();
        } else {
            $_SESSION['info'] = 'Data Gagal Disimpan';
            header("Location:../history-input-stock.php");
            exit();
        }
    }   
?>