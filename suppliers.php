<?php
    session_start();
    if(!isset($_SESSION['user'])) { header('location: ./login'); }

    require_once('./config/Database.php');
    require_once('./models/Permissions.php');
    require_once('./models/Suppliers.php');

    $P = Permissions::getUserPermissions(new Database(),$_SESSION['user']['id'])['data'];
    if(!$P['suppliers']['view']) { header('location: ./'); }

    // Create
    if (isset($_POST['Create'])) {
        if (empty($_POST['supplier_name'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $notify = Suppliers::Create(new Database(),$_SESSION['user']['id'],$_POST);
        }
    }

    // Edit
    if (isset($_POST['Edit'])) {
        if (empty($_POST['supplier_name']) || empty($_POST['id'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $notify = Suppliers::Edit(new Database(),$_SESSION['user']['id'],$_POST);
        }
    }

    // Delete
    if (isset($_POST['Delete'])) {
        if (empty($_POST['id'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $notify = Suppliers::Delete(new Database(),$_SESSION['user']['id'],$_POST);
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="./assets/dist/img/icons8_bill_80px.png" type="image/x-icon">
  <title>GRN & Billing | Suppliers</title>
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
            <h1 class="m-0 text-dark">Suppliers</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active">Suppliers</li>
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
                            <h3 class="card-title">All Suppliers</h3>
                            <div class="card-tools">
                                <?php if($P['suppliers']['create']) { ?>
                                <button type="button" class="btn btn-block btn-primary btn-sm" data-toggle="modal" data-target="#addModal">Add Suppliers</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:10%;">SI</th>
                                        <th>Supplier's Name</th>
                                        <?php if($P['suppliers']['modify']) { ?>
                                        <th style="width:30%;">Action</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $F = Suppliers::FetchAll(new Database());
                                        if ($F['success']) {
                                            $c = 1;
                                            foreach ($F['data'] as $k => $v) {
                                    ?>
                                    <tr>
                                        <td><?php echo $c++; ?></td>
                                        <td><?php echo $v['name']; ?></td>
                                        <?php if($P['suppliers']['modify']) { ?>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info editModal" data-toggle="modal" data-target="#editModal" data-id="<?php echo $v['id']; ?>" data-name="<?php echo $v['name']; ?>"><i class="fas fa-edit"></i></button>
                                                <button type="button" class="btn btn-danger deleteModal" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $v['id']; ?>" data-name="<?php echo $v['name']; ?>"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                        <?php } ?>
                                    </tr>
                                    <?php }} ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>SI</th>
                                        <th>Supplier's Name</th>
                                        <?php if($P['suppliers']['modify']) { ?>
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

<?php if($P['suppliers']['create']) { ?>
<!-- Add Modal -->
<div class="modal fade" id="addModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" role="form">
                <div class="modal-header">
                    <h4 class="modal-title">New Supplier</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="supplier_name">Supplier's Name</label>&nbsp;<small class="text-danger"><em>*</em></small>
                        <input type="text" class="form-control" name="supplier_name" placeholder="Enter Name" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" name="Create" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<?php } ?>

<?php if($P['suppliers']['modify']) { ?>
<!-- Edit Modal -->
<div class="modal fade" id="editModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" role="form">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Supplier</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-group">
                        <label for="supplier_name">Supplier's Name</label>&nbsp;<small class="text-danger"><em>*</em></small>
                        <input type="text" class="form-control" id="edit_name" name="supplier_name" placeholder="Enter Name" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                    <h4 class="modal-title">Delete Supplier</h4>
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
<!-- AdminLTE App -->
<script src="./assets/dist/js/adminlte.js"></script>
<!-- Notification -->
<?php require_once('templates/notify.php'); ?>
<!-- page script -->
<script>
  $(function () {
    $("#example1").DataTable();

    $('.editModal').click(function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
    })

    $('.deleteModal').click(function() {
        $('#delete_id').val($(this).data('id'));
        $('#delete_name').text($(this).data('name'));
    })
  });
</script>
</body>
</html>
