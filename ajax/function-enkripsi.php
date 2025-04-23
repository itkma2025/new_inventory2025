<?php
// Include file yang berisi fungsi enkripsi
require_once "../function/function-enkripsi.php";

// Ambil data dari request
$formattedStartDate = $_POST['formattedStartDate'];
$formattedEndDate = $_POST['formattedEndDate'];
$key = $_POST['key'];

// Lakukan enkripsi
$encryptedStartDate = encrypt($formattedStartDate, $key);
$encryptedEndDate = encrypt($formattedEndDate, $key);

// Kembalikan hasilnya sebagai JSON
$result = array(
    'startDate' => $encryptedStartDate,
    'endDate' => $encryptedEndDate
);

// Output sebagai JSON
header('Content-Type: application/json');
echo json_encode($result);
?>
