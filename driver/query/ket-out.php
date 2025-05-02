<?php  
    $sql_ket_out = " SELECT 
                        id_ket_out,
                        ket_out
                    FROM keterangan_out 
                    ORDER BY ket_out ASC";
    $query_ket_out = $connect->query($sql_ket_out);
?>