<?php  
    function getParamTypes($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_double($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b'; // blob atau fallback
            }
        }
        return $types;
    }

    // contoh penggunaan
    // Dapatkan tipe berdasarkan isi params
    // $types = getParamTypes($params);

?>