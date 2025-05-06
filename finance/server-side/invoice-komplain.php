<?php
include "../koneksi.php";

$columns = array(
    0 => 'id_produk_reg',
    1 => 'kode_produk',
    2 => 'nama_produk',
    3 => 'nama_merk',
    4 => 'harga_produk',
    5 => 'stock',
    6 => 'stock_status',
    7 => 'status',
    8 => 'aksi'
);

$sql = "SELECT
            ik.id_inv,
            ik.no_komplain,
            ik.tgl_komplain,
            ik.kat_komplain,
            ik.kondisi_pesanan,
            ik.status_retur,
            ik.status_refund,
            ik.status_komplain
            

        ";

// Proses filtering
if (!empty($_POST['search']['value'])) {
    $searchValue = $_POST['search']['value'];
    $sql .= " WHERE (nama_produk LIKE '%$searchValue%') ";
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

while ($row = mysqli_fetch_assoc($query)) {
    $id_produk = base64_encode($row['produk_id']);
    $stock = $row['stock'];
    $min_stock = $row['min_stock'];
    $max_stock = $row['max_stock'];
    $low = $min_stock * 0.25;
    $low_lev = $min_stock - $low;
    $med_lev = $min_stock + $low;
    $high = $max_stock * 0.25;
    $high_lev = $max_stock - $high;
    $stock_status = '';
    $tampil_stock = number_format($row['stock']);
    $id_produk_spr = $row['id_produk_spr'];

    $stockCell = ''; // Initialize an empty cell
    if ($stock <= $low_lev) {
        $stockCell = "<div class='text-end text-white text-nowrap p-1' style='background-color: #cc0000'; baground-size: cover;>" . ($tampil_stock) . "</div>";
    } else if ($stock >= $low_lev && $stock <= $min_stock) {
        $stockCell = "<div class='text-end text-nowrap p-1' style='background-color: #ff4500'>" . ($tampil_stock) . "</div>";
    } else if ($stock >= $min_stock && $stock <= $high_lev) {
        $stockCell = "<div class='text-end text-nowrap p-1' style='background-color: #ffff00'>" . ($tampil_stock) . "</div>";
    } else if ($stock >= $high_lev && $stock <= $max_stock) {
        $stockCell = "<div class='text-end text-white text-nowrap p-1' style='background-color: #469536'>" . ($tampil_stock) . "</div>";
    } else if ($stock > $max_stock) {
        $stockCell = "<div class='text-end text-white text-nowrap p-1' style='background-color: #006600'>" . ($tampil_stock) . "</div>";
    }

    $levelStock = '';
    if ($stock < 1) {
        $levelStock = "<div class='text-end text-nowrap p-1'>" . ($stock_status = 'Habis') . "</div>";
      } else if ($stock <= $low_lev) {
        $levelStock = "<div class='text-end text-nowrap p-1'>" . ($stock_status = 'Very Low') . "</div>";
      } else if ($stock >= $low_lev && $stock <= $min_stock) {
        $levelStock = "<div class='text-end text-nowrap p-1'>" . ($stock_status = 'Low') . "</div>";
      } else if ($stock >= $min_stock && $stock <= $high_lev) {
        $levelStock = "<div class='text-end text-nowrap p-1'>" . ($stock_status = 'Medium') . "</div>";
      } else if ($stock >= $high_lev && $stock <= $max_stock) {
        $levelStock = "<div class='text-end text-nowrap p-1'>" . ($stock_status = 'High') . "</div>";
      } else if ($stock > $max_stock) {
        $levelStock = "<div class='text-end text-nowrap p-1'>" . ($stock_status = 'Very High') . "</div>";
      }

    // Inisialisasi tombol Hapus Data
    $hapusProduk = '';
    if($id_produk_spr == 0){
        $hapusProduk ='<button class="btn btn-danger btn-sm mt-1" title="Hapus Data" data-bs-toggle="modal" data-bs-target="#hapusData" data-id="'.$id_produk.'" data-nama="'.$row['nama_produk'].'" data-merk="'.$row['nama_merk'].'">
                            <i class="bi bi-trash"></i>
                        </button>';
    }
    
    $data[] = array(
        '<div class="text-center text-nowrap p-1">'.$no.'</div>',
        '<div class="text-center text-nowrap p-1">'.$row['kode_produk'].'</div>',
        '<div class="text-nowrap p-1">'.$row['nama_produk'].'</div>',
        '<div class="text-center text-nowrap p-1">'.$row['nama_merk'].'</div>',
        '<div class="text-end text-nowrap p-1">'.number_format($row['harga_produk']).'</div>',
        $stockCell,
        $levelStock,
        '<div class="p-1 text-center"> 
            <button class="btn btn-primary btn-sm" title="Detail" data-bs-toggle="modal" data-bs-target="#detailProduk"
                data-kode-produk="'.$row['kode_produk'].'" 
                data-nama-produk="'.$row['nama_produk'].'" 
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

            <a class="btn btn-warning btn-sm" href="edit-produk-reg.php?edit-data='. $id_produk .'" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            '.$hapusProduk.'
            <a class="btn btn-info btn-sm mt-1" href="cetak-qr-code.php?id='. $id_produk .'">
                <i class="bi bi-qr-code-scan"></i>
            </a>
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

