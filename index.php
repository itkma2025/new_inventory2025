<?php
  include "akses.php";

  if($user_role == 'Finance'){
    header("Location: finance/dashboard.php");
  } else if ($user_role == 'Driver'){
      header("Location: driver/dashboard.php");
  } else if ($user_role == 'Admin Gudang'){
      header("Location: dashboard.php");
  } else if ($user_role == 'Operator Gudang'){  
      header("Location: scan-qr.php");
  } else {
      header("Location: dashboard.php");
  }

  echo $user_role;
?>