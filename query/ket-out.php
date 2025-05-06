<?php
$sql_ket_out = " SELECT 
                        id_ket_out,
                        ket_out,
                        status_aktif
                    FROM keterangan_out 
                    WHERE status_aktif = '1' ORDER BY ket_out ASC";
$query_ket_out = $connect->query($sql_ket_out);
