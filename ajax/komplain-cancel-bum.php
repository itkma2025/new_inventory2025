<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start(); // Menangguhkan output

include '../akses.php'; // Include koneksi database
require_once "../function/function-enkripsi.php";

$response = [];

if (isset($_POST['id_inv'])) {
    // Terima data id yang dikirimkan
    $id_inv_encrypted = $_POST['id_inv'];
    
    // Dekripsi id sebelum digunakan
    $id_inv = decrypt($id_inv_encrypted, $key_spk);

    // Simpan $id_inv ke dalam file post_ajax.txt
    $file = 'post_ajax.txt'; // Pastikan file ini memiliki izin tulis
    file_put_contents($file, $id_inv . PHP_EOL, FILE_APPEND);

    try {
        // Mulai transaksi
        $connect->begin_transaction();

        // Query UPDATE untuk membatalkan order
        $stmt = $connect->prepare("UPDATE inv_bum SET status_transaksi = 'Cancel Order' WHERE id_inv_bum = ?");
        if (!$stmt) {
            throw new Exception("Error prepare statement (UPDATE): " . $connect->error);
        }
        $stmt->bind_param('s', $id_inv);
        if (!$stmt->execute()) {
            throw new Exception("Error execute statement (UPDATE): " . $stmt->error);
        }

        // Query DELETE untuk menghapus dari finance
        $stmt = $connect->prepare("DELETE FROM finance WHERE id_inv = ?");
        if (!$stmt) {
            throw new Exception("Error prepare statement (DELETE): " . $connect->error);
        }
        $stmt->bind_param('s', $id_inv);
        if (!$stmt->execute()) {
            throw new Exception("Error execute statement (DELETE): " . $stmt->error);
        }

        // Jika semua berhasil, commit transaksi
        $connect->commit();
        
        // Mengembalikan respons sukses
        $response = ['success' => true, 'message' => 'Order berhasil dibatalkan'];

    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $connect->rollback();
        
        // Menangani error
        $response = ['success' => false, 'message' => 'Gagal membatalkan order: ' . $e->getMessage()];
    } finally {
        // Tutup statement dan koneksi
        if (isset($stmt)) {
            $stmt->close();
        }
        $connect->close();
    }

} else {
    // ID tidak ditemukan dalam permintaan
    $response = ['success' => false, 'message' => 'ID tidak ditemukan dalam permintaan'];
}

// Mengirimkan respon dalam format JSON
echo json_encode($response);

// Flush the output buffer and send the output
ob_end_flush();
?>
