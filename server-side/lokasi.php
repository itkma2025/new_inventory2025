<?php
// Koneksi ke database
include "../koneksi.php";
require_once "../function/function-enkripsi.php";


// Definisikan kolom yang bisa diurutkan
$columns = array(
    0 => 'id_lokasi',
    1 => 'nama_lokasi',
    2 => 'no_lantai',
    3 => 'nama_area',
    4 => 'no_rak'
);  

// Query utama
$sql = "SELECT 
            lp.id_lokasi, 
            lp.nama_lokasi,
            lp.no_lantai,
            lp.nama_area,
            lp.no_rak,
            DATE_FORMAT(lp.created_date, '%d/%m/%Y, %H:%i:%s') AS created_date,
            CASE 
                WHEN lp.updated_date = '0000-00-00 00:00:00' THEN '-'
                ELSE DATE_FORMAT(lp.updated_date, '%d/%m/%Y, %H:%i:%s')
            END AS updated_date,
            uc.nama_user AS user_created, 
            COALESCE(uu.nama_user, '-') AS user_updated
        FROM tb_lokasi_produk AS lp
        LEFT JOIN $database2.user AS uc ON lp.created_by = uc.id_user
        LEFT JOIN $database2.user AS uu ON lp.updated_by = uu.id_user";

// Proses filtering
if (!empty($_POST['search']['value'])) {
    $searchValue = $_POST['search']['value'];
    $sql .= " WHERE (nama_lokasi LIKE '%$searchValue%' 
                     OR no_lantai LIKE '%$searchValue%' 
                     OR nama_area LIKE '%$searchValue%' 
                     OR no_rak LIKE '%$searchValue%') ";
}

// Urutan
$orderColumn = $columns[$_POST['order'][0]['column']];
$orderDir = $_POST['order'][0]['dir'];
$sql .= " ORDER BY $orderColumn $orderDir ";

// Jumlah total data
$query = mysqli_query($connect, $sql);
$totalData = mysqli_num_rows($query);

// Limit data yang ditampilkan
$start = $_POST['start'];
$length = $_POST['length'];
$sql .= " LIMIT $start, $length ";

$query = mysqli_query($connect, $sql);

$data = array();
$no = $start + 1;

while ($row = mysqli_fetch_array($query)) {
    $id_lokasi = base64_encode($row['id_lokasi']);

    $data[] = array(
        '<div class="text-center text-nowrap">'.$no.'</div>',
        '<div class="text-center text-nowrap">'.$row['nama_lokasi'].'</div>',
        '<div class="text-center text-nowrap">'.$row['no_lantai'].'</div>',
        '<div class="text-center text-nowrap">'.$row['nama_area'].'</div>',
        '<div class="text-center text-nowrap">'.$row['no_rak'].'</div>',
        '<div class="text-center text-nowrap">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal3" 
                data-user="' . $row['user_created'] . '" data-lokasi="' . $row['nama_lokasi'] . '" 
                data-lantai="' . $row['no_lantai'] . '" data-area="' . $row['nama_area'] . '" 
                data-rak="' . $row['no_rak'] . '" data-created="' . $row['created_date'] . '" 
                data-updated="' . $row['updated_date'] . '" data-userupdated="' . $row['user_updated'] . '" title="Detail Data">
                <i class="bi bi-info-circle"></i>
            </button>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modal2" 
                data-id="' . encrypt($row['id_lokasi'], $key_global)  . '" data-user="' . $row['user_created'] . '" 
                data-lokasi="' . $row['nama_lokasi'] . '" data-lantai="' . $row['no_lantai'] . '" 
                data-area="' . $row['nama_area'] . '" data-rak="' . $row['no_rak'] . '" 
                data-created="' . $row['created_date'] . '" data-updated="' . $row['updated_date'] . '" 
                data-userupdated="' . $row['user_updated'] . '" title="Ubah Data">
                <i class="bi bi-pencil"></i>
            </button>
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#hapusData" data-id="' . encrypt($row['id_lokasi'], $key_global) . '" data-lokasi="' . $row['nama_lokasi'] . ' " data-lantai="' . $row['no_lantai'] . '" data-area="' . $row['nama_area'] . '"  data-rak="' . $row['no_rak'] . '">
                <i class="bi bi-trash"></i>
            </button>
        </div>'
    );
    $no++;
}

$output = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalData,
    "data" => $data
);

echo json_encode($output);
?>
