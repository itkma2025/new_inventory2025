<?php
    require_once "../akses.php";
    require_once "../function/function-enkripsi.php";
    if (isset($_POST['id_spk'])) {
        $id_spk = $_POST['id_spk'];
        $id_spk_decrypt = decrypt($id_spk, $key_spk);
        
        // Query tanpa prepared statement
        $query = "SELECT id_transaksi FROM transaksi_produk_reg WHERE id_spk = '$id_spk_decrypt'";
        $result = $connect->query($query);

        if ($result->num_rows > 0) {
            // Ambil data dan tampilkan
            while ($row = $result->fetch_assoc()) {
                $id_trx =  $row['id_transaksi'];

                // Query kedua tanpa prepared statement
                $query_temp = "SELECT id_transaksi, status_input, keterangan_ks FROM tmp_kartu_stock WHERE id_transaksi = '$id_trx'";
                $result_temp = $connect->query($query_temp);

                while ($row_temp = $result_temp->fetch_assoc()) {
                    $id_trx_temp = $row_temp['id_transaksi'];
                    $status_input = $row_temp['status_input'];
                    $ket_ks = $row_temp['keterangan_ks'];

                    if ($status_input == 0 && $ket_ks == 0) {
                        $query_update = "UPDATE transaksi_produk_reg SET status_ks = ? WHERE id_transaksi = ?";
                        $stmt_update = $connect->prepare($query_update);
                        $status_ks = 0;
                        $stmt_update->bind_param("is", $status_ks, $id_trx_temp);
                    } else if ($status_input == 1 && $ket_ks == 0) {
                        $query_update = "UPDATE transaksi_produk_reg SET status_ks = ? WHERE id_transaksi = ?";
                        $stmt_update = $connect->prepare($query_update);
                        $status_ks = 1;
                        $stmt_update->bind_param("is", $status_ks, $id_trx_temp);
                    } else if ($status_input == 1 && $ket_ks == 1) {
                        $query_update = "UPDATE transaksi_produk_reg SET status_ks = ? WHERE id_transaksi = ?";
                        $stmt_update = $connect->prepare($query_update);
                        $status_ks = 2;
                        $stmt_update->bind_param("is", $status_ks, $id_trx_temp);
                    }
                    
                    // Eksekusi query update
                    $stmt_update->execute();
                    $stmt_update->close();
                    
                }
            }
        } else {
            echo "Data tidak ditemukan."; // Letakkan else di sini, di dalam blok if
        }

        // Tutup koneksi
        $connect->close();
    } else {
        echo json_encode(['error' => 'ID SPK tidak ditemukan.']);
    }
?>
