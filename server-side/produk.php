<?php
ob_start(); // Mulai menangkap output
require_once "../koneksi.php";
require_once "../function/function-enkripsi.php";
header('Content-Type: application/json');

$columns = array(
    0 => 'id_produk_reg',
    1 => 'kode_produk',
    2 => 'nama_produk',
    3 => 'satuan',
    4 => 'nama_merk',
    5 => 'harga_produk',
    6 => 'stock',
    7 => 'stock_status',
    7 => 'status_akl',
    9 => 'aksi'
);

$sql = "SELECT 
            pr.id_produk_reg,
            pr.kode_produk,
            pr.harga_produk,
            pr.nama_produk,
            pr.kode_katalog,
            pr.satuan,
            pr.gambar,
            pr.deskripsi,
            DATE_FORMAT(pr.created_date, '%d/%m/%Y, %H:%i:%s') AS produk_created,
            CASE 
                WHEN pr.updated_date = '0000-00-00 00:00:00' THEN '-'
                ELSE DATE_FORMAT(pr.updated_date, '%d/%m/%Y, %H:%i:%s')
            END AS produk_updated,
            uc.nama_user as user_created, 
            COALESCE(uu.nama_user, '-') AS user_updated, 
            mr.nama_merk,
            kp.no_izin_edar,
            kp.nama_kategori as kat_prod,
            kj.nama_kategori as kat_penj,
            kj.min_stock,
            kj.max_stock,
            gr.nama_grade,
            lok.nama_lokasi,
            lok.no_lantai,
            lok.nama_area,
            lok.no_rak,
            COALESCE(spr.id_produk_reg, 0) AS id_produk_spr,
            spr.stock
        FROM tb_produk_reguler as pr
        LEFT JOIN $database2.user AS uc ON pr.created_by = uc.id_user
        LEFT JOIN $database2.user AS uu ON pr.updated_by = uu.id_user
        LEFT JOIN tb_merk mr ON pr.id_merk = mr.id_merk
        LEFT JOIN tb_kat_produk kp ON pr.id_kat_produk = kp.id_kat_produk
        LEFT JOIN tb_kat_penjualan kj ON pr.id_kat_penjualan = kj.id_kat_penjualan
        LEFT JOIN tb_produk_grade gr ON pr.id_grade = gr.id_grade
        LEFT JOIN tb_lokasi_produk lok ON pr.id_lokasi = lok.id_lokasi
        LEFT JOIN stock_produk_reguler spr ON pr.id_produk_reg = spr.id_produk_reg";

// Proses filtering
$whereClauses = array();
if (!empty($_POST['search']['value'])) {
    $searchValue = mysqli_real_escape_string($connect, $_POST['search']['value']);
    $keywords = explode(" ", $searchValue);
    
    foreach ($keywords as $keyword) {
        $whereClauses[] = "(pr.nama_produk LIKE '%$keyword%' OR pr.kode_produk LIKE '%$keyword%')";
    }
}

if (!empty($whereClauses)) {
    $sql .= " WHERE " . implode(" AND ", $whereClauses);
}

// Urutan berdasarkan kolom yang dipilih
$orderColumnIndex = $_POST['order'][1]['column'];
$orderDir = $_POST['order'][0]['dir'];
$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'pr.nama_produk';
$sql .= " ORDER BY $orderColumn $orderDir";

// Total data tanpa filter
$sqlTotal = "SELECT COUNT(*) as total FROM tb_produk_reguler";
$totalResult = mysqli_query($connect, $sqlTotal);
$totalData = mysqli_fetch_assoc($totalResult)['total'];

// Total data setelah filter
$sqlFiltered = "SELECT COUNT(*) as total FROM tb_produk_reguler as pr";
if (!empty($whereClauses)) {
    $sqlFiltered .= " WHERE " . implode(" AND ", $whereClauses);
}
$totalFilteredResult = mysqli_query($connect, $sqlFiltered);
$totalFiltered = mysqli_fetch_assoc($totalFilteredResult)['total'];

// Pagination
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$sql .= " LIMIT $start, $length";

$query = mysqli_query($connect, $sql);

$data = array();
$no = $start + 1;

