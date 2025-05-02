<?php  
    $sql_ket_in = " SELECT 
                        id_ket_in,
                        ket_in
                    FROM keterangan_in 
                    ORDER BY ket_in ASC";
    $query_ket_in = $connect->query($sql_ket_in);
?>