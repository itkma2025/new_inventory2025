<?php  
    require_once "../../akses.php";
    require_once "../../function/uuid.php";
    $key = "K@rsa2024?";
    if(isset($_POST['komplain'])){
        $id_inv = htmlspecialchars(decrypt($_POST['id_inv'], $key));
        $id_komplain = htmlspecialchars(decrypt($_POST['id_komplain'], $key)); 
        $id_kondisi = htmlspecialchars(decrypt($_POST['id_kondisi'], $key));
        $kat_komplain = htmlspecialchars($_POST['kat_komplain']);
        $kondisi_pesanan = htmlspecialchars($_POST['kondisi_pesanan']);
        $retur = htmlspecialchars($_POST['retur']);
        $refund = htmlspecialchars(!empty($_POST['refund']) ? $_POST['refund'] : '');
        $catatan = htmlspecialchars($_POST['catatan']);
        $tgl = htmlspecialchars($_POST['tgl']);
        $year_komplain = date('Y');
        $sql  = mysqli_query($connect, "SELECT max(no_komplain) as maxID, STR_TO_DATE(tgl_komplain, '%d/%m/%Y') AS tgl FROM inv_komplain WHERE YEAR(STR_TO_DATE(tgl_komplain, '%d/%m/%Y')) = '$year_komplain'");
        $data = mysqli_fetch_array($sql);
        $array_bln = array(1 => "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX", "X", "XI", "XII");
        $kode = $data['maxID'];
        $ket1 = "/CC/KMA/";
        $bln = $array_bln[date('n')];
        $ket2 = "/";
        $ket3 = date("Y");
        $urutkan = (int)substr($kode, 0, 3);
        $urutkan++;
        $no_komplain = sprintf("%03s", $urutkan) . $ket1 . $bln . $ket2 . $ket3;

        // Menampilkan data refund
        $sql_refund = $connect->query("SELECT id_tagihan FROM finance WHERE id_inv = '$id_inv'");
        $data_refund = mysqli_fetch_array($sql_refund);
        $id_refund = $data_refund['id_tagihan'];

        $id_inv_substr = substr($id_inv, 0, 3); // Mengambil 3 karakter pertama
        if($id_inv_substr == "NON"){
            $connect->begin_transaction();
            try{
                // Hapus data pada bagian finance
                $del_finance = $connect->query("DELETE FROM finance WHERE id_inv = '$id_inv'");
               
                // Hapus data pada tabel history_produk_terjual
                $del_history_produk = $connect->query("DELETE FROM history_produk_terjual WHERE id_inv = '$id_inv'");
                   
                // Update status transaksi pada inv_nonppn
                $query_update_inv = $connect->query("UPDATE inv_nonppn SET status_transaksi = 'Komplain' WHERE id_inv_nonppn = '$id_inv'");
                       
                // Insert data ke tabel inv_komplain
                $query_komplain = $connect->query("INSERT INTO inv_komplain (id_komplain, id_inv, no_komplain, tgl_komplain) VALUES ('$id_komplain', '$id_inv', '$no_komplain', '$tgl')");
                         
                // Insert data ke tabel komplain_kondisi
                $query_kondisi_komplain = $connect->query("INSERT INTO komplain_kondisi (id_kondisi, id_komplain, kat_komplain, kondisi_pesanan, status_retur, status_refund, catatan) VALUES ('$id_kondisi', '$id_komplain', '$kat_komplain', '$kondisi_pesanan', '$retur', '$refund', '$catatan')");
                             
                // Hapus refund 
                $delete_refund = $connect->query("DELETE FROM finance_refund WHERE id_refund = '$id_refund'");
                                   
                // Insert data ke tabel tmp_produk_komplain
                $query_tmp_ref = $connect->query("INSERT IGNORE INTO tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp, created_date) SELECT tpr.id_transaksi, spk.id_inv, tpr.id_produk, tpr.nama_produk_spk, tpr.harga, tpr.qty, tpr.disc, tpr.total_harga, 1 as status_tmp, tpr.created_date FROM spk_reg AS spk LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk WHERE spk.id_inv = '$id_inv'");

              // Cek proses
              if (!$del_finance && !$del_history_produk && !$query_update_inv && !$query_komplain && !$query_kondisi_komplain && !$delete_refund && !$query_tmp_ref) {
                    throw new Exception();
                } else {
                    
                    // Semua operasi berhasil, commit transaksi
                    $connect->commit();
                    $_SESSION['info'] = "Disimpan";
                    header("Location:../list-refund-dana.php?date_range=year");
                    exit; // Pastikan script berhenti setelah redirect
                }              
           } catch (Exception $e) {
                // Rollback transaksi jika terjadi exception
                $connect->rollback();
                // Jika ingin cek pesan error tampilkan
                //  $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();

                $error_message = "Terjadi masalah pada server";
                header("Location:../list-refund-dana.php?date_range=year");
                $_SESSION['gagal'] = $error_message;
                exit; // Pastikan script berhenti setelah redirect
               
           }
        } else if($id_inv_substr == "PPN"){
            $connect->begin_transaction();
            try{
                // Hapus data pada bagian finance
                $del_finance = mysqli_query($connect, "DELETE FROM finance WHERE id_inv = '$id_inv'");
               
                // Hapus data pada tabel history_produk_terjual
                $del_history_produk = mysqli_query($connect, "DELETE FROM history_produk_terjual WHERE id_inv = '$id_inv'");
                  
                // Update status transaksi pada inv_ppn
                $query_update_inv = mysqli_query($connect, "UPDATE inv_ppn SET status_transaksi = 'Komplain' WHERE id_inv_ppn = '$id_inv'");

                // Insert data ke tabel inv_komplain
                $query_komplain = mysqli_query($connect, "INSERT INTO inv_komplain (id_komplain, id_inv, no_komplain, tgl_komplain) VALUES ('$id_komplain', '$id_inv', '$no_komplain', '$tgl')");
                            
                // Insert data ke tabel komplain_kondisi
                $query_kondisi_komplain = mysqli_query($connect, "INSERT INTO komplain_kondisi (id_kondisi, id_komplain, kat_komplain, kondisi_pesanan, status_retur, status_refund, catatan) VALUES ('$id_kondisi', '$id_komplain', '$kat_komplain', '$kondisi_pesanan', '$retur', '$refund', '$catatan')");

                // Delete refund
                $delete_refund = $connect->query("DELETE FROM finance_refund WHERE id_refund = '$id_refund'");
                                   
                // Insert data ke tabel tmp_produk_komplain
                $query_tmp_ref = mysqli_query($connect, "INSERT IGNORE INTO tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp, created_date) SELECT tpr.id_transaksi, spk.id_inv, tpr.id_produk, tpr.nama_produk_spk, tpr.harga, tpr.qty, tpr.disc, tpr.total_harga, 1 as status_tmp, tpr.created_date FROM spk_reg AS spk LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk WHERE spk.id_inv = '$id_inv'");

                // Cek proses
                if (!$del_finance && !$del_history_produk && !$query_update_inv && !$query_komplain && !$query_kondisi_komplain && !$delete_refund && !$query_tmp_ref) {
                    throw new Exception();
                } else {
                    // Semua operasi berhasil, commit transaksi
                    $connect->commit();
                    $_SESSION['info'] = "Disimpan";
                    header("Location:../list-refund-dana.php?date_range=year");
                    exit; // Pastikan script berhenti setelah redirect
                }
           } catch (Exception $e) {
                // Rollback transaksi jika terjadi exception
                $connect->rollback();
                // Jika ingin cek pesan error tampilkan
                //  $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();

                $error_message = "Terjadi masalah pada server";
                header("Location:../list-refund-dana.php?date_range=year");
                $_SESSION['gagal'] = $error_message;
                exit; // Pastikan script berhenti setelah redirect
           }
        } else if($id_inv_substr == "BUM"){
            $connect->begin_transaction();
            $proses = "";
            try {
                // Hapus data pada bagian finance
                $del_finance = mysqli_query($connect, "DELETE FROM finance WHERE id_inv = '$id_inv'");
            
                // Hapus data pada tabel history_produk_terjual
                $del_history_produk = mysqli_query($connect, "DELETE FROM history_produk_terjual WHERE id_inv = '$id_inv'");
            
                // Update status transaksi pada inv_bum
                $query_update_inv = mysqli_query($connect, "UPDATE inv_bum SET status_transaksi = 'Komplain' WHERE id_inv_bum = '$id_inv'");
            
                // Insert data ke tabel inv_komplain
                $query_komplain = mysqli_query($connect, "INSERT INTO inv_komplain (id_komplain, id_inv, no_komplain, tgl_komplain) VALUES ('$id_komplain', '$id_inv', '$no_komplain', '$tgl')");
            
                // Insert data ke tabel komplain_kondisi
                $query_kondisi_komplain = mysqli_query($connect, "INSERT INTO komplain_kondisi (id_kondisi, id_komplain, kat_komplain, kondisi_pesanan, status_retur, status_refund, catatan) VALUES ('$id_kondisi', '$id_komplain', '$kat_komplain', '$kondisi_pesanan', '$retur', '$refund', '$catatan')");
            
                $delete_refund = $connect->query("DELETE FROM finance_refund WHERE id_refund = '$id_refund'");
            
                // Insert data ke tabel tmp_produk_komplain
                $query_tmp_ref = mysqli_query($connect, "INSERT IGNORE INTO tmp_produk_komplain (id_tmp, id_inv, id_produk, nama_produk, harga, qty, disc, total_harga, status_tmp, created_date) SELECT tpr.id_transaksi, spk.id_inv, tpr.id_produk, tpr.nama_produk_spk, tpr.harga, tpr.qty, tpr.disc, tpr.total_harga, 1 as status_tmp, tpr.created_date FROM spk_reg AS spk LEFT JOIN transaksi_produk_reg tpr ON spk.id_spk_reg = tpr.id_spk WHERE spk.id_inv = '$id_inv'");
                
                // Cek Proses
                if (!$del_finance && !$del_history_produk && !$query_update_inv && !$query_komplain && !$query_kondisi_komplain && !$delete_refund && !$query_tmp_ref) {
                    throw new Exception();
                } else {
                    // Semua operasi berhasil, commit transaksi
                    $connect->commit();
                    $_SESSION['info'] = "Disimpan";
                    header("Location:../list-refund-dana.php?date_range=year");
                    exit; // Pastikan script berhenti setelah redirect
                }
            } catch (Exception $e) {
                // Rollback transaksi jika terjadi exception
                $connect->rollback();
                // Jika ingin cek pesan error tampilkan
                //  $error_message = "Terjadi kesalahan saat melakukan transaksi: " . $e->getMessage();

                $error_message = "Terjadi masalah pada server";
                header("Location:../list-refund-dana.php?date_range=year");
                $_SESSION['gagal'] = $error_message;
                exit; // Pastikan script berhenti setelah redirect
            }
        }
    } else {
        header("Location:../list-refund-dana.php?date_range=year");
    }
?>