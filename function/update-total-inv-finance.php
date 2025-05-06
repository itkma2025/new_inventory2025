<?php
    require_once "../akses.php";
    function updateTotalInvFnc($connect) {
        $update_finance = "UPDATE finance AS fnc
                            LEFT JOIN inv_nonppn AS nonppn ON fnc.id_inv = nonppn.id_inv_nonppn
                            LEFT JOIN inv_ppn AS ppn ON fnc.id_inv = ppn.id_inv_ppn
                            LEFT JOIN inv_bum AS bum ON fnc.id_inv = bum.id_inv_bum
                            SET fnc.total_inv = COALESCE(nonppn.total_inv, ppn.total_inv, bum.total_inv)
                            WHERE COALESCE(nonppn.total_inv, ppn.total_inv, bum.total_inv) <> fnc.total_inv;
        ";
        $proses_update = $connect->query($update_finance);
        if($proses_update){
            ?>
                <script>
                    console.log("Status : Oke")
                </script>
            <?php
        }
    }

    updateTotalInvFnc($connect);
?>