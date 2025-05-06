<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="assets/img/logo-kma.png" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.20/dist/sweetalert2.all.min.js"></script>
</head>

<body>
    <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }        
        require_once __DIR__ . '/koneksi.php';
        require_once __DIR__ . '/function/function-enkripsi.php';
        date_default_timezone_set('Asia/Jakarta');
        
        // Cek apakah token tersedia di URL atau session
        if (isset($_GET['tkn']) || isset($_COOKIE['jwt_token_test'])) {
            // Jika token ada di URL, perbarui token di session
            if (isset($_GET['tkn'])) {
                $jwt = $_GET['tkn'];
                setcookie('jwt_token_test', $jwt, [
                    'expires' => time() + 14400,
                    'path' => '/',
                    'domain' => 'localhost',
                    'secure' => false,  // Hanya HTTPS
                    'httponly' => true, // Tidak dapat diakses oleh JavaScript
                    'samesite' => 'Lax' // Atau 'Strict' jika bukan lintas domain
                ]);
        
                header("Location: index.php");
                exit();
            } else {
                // Jika tidak, gunakan token dari session
                if (isset($_COOKIE['jwt_token_test'])) {
                    $jwt = $_COOKIE['jwt_token_test'];
                } else {
                    header("Location: logout.php");
                    exit();
                }
            }
        
            // Pengecekan token di database
            $query = "SELECT 
                        us.id_user_status,
                        us.status_perangkat, 
                        us.id_user_akses, 
                        ut.expired_token_time,
                        us.status_klik_active,
                        u.id_user,
                        ur.nama_role,
                        u.nama_user,
                        ua.is_blocked,
                        wb.status_domain
                      FROM user_status us
                      LEFT JOIN user_token ut ON ut.id_token = us.id_token
                      LEFT JOIN user_akses ua ON us.id_user_akses = ua.id_user_akses
                      LEFT JOIN user u ON ua.id_user = u.id_user
                      LEFT JOIN user_role ur ON u.id_user_role = ur.id_user_role
                      LEFT JOIN website_management wb ON ua.id_website = wb.id_website
                      WHERE ut.token = '$jwt'";
            $result = mysqli_query($koneksi2, $query);
            $data = mysqli_fetch_assoc($result);
            $_SESSION['tiket_id'] = encrypt($data['id_user'], $key_global);
            $nama_user = $data['nama_user'];
            $id_user = $data['id_user'];
            $_SESSION['tiket_nama'] = encrypt($nama_user, $key_global);
            $role =  $data['nama_role'];
        
            // Setting Role akses
            $user_role = $role;
            require_once "function/role-akses.php";
        
            if ($data) {
                $current_time = time();
                $expired_token_time = strtotime($data['expired_token_time']);
                // Periksa apakah sesi sudah berakhir (30 menit tidak ada aktivitas)
                $session_time = 1800; // 30 menit
                $current_time = time();
                $id_user_status = $data['id_user_status'];
                $status_klik_active = $data['status_klik_active'];
                
                // Cek apakah perangkat online
                if ($data['status_perangkat'] == 'Online') {
                    if (isset($_SESSION['last_activity']) && ($current_time - $_SESSION['last_activity']) > $session_time) {
                        // Jika sesi telah berakhir, hancurkan sesi dan redirect ke logout.php
                        // session_unset();
                        // session_destroy();
                        header("location: logout.php");
                        exit();
                    } else {
                        // Perbarui waktu aktivitas terakhir setiap kali ada aktivitas
                        $_SESSION['last_activity'] = $current_time;
                    }
        
                    // Cek apakah token sudah kadaluarsa
                    if ($current_time >= $expired_token_time) {
                        header("Location: logout.php");
                        exit();
                    }
        
                    // Cek apakah id_user_akses kosong atau null
                    if (empty($data['id_user_akses'])) {
                        header("Location: logout.php");
                        exit();
                    }
        
                    
                    // Cek apakah user diblokir
                    if ($data['is_blocked'] == 1) {
                        echo "<script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Maaf akses anda telah di blokir',
                                    showConfirmButton: false,
                                    timer: 2000
                                }).then(() => {
                                    window.location.href = 'logout.php';
                                });
                              </script>";
                        exit();
                    }
                    
                     // Cek apakah domain non aktif
                    if ($data['status_domain'] == 'nonaktif') {
                        echo "<script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Maaf website sedang di nonaktifkan',
                                    showConfirmButton: false,
                                    timer: 2000
                                }).then(() => {
                                    window.location.href = 'logout.php';
                                });
                              </script>";
                        exit();
                    }
        
                    // Update jumlah_gagal_login menjadi 0 di tabel user
                    $query_update = "UPDATE user SET jumlah_gagal_login = 0 WHERE id_user = '{$data['id_user']}'";
                    mysqli_query($koneksi2, $query_update);
                    
                    $query_status_klik = "UPDATE user_status 
                                          SET status_klik_active = status_klik_active + 1 
                                          WHERE id_user_status = '$id_user_status'";
                    mysqli_query($koneksi2, $query_status_klik);
                    
                } else {
                    // Perangkat tidak online
                    header("Location: logout.php");
                    exit();
                } 
            } else {
                // Token tidak valid
                header("Location: logout.php");
                exit();
            }
        } else {
          // Token tidak valid
          header("Location: logout.php");
          exit();
        }
    ?>
    <script>
        window.onload = function() {
            // Cek apakah elemen dengan ID 'countdown' ada di halaman
            var countdownElement = document.getElementById("countdown");
            if (countdownElement) {
                // Set the date we're counting down to (30 minutes from now)
                var countDownDate = new Date().getTime() + (30 * 60 * 1000);

                // Update the count down every 1 second
                var x = setInterval(function() {

                    // Get today's date and time
                    var now = new Date().getTime();

                    // Find the distance between now and the count down date
                    var distance = countDownDate - now;

                    // Time calculations for minutes and seconds
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Pad the numbers with leading zeros if they are less than 10
                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;

                    // Output the result in the element with id="countdown"
                    countdownElement.innerHTML = minutes + ":" + seconds;

                    // If the count down is over, write some text and redirect
                    if (distance < 0) {
                        clearInterval(x);
                        window.location.href = 'logout.php';
                    }
                }, 1000);
            } else {
                console.log("Element with id 'countdown' not found. Countdown will not start.");
            }
        };
    </script>
</body>

</html>