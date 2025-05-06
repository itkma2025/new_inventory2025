<?php
include "../akses.php";

$id_user = decrypt($_SESSION['tiket_id'], $key_global);

// Penghubung Library
require_once '../assets/vendor/autoload.php';
// Library Tangal
use Carbon\Carbon;
$datetime_now = Carbon::now();

// Library Debugging
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
// Inisialisasi Whoops
// Atur status aktif/non-aktif Whoops
$whoops_enabled = false; // Ubah menjadi false untuk menonaktifkan

if ($whoops_enabled) {
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();
}
require_once "../function/uuid.php";
// Library sanitasi input data
require_once "../function/sanitasi_input.php";
$sanitasi_post = sanitizeInput($_POST);
$sanitasi_get = sanitizeInput($_GET);

if (isset($sanitasi_post['simpan-sph'])) {
    $uuid = uuid();
    $day = date('d');
    $month = date('m');
    $year = date('y');
    $id_sph = "SPH" . $year . "" . $month . "" . $uuid . "" . $day;
    $id_sph_encrypt = encrypt($id_sph, $key_global);
    $no_sph = $sanitasi_post['no_sph'];
    $tgl = $sanitasi_post['tgl'];
    $up = $sanitasi_post['up'];
    $id_cs = $sanitasi_post['id_cs'];
    $alamat = $sanitasi_post['alamat'];
    $ttd = $sanitasi_post['ttd'];
    $jabatan = $sanitasi_post['jabatan'];
    $perihal = $sanitasi_post['perihal'];
    $note = $sanitasi_post['note'];
    $user = $id_user;

    // Begin transaction
    mysqli_begin_transaction($connect);

    try {
        $simpan_sph = mysqli_query($connect, "INSERT INTO sph
                                                (id_sph, no_sph, tanggal, up, id_cs, alamat, ttd_oleh, jabatan, perihal, note, created_by)
                                                values
                                                ('$id_sph', '$no_sph', '$tgl', '$up', '$id_cs', '$alamat', '$ttd', '$jabatan', '$perihal', '$note', '$user')              
                                            ");

        // Commit the transaction
        mysqli_commit($connect);
        // Redirect to the invoice page
        $_SESSION['info'] = "Disimpan";
        header("Location:../tampil-data-sph.php?id=$id_sph_encrypt");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        mysqli_rollback($connect);
        $_SESSION['info'] = "Data Gagal Disimpan";
        header("Location:../tampil-data-sph.php?id=$id_sph_encrypt");
    }
} else if (isset($sanitasi_post['simpan-cek-produk'])) {
    $id_trx = $sanitasi_post['id_trx'];
    $id_sph = $sanitasi_post['id_sph'];
    $nama_produk = $sanitasi_post['nama_produk'];
    $qty = $sanitasi_post['qty']; // Mengambil nilai qty yang diperbarui
    $harga = $sanitasi_post['harga'];

    // Dekripsi setiap elemen dalam array $id_sph
    $id_sph = array_map(function ($sph) use ($key_global) {
        return decrypt($sph, $key_global);
    }, $id_sph);

    // Catat waktu mulai
    $startTime = microtime(true);

    // Mulai transaksi database
    mysqli_begin_transaction($connect);

    try {
        // Persiapkan query dengan Prepared Statement
        $stmt = mysqli_prepare($connect, "UPDATE transaksi_produk_sph SET nama_produk_sph = ?, harga = ?, qty = ?, status_trx = 1 WHERE id_transaksi = ?");
        if (!$stmt) {
            throw new Exception("Gagal mempersiapkan statement.");
        }

        // Batasi data yang diproses dalam satu batch
        $chunkSize = 10; // Contoh: 10 data per batch
        $totalData = count($id_trx);

        for ($i = 0; $i < $totalData; $i += $chunkSize) {
            $endIndex = min($i + $chunkSize, $totalData);
            for ($j = $i; $j < $endIndex; $j++) {
                // Ambil data dari batch
                $currentId = $id_trx[$j];
                $namaProduk = $nama_produk[$j];
                $newQtyInt = str_replace(',', '', $qty[$j]); // Hapus tanda ribuan (,)
                $newQtyInt = intval($newQtyInt); // Konversi ke integer
                $newHarga = str_replace(',', '', $harga[$j]); // Hapus tanda ribuan (,)
                $newHarga = intval($newHarga); // Konversi ke integer

                // Binding parameter (integer untuk harga dan qty, string untuk id_transaksi)
                mysqli_stmt_bind_param($stmt, 'siis', $namaProduk, $newHarga, $newQtyInt, $currentId);

                // Eksekusi statement
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Terjadi kesalahan saat menyimpan data untuk ID: $currentId");
                }
            }
        }

        // Tutup statement setelah selesai
        mysqli_stmt_close($stmt);

        // Commit transaksi jika berhasil
        mysqli_commit($connect);

        // Catat waktu akhir
        $endTime = microtime(true);

        // Hitung waktu proses
        $processTime = $endTime - $startTime;

        // Simpan waktu proses ke sesi
        $_SESSION['process_time'] = $processTime;

        // Simpan waktu ke dalam file log baru
        $logMessage = "Proses berhasil: Waktu eksekusi untuk SPH ID $id_sph[0] adalah " . number_format($processTime, 6) . " detik.\n";
        file_put_contents("log_proses.txt", $logMessage, FILE_APPEND);

        // Redirect ke halaman detail dengan id terenkripsi
        $id_sph_encrypt = encrypt($id_sph[0], $key_global); // Enkripsi salah satu ID untuk URL
        $_SESSION['info'] = "Disimpan";
        header("Location: ../tampil-data-sph.php?id=$id_sph_encrypt");
        exit();

    } catch (Exception $e) {
        // Rollback transaksi jika terjadi kesalahan
        mysqli_rollback($connect);

        // Catat waktu akhir
        $endTime = microtime(true);

        // Hitung waktu proses meskipun gagal
        $processTime = $endTime - $startTime;

        // Simpan waktu proses ke sesi
        $_SESSION['process_time'] = $processTime;

        // Simpan waktu ke dalam file log baru, termasuk pesan error
        $logMessage = "Proses gagal: Waktu eksekusi untuk SPH ID $id_sph[0] adalah " . number_format($processTime, 6) . " detik. Error: " . $e->getMessage() . "\n";
        file_put_contents("log_proses.txt", $logMessage, FILE_APPEND);

        // Redirect ke halaman yang sama dengan pesan error
        $id_sph_encrypt = encrypt($id_sph[0], $key_global); // Enkripsi salah satu ID untuk URL
        $_SESSION['info'] = "Data Gagal Disimpan";
        header("Location: ../tampil-data-sph.php?id=$id_sph_encrypt");
        exit();
    }
} else if (isset($sanitasi_post['ubah-cs-sph'])) {
    $id_sph = decrypt($sanitasi_post['id_sph'], $key_global);
    $id_sph_encrypt = encrypt($id_sph, $key_global);
    $tanggal = $sanitasi_post['tanggal'];
    $up = $sanitasi_post['up'];
    $id_cs = $sanitasi_post['id_cs'];
    $alamat = $sanitasi_post['alamat'];
    $ttd = $sanitasi_post['ttd'];
    $jabatan = $sanitasi_post['jabatan'];
    $perihal = $sanitasi_post['perihal'];
    $note = $sanitasi_post['note'];

    $sph_update = mysqli_query($connect, "UPDATE sph SET 
                                                tanggal = '$tanggal',
                                                up = '$up',
                                                id_cs = '$id_cs',
                                                alamat = '$alamat',
                                                ttd_oleh = '$ttd',
                                                jabatan = '$jabatan',
                                                perihal = '$perihal',
                                                note = '$note'
                                                WHERE id_sph = '$id_sph'");
    if ($sph_update) {
        $_SESSION['info'] = "Diupdate";
        header("Location:../tampil-data-sph.php?id=$id_sph_encrypt");
    } else {
        $_SESSION['info'] = "Data Gagal Diupdate";
        header("Location:../tampil-data-sph.php?id=$id_sph_encrypt");
    }
} else if (isset($sanitasi_post['edit-br'])) {
    $id_trx = decrypt($sanitasi_post['id_trx'], $key_global);
    $id_sph = decrypt($sanitasi_post['id_sph'], $key_global);
    $id_sph_encrypt = encrypt($id_sph, $key_global);
    $nama_produk_edit = $sanitasi_post['nama_produk_edit'];
    $qty_edit = $sanitasi_post['qty_edit'];
    $qty = str_replace(',', '', $qty_edit); // Menghapus tanda ribuan (,)
    $qty = intval($qty); // Mengubah string harga menjadi integer
    $harga_edit = $sanitasi_post['harga'];
    $harga = str_replace(',', '', $harga_edit); // Menghapus tanda ribuan (,)
    $harga = intval($harga); // Mengubah string harga menjadi integer

    $trx_edit = mysqli_query($connect, "UPDATE transaksi_produk_sph SET nama_produk_sph = '$nama_produk_edit',  harga = '$harga', qty = '$qty' WHERE id_transaksi = '$id_trx'");

    if ($trx_edit) {
        $_SESSION['info'] = "Diupdate";
        header("Location:../tampil-data-sph.php?id=$id_sph_encrypt");
    } else {
        $_SESSION['info'] = "Data Gagal Diupdate";
        header("Location:../tampil-data-sph.php?id=$id_sph_encrypt");
    }
} else if (isset($_GET['hapus'])) {
    $id_trx = $_GET['hapus'];
    $id_sph = $_GET['id_sph'];
    $id_trx_decrypt = decrypt($id_trx, $key_global);

    $sql_del = mysqli_query($connect, "DELETE FROM transaksi_produk_sph WHERE id_transaksi = '$id_trx_decrypt'");

    if ($sql_del) {
        $_SESSION['info'] = "Dihapus";
        header("Location:../tampil-data-sph.php?id=$id_sph");
    } else {
        $_SESSION['info'] = "Data Gagal Dihapus";
        header("Location:../tampil-data-sph.php?id=$id_sph");
    }
} else if (isset($sanitasi_post['cancel'])) {
    $id_sph = $sanitasi_post['id_sph'];
    $alasan = $sanitasi_post['alasan'];
    $id_sph_decrypt = decrypt($id_sph, $key_global);
    $cancel_sph = mysqli_query($connect, "UPDATE sph SET status_cancel = 1, alasan_cancel = '$alasan' WHERE id_sph = '$id_sph_decrypt'");

    if ($cancel_sph) {
        $_SESSION['info'] = "Dicancel";
        header("Location:../sph.php");
    } else {
        $_SESSION['info'] = "Data Gagal Dicancel";
        header("Location:../sph.php");
    }
}
?>