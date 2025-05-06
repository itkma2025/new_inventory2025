<?php
  session_start();

  include "koneksi.php";
  date_default_timezone_set('Asia/Jakarta');
  $datetime = date('d/m/Y H:i:s');

  $id_status = base64_decode($_GET['id_off']);

  $connect->begin_transaction();
  try{
    $update_status_user = mysqli_query($connect, "UPDATE user_status SET logout_time = '$datetime', status_perangkat = 'Offline' WHERE id_user_status = '$id_status'");

    $update_history = mysqli_query($connect, "UPDATE user_history SET logout_time = '$datetime' WHERE id_user_status = '$id_status'");
      

    if ($update_status_user && $update_history) {
      $connect->commit();
      $_SESSION['info'] = 'Dilogout';
      header("Location:data-user.php");
    } else {
        // Rollback transaksi jika salah satu operasi gagal
        $connect->rollback();
        $_SESSION['info'] = 'Terjadi kesalahan server, silahkan ulangi kembali';
        header("Location:data-user.php");
    }   
  }catch (Exception $e){
      // Rollback transaksi jika salah satu operasi gagal
      $connect->rollback();
      $_SESSION['info'] = 'Terjadi kesalahan server, silahkan ulangi kembali';
      header("Location:data-user.php");
  }  
?>