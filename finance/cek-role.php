<?php  
if ($role != 'Finance') {
    echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">';
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>';
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Anda tidak memiliki akses ke website ini!',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = '../logout.php';
            });
          </script>";
    exit();
}
?>
