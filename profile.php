<?php
    session_start();
    if(!isset($_SESSION['user'])) { header('location: ./login'); }

    include_once('./config/Database.php');
    include_once('./models/Users.php');

    if (isset($_POST['Upload'])) {
        if ($_FILES['image']['size'] == 0) {
            $notify = array('msg'=>["Select an Image First!"],'success'=>false);
        } else {
            $notify = Users::Upload(new Database(),$_SESSION['user']['id'],$_FILES['image']);
        }
    }

    if (isset($_POST['Update'])) {
        if (empty($_POST['password']) || empty($_POST['confirm_password'])) {
            $notify = array('msg'=>["Insert Required Data!"],'success'=>false);
        } elseif ($_POST['password'] != $_POST['confirm_password']) {
            $notify = array('msg'=>["Password Mismatch!"],'success'=>false);
        } else {
            $notify = Users::Update(new Database(),$_SESSION['user']['id'],$_POST);
        }
    }

    $U = Users::FetchInfo(new Database(), $_SESSION['user']['id']);
    if($U['success']) { $U = $U['data']; } else { $notify = $U; }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="./assets/dist/img/icons8_bill_80px.png" type="image/x-icon">
  <title>GRN & Billing | Profile</title>
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
            <h1 class="m-0 text-dark">Profile</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active">Profile</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-4 text-center">
                    <div class="card card-info card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" src="./assets/images/<?php if(empty($IMG)){echo 'user.png';}else{echo $IMG;} ?>" alt="User profile picture">
                            </div>
                            <h3 class="profile-username text-center mt-3"><?php echo $U['name']; ?></h3>
                            <p class="text-muted text-center"><?php echo $U['groupName']; ?></p>
                            <p>
                                <small style="float:left;">Last Access:</small>
                                <small class="float-right">
                                    <em>
                                        <?php echo (isset($U['lastAccess']))?$U['lastAccess']:'Never'; ?>
                                    </em>
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="card card-info card-outline">
                        <div class="card-body">
                            <form role="form" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="exampleInputFile">Profile Picture</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="image" class="custom-file-input" id="exampleInputFile" accept="image/x-png,image/jpg,image/jpeg,image/webp">
                                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                                        </div>
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text" name="Upload">Upload</button>
                                            <!-- <span >Upload</span> -->
                                        </div>
                                    </div>
                                </div>
                                
                            </form>
                            <form role="form" method="post">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Password</label>
                                            <input type="password" class="form-control" id="exampleInputPassword1" name="password" placeholder="Password">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Confirm Password</label>
                                            <input type="password" class="form-control" id="exampleInputPassword1" name="confirm_password" placeholder="Password">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="Update" class="btn btn-primary">Update</button>
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
<!-- AdminLTE App -->
<script src="./assets/dist/js/adminlte.js"></script>
<!-- Notification -->
<?php require_once('templates/notify.php'); ?>
</body>
</html>
