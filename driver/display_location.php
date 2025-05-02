<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Location Details</title>
</head>
<body>
    <h1>Location Details</h1>
    <p><strong>Kelurahan:</strong> <?php echo isset($_SESSION['kelurahan']) ? $_SESSION['kelurahan'] : 'Data tidak tersedia'; ?></p>
    <p><strong>Kecamatan:</strong> <?php echo isset($_SESSION['kecamatan']) ? $_SESSION['kecamatan'] : 'Data tidak tersedia'; ?></p>
    <p><strong>Kota:</strong> <?php echo isset($_SESSION['kota']) ? $_SESSION['kota'] : 'Data tidak tersedia'; ?></p>
    <p><strong>Provinsi:</strong> <?php echo isset($_SESSION['provinsi']) ? $_SESSION['provinsi'] : 'Data tidak tersedia'; ?></p>
    <p><strong>Kode Pos:</strong> <?php echo isset($_SESSION['kode_pos']) ? $_SESSION['kode_pos'] : 'Data tidak tersedia'; ?></p>
</body>
</html>
