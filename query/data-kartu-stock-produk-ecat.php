<?php  
    $sql_produk_ecat = "SELECT 
                            pr.id_produk_ecat AS id_produk,
                            pr.kode_produk,
                            pr.nama_produk,
                            mr.nama_merk,
                            kp.nama_kategori,
                            kj.min_stock_ready, 
                            kj.max_stock_ready,
                            lok.nama_lokasi,
                            lok.no_lantai,
                            lok.no_rak,
                            ksr.id_kartu_stock,
                            SUM(ksr.qty_in) AS total_in,
                            SUM(ksr.qty_out) AS total_out
                        FROM tb_produk_ecat as pr
                        LEFT JOIN tb_merk mr ON (pr.id_merk = mr.id_merk)
                        LEFT JOIN tb_kat_produk kp ON (pr.id_kat_produk = kp.id_kat_produk)
                        LEFT JOIN tb_kat_penjualan AS kj ON (kj.id_kat_penjualan = pr.id_kat_penjualan)
                        LEFT JOIN tb_lokasi_produk lok ON (pr.id_lokasi = lok.id_lokasi)
                        LEFT JOIN kartu_stock_ecat ksr ON (pr.id_produk_ecat = ksr.id_produk)
                        WHERE ksr.id_produk IS NOT NULL";
    // Menampilkan urutan ASC   
    $sql_produk_asc = $sql_produk_ecat . " GROUP BY ksr.id_produk ORDER BY pr.nama_produk ASC";
    $query_produk = $connect->query($sql_produk_asc);

     // Menampilkan group by kategori produk
     $sql_kategori_produk = $sql_produk_ecat . " GROUP BY kp.nama_kategori ORDER BY kp.nama_kategori ASC";
     $query_kategori_produk = $connect->query($sql_kategori_produk);
 
     // Menampilkan group by lokasi produk
     $sql_nama_lokasi = $sql_produk_ecat . " GROUP BY lok.nama_lokasi ORDER BY lok.nama_lokasi ASC";
     $query_nama_lokasi = $connect->query($sql_nama_lokasi);
 
     $sql_no_lantai = $sql_produk_ecat . " GROUP BY lok.no_lantai ORDER BY lok.no_lantai ASC";
     $query_no_lantai = $connect->query($sql_no_lantai);
?>