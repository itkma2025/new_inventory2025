<?php
require_once "../../akses.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = htmlspecialchars(decrypt($_POST['id'], $key_finance));
    $bill = htmlspecialchars(decrypt($_POST['bill'], $key_finance));
    $status_tagihan = 1;

    // Echo data untuk debugging
    echo 'Received ID: ' . $id . '<br>';
    echo 'Received Bill: ' . $bill . '<br>';

    // Gunakan parameter binding untuk menghindari SQL Injection
    $stmt = $connect->prepare("UPDATE finance SET id_tagihan = ?, status_tagihan = ? WHERE id_finance = ?");
    if ($stmt) {
        $stmt->bind_param("sis", $bill, $status_tagihan, $id);
        if ($stmt->execute()) {
            echo 'Update successful';
        } else {
            echo 'Update failed: ' . $stmt->error;
        }
        $stmt->close();
    } else {
        echo 'Prepare failed: ' . $connect->error;
    }

    $connect->close();
} else {
    echo 'Invalid request method';
}
?>
