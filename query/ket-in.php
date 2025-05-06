<?php
$sql_ket_in = " SELECT 
                        id_ket_in,
                        ket_in,
                        status_aktif
                    FROM keterangan_in 
                    WHERE status_aktif = '1' ORDER BY ket_in ASC";
$query_ket_in = $connect->query($sql_ket_in);
