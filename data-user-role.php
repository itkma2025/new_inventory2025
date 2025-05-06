<?php
$page = 'role-user';
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
</head>

<body>
  <!-- nav header -->
  <?php include "page/nav-header.php" ?>
  <!-- end nav header -->

  <!-- sidebar  -->
  <?php include "page/sidebar.php"; ?>
  <!-- end sidebar -->

  <!-- SWEET ALERT -->
  <div class="info-data" data-infodata="<?php if (isset($_SESSION['info'])) {
                                          echo $_SESSION['info'];
                                        }
                                        unset($_SESSION['info']); ?>"></div>
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
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section dashboard">
      <div class="container-fluid">
        <div class="card shadow">
          <div class="container-fluid">
            <div class="card-header bg-body mb-2 text-center">
              <h5>Role User</h5>
            </div>
            <div class="card-body rounded-3">
              <!-- Button trigger modal -->
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-user-role">
                <i class="bi bi-plus-circle"> Tambah data user role</i>
              </button>
              <div class="table-responsive mt-3">
                <table class="table table-hover table-striped table-bordered" id="table1">
                  <thead>
                    <tr class="text-white" style="background-color: #051683;">
                      <td class="col-1 text-center p-3">No</td>
                      <td class="col-6 text-center p-3">Hak Akses</td>
                      <td class="col-2 text-center p-3">Tanggal Buat</td>
                      <td class="col-2 text-center p-3">Aksi</td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    date_default_timezone_set('Asia/Jakarta');
                    include "koneksi.php";
                    $no = 1;
                    $sql = "SELECT * FROM user_role ";
                    $query = mysqli_query($connect, $sql) or die(mysqli_error($connect));;
                    while ($data = mysqli_fetch_array($query)) {
                    ?>
                      <tr>
                        <td class="text-center"><?php echo $no; ?></td>
                        <td><?php echo $data['role']; ?></td>
                        <td class="text-center"><?php echo date($data['created_date']); ?></td>
                        <td class="text-center">
                          <button name="edit-data" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit" data-id="<?php echo $data['id_user_role'] ?>" data-role="<?php echo $data['role'] ?>">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <a href="proses/proses-role.php?hapus-role=<?php echo $data['id_user_role'] ?>" class="btn btn-danger btn-sm delete-data"><i class="bi bi-trash"></i></a>
                        </td>
                        <!-- Modal Edit Role -->
                        <div class="modal fade" data-backdrop="static" tabindex="-1" role="dialog" id="edit">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Data Role</h1>
                              </div>
                              <form action="proses/proses-role.php" method="POST">
                                <div class="modal-body">
                                  <div class="mb-3">
                                    <label class="form-label">Role User</label>
                                    <input type="hidden" class="form-control" name="id_user_role" id="id_role">
                                    <input type="text" class="form-control" name="role" id="role" required>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="submit" name="edit-role" class="btn btn-primary"><i class="bx bx-save"></i> Ubah Data</button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        <!-- end modal Edit Role -->
                      </tr>
                      <?php $no++; ?>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- Modal Input Role User -->
  <div class="modal fade" id="modal-user-role" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Role</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="proses/proses-role.php" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <?php
              $UUID = generate_uuid();
              ?>
              <label class="form-label">Role User</label>
              <input type="hidden" class="form-control" name="id_user_role" value="RL<?php echo $UUID; ?>">
              <input type="text" class="form-control" name="role" required>
              <input type="hidden" class="form-control" name="created" value="<?php echo date('d/m/Y, G:i') ?>">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="simpan-role" class="btn btn-primary"><i class="bx bx-save"></i> Simpan Data</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- End Modal Input Role User -->

  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <?php include "page/script.php" ?>
</body>

</html>
<?php
function generate_uuid()
{
  return sprintf(
    '%04x%04x%04x',
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0x0fff) | 0x4000,
    mt_rand(0, 0x3fff) | 0x8000,
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff),
    mt_rand(0, 0xffff)
  );
}

?>

<script>
  $('#edit').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var role = button.data('role');
    var modal = $(this);
    modal.find('.modal-body #id_role').val(id);
    modal.find('.modal-body #role').val(role);
  })
</script>