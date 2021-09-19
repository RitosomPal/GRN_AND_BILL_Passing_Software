<?php
    session_start();
    if(isset($_SESSION['user'])) { header('location: ./'); }

    include_once('./config/Database.php');

    if(isset($_POST['Recover'])) {
        if (empty($_POST['password']) || $_FILES['file']['size'] < 1) {
            $notify = array('msg'=>['Insert all required data.'], 'success'=>false);
        } elseif (md5($_POST['password']) != "6ad3e1c21c6769a0743c5b9884395c2f") {
            $notify = array('msg'=>['Invalid Password.'], 'success'=>false);
        } else {
            $db = new Database;
            $notify = $db->Recover($_FILES['file']);
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="./assets/dist/img/icons8_bill_80px.png" type="image/x-icon">
  <title>GRN & Billing | Upload Backup</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="./assets/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="./assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="./assets/dist/css/adminlte.min.css">
  <!-- Toastr -->
  <link rel="stylesheet" href="./assets/plugins/toastr/toastr.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="./login"><b>GRN & Billing</b>&nbsp;Software</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Upload backup to continue</p>

      <form method="post" enctype="multipart/form-data">
        <div class="input-group mb-3">
            <div class="custom-file">
                <input type="file" class="custom-file-input" name="file" accept=".db" id="exampleInputFile">
                <label class="custom-file-label" for="exampleInputFile">Backup file</label>
            </div>
            <div class="input-group-append">
                <span class="input-group-text" id=""><i class="fas fa-database"></i></span>
            </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <p class="mb-1">
              <a href="login">Login</a>
            </p>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" name="Recover" class="btn btn-primary btn-block">Recover</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="./assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="./assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Toastr -->
<script src="./assets/plugins/toastr/toastr.min.js"></script>
<!-- AdminLTE App -->
<script src="./assets/dist/js/adminlte.min.js"></script>
<!-- Notification -->
<?php require_once('templates/notify.php'); ?>
</body>
</html>
