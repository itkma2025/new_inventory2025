<?php
$page = 'data-user';
include "akses.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Inventory KMA</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <?php include "page/head.php"; ?>
  <style>
    .table{
      width: 100% !important;
    }
  </style>
</head>

<body>
  <!-- nav header -->
  <?php include "page/nav-header.php" ?>
  <!-- end nav header -->

  <!-- sidebar  -->
  <?php include "page/sidebar.php"; ?>
  <!-- end sidebar -->

  <!-- SWEET ALERT -->
  <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) { echo $_SESSION['info']; } unset($_SESSION['info']); ?>"></div>
  <!-- END SWEET ALERT -->

  <main id="main" class="main">
    <!-- Loading -->
    <div class="loader loader">
      <div class="loading">
        <img src="img/loading.gif" width="200px" height="auto">
      </div>
    </div>
    <!-- ENd Loading -->
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Data User</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section dashboard">
      <div class="container-fluid">
        <div class="card shadow">
          <div class="container-fluid p-3">
            <div class="card-body rounded-3">
              <!-- Pills Tabs -->
              <ul class="nav nav-pills mb-3 ms-3" id="pills-tab" role="tablist">
                <li class="nav-item me-4" role="presentation">
                  <button type="button" class="btn btn-outline-primary position-relative active" id="user-tab" data-bs-toggle="pill" data-bs-target="#user">
                    Data User
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" id="totalUser">
                      <span class="visually-hidden"></span>
                    </span>
                  </button>
                </li>
                <li class="nav-item  me-4" role="presentation">
                  <button type="button" class="btn btn-outline-primary position-relative" id="user-active-tab" data-bs-toggle="pill" data-bs-target="#user-active">
                    User active
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" id="totalUserActive">
                      <span class="visually-hidden"></span>
                    </span>
                  </button>
                </li>
                <li class="nav-item  me-4" role="presentation">
                  <button type="button" class="btn btn-outline-primary position-relative" id="user-request-tab" data-bs-toggle="pill" data-bs-target="#user-request">
                    Data request user
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" id="totalUserRequest">
                      <span class="visually-hidden"></span>
                    </span>
                  </button>
                </li>
              </ul>
              <div class="tab-content pt-2" id="myTabContent">
                <div class="tab-pane fade show active" id="user" role="tabpanel" aria-labelledby="home-tab">
                  <a href="registrasi-user.php" class="btn btn-primary ms-3">
                    <i class="bi bi-plus-circle"> Tambah data user</i>
                  </a>
                  <!-- Table User -->
                  <div class="table-responsive mt-3">
                    <table class="table table-hover table-striped table-bordered" id="table1">
                      <thead>
                        <tr class="text-white" style="background-color: #051683;">
                          <td class="text-center p-3" style="width: 60px;">No</td>
                          <td class="text-center p-3" style="width: 200px;">Nama User</td>
                          <td class="text-center p-3" style="width: 200px;">Email</td>
                          <td class="text-center p-3" style="width: 200px;">Username</td>
                          <td class="text-center p-3" style="width: 150px;">Role</td>
                          <td class="text-center p-3" style="width: 120px;">Tgl. Approval</td>
                          <td class="text-center p-3" style="width: 120px;">Approval by</td>
                          <td class="text-center p-3" style="width: 100px;">Aksi</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        date_default_timezone_set('Asia/Jakarta');
                        include "koneksi.php";
                        $no = 1;
                        $sql = "SELECT 
                                  u.id_user, u.nama_user, u.email, u.username, u.tgl_approval, u.approval_by, 
                                  ur.id_user_role, ur.role 
                                FROM user AS u 
                                LEFT JOIN user_role AS ur ON (u.id_user_role = ur.id_user_role)
                                WHERE approval = 1 ORDER BY nama_user ASC";
                        $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));
                        $total_user = mysqli_num_rows($query);
                        while ($data = mysqli_fetch_array($query)) {
                        ?>
                          <tr>
                            <td class="text-center"><?php echo $no; ?></td>
                            <td class="text-nowrap"><?php echo $data['nama_user']; ?></td>
                            <td class="text-nowrap"><?php echo $data['email']; ?></td>
                            <td class="text-nowrap"><?php echo $data['username']; ?></td>
                            <td class="text-center"><?php echo $data['role']; ?></td>
                            <td class="text-nowrap text-center "><?php echo $data['tgl_approval']; ?></td>
                            <td class="text-nowrap "><?php echo $data['approval_by']; ?></td>
                            <td class="text-nowrap text-center">
                              <a href="" name="edit-user" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit-user<?php echo $data['id_user'] ?>"><i class="bi bi-pencil"></i></a>
                              <a href="proses/proses-user.php?hapus-user=<?php echo $data['id_user'] ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                            </td>
                          </tr>
                          <?php $no++; ?>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="tab-pane fade" id="user-active" role="tabpanel" aria-labelledby="profile-tab">
                  <!-- Table User Aktive -->
                  <div class="table-responsive mt-3">
                    <table class="table table-hover table-striped table-bordered" id="tableExport">
                      <thead>
                        <tr class="text-white" style="background-color: #051683;">
                          <td class="text-center text-nowrap p-3" style="width: 50px;">No</td>
                          <td class="text-center text-nowrap p-3" style="width: 200px;">Nama User</td>
                          <td class="text-center text-nowrap p-3" style="width: 100px;">Role</td>
                          <td class="text-center text-nowrap p-3" style="width: 100px;">Waktu Login</td>
                          <td class="text-center text-nowrap p-3" style="width: 150px;">Ip Address</td>
                          <td class="text-center text-nowrap p-3" style="width: 150px;">Jenis Perangkat</td>
                          <td class="text-center text-nowrap p-3" style="width: 150px;">Lokasi</td>
                          <td class="text-center text-nowrap p-3" style="width: 100px;">Aksi</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        date_default_timezone_set('Asia/Jakarta');
                        include "koneksi.php";
                        $id_status = base64_decode($_SESSION['id_status']);
                        $no = 1;
                        $sql_active = " SELECT 
                                          us.id_user_status, us.login_time, us.jenis_perangkat, us.status_perangkat,
                                          uh.ip_login, uh.os, uh.lokasi,  
                                          u.nama_user,
                                          ur.role
                                        FROM user_status AS us 
                                        LEFT JOIN user u ON (us.id_user = u.id_user)
                                        LEFT JOIN user_history uh ON (us.id_user_status = uh.id_user_status)
                                        LEFT JOIN user_role ur ON (u.id_user_role = ur.id_user_role)
                                        WHERE status_perangkat = 'Online'";
                        $query_active = mysqli_query($connect, $sql_active) or die(mysqli_error($connect));
                        $total_user_active = mysqli_num_rows($query_active);
                        while ($data_active = mysqli_fetch_array($query_active)) {
                        ?>
                          <tr>
                            <td class="text-nowrap text-center"><?php echo $no; ?></td>
                            <td class="text-nowrap"><?php echo $data_active['nama_user']; ?></td>
                            <td class="text-nowrap"><?php echo $data_active['role']; ?></td>
                            <td class="text-nowrap text-center"><?php echo $data_active['login_time']; ?></td>
                            <td class="text-center"><?php echo $data_active['ip_login']; ?></td>
                            <td class="text-nowrap"><?php echo $data_active['jenis_perangkat']; ?></td>
                            <td class="text-nowrap"><?php echo $data_active['lokasi']; ?></td>
                            <td class="text-nowrap text-center">
                              <?php  
                                if ($id_status === $data_active['id_user_status']) {
                                  
                                }else{
                                  ?>
                                    <button class="btn btn-danger btn-sm" title="Ubah Status" data-bs-toggle="modal" data-bs-target="#off" data-id="<?php echo base64_encode($data_active['id_user_status']); ?>" data-nama="<?php echo $data_active['nama_user']; ?>" data-ip="<?php echo $data_active['ip_login']; ?>"><i class="bi bi-x-circle-fill"></i></button>
                                  <?php
                                }
                              ?>
                            </td>
                          </tr>
                          <?php $no++; ?>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="tab-pane fade" id="user-request" role="tabpanel" aria-labelledby="profile-tab">
                  <!-- Table User Aktive -->
                  <div class="table-responsive mt-3">
                    <table class="table table-hover table-striped table-bordered" id="tableExport2">
                      <thead>
                        <tr class="text-white" style="background-color: #051683;">
                          <td class="text-center p-3" style="width: 60px;">No</td>
                          <td class="text-center p-3" style="width: 200px;">Nama User</td>
                          <td class="text-center p-3" style="width: 200px;">Email</td>
                          <td class="text-center p-3" style="width: 200px;">Username</td>
                          <td class="text-center p-3" style="width: 150px;">Role</td>
                          <td class="text-center p-3" style="width: 150px;">Tgl. Verifikasi</td>
                          <td class="text-center p-3" style="width: 100px;">Aksi</td>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        date_default_timezone_set('Asia/Jakarta');
                        include "koneksi.php";
                        $no = 1;
                        $sql_request = "SELECT 
                                          u.id_user, u.nama_user, u.email, u.username, u.tgl_verifikasi,
                                          ur.id_user_role, ur.role
                                        FROM user AS u 
                                        JOIN user_role AS ur ON (u.id_user_role = ur.id_user_role)
                                        WHERE approval = 0 ORDER BY tgl_verifikasi DESC";
                        $query_request = mysqli_query($connect, $sql_request) or die(mysqli_error($connect));
                        $total_user_request = mysqli_num_rows($query_request);
                        while ($data_request = mysqli_fetch_array($query_request)) {
                        ?>
                          <tr>
                            <td class="text-nowrap text-center"><?php echo $no; ?></td>
                            <td class="text-nowrap"><?php echo $data_request['nama_user']; ?></td>
                            <td class="text-nowrap"><?php echo $data_request['email']; ?></td>
                            <td class="text-nowrap"><?php echo $data_request['username']; ?></td>
                            <td class="text-nowrap"><?php echo $data_request['role']; ?></td>
                            <td class="text-nowrap text-center">
                              <?php 
                                if($data_request['tgl_verifikasi'] != '') {
                                  echo $data_request['tgl_verifikasi']; 
                                } else {
                                  echo "Belum Verifikasi"; 
                                }
                              ?>
                            </td>
                            <td class="text-nowrap text-center">
                              <button class="btn btn-success btn-sm" title="Terima" data-bs-toggle="modal" data-bs-target="#terima" data-id="<?php echo $data_request['id_user']; ?>" data-nama="<?php echo $data_request['nama_user']; ?>"><i class="bi bi-check-circle-fill"></i></button>
                              <button class="btn btn-danger btn-sm" title="Tolak" data-bs-toggle="modal" data-bs-target="#tolak" data-bs-target="#terima" data-id="<?php echo $data_request['id_user']; ?>" data-nama="<?php echo $data_request['nama_user']; ?>"><i class="bi bi-x-circle"></i></button>
                            </td>
                          </tr>
                          <?php $no++; ?>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div><!-- End Pills Tabs -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</html>
<!-- Modal Tolak-->
<div class="modal fade" id="off" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Konfirmasi</h1>
      </div>
      <div class="modal-body">
        <form action="logout-paksa.php" method="GET">
          <input type="hidden" id="id_user_off" name="id_off">
          Apakah Anda yakin ingin logout <b id="nama_user_off"></b> dengan IP <b id="ip_off"></b> ?
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">Ya, lanjutkan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Terima -->
<div class="modal fade" id="terima" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Konfirmasi</h1>
      </div>
      <div class="modal-body">
        <form action="proses/proses-user.php" method="POST">
          <input type="hidden" id="id_user" name="id_user">
          Apakah anda yakin ingin menerima permintaan <b id="nama_user"></b>?
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="acc-user">Ya, terima</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tolak-->
<div class="modal fade" id="tolak" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Konfirmasi</h1>
      </div>
      <div class="modal-body">
        <form action="proses/proses-user.php" method="POST">
          <input type="hidden" id="id_user" name="id_user">
          Apakah anda yakin ingin menolak permintaan <b id="nama_user"></b>?
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" name="tolak-user">Ya, tolak</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  $('#off').on('show.bs.modal', function(event) {
      // Mendapatkan data dari tombol yang ditekan
      var button = $(event.relatedTarget);
      var id = button.data('id');
      var nama = button.data('nama');
      var ip = button.data('ip');
 
      // Membuat Variable untuk menampilkan data
      var modal = $(this);
      var idInput = modal.find('.modal-body #id_user_off');
      var namaInput = modal.find('.modal-body #nama_user_off');
      var ipInput = modal.find('.modal-body #ip_off');
    
      // Menampilkan data
      idInput.val(id);
      namaInput.text(nama); 
      ipInput.text(ip); 
  });

  $('#terima').on('show.bs.modal', function(event) {
      // Mendapatkan data dari tombol yang ditekan
      var button = $(event.relatedTarget);
      var id = button.data('id');
      var nama = button.data('nama');
 
      // Membuat Variable untuk menampilkan data
      var modal = $(this);
      var idInput = modal.find('.modal-body #id_user');
      var namaInput = modal.find('.modal-body #nama_user');
    
      // Menampilkan data
      idInput.val(id);
      namaInput.text(nama); 
  });

  $('#tolak').on('show.bs.modal', function(event) {
      // Mendapatkan data dari tombol yang ditekan
      var button = $(event.relatedTarget);
      var id = button.data('id');
      var nama = button.data('nama');
 
      // Membuat Variable untuk menampilkan data
      var modal = $(this);
      var idInput = modal.find('.modal-body #id_user');
      var namaInput = modal.find('.modal-body #nama_user');
    
      // Menampilkan data
      idInput.val(id);
      namaInput.text(nama); 
  });
</script>
<script>
  // Buat Variable
  var total_user = "<?php echo $total_user ?>";
  var total_user_active = "<?php echo $total_user_active ?>";
  var total_user_request = "<?php echo $total_user_request ?>";

  // Fungsi untuk menampilkan atau menyembunyikan elemen berdasarkan nilai
  function updateSpan(spanId, value) {
    var spanElement = document.getElementById(spanId);
    if (value == 0) {
      spanElement.style.display = 'none';
    } else {
      spanElement.textContent = value;
    }
  }

  // Tampilkan atau sembunyikan span berdasarkan nilai
  updateSpan('totalUser', total_user);
  updateSpan('totalUserActive', total_user_active);
  updateSpan('totalUserRequest', total_user_request);
</script>
