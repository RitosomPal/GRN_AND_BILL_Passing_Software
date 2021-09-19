<?php
    session_start();
    if(!isset($_SESSION['user'])) { header('location: ./login'); }

    require_once('./config/Database.php');
    require_once('./models/Permissions.php');
    require_once('./models/Books.php');
    require_once('./models/Suppliers.php');
    require_once('./models/Sites.php');
    require_once('./models/Items.php');
    require_once('./models/Units.php');
    require_once('./models/GRN.php');

    $P = Permissions::getUserPermissions(new Database(),$_SESSION['user']['id'])['data'];
    if(!$P['grn']['create']) { header('location: ./'); }

    // Create
    if(isset($_POST['Create']) || isset($_POST['Cancelled'])) {
        if (isset($_POST['Create']) && (empty( $_POST['book']) || empty($_POST['supplier']) || empty($_POST['site'])  || empty($_POST['vehicle'])  || empty($_POST['date']))) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } elseif(isset($_POST['Cancelled']) && empty($_POST['book'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $grn['grnNo'] = $_POST['grnNo'];            unset($_POST['grnNo']);
            $grn['book'] = $_POST['book'];              unset($_POST['book']);
            $grn['supplier'] = $_POST['supplier'];      unset($_POST['supplier']);
            $grn['site'] = $_POST['site'];              unset($_POST['site']);
            $grn['vehicle'] = $_POST['vehicle'];        unset($_POST['vehicle']);
            $grn['date'] = $_POST['date'];              unset($_POST['date']);
            
            if(isset($_POST['Create'])) { 
                $grn['cancelled'] = 0;      unset($_POST['Create']);
            } else { 
                $grn['cancelled'] = 1;      unset($_POST['Cancelled']);
            }
            unset($_POST['id']);

            $notify = GRN::Create(new Database(),$_SESSION['user']['id'],$grn, $_POST);
        }
    }

    // Edit
    if(isset($_POST['Edit']) || isset($_POST['Ecancelled'])) {
        if (isset($_POST['Edit']) &&  (empty($_POST['book']) || empty($_POST['supplier']) || empty($_POST['site'])  || empty($_POST['vehicle'])  || empty($_POST['date']) || empty($_POST['id']))) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } elseif(isset($_POST['Cancelled']) && (empty($_POST['book']) || empty($_POST['date']) || empty($_POST['id']))) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $grn['grnNo'] = $_POST['grnNo'];            unset($_POST['grnNo']);
            $grn['book'] = $_POST['book'];              unset($_POST['book']);
            $grn['supplier'] = $_POST['supplier'];      unset($_POST['supplier']);
            $grn['site'] = $_POST['site'];              unset($_POST['site']);
            $grn['vehicle'] = $_POST['vehicle'];        unset($_POST['vehicle']);
            $grn['id'] = $_POST['id'];                  unset($_POST['id']);
            $grn['date'] = $_POST['date'];              unset($_POST['date']);
            
            if(isset($_POST['Edit'])) { 
                $grn['cancelled'] = 0;      unset($_POST['Edit']);
            } else { 
                $grn['cancelled'] = 1;      unset($_POST['Ecancelled']);
            }
            
            $notify = GRN::Edit(new Database(),$_SESSION['user']['id'],$grn, $_POST);
        }
    }

    // Delete
    if(isset($_POST['Delete'])) {
        if (empty( $_POST['grnId'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $notify = GRN::Delete(new Database(),$_SESSION['user']['id'], $_POST);
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="./assets/dist/img/icons8_bill_80px.png" type="image/x-icon">
  <title>GRN & Billing | GRN</title>
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
    <datalist id="items">
        <option data-value=""></option>
        <?php
            foreach (Items::FetchAll(new Database())['data'] as $v) {
        ?>
        <option data-value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
        <?php } ?>
    </datalist>
    <datalist id="units">
        <option data-value=""></option>
        <?php
            foreach (Units::FetchAll(new Database())['data'] as $v) {
        ?>
        <option data-value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
        <?php } ?>
    </datalist>
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
            <h1 class="m-0 text-dark">Create Goods Receipt Note</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active">GRN</li>
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
                        <form method="post" role="form">
                            <div class="card-content">
                                <div class="card-header">
                                    <h3 class="card-title"><span id="ttl">Create</span> Goods Receipt Note</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                            <i class="fas fa-expand"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <input type="hidden" id="edit_id" name="id">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="grn_book">GRN Book</label>
                                                <select class="form-control" id="edit_book" name="book" required>
                                                    <!-- <option value="">-- Select Book --</option> -->
                                                    <?php
                                                        $book = NULL;
                                                        foreach (Books::FetchAll(new Database())['data'] as $v) {
                                                            if ($v['active']) {
                                                                $book = $v['id'];
                                                    ?>
                                                    <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                                    <?php }} ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="grn_book">GRN No</label>
                                                <select class="form-control" id="edit_grnNo" name="grnNo" required>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="grn_date">GRN Date</label>
                                                <input type="date" name="date" id="edit_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="supplier_id">Supplier's Name</label>
                                                <select class="form-control" id="edit_supplier" name="supplier">
                                                    <option value="">-- Select Supplier --</option>
                                                    <?php
                                                        foreach (Suppliers::FetchAll(new Database())['data'] as $v) {
                                                    ?>
                                                    <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="grn_site">Site Name</label>
                                                <select class="form-control" id="edit_site" name="site" required>
                                                    <option value="">-- Select Site --</option>
                                                    <?php
                                                        foreach (Sites::FetchAll(new Database())['data'] as $v) {
                                                            if (intval($v['active'])) {
                                                    ?>
                                                    <option value="<?php echo $v['name']; ?>"><?php echo $v['name']; ?></option>
                                                    <?php }} ?>
                                                </select>
                                                <!-- <input type="text" class="form-control" name="site" id="edit_site" placeholder="Enter Site Name" autocomplete="off" required> -->
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label for="grn_vehicle">Vehicle Number</label>
                                                <input type="text" class="form-control" name="vehicle" id="edit_vehicle" placeholder="Enter Vehicle Number" autocomplete="off" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row table-responsive p-0" style="max-height: 19rem;">
                                        <table class="table table-head-fixed text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%"></th>
                                                    <th style="width: 5%">#</th>
                                                    <th style="width: 60%">Item</th>
                                                    <th style="width: 15%">Quantity Recived</th>
                                                    <th style="width: 10%">Units</th>
                                                    <th style="width: 5%">
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-success btn-xs" id="add-row" style="width:1.6rem;height:1.6rem;"><i class="fas fa-plus"></i></button>
                                                            <button type="button" class="btn btn-danger btn-xs" id="delete-row" style="width:1.6rem;height:1.6rem;"><i class="fas fa-trash-alt"></i></button>
                                                        </div>                             
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="item_list">
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" name="record" type="checkbox">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label id="i-1">1.</label>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control i" id="iv1" list="items" autocomplete="off" >
                                                        <input type="hidden" id="i1" name="l1[i]">
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" id="q1" name="l1[q]" value="0" step="any">
                                                    </td>
                                                    <td colspan="2">
                                                        <input type="text" class="form-control u" id="uv1" list="units" autocomplete="off" >
                                                        <input type="hidden" id="u1" name="l1[u]">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-2">
                                            <a class="btn btn-block btn-default" href="./grn_create" role="button">Reset</a>
                                        </div>
                                        <div class="col-6"></div>
                                        <div class="col-2">
                                            <button type="submit" name="Cancelled" class="btn btn-block btn-warning"><strong>Cancelled</strong></button>
                                        </div>
                                        <div class="col-2">
                                            <button type="submit" name="Create" class="btn btn-block btn-info"><strong>Save</strong></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php if($P['grn']['view']) { ?>
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-content">
                            <div class="card-header">
                                <h3 class="card-title">Today's Goods Receipt Note</h3>
                                <div class="card-tools">
                                </div>
                            </div>
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width:5%;">SI</th>
                                            <th style="width:10%;">Book</th>
                                            <th style="width:15%;">GRN Number</th>
                                            <th style="width:20%;">Supplier's Name</th>
                                            <th style="width:20%;">Site Name</th>
                                            <th style="width:15%;">Status</th>
                                            <?php if($P['grn']['modify']) { ?>
                                            <th style="width:15%;">Action</th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $F = GRN::FetchByMDate(new Database(),$_SESSION['user']['id'], date('Y-m-d'), date('Y-m-d'), 'DESC');
                                            if ($F['success']) {
                                                $c = 1;
                                                foreach ($F['data'] as $k => $v) {
                                                    if ($v['userId'] == $_SESSION['user']['id']) {
                                        ?>
                                        <tr>
                                            <td><?php echo $c++; ?></td>
                                            <td><?php echo $v['bookName']; ?></td>
                                            <td><?php echo $v['grnNo']; ?></td>
                                            <td><?php echo ($v['supplierName'])? $v['supplierName'] : 'None'; ?></td>
                                            <td><?php echo $v['siteName']; ?></td>
                                            <td <?php echo ($v['cancelled'])? 'class="text-danger"': ''; ?>><?php echo ($v['cancelled'])? 'Cancelled': 'Saved'; ?></td>
                                            <td class="text-center">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-info viewModal" data-toggle="modal" data-target="#viewModal" data-id="<?php echo $v['id']; ?>" data-book="<?php echo $v['bookName']; ?>" data-grnno="<?php echo $v['grnNo']; ?>" data-supplier="<?php echo $v['supplierName']; ?>" data-site="<?php echo $v['siteName']; ?>" data-vehicle="<?php echo $v['vehicleNo']; ?>" data-date="<?php echo implode('/', array_reverse(explode('-',$v['date']))); ?>"><i class="fas fa-eye"></i></button>
                                                    <?php if($P['grn']['modify'] && $v['billed'] == 0) { ?>
                                                    <button type="button" class="btn btn-warning editModal" data-id="<?php echo $v['id']; ?>" data-book="<?php echo $v['bookId']; ?>" data-grnno="<?php echo $v['grnNo']; ?>" data-supplier="<?php echo $v['supplierId']; ?>" data-site="<?php echo $v['siteName']; ?>" data-vehicle="<?php echo $v['vehicleNo']; ?>" data-date="<?php echo $v['date']; ?>"><i class="fas fa-edit"></i></button>
                                                    <button type="button" class="btn btn-danger deleteModal" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $v['id']; ?>" data-grnno="<?php echo $v['grnNo']; ?>"><i class="fas fa-trash"></i></button>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php }}} ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>SI</th>
                                            <th>Book</th>
                                            <th>GRN Number</th>
                                            <th>Supplier's Name</th>
                                            <th>Site Name</th>
                                            <th>Status</th>
                                            <?php if($P['grn']['modify']) { ?>
                                            <th>Action</th>
                                            <?php } ?>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
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

<!-- View Modal -->
<div class="modal fade" id="viewModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Goods Receipt Note</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="grn_date">GRN Book</label>
                            <input type="text" id="view_book" class="form-control" placeholder="Book Name">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="grn_no">GRN Number</label>
                            <input type="text" id="view_grnNo" class="form-control" placeholder="GRN Number">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="grn_date">GRN Date</label>
                            <input type="text" id="view_date" class="form-control" placeholder="GRN Date">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="view_supplier">Supplier's Name</label>
                            <input type="text" id="view_supplier" class="form-control" placeholder="Supplier's Name">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="view_site">Site Name</label>
                            <input type="text" id="view_site" class="form-control" placeholder="Site Name">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="view_vehicle">Vehicle Number</label>
                            <input type="text" id="view_vehicle" class="form-control" placeholder="Vehicle Number">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col table-responsive p-0" style="max-height: 15rem;">
                        <table class="table table-head-fixed text-nowrap">
                            <thead>
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 45%">Item</th>
                                    <th style="width: 20%">Quantity Recived</th>
                                    <th style="width: 15%">Units</th>
                                    <th style="width: 15%">Billed</th>
                                </tr>
                            </thead>
                            <tbody id="view_List">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Print</button> -->
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<?php if($P['grn']['modify']) { ?>
<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" role="form">
                <div class="modal-header">
                    <h4 class="modal-title">Delete GRN</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="delete_id" name="grnId">
                    <p class="h5">Are you sure, you wnat to remove <strong id="delete_grnNo"></strong>?</p>
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
<script src="./assets/js/grn.js"></script>
<script>
    $(document).ready(function () {
        $("#example1").DataTable();

        $('.viewModal').click(async function() {
            $('#view_book').val($(this).data('book'));
            $('#view_grnNo').val($(this).data('grnno'));
            $('#view_supplier').val($(this).data('supplier'));
            $('#view_site').val($(this).data('site'));
            $('#view_vehicle').val($(this).data('vehicle'));
            $('#view_date').val($(this).data('date'));
            let userId = '<?php echo $_SESSION['user']['id']; ?>';
            let grnId = $(this).data('id');
            try {
                let list = await GrnListByGrnId(userId, grnId);
                $('#view_List').empty();
                list.forEach((L,I) => {
                    $('#view_List').append(`
                        <tr>
                            <td>${I+1}</td>
                            <td>${L.item}</td>
                            <td>${L.qtyRec}</td>
                            <td>${L.unit}</td>
                            <td>${parseInt(L.billed) ? 'Yes' : 'No'}</td>
                        </tr>
                    `);
                });
            }
            catch(e) {
                console.error(e)
                $("#view_List").empty()
            }            
        })

        $('.editModal').click(async function() {
            $('#edit_id').val($(this).data('id'));
            $(`#edit_grnNo`).prepend(`<option value="${$(this).data('grnno')}" selected>${$(this).data('grnno')}</option>`);
            $(`#edit_book > option[value='${$(this).data('book')}']`).prop('selected', true)
            $(`#edit_supplier > option[value='${$(this).data('supplier')}']`).prop('selected', true)
            $('#edit_site').val($(this).data('site'));
            $('#edit_vehicle').val($(this).data('vehicle'));
            $('#edit_date').val($(this).data('date'));
            $('button[name ="Create"]').attr('name', 'Edit');
            $('button[name ="Cancelled"]').attr('name', 'Ecancelled');

            let userId = '<?php echo $_SESSION['user']['id']; ?>';
            let grnId = $(this).data('id');
            try {
                let list = await GrnListByGrnId(userId, grnId);
                await updateList(list)
            }
            catch(e) {
                console.error(e)
                $("#item_list").empty()
                await addRow();
            }
            $('#ttl').text('Update');
            $(window).scrollTop(0);
        })

        $('.deleteModal').click(function() {
            $('#delete_id').val($(this).data('id'));
            $('#delete_grnNo').text($(this).data('grnno'));
        })
    });
</script>
<script>
    $(document).ready(function () {
        // Onload
        (async function() {
            let bookid = $('#edit_book').val()
            let userId = '<?php echo $_SESSION['user']['id']; ?>';
            let list = await GrnNosByBookId(userId, bookid);
            $('#edit_grnNo').empty()
            list.forEach(gn => {
                $('#edit_grnNo').append(`<option value="${gn}">${gn}</option>`)
            });
        })();

        $("#add-row").click(async function () {
            await addRow()
            $('.table-responsive').scrollTop($('.table-responsive')[0].scrollHeight);
        })

        $("#delete-row").click(async function () {
            await deleteRow()
        })

        $('#item_list').on('change', '.i', function() {
            let Index = $(this)[0].id.substring(2, $(this)[0].id.length)
            let ItemName = $(`#iv${Index}`).val()
            let ItemId = $(`#items option`).filter(function() {return ($(this).text() === ItemName && ItemName != '');}).data('value')
            if (ItemId === undefined) {
                $(`#iv${Index}`).val('')
                $(`#i${Index}`).val('')
            } else {
                $(`#i${Index}`).val(ItemId)
            }
            
        })

        $('#edit_book').on('change', async function() {
            let bookid = this.value
            let userId = '<?php echo $_SESSION['user']['id']; ?>';
            let list = await GrnNosByBookId(userId, bookid);
            $('#edit_grnNo').empty()
            list.forEach(gn => {
                $('#edit_grnNo').append(`<option value="${gn}">${gn}</option>`)
            });
        })

        $('#item_list').on('change', '.u', function() {
            let Index = $(this)[0].id.substring(2, $(this)[0].id.length)
            let UnitName = $(`#uv${Index}`).val()
            let UnitId = $(`#units option`).filter(function() {return ($(this).text() === UnitName && UnitName != '');}).data('value')
            if (UnitId === undefined) {
                $(`#uv${Index}`).val('')
                $(`#u${Index}`).val('')
            } else {
                $(`#u${Index}`).val(UnitId)
            }
        })
    });
</script>
</body>
</html>
