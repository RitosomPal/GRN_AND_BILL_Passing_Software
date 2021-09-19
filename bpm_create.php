<?php
  session_start();
  if(!isset($_SESSION['user'])) { header('location: ./login'); }

  require_once('./config/Database.php');
  require_once('./models/Permissions.php');
  require_once('./models/Books.php');
  require_once('./models/Suppliers.php');
  require_once('./models/BPM.php');

  $P = Permissions::getUserPermissions(new Database(),$_SESSION['user']['id'])['data'];
  if(!$P['bpm']['create']) { header('location: ./'); }

  // Create
  if (isset($_POST['Create'])) {
    if (empty( $_POST['regNo']) || empty($_POST['supplier']) || empty($_POST['date']) || empty($_POST['billNo'])) {
      $notify = array('msg'=>["Insert all required data."],'success'=>false);
    } else {
        unset($_POST['Create']);
        $bpm['supplier'] = $_POST['supplier'];      unset($_POST['supplier']);
        $bpm['date'] = $_POST['date'];              unset($_POST['date']);
        $bpm['billNo'] = $_POST['billNo'];          unset($_POST['billNo']);
        $bpm['regNo'] = $_POST['regNo'];            unset($_POST['regNo']);
        $bpmList = array();
        if (isset($_POST['id'])) {
          foreach ($_POST['id'] as $key => $value) {
            array_push($bpmList,array("billedQty"=>$_POST['qb'][$key], "grnlistId"=>$value));
          }
          $notify = BPM::Create(new Database(),$_SESSION['user']['id'],$bpm, $bpmList);
        } else {
          $notify = array('msg'=>["Insert all required data."],'success'=>false);
        }
    }
  }

  // Edit
  if(isset($_POST['Edit'])) {
    if (empty($_POST['id']) || empty($_POST['date'])  || empty( $_POST['regNo']) ||  empty($_POST['billNo'])) {
        $notify = array('msg'=>["Insert all required data."],'success'=>false);
    } else {
      unset($_POST['Edit']);
      $bpm['id'] = $_POST['editId'];              unset($_POST['editId']);
      $bpm['billNo'] = $_POST['billNo'];          unset($_POST['billNo']);
      $bpm['date'] = $_POST['date'];              unset($_POST['date']);
      $bpm['regNo'] = $_POST['regNo'];            unset($_POST['regNo']);
      $bpmList = array();
      foreach ($_POST['id'] as $key => $value) {
        array_push($bpmList,array("billedQty"=>$_POST['qb'][$key], "grnlistId"=>$value));
      }

      $notify = BPM::Edit(new Database(),$_SESSION['user']['id'],$bpm, $bpmList);
    }
  }

  // Delete
  if(isset($_POST['Delete'])) {
    if (empty( $_POST['billId'])) {
        $notify = array('msg'=>["Insert all required data."],'success'=>false);
    } else {
        $notify = BPM::Delete(new Database(),$_SESSION['user']['id'], $_POST);
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
                        <form method="post" role="form">
                            <div class="card-content">
                                <div class="card-header">
                                    <h3 class="card-title">Bill</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                            <i class="fas fa-expand"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                  <input type="hidden" id="edit_id" name="editId">
                                  <div class="row fd">
                                    <div class="col-sm-6">
                                      <div class="form-group">
                                          <label for="supplier_id">Supplier</label>
                                          <select class="form-control s" id="edit_supplier" name="supplier" required>
                                              <option value="">-- Select Supplier --</option>
                                              <?php
                                                  foreach (Suppliers::FetchAll(new Database())['data'] as $v) {
                                              ?>
                                              <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                              <?php } ?>
                                          </select>
                                      </div>
                                    </div>
                                    <div class="col-sm-6">
                                      <div class="form-group">
                                          <label for="bill_date">Bill Date</label>
                                          <input type="date" name="date" class="form-control" id="edit_date" value="<?php echo date('Y-m-d'); ?>" required>
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
                                          <input type="text" class="form-control" id="edit_billNo" name="billNo" placeholder="Enter Bill No." autocomplete="off" required>
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
                                <div class="card-footer sh" style="display: none;">
                                    <div class="row">
                                        <div class="col-2">
                                            <a class="btn btn-block btn-default" href="./bpm_create" role="button">Reset</a>
                                        </div>
                                        <div class="col-6"></div>
                                        <div class="col-2"></div>
                                        <div class="col-2">
                                            <button type="submit" name="Create" class="btn btn-block btn-info"><strong>Save</strong></button>
                                        </div>
                                        <!-- <div class="col-2">
                                            <button type="submit" class="btn btn-block btn-primary"><strong>Print & Save</strong></button>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php if($P['bpm']['view']) { ?>
            <div class="row">
              <div class="col-12">
                  <div class="card card-primary card-outline">
                      <div class="card-content">
                          <div class="card-header">
                              <h3 class="card-title">Today's Bills</h3>
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
                                          <?php if($P['bpm']['modify']) { ?>
                                          <th style="width:20%;">Action</th>
                                          <?php } ?>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <?php
                                          $F = BPM::FetchByMDate(new Database(),$_SESSION['user']['id'], date('Y-m-d'), date('Y-m-d'), 'DESC');
                                          if ($F['success']) {
                                              $c = 1;
                                              foreach ($F['data'] as $k => $v) {
                                                  if ($v['userId'] == $_SESSION['user']['id']) {
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
                                                  <button type="button" class="btn btn-warning editModal" data-id="<?php echo $v['id']; ?>" data-regno="<?php echo $v['regNo']; ?>" data-billno="<?php echo $v['billNo']; ?>" data-supplier="<?php echo $v['supplierId']; ?>" data-date="<?php echo $v['date']; ?>"><i class="fas fa-edit"></i></button>
                                                  <button type="button" class="btn btn-danger deleteModal" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $v['id']; ?>" data-billno="<?php echo $v['billNo']; ?>"><i class="fas fa-trash"></i></button>
                                                  <?php } ?>
                                              </div>
                                          </td>
                                      </tr>
                                      <?php }}} ?>
                                  </tbody>
                                  <tfoot>
                                      <tr>
                                          <th>SI</th>
                                          <th>Reg. No</th>
                                          <th>Bill Number</th>
                                          <th>Supplier's Name</th>
                                          <?php if($P['bpm']['modify']) { ?>
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
                  <table class="table table-head-fixed text-nowrap">
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
<!-- AdminLTE App -->
<script src="./assets/dist/js/adminlte.js"></script>
<!-- Notification -->
<?php require_once('templates/notify.php'); ?>
<!-- page script -->
<script src="./assets/js/bpm.js"></script>
<script>
  $(function () {
    $("#example1").DataTable();
    toastr.options = {
      "closeButton": true,
      "newestOnTop": true,
      "preventDuplicates": true
    }

    $('.viewModal').click(async function() {
      $('#view_regNo').val($(this).data('regno'));
      $('#view_billNo').val($(this).data('billno'));
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
      $(`#edit_supplier > option[value='${$(this).data('supplier')}']`).prop('selected', true)
      $('#edit_date').val($(this).data('date'));
      $('#edit_regNo').val($(this).data('regno'));
      $('#edit_billNo').val($(this).data('billno'));
      $(`#edit_book`).prop('disabled',true);
      $(`#edit_supplier`).prop('disabled',true);
      $('button[name ="Create"]').attr('name', 'Edit');
      let userId = '<?php echo $_SESSION['user']['id']; ?>';
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
<script>
    $(document).ready(function () {
      $('.fd').on('change', 'select',async function() {
        let supplier = $('.s').val();
        if (supplier) {
          try {
            let userId = '<?php echo $_SESSION['user']['id']; ?>';
            let list  = await GrnListBySupplierId(userId, supplier);
            list = list.filter((e) => {return (!parseInt(e.billed) && !parseInt(e.cancelled))})
            if (list.length > 0) {
              toastr.success(list.length+' Data Found!')
              $('#item_list').empty()
              let c = 1;
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
              $('.sh').css('display','block');
            } else {
              $('.sh').css('display','none');
              toastr.error('No Data Found!')
            }
          }
          catch (e) {
            toastr.error(e[0])
            $('.sh').css('display','none');
          }
        } else {
          $('.sh').css('display','none');
        }
      })

      $('#item_list').on('change','input[type="checkbox"]', function() {
        let LI =  $(this)[0].id
        let Index = LI.substring(2, LI.length)
        if ($(this).is(":checked")) {
          $(`#qb${Index}`).prop('required',true);
        } else {
          $(`#qb${Index}`).prop('required',false);
        }
      })

      $('#item_list').on('change','input[type="number"]', function() {
        let LI =  $(this)[0].id
        let Index = LI.substring(2, LI.length)
        if ($(this).val() > parseInt($(`#qr${Index}`).text())) {
          $(this).val(parseInt($(`#qr${Index}`).text()))
          toastr.warning('Max limit is '+$(`#qr${Index}`).text())
        } else if ($(this).val() < 0)  {
          $(this).val(0)
          toastr.warning('Min limit is 0')
        }
      }) 
    });

    $(document).keypress(
      function(event){
        if (event.which == '13') {
          event.preventDefault();
        }
    });
</script>
</body>
</html>
