<?php  
    require_once "akses.php";
    $id_user = decrypt($_SESSION['tiket_id'], $key_global);
    $sql_history_input_stock = "SELECT 
                                    id_kartu_stock,
                                    id_produk,
                                    nama_produk,
                                    qty_in,
                                    qty_out,
                                    created_date,
                                    status_barang
                                FROM (
                                    SELECT
                                        reg.id_kartu_stock AS id_kartu_stock,
                                        reg.id_produk AS id_produk,
                                        tpr.nama_produk AS nama_produk,
                                        reg.qty_in,
                                        reg.qty_out,
                                        reg.created_date AS created_date,
                                        reg.status_barang AS status_barang
                                    FROM kartu_stock_reg reg
                                    LEFT JOIN tb_produk_reguler tpr ON reg.id_produk = tpr.id_produk_reg
                                    WHERE reg.created_by = '$id_user'

                                    UNION

                                    SELECT
                                        ecat.id_kartu_stock AS id_kartu_stock,
                                        ecat.id_produk AS id_produk,
                                        tpe.nama_produk AS nama_produk,
                                        ecat.qty_in,
                                        ecat.qty_out,
                                        ecat.created_date AS created_date,
                                        ecat.status_barang AS status_barang
                                    FROM kartu_stock_ecat ecat
                                    LEFT JOIN tb_produk_ecat tpe ON ecat.id_produk = tpe.id_produk_ecat
                                    WHERE ecat.created_by = '$id_user'

                                    UNION

                                    SELECT
                                        set_reg.id_kartu_stock AS id_kartu_stock,
                                        set_reg.id_produk AS id_produk,
                                        tpsm.nama_set_marwa AS nama_produk, -- Tidak ada kolom 'nama_produk' di tb_produk_set_marwa, ganti dengan string kosong
                                        set_reg.qty_in,
                                        set_reg.qty_out,
                                        set_reg.created_date AS created_date,
                                        set_reg.status_barang AS status_barang
                                    FROM kartu_stock_set_reg set_reg
                                    LEFT JOIN tb_produk_set_marwa tpsm ON (set_reg.id_produk = tpsm.id_set_marwa)
                                    WHERE set_reg.created_by = '$id_user'

                                    UNION

                                    SELECT
                                        set_ecat.id_kartu_stock AS id_kartu_stock,
                                        set_ecat.id_produk AS id_produk,
                                        tpse.nama_set_ecat AS nama_produk, -- Tidak ada kolom 'nama_produk' di tb_produk_set_ecat, ganti dengan string kosong
                                        set_ecat.qty_in,
                                        set_ecat.qty_out,
                                        set_ecat.created_date AS created_date,
                                        set_ecat.status_barang AS status_barang
                                    FROM kartu_stock_set_ecat set_ecat
                                    LEFT JOIN tb_produk_set_ecat tpse ON (set_ecat.id_produk = tpse.id_set_ecat)
                                    WHERE set_ecat.created_by = '$id_user'
                                ) AS result";
$user_history_input_stock = $connect->query($sql_history_input_stock);
?>