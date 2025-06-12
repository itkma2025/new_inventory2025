<?php
require_once __DIR__ . "/../koneksi.php";
// Penghubung Library
require_once '../assets/vendor/autoload.php';
// Library Tangal
use Carbon\Carbon;
$datetime_now = Carbon::now();

// Library Debugging
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
// Library sanitasi input data
require_once __DIR__ . "/../function/sanitasi_input.php";
$sanitasi_get = sanitizeInput($_GET);

$jenis = isset($sanitasi_get['jenis']) ? $sanitasi_get['jenis'] : '';
if (!in_array($jenis, ['local', 'import'])) {
    exit('<option value="">Jenis kategori tidak valid.</option>');
}

$sql = "SELECT * FROM tb_merk WHERE jenis_merk = ?";
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, "s", $jenis);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

echo '<option value="">Pilih Merk...</option>';
while ($data = mysqli_fetch_array($result)) {
    echo '<option value="' . $data['id_merk'] . '">' . htmlspecialchars($data['nama_merk']) . '</option>';
}
?>
