<?php  
    function getStatusRefund($status) {
        switch ($status) {
            case 0:
                return "Open";
            case 1:
                return "Close";
            case 2:
                return "Sudah Bayar (Belum Lunas)";
            case 3:
                return "Cancel";
            default:
                return "Unknown Status";
        }
    }
    
    // Contoh penggunaan function:
    // $status = 2;
    // echo getStatusRefund($status); // Output: Sudah Bayar (Belum Lunas)
    
?>