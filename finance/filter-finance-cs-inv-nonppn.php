<?php
require_once "../akses.php";
$no = 1;
$sql_inv = "SELECT nonppn.id_inv_nonppn,
                    nonppn.no_inv,
                    DATE_FORMAT(nonppn.tgl_inv, '%d/%m/%Y') AS tgl_inv,
                    nonppn.cs_inv,
                    DATE_FORMAT(nonppn.tgl_tempo, '%d/%m/%Y') AS tgl_tempo,
                    nonppn.kategori_inv,
                    nonppn.total_inv,
                    nonppn.status_transaksi,
                    fnc.id_inv,
                    fnc.status_pembayaran,
                    fnc.status_lunas
            FROM inv_nonppn AS nonppn
            LEFT JOIN finance fnc ON (nonppn.id_inv_nonppn = fnc.id_inv)";

// Check if start_date and end_date are provided for date filtering
if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];

    // Konversi format tanggal dari dd/mm/yyyy menjadi yyyy-mm-dd
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));

    $sql_inv .= " WHERE nonppn.tgl_inv BETWEEN '$start_date' AND '$end_date'";
}

$sql_inv .= " ORDER BY tgl_inv ASC";
$query_inv = mysqli_query($connect, $sql_inv);
?>

<!-- Hasil data dalam bentuk tabel HTML -->
<table border="1">
    <thead>
        <tr>
            <th>No.</th>
            <th>Tanggal Invoice</th>
            <th>Nomor Invoice</th>
            <!-- Kolom lain yang Anda inginkan -->
        </tr>
    </thead>
    <tbody>
        <?php while ($data = mysqli_fetch_array($query_inv)) { ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo $data['tgl_inv']; ?></td>
                <td><?php echo $data['no_inv']; ?></td>
                <!-- Isi dengan kolom lain yang Anda inginkan -->
            </tr>
        <?php } ?>
    </tbody>
</table>
