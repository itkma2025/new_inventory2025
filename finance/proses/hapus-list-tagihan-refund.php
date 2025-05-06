<?php  
require_once "../../akses.php";

if (isset($_POST['refund'])) {
    $id_refund = htmlspecialchars($_POST['id_refund']);
    $jenis = htmlspecialchars($_POST['jenis']);
    $no_refund = htmlspecialchars($_POST['no_refund']);
    $cs_tagihan = htmlspecialchars($_POST['cs_tagihan']);
    $id_finance = htmlspecialchars(decrypt($_POST['id_finance'], $key_finance));
    $id_inv = htmlspecialchars(decrypt($_POST['id_inv'], $key_finance));
    $id_bill = htmlspecialchars($_POST['id_bill']);
    $alasan = htmlspecialchars($_POST['alasan']);
    $total_tagihan = htmlspecialchars($_POST['total_tagihan']);
    $total_inv = htmlspecialchars($_POST['total_inv']);
    $total_akhir = $total_tagihan - $total_inv;
    $created_by = base64_decode($_SESSION['tiket_id']);

    // Validasi nonce
    if (!isset($_POST['nonce']) || $_POST['nonce'] !== $_SESSION['nonce']) {
        $_SESSION['info'] = "Terjadi Kesalahan Pada Server";
        header("Location:../detail-bill.php?id='$id_bill'");
        exit();
    }

    // Nonce valid, hapus nonce dari sesi untuk mencegah penggunaan ulang
    unset($_SESSION['nonce']);

    if($jenis == "pengembalian_dana"){
        // Mulai transaksi
        mysqli_begin_transaction($connect);
        try {
            // Simpan data refund
            $stmt = $connect->prepare("INSERT INTO finance_refund 
                                        (id_refund, id_inv, no_refund, cs_refund, total_inv, alasan_refund, created_by) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssdss", $id_refund, $id_inv, $no_refund, $cs_tagihan, $total_inv, $alasan, $created_by);
            $insert_finance_refund = $stmt->execute();

            // Update finance
            $stmt = $connect->prepare("UPDATE finance SET id_tagihan = ?, status_tagihan = '0' WHERE id_finance = ?");
            $stmt->bind_param("ss", $id_refund, $id_finance);
            $update_finance = $stmt->execute();

            // Update data pembayaran
            $stmt = $connect->prepare("UPDATE finance_bayar SET id_tagihan = ? WHERE id_finance = ?");
            $stmt->bind_param("ss", $id_refund, $id_finance);
            $update_finance_bayar = $stmt->execute();

            if ($insert_finance_refund && $update_finance && $update_finance_bayar) {
                // Jika tidak terjadi kesalahan, commit transaksi
                mysqli_commit($connect);
                $_SESSION['info'] = "Disimpan";
                header("Location:../detail-bill.php?id='$id_bill'");
                exit();
            } else {
                // Jika terjadi kesalahan, rollback transaksi
                mysqli_rollback($connect);
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../detail-bill.php?id='$id_bill'");
                exit();
            }
        } catch (Exception $e) {
            // Jika terjadi kesalahan, rollback transaksi
            mysqli_rollback($connect);
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../detail-bill.php?id='$id_bill'");
            exit();
        }
    } else if($jenis == "hapus_list"){
        $id_finance = mysqli_real_escape_string($connect, $id_finance); // Hindari SQL Injection
        // Mulai transaksi
        mysqli_begin_transaction($connect);
        try {
            // Query untuk mendapatkan path file dari database
            $cek_path = $connect->query("SELECT CONCAT(path, bukti_tf) AS full_path FROM finance_bukti_tf WHERE id_finance = '$id_finance'");
            
            // Update finance
            $stmt = $connect->prepare("UPDATE finance SET id_tagihan = '', status_tagihan = '0' WHERE id_finance = ?");
            $stmt->bind_param("s", $id_finance);
            $update_finance = $stmt->execute();

            // Hapus Finance Bayar
            $stmt = $connect->prepare("DELETE FROM finance_bayar WHERE id_finance = ?");
            $stmt->bind_param("s", $id_finance);
            $hapus_finance_bayar = $stmt->execute();

            // Hapus Bukti Tf
            $stmt = $connect->prepare("DELETE FROM finance_bukti_tf WHERE id_finance = ?");
            $stmt->bind_param("s", $id_finance);
            $hapus_bukti_tf = $stmt->execute();

            if ($update_finance && $hapus_finance_bayar && $hapus_bukti_tf) {
                // Hapus file-file yang terkait
                while ($row = $cek_path->fetch_assoc()) {
                    $full_path = $row['full_path'];
                    // Hapus file menggunakan unlink
                    if (file_exists($full_path)) {
                        if (unlink($full_path)) {
                            echo "File $full_path berhasil dihapus.<br>";
                        } else {
                            echo "Gagal menghapus file $full_path.<br>";
                        }
                    } else {
                        echo "File $full_path tidak ditemukan.<br>";
                    }
                }

                // Jika tidak terjadi kesalahan, commit transaksi
                mysqli_commit($connect);
                $_SESSION['info'] = "Disimpan";
                header("Location:../detail-bill.php?id='$id_bill'");
                exit();
            } else {
                // Jika terjadi kesalahan pada salah satu operasi, rollback transaksi
                mysqli_rollback($connect);
                $_SESSION['info'] = "Data Gagal Disimpan";
                header("Location:../detail-bill.php?id='$id_bill'");
                exit();
            }
        } catch (Exception $e) {
            // Jika terjadi kesalahan, rollback transaksi
            mysqli_rollback($connect);
            $_SESSION['info'] = "Data Gagal Disimpan";
            header("Location:../detail-bill.php?id='$id_bill'");
            exit();
        } finally {
            $connect->close();
        }
    } else {
        header("Location:../logout.php");
        exit();
    }
} else {
    header("Location:../logout.php");
    exit();
}
?>
