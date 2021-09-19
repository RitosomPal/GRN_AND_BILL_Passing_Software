<?php
    session_start();
    if(!isset($_SESSION['user'])) { header('location: ./login'); }

    require_once('./config/Database.php');
    require_once('./models/Permissions.php');
    require_once('./models/Books.php');

    $P = Permissions::getUserPermissions(new Database(),$_SESSION['user']['id'])['data'];
    if(!$P['books']['view']) { header('location: ./'); }

    // Create
    if (isset($_POST['Create'])) {
        if (empty($_POST['book_name']) || empty($_POST['grn_pattern']) || empty($_POST['starting_page']) || empty($_POST['starting_page'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $_POST['active'] = (isset($_POST['active']))?1:0;
            $notify = Books::Create(new Database(),$_SESSION['user']['id'],$_POST);
        }
    }

    // Edit
    if (isset($_POST['Edit'])) {
        if (empty($_POST['book_name']) || empty($_POST['grn_pattern']) || empty($_POST['id'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $_POST['active'] = (isset($_POST['active']))?1:0;
            $notify = Books::Edit(new Database(),$_SESSION['user']['id'],$_POST);
        }
    }

    // Delete
    if (isset($_POST['Delete'])) {
        if (empty($_POST['id'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $notify = Books::Delete(new Database(),$_SESSION['user']['id'],$_POST);
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="./assets/dist/img/icons8_bill_80px.png" type="image/x-icon">
  <title>GRN & Billing | Books</title>
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
            <h1 class="m-0 text-dark">Books</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active">Books</li>
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
                            <h3 class="card-title">All Books</h3>
                            <div class="card-tools">
                                <?php if($P['books']['create']) { ?>
                                <button type="button" class="btn btn-primary btn-sm addModal" data-toggle="modal" data-target="#addModal">Add Book</button>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width:10%;">SI</th>
                                        <th>Book Name</th>
                                        <th style="width:20%;">GRN No. Pattern</th>
                                        <th style="width:20%;">Pages</th>
                                        <th style="width:10%;">Status</th>
                                        <?php if($P['books']['modify']) { ?>
                                        <th style="width:20%;">Action</th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $F = Books::FetchAll(new Database());
                                        if ($F['success']) {
                                            $c = 1;
                                            foreach ($F['data'] as $k => $v) {
                                                $pl = (strlen($v['lastPage']) > 4) ? strlen($v['lastPage']) : 4;
                                    ?>
                                    <tr>
                                        <td><?php echo $c++; ?></td>
                                        <td><?php echo $v['name']; ?></td>
                                        <td>GRN-<?php echo $v['grnIdPattern']; ?><?php echo str_repeat('x',$pl); ?></td>
                                        <td><?php echo str_pad($v['startingPage'], $pl, "0", STR_PAD_LEFT).' - '.str_pad($v['lastPage'], $pl, "0", STR_PAD_LEFT); ?></td>
                                        <td class="text-center"><span class="badge badge-<?php echo (intval($v['active']))? 'success' : 'danger'; ?>"><?php echo (intval($v['active']))? 'Active' : 'In-Active'; ?></span></td>
                                        <?php if($P['books']['modify']) { ?>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info editModal" data-toggle="modal" data-target="#editModal" data-id="<?php echo $v['id']; ?>" data-name="<?php echo $v['name']; ?>" data-pattern="<?php echo $v['grnIdPattern']; ?>" data-sp="<?php echo $v['startingPage']; ?>" data-lp="<?php echo $v['lastPage']; ?>" data-active="<?php echo $v['active']; ?>"><i class="fas fa-edit"></i></button>
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
                                        <th>Book Name</th>
                                        <th>GRN No. Pattern</th>
                                        <th>Pages</th>
                                        <th>Status</th>
                                        <?php if($P['books']['modify']) { ?>
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

<?php if($P['books']['create']) { ?>
<!-- Add Modal -->
<div class="modal fade" id="addModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" role="form">
                <div class="modal-header">
                    <h4 class="modal-title">New Book</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="book">Book Name</label>&nbsp;<small class="text-danger"><em>*</em></small>
                        <input type="text" class="form-control" name="book_name" placeholder="Enter Book Name" required>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <label for="book">Starting Page No.</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <input type="number" class="form-control" id="add_sp" name="starting_page" placeholder="Enter First Page No." min="1" value="1" required>
                            </div>
                            <div class="col-6">
                                <label for="book">Last Page No.</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <input type="number" class="form-control" id="add_lp" name="last_page" placeholder="Enter Last Page No." min="1" value="100" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="book">GRN Number Pattern</label>&nbsp;<small class="text-danger"><em>*</em></small>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">GRN-</span>
                            </div>
                            <input type="text" class="form-control" name="grn_pattern" placeholder="Enter Pattern (ex. A)" required>
                            <div class="input-group-append">
                                <span class="input-group-text dp">xxxx</span>
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

<?php if($P['books']['modify']) { ?>
<!-- Edit Modal -->
<div class="modal fade" id="editModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" role="form">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Book</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="form-group">
                        <label for="book">Book Name</label>&nbsp;<small class="text-danger"><em>*</em></small>
                        <input type="text" class="form-control" id="edit_name" name="book_name" placeholder="Enter Book Name" required>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-6">
                                <label for="book">Starting Page No.</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <input type="number" class="form-control" id="edit_sp" name="starting_page" placeholder="Enter First Page No." min="1" required>
                            </div>
                            <div class="col-6">
                                <label for="book">Last Page No.</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                <input type="number" class="form-control" id="edit_lp" name="last_page" placeholder="Enter Last Page No." min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="book">GRN Number Pattern</label>&nbsp;<small class="text-danger"><em>*</em></small>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">GRN-</span>
                            </div>
                            <input type="text" class="form-control" id="edit_pattern" name="grn_pattern" placeholder="Enter Pattern (ex. A -> GRN-A0000)" required>
                            <div class="input-group-append">
                                <span class="input-group-text dp" id="edp"></span>
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
                    <h4 class="modal-title">Delete Book</h4>
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
      $(this).bootstrapSwitch('state', false);
    });
    toastr.options = {
      "closeButton": true,
      "newestOnTop": true,
      "preventDuplicates": true
    }

    $('#add_sp').change(function() {
        if (parseInt($(this).val()) > parseInt($('#add_lp').val())) {
            $(this).val($('#add_lp').val())
            toastr.warning('Max limit is '+$('#add_lp').val())
        }
    })

    $('#add_lp').change(function() {
        if (parseInt($(this).val()) < parseInt($('#add_sp').val())) {
            $(this).val($('#add_sp').val())
            toastr.warning('Min limit is '+$('#add_sp').val())
        }

        let len = $(this).val().length
        len = (len > 4)?len:4;
        $('.dp').text(''.padStart(len, "x"))
    })

    $('#edit_sp').change(function() {
        if (parseInt($(this).val()) > parseInt($('#edit_lp').val())) {
            $(this).val($('#edit_lp').val())
            toastr.warning('Max limit is '+$('#edit_lp').val())
        }
    })

    $('#edit_lp').change(function() {
        if (parseInt($(this).val()) < parseInt($('#edit_sp').val())) {
            $(this).val($('#edit_sp').val())
            toastr.warning('Min limit is '+$('#edit_sp').val())
        }

        let len = $(this).val().length
        len = (len > 4)?len:4;
        $('.dp').text(''.padStart(len, "x"))
    })

    $('.addModal').click(function() {
        $('#add_sp').val(1)
        $('#add_lp').val(100)
        $('.dp').text(''.padStart(4, "x"))
    })

    $('.editModal').click(function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_pattern').val($(this).data('pattern'));
        $('#edit_sp').val($(this).data('sp'));
        $('#edit_lp').val($(this).data('lp'));
        let len = $(this).data('lp').toString().length
        len = (len > 4)?len:4;
        $('#edp').text(''.padStart(len, "x"));
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