while ($row = mysqli_fetch_assoc($query)) {
    $id_produk = encrypt($row['id_produk_reg'], $key_global);
    $stock = $row['stock'];
    $min_stock = $row['min_stock'];
    $max_stock = $row['max_stock'];

    // Menentukan warna latar belakang berdasarkan level stok
    $stockStatus = [
        "Very Low" => "#cc0000",
        "Low" => "#ff4500",
        "Medium" => "#8FBC8F",
        "High" => "#469536",
        "Very High" => "#006600"
    ];
    
    $stockLevel = "Medium";
    if ($stock <= $min_stock * 0.75) {
        $stockLevel = "Very Low";
    } elseif ($stock <= $min_stock) {
        $stockLevel = "Low";
    } elseif ($stock >= $max_stock * 0.75) {
        $stockLevel = "High";
    } elseif ($stock > $max_stock) {
        $stockLevel = "Very High";
    }

    $status_akl = "";
    if($row['no_izin_edar'] == '--'){
        $status_akl = '<span class="badge bg-danger" style="font-size:13px;">Tidak Ada</span>';
    } else {
        $status_akl = '<span class="badge bg-success" style="font-size:13px;">Ada</span>';
    }

    $stockCell = "<div class='text-end text-nowrap p-1' style='background-color: " . $stockStatus[$stockLevel] . "; color: white;'>".number_format($stock)."</div>";

    $hapusProduk = ($row['id_produk_spr'] == 0) ? 
        '<button class="btn btn-danger btn-sm mt-1" title="Hapus Data" data-bs-toggle="modal" data-bs-target="#hapusData" data-id="'.$id_produk.'" data-nama="'.$row['nama_produk'].'" data-merk="'.$row['nama_merk'].'">
            <i class="bi bi-trash"></i>
        </button>' : '';

    $data[] = array(
        '<div class="text-center text-nowrap p-1">'.$no.'</div>',
        '<div class="text-center text-nowrap p-1">'.$row['kode_produk'].'</div>',
        '<div class="text-nowrap p-1">'.$row['nama_produk'].'</div>',
        '<div class="text-center text-nowrap p-1">'.$row['satuan'].'</div>',
        '<div class="text-center text-nowrap p-1">'.$row['nama_merk'].'</div>',
        '<div class="text-end text-nowrap p-1">'.number_format($row['harga_produk']).'</div>',
        $stockCell,
        '<div class="text-end text-nowrap p-1">'.$stockLevel.'</div>',
        '<div class="text-center text-nowrap p-1">'.$status_akl.'</div>',
        '<div class="p-1 text-center text-nowrap"> 
            <button class="btn btn-primary btn-sm" title="Detail" data-bs-toggle="modal" data-bs-target="#detailProduk"
                data-kode-produk="'.$row['kode_produk'].'" 
                data-nama-produk="'.$row['nama_produk'].'" 
                data-kode-katalog="'.$row['kode_katalog'].'" 
                data-satuan="'.$row['satuan'].'" 
                data-merk-produk="'.$row['nama_merk'].'" 
                data-harga-produk="'.number_format($row['harga_produk'], 0,'.','.').'" 
                data-stock-produk="'.$row['stock'].'" 
                data-kategori-produk="'.$row['kat_prod'].'" 
                data-izin-edar="'.$row['no_izin_edar'].'" 
                data-kategori-penjualan="'.$row['kat_penj'].'" 
                data-grade-produk="'.$row['nama_grade'].'" 
                data-lokasi-produk="'.$row['nama_lokasi'].'" 
                data-lantai-produk="'.$row['no_lantai'].'" 
                data-area-produk="'.$row['nama_area'].'" 
                data-rak-produk="'.$row['no_rak'].'" 
                data-created-produk="'.$row['produk_created'].'" 
                data-user-created="'.$row['user_created'].'" 
                data-update-produk="'.$row['produk_updated'].'" 
                data-user-update="'.$row['user_updated'].'" 
                data-gambar-produk="'.$row['gambar'].'">
                <i class="bi bi-info"></i>
            </button>
            <a class="btn btn-warning btn-sm" href="edit-produk-reg.php?edit-data='.$id_produk.'" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <br>'.$hapusProduk.'
            <a class="btn btn-info btn-sm mt-1" href="cetak-qr-code.php?id='. $id_produk .'">
                <i class="bi bi-qr-code-scan"></i>
            </a>
        </div>'
    );

    $no++;
}

// Bersihkan buffer dan keluarkan data JSON
ob_end_clean();
echo json_encode(array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $totalData,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
));
exit();
