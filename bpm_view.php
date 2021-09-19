<?php
    session_start();
    if(!isset($_SESSION['user'])) { header('location: ./login'); }

    require_once('./config/Database.php');
    require_once('./models/Permissions.php');
    require_once('./models/Books.php');
    require_once('./models/Suppliers.php');
    require_once('./models/BPM.php');

    $P = Permissions::getUserPermissions(new Database(),$_SESSION['user']['id'])['data'];
    if(!$P['bpm']['view']) { header('location: ./'); }

    $F = BPM::FetchByDate(new Database(),$_SESSION['user']['id'], date('Y-m-d'), date('Y-m-d'), 'DESC');

    // Edit
    if(isset($_POST['Edit'])) {
        if (empty($_POST['id']) || empty($_POST['date'])   || empty( $_POST['regNo']) ||  empty($_POST['billNo'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            unset($_POST['Edit']);
            $bpm['id'] = $_POST['editId'];              unset($_POST['editId']);
            $bpm['billNo'] = $_POST['billNo'];          unset($_POST['billNo']);
            $bpm['date'] = $_POST['date'];              unset($_POST['date']);
            $bpm['regNo'] = $_POST['regNo'];            unset($_POST['regNo']);
            $bpmList = array();
            unset($_POST['Edit']);
            $SF = $_POST['sf'];
            unset($_POST['sf']);
            if(isset($_POST['Book'])) { $TYPE = 'Book'; unset($_POST['Book']); }
            else if (isset($_POST['Supplier'])) { $TYPE = 'Supplier'; unset($_POST['Supplier']); }
            else if (isset($_POST['Date'])) { $TYPE = 'Date'; unset($_POST['Date']); }
            else if (isset($_POST['BPM'])) { $TYPE = 'BPM'; unset($_POST['BPM']); }
            else {
                $SF = date('d/m/Y').' - '.date('d/m/Y');
                $TYPE = 'Date';
            }

            foreach ($_POST['id'] as $key => $value) {
                array_push($bpmList,array("billedQty"=>$_POST['qb'][$key], "grnlistId"=>$value));
                unset($_POST['qb'][$key]);
                unset($_POST['id'][$key]);
            }
            $notify = BPM::Edit(new Database(),$_SESSION['user']['id'],$bpm, $bpmList);
            $_POST['sf']=$SF;
            $_POST[$TYPE]='';
            $_POST['M'] = true;
        }
    }

    // Delete
    if(isset($_POST['Delete'])) {
        if (empty($_POST['billId'])) {
            $notify = array('msg'=>["Insert all required data."],'success'=>false);
        } else {
            $notify = BPM::Delete(new Database(),$_SESSION['user']['id'], $_POST);
            $SF = $_POST['sf'];
            unset($_POST['sf']);
            if(isset($_POST['Book'])) { $TYPE = 'Book'; unset($_POST['Book']); }
            else if (isset($_POST['Supplier'])) { $TYPE = 'Supplier'; unset($_POST['Supplier']); }
            else if (isset($_POST['Date'])) { $TYPE = 'Date'; unset($_POST['Date']); }
            else if (isset($_POST['BPM'])) { $TYPE = 'BPM'; unset($_POST['BPM']); }
            else {
                $SF = date('d/m/Y').' - '.date('d/m/Y');
                $TYPE = 'Date';
            }
            $_POST['sf']=$SF;
            $_POST[$TYPE]='';
            $_POST['M'] = true;
        }
    }

    // Search
    if(isset($_POST['sf'])) {
        if (empty($_POST['sf'])) {
            $notify = array('msg'=>["Insert required data."],'success'=>false);
        } else {
            if (isset($_POST['Supplier'])) {
                $F = BPM::FetchBySupplier(new Database(),$_SESSION['user']['id'], $_POST['sf'], 'DESC');
            } elseif (isset($_POST['Date'])) {
                $date = explode('-',$_POST['sf']);
                $from = implode('-', array_reverse(explode('/',trim($date[0]))));
                $to = implode('-', array_reverse(explode('/',trim($date[1]))));
                $F = BPM::FetchByDate(new Database(), $_SESSION['user']['id'], $from, $to, 'DESC');
            } elseif (isset($_POST['BPM'])) {
                $F = BPM::FetchByBillNo(new Database(),$_SESSION['user']['id'], $_POST['sf'], 'DESC');
            } elseif (isset($_POST['REG'])) {
                $F = BPM::FetchByRegNo(new Database(),$_SESSION['user']['id'], $_POST['sf'], 'DESC');
            }

            if(!isset($_POST['M'])) {
                $notify = $F;
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
  <title>GRN & Billing | BPM</title>
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
  <!-- daterange picker -->
  <link rel="stylesheet" href="./assets/plugins/daterangepicker/daterangepicker.css">
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
            <h1 class="m-0 text-dark">Bill Passing Module</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active">BPM</li>
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
                            <h3 class="card-title">Search Goods Receipt Note</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-group">
                                                    <label for="supplier_id">Search By Supplier</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                                    <select class="form-control" name="sf" required>
                                                        <option value="">-- Select Supplier --</option>
                                                        <?php
                                                            foreach (Suppliers::FetchAll(new Database())['data'] as $v) {
                                                        ?>
                                                        <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <button type="submit" name="Supplier" class="btn btn-primary">Search</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-group">
                                                    <label>Search By Date</label>&nbsp;<small class="text-danger"><em>*</em></small>

                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                        <span class="input-group-text">
                                                            <i class="far fa-calendar-alt"></i>
                                                        </span>
                                                        </div>
                                                        <input type="text" name="sf" class="form-control float-right" id="reservation" required>
                                                    </div>
                                                    <!-- /.input group -->
                                                </div>
                                                <button type="submit" name="Date" class="btn btn-primary">Search</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-group">
                                                    <label for="grn">Search By Reg. No.</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                                    <input type="text" class="form-control" name="sf" placeholder="Enter Registration Number" autocomplete="off" required>
                                                </div>
                                                <button type="submit" name="REG" class="btn btn-primary">Search</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <form method="post">
                                                <div class="form-group">
                                                    <label for="bpm">Search By Bill No.</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                                    <input type="text" class="form-control" name="sf" placeholder="Enter Bill Number" autocomplete="off" required>
                                                </div>
                                                <button type="submit" name="BPM" class="btn btn-primary">Search</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
              <div class="col-12">
                  <div class="card card-primary card-outline">
                      <div class="card-content">
                          <div class="card-header">
                              <h3 class="card-title">Bill's List</h3>
                              <div class="card-tools">
                              </div>
                          </div>
                          <div class="card-body">
                              <table id="example1" class="table table-bordered table-striped">
                                  <thead>
                                      <tr>
                                          <th style="width:10%;">SI</th>
                                          <th style="width:30%;">Reg. No</th>
                                          <th style="width:30%;">Bill Number</th>
                                          <th style="width:40%;">Supplier's Name</th>
                                          <th style="width:20%;">Action</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php
                                        if ($F['success']) {
                                        $c = 1;
                                        foreach ($F['data'] as $k => $v) {
                                      ?>
                                      <tr>
                                          <td><?php echo $c++; ?></td>
                                          <td><?php echo $v['regNo']; ?></td>
                                          <td><?php echo $v['billNo']; ?></td>
                                          <td><?php echo $v['supplierName']; ?></td>
                                          <td class="text-center">
                                              <div class="btn-group">
                                                  <button type="button" class="btn btn-info viewModal" data-toggle="modal" data-target="#viewModal" data-id="<?php echo $v['id']; ?>" data-regno="<?php echo $v['regNo']; ?>" data-billno="<?php echo $v['billNo']; ?>" data-supplier="<?php echo $v['supplierName']; ?>" data-date="<?php echo implode('/', array_reverse(explode('-',$v['date']))); ?>"><i class="fas fa-eye"></i></button>
                                                  <?php if($P['bpm']['modify']) { ?>
                                                  <button type="button" class="btn btn-warning editModal" data-toggle="modal" data-target="#editModal" data-id="<?php echo $v['id']; ?>" data-regno="<?php echo $v['regNo']; ?>" data-billno="<?php echo $v['billNo']; ?>" data-suppliern="<?php echo $v['supplierName']; ?>" data-supplier="<?php echo $v['supplierId']; ?>" data-date="<?php echo $v['date']; ?>" <?php if ($_SESSION['user']['id'] != 1 && $v['userId'] != $_SESSION['user']['id']) { echo 'title="Your are not allowed to delete!" disabled'; } ?>><i class="fas fa-edit"></i></button>
                                                  <button type="button" class="btn btn-danger deleteModal" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $v['id']; ?>" data-billno="<?php echo $v['billNo']; ?>" <?php if ($_SESSION['user']['id'] != 1 && $v['userId'] != $_SESSION['user']['id']) { echo 'title="Your are not allowed to delete!" disabled'; } ?>><i class="fas fa-trash"></i></button>
                                                  <?php } ?>
                                              </div>
                                          </td>
                                      </tr>
                                      <?php }} ?>
                                  </tbody>
                                  <tfoot>
                                      <tr>
                                          <th>SI</th>
                                          <th>Reg. No</th>
                                          <th>Bill Number</th>
                                          <th>Supplier's Name</th>
                                          <th>Action</th>
                                      </tr>
                                  </tfoot>
                              </table>
                          </div>
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

<!-- View Modal -->
<div class="modal fade" id="viewModal" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
          <h4 class="modal-title">View Bill</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
          </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-6">
            <div class="form-group">
                <label for="supplier_id">Supplier</label>
                <input type="text" class="form-control" id="view_supplier" placeholder="Supplier Name">
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
                <label for="bill_date">Bill Date</label>
                <input type="text" class="form-control" id="view_date" placeholder="Bill Date">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
              <div class="form-group">
                  <label for="grn_book">Register No.</label>
                  <input type="text" class="form-control" name="regNo" id="view_regNo" placeholder="As on Register" required>
              </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group">
                <label for="bill_no">Bill Number</label>
                <input type="text" class="form-control" id="view_billNo" placeholder="Enter Bill No.">
            </div>
          </div>
        </div>
        <div class="row">
            <div class="col table-responsive p-0" style="max-height: 15rem;">
                <table id="printTable" class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 10%">Book</th>
                        <th style="width: 10%">GRN No.</th>
                        <th style="width: 25%">Item</th>
                        <th style="width: 10%">Quantity Recived</th>
                        <th style="width: 10%">Units</th>
                        <th style="width: 10%">Quantity Recived Date</th>
                        <th style="width: 10%">Quantity Billed</th>
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

<?php if($P['bpm']['modify']) { ?>
<!-- Edit Modal -->
<div class="modal fade" id="editModal" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form method="post" role="form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Bill</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="card-body">
                    <input type="hidden" id="edit_id" name="editId">
                    <input type="hidden" name="sf" value="<?php echo @$_POST['sf']; ?>">
                    <input type="hidden" name="<?php unset($_POST['sf']); echo array_key_last($_POST); ?>">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="supplier_id">Supplier</label>
                                <input type="text" class="form-control" id="edit_supplier" placeholder="Supplier Name" Disabled>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="bill_date">Bill Date</label>
                                <input type="date" name="date" class="form-control" id="edit_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="grn_book">Register No.</label>
                                <input type="text" class="form-control" name="regNo" id="edit_regNo" placeholder="As on Register" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="bill_no">Bill Number</label>
                                <input type="text" class="form-control" id="edit_billNo" name="billNo" placeholder="Enter Bill No.">
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="card-body sh" style="display: none;">
                        <div class="row">
                        <div class="col table-responsive p-0" style="max-height: 15rem;">
                            <table class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                    <th style="width: 5%">#</th>
                                    <th style="width: 10%">Book</th>
                                    <th style="width: 10%">GRN No.</th>
                                    <th style="width: 20%">Item</th>
                                    <th style="width: 10%">Quantity Recived</th>
                                    <th style="width: 10%">Units</th>
                                    <th style="width: 10%">Quantity Recived Date</th>
                                    <th style="width: 10%">Quantity Billed</th>
                                    <th style="width: 5%"></th>
                                    </tr>
                                </thead>
                                <tbody id="item_list">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-2">
                            <a class="btn btn-block btn-default" href="./bpm_view" role="button">Reset</a>
                        </div>
                        <div class="col-6"></div>
                        <div class="col-2"></div>
                        <div class="col-2">
                            <button type="submit" name="Edit" class="btn btn-block btn-info"><strong>Save</strong></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
                    <h4 class="modal-title">Delete BPM</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="sf" value="<?php echo @$_POST['sf']; ?>">
                    <input type="hidden" name="<?php unset($_POST['sf']); echo array_key_last($_POST); ?>">
                    <input type="hidden" id="delete_id" name="billId">
                    <p class="h5">Are you sure, you wnat to remove <strong id="delete_billNo"></strong>?</p>
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
<!-- InputMask -->
<script src="./assets/plugins/moment/moment.min.js"></script>
<script src="./assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<!-- date-range-picker -->
<script src="./assets/plugins/daterangepicker/daterangepicker.js"></script>
<!-- AdminLTE App -->
<script src="./assets/dist/js/adminlte.js"></script>
<!-- Notification -->
<?php require_once('templates/notify.php'); ?>
<!-- page script -->
<script src="./assets/js/bpm.js"></script>
<script>
  $(function () {
    $("#example1").DataTable();
    $('#reservation').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
    toastr.options = {
      "closeButton": true,
      "newestOnTop": true,
      "preventDuplicates": true
    }

    $('.viewModal').click(async function() {
    //   $('#view_book').val($(this).data('book'));
      $('#view_billNo').val($(this).data('billno'));
      $('#view_regNo').val($(this).data('regno'));
      $('#view_supplier').val($(this).data('supplier'));
      $('#view_date').val($(this).data('date'));
      let userId = '<?php echo $_SESSION['user']['id']; ?>';
      let billId = $(this).data('id');
      let list = await BpmListByBillId(userId, billId);
      $('#view_List').empty();
      list.forEach((L,I) => {
          $('#view_List').append(`
              <tr>
                <td>${I+1}.</td>
                <td>${L.bookName}</td>
                <td>${L.grnNo}</td>
                <td>${L.item}</td>
                <td>${L.qtyRec}</td>
                <td>${L.unit}</td>
                <td>${L.qtyRecDate}</td>
                <td>${L.billedQty}</td>
              </tr>
          `);
      });
    })
    $('.editModal').click(async function() {
      $('#edit_id').val($(this).data('id'));
    //   $('#edit_book').val($(this).data('bookn'));
      $('#edit_supplier').val($(this).data('suppliern'));
    //   $(`#edit_book > option[value='${$(this).data('book')}']`).prop('selected', true)
    //   $(`#edit_supplier > option[value='${$(this).data('supplier')}']`).prop('selected', true)
      $('#edit_date').val($(this).data('date'));
      $('#edit_regNo').val($(this).data('regno'));
      $('#edit_billNo').val($(this).data('billno'));
      $(`#edit_book`).prop('disabled',true);
      $(`#edit_supplier`).prop('disabled',true);
      $('button[name ="Create"]').attr('name', 'Edit');
      let userId = '<?php echo $_SESSION['user']['id']; ?>';
      let book = $(this).data('book');
      let supplier = $(this).data('supplier');
      let billId = $(this).data('id');
      let list = await BpmListByBillId(userId, billId);
      $('#item_list').empty()
      let c = 1;
      list.forEach(l => {
        $('#item_list').append(`
          <tr>
            <td>${c}.</td>
            <td>${l.bookName}</td>
            <td>${l.grnNo}</td>
            <td>${l.item}</td>
            <td id="qr${c}">${l.qtyRec}</td>
            <td>${l.unit}</td>
            <td>${l.qtyRecDate}</td>
            <td><input class="form-control qb" id="qb${c}" type="number" step="any" name="qb[${c}]" value="${l.billedQty}" min="0" max="${parseInt(l.qtyRec)}" class="form-control"></td>
            <td>
              <div class="form-check pt-2">
                <input class="form-check-input" id="cb${c}" name="id[${c++}]" value="${l.grnlistId}" type="checkbox" checked>
              </div>
            </td>
          </tr>
        `);
      });
      list  = await GrnListBySupplierId(userId, supplier);
      list = list.filter((e) => {return (!parseInt(e.billed) && !parseInt(e.cancelled))})
      if (list.length > 0) {
        list.forEach(l => {
          $('#item_list').append(`
            <tr>
              <td>${c}.</td>
              <td>${l.bookName}</td>
              <td>${l.grnNo}</td>
              <td>${l.itemName}</td>
              <td id="qr${c}">${l.qtyRec}</td>
              <td>${l.unitName}</td>
              <td>${l.date}</td>
              <td><input class="form-control qb" id="qb${c}" type="number" step="any" name="qb[${c}]" min="0" max="${parseInt(l.qtyRec)}" class="form-control"></td>
              <td>
                <div class="form-check pt-2">
                  <input class="form-check-input" id="cb${c}" name="id[${c++}]" value="${l.grnListId}" type="checkbox">
                </div>
              </td>
            </tr>
          `);
        });
      }
      $('.sh').css('display','block');
    })
    $('.deleteModal').click(function() {
      $('#delete_id').val($(this).data('id'));
      $('#delete_billNo').text($(this).data('billno'));
    })
  });
</script>
</body>
</html>
