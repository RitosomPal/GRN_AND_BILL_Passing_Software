<?php
    session_start();
    if(!isset($_SESSION['user'])) { header('location: ./login'); }

    require_once('./config/Database.php');
    require_once('./models/Permissions.php');
    include_once('./models/Users.php');

    $db = new Database;
    $P = Permissions::getUserPermissions($db,$_SESSION['user']['id'])['data'];
    if(!$P['groups']['view']) { header('location: ./'); }
    // Import
    if(isset($_POST['Import'])) {
        if (empty($_POST['password']) || $_FILES['file']['size'] < 1) {
            $notify = array('msg'=>['Insert all required data.'], 'success'=>false);
        } else {
            $notify = Users::Login(new Database(), array('username'=>'admin','password'=>$_POST['password']));
            if ($notify['success']) {
                $notify = $db->Recover($_FILES['file']);
            } else {
                $notify = array('msg'=>['Invalid Password.'], 'success'=>false);
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="./assets/dist/img/icons8_bill_80px.png" type="image/x-icon">
  <title>GRN & Billing | Import</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="./assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./assets/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="./assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="./assets/plugins/toastr/toastr.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="./assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="./assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <?php require_once('templates/nav.php'); ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <?php require_once('templates/aside.php'); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Import</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">System</a></li>
              <li class="breadcrumb-item active">Import</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row  justify-content-center">
                <div class="col-sm-6">
                    <div class="card card-info card-outline">
                        <div class="card-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-group mb-3">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" name="file" accept=".db" id="exampleInputFile">
                                                <label class="custom-file-label" for="exampleInputFile">Backup file</label>
                                            </div>
                                            <div class="input-group-append">
                                                <span class="input-group-text" id=""><i class="fas fa-database"></i></span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Password</label>
                                            <input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password" required>
                                        </div>
                                        <div>
                                            <button type="submit" name="Import" class="btn btn-primary">Restore</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php require_once('templates/footer.php') ?>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="./assets/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="./assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script> $.widget.bridge('uibutton', $.ui.button) </script>
<!-- Bootstrap 4 -->
<script src="./assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- overlayScrollbars -->
<script src="./assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- Toastr -->
<script src="./assets/plugins/toastr/toastr.min.js"></script>
<!-- DataTables -->
<script src="./assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="./assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="./assets/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="./assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- Bootstrap Switch -->
<script src="./assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- AdminLTE App -->
<script src="./assets/dist/js/adminlte.js"></script>
<!-- Notification -->
<?php require_once('templates/notify.php'); ?>
<!-- page script -->
<script>
   $(function () {
        $('#c3').on('change', function(){
            if(this.checked) {
                $('#c2').prop('checked', true);
            }
        })
        $('#c1').on('change', function(){
            $('#c1').prop('checked', true);
        })
        $('#c2').on('change', function(){
            if(!this.checked && $('#c3').prop('checked')) {
                $('#c2').prop('checked', true);
            }
        })
   })
</script>
</body>
</html>
