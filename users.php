<?php
    session_start();
    if(!isset($_SESSION['user'])) { header('location: ./login'); }

    require_once('./config/Database.php');
    require_once('./models/Permissions.php');
    require_once('./models/Users.php');
    require_once('./models/Groups.php');

    $P = Permissions::getUserPermissions(new Database(),$_SESSION['user']['id'])['data'];
    if(!$P['users']['view']) { header('location: ./'); }

    // Create
    if (isset($_POST['Create'])) {
        if (empty($_POST['group_id']) || empty($_POST['name']) || empty($_POST['username']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } elseif ($_POST['password'] != $_POST['confirm_password']) {
            $notify = array('msg'=>["Password Mismatch!"],'success'=>false);
        } else {
            $_POST['active'] = (isset($_POST['active']))?1:0;
            $notify = Users::Create(new Database(),$_SESSION['user']['id'],$_POST,$_FILES['image']);
        }
    }

    // Edit
    if (isset($_POST['Edit'])) {
        if (empty($_POST['group_id']) || empty($_POST['name']) || empty($_POST['username']) || empty($_POST['id'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } elseif (!empty($_POST['password']) && ($_POST['password'] != $_POST['confirm_password'])) {
            $notify = array('msg'=>["Password Mismatch!"],'success'=>false);
        } else {
            $_POST['active'] = (isset($_POST['active']))?1:0;
            $notify = Users::Edit(new Database(),$_SESSION['user']['id'],$_POST,$_FILES['image']);
        }
    }

    // Delete
    if (isset($_POST['Delete'])) {
        if (empty($_POST['id'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $notify = Users::Delete(new Database(),$_SESSION['user']['id'],$_POST);
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="./assets/dist/img/icons8_bill_80px.png" type="image/x-icon">
  <title>GRN & Billing | Users</title>
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
            <h1 class="m-0 text-dark">Users</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active">Users</li>
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
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">All Users</h3>
                            <div class="card-tools">
                                <?php if($P['users']['create']) { ?>
                                <button type="button" class="btn btn-block btn-primary btn-sm" data-toggle="modal" data-target="#addModal">Add User</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:10%;">SI</th>
                                        <th style="width:20%;">Group</th>
                                        <th style="width:20%;">Name</th>
                                        <th style="width:20%;">Username</th>
                                        <th style="width:10%;">Status</th>
                                        <?php if($P['users']['modify']) { ?>
                                        <th style="width:20%;">Action</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $F = Users::FetchAll(new Database());
                                        if ($F['success']) {
                                            $c = 1;
                                            foreach ($F['data'] as $k => $v) {
                                    ?>
                                    <tr>
                                        <td><?php echo $c++; ?></td>
                                        <td><?php echo $v['groupName']; ?></td>
                                        <td><?php echo $v['name']; ?></td>
                                        <td><?php echo $v['username']; ?></td>
                                        <td class="text-center"><span class="badge badge-<?php echo (intval($v['active']))? 'success' : 'danger'; ?>"><?php echo (intval($v['active']))? 'Active' : 'In-Active'; ?></span></td>
                                        <?php if($P['users']['modify']) { ?>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info editModal" data-toggle="modal" data-target="#editModal" data-id="<?php echo $v['id']; ?>" data-active="<?php echo $v['active']; ?>" data-group="<?php echo $v['groupId']; ?>" data-name="<?php echo $v['name']; ?>" data-username="<?php echo $v['username']; ?>" ><i class="fas fa-edit"></i></button>
                                                <button type="button" class="btn btn-danger deleteModal" data-toggle="modal" data-target="#deleteModal"  data-id="<?php echo $v['id']; ?>" data-name="<?php echo $v['name']; ?>"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                    <?php }} ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>SI</th>
                                        <th>Group</th>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <?php if($P['users']['modify']) { ?>
                                        <th>Action</th>
                                        <?php } ?>
                                    </tr>
                                </tfoot>
                            </table>
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

<?php if($P['users']['create']) { ?>
<!-- Add Modal -->
<div class="modal fade" id="addModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" role="form" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title">New User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="group">Group</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <select class="form-control" id="" name="group_id" required>
                                    <option value="">-- Select Group --</option>
                                    <?php
                                        foreach (Groups::FetchAll(new Database())['data'] as $grp) {
                                    ?>
                                    <option value="<?php echo $grp['id']; ?>"><?php echo $grp['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Full Name</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <input type="text" class="form-control" id="" name="name" placeholder="Enter Full Name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="image">Profile Picture</label>&nbsp;<small><em>(Optional)</em></small>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="" name="image">
                                        <label class="custom-file-label" for="image">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="username">Username</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <input type="text" class="form-control" id="" name="username" placeholder="Enter Username" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="password">Password</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <input type="password" class="form-control" id="" name="password" placeholder="Enter Password" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <input type="password" class="form-control" id="" name="confirm_password" placeholder="Re-Enter Password" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="checkbox" name="active" checked data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Active" data-off-text="In-Active">
                    <button type="submit" name="Create" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<?php } ?>

<?php if($P['users']['modify']) { ?>
<!-- Edit Modal -->
<div class="modal fade" id="editModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" role="form" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title">Edit User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="group">Group</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <select class="form-control" id="edit_group" name="group_id" required>
                                    <option value="">-- Select Group --</option>
                                    <?php
                                        foreach (Groups::FetchAll(new Database())['data'] as $grp) {
                                    ?>
                                    <option value="<?php echo $grp['id']; ?>"><?php echo $grp['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name">Full Name</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <input type="text" class="form-control" id="edit_name" name="name" placeholder="Enter Full Name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="image">Profile Picture</label>&nbsp;<small><em>(Optional)</em></small>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="" name="image">
                                        <label class="custom-file-label" for="image">Choose file</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="username">Username</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <input type="text" class="form-control" id="edit_username" name="username" placeholder="Enter Username" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="password">Password</label>&nbsp;<small><em>(Optional)</em></small>
                                <input type="password" class="form-control" name="password" placeholder="Enter Password">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" name="confirm_password" placeholder="Re-Enter Password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="checkbox" id="edit_active" name="active" checked data-bootstrap-switch data-off-color="danger" data-on-color="success" data-on-text="Active" data-off-text="In-Active">
                    <button type="submit" name="Edit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" role="form">
                <div class="modal-header">
                    <h4 class="modal-title">Delete User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="delete_id" name="id">
                    <p class="h5">Are you sure, you wnat to remove <strong id="delete_name"></strong>?</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="Delete" class="btn btn-primary">Delete</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<?php } ?>

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
    $("#example1").DataTable();

    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

    $('.editModal').click(function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_username').val($(this).data('username'));
        $(`#edit_group > option[value='${$(this).data('group')}']`).prop('selected', true)
        $('#edit_active').bootstrapSwitch('state', parseInt($(this).data('active')));
    })

    $('.deleteModal').click(function() {
        $('#delete_id').val($(this).data('id'));
        $('#delete_name').text($(this).data('name'));
    })
  });
</script>
</body>
</html>