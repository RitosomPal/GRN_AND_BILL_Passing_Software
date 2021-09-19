<?php
    session_start();
    if(!isset($_SESSION['user'])) { header('location: ./login'); }

    require_once('./config/Database.php');
    require_once('./models/Permissions.php');
    require_once('./models/Suppliers.php');
    require_once('./models/Sites.php');
    require_once('./models/Books.php');
    require_once('./models/Items.php');
    require_once('./models/Report.php');

    $P = Permissions::getUserPermissions(new Database(),$_SESSION['user']['id'])['data'];
    if(!$P['bpm']['report'] && !$P['grn']['report']) { header('location: ./'); }

    if(isset($_POST['Search'])) {
        $notify = Report::Report6(new Database(),$_SESSION['user']['id'],$_POST);
        if ($notify['success']) {
            $F = $notify['data'];
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="shortcut icon" href="./assets/dist/img/icons8_bill_80px.png" type="image/x-icon">
  <title>GRN & Billing | Reports</title>
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
  <link rel="stylesheet" href="./assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
            <h1 class="m-0 text-dark">Reports</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active">Reports</li>
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
                <div class="col">
                    <div class="card card-primary card-outline">
                        <form method="post">
                        <div class="card-header">
                            <h3 class="card-title">Report 6 (General Report)</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h5>Search By</h5>
                                    <div class="d-flex flex-wrap">
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input sc" data-by="Date" type="checkbox">
                                                <label class="form-check-label">Date</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input sc" data-by="Book" type="checkbox">
                                                <label class="form-check-label">Book</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input sc" data-by="Site" type="checkbox">
                                                <label class="form-check-label">Site</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input sc" data-by="Supplier" type="checkbox">
                                                <label class="form-check-label">Supplier</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input sc" data-by="Item" type="checkbox">
                                                <label class="form-check-label">Item</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input sc" name="sfYetToBill" data-by="YetToBill" type="checkbox">
                                                <label class="form-check-label">Yet To Be Billed</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <h5>GRN View</h5>
                                    <div class="d-flex flex-wrap">
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="grn[grnNo]" type="checkbox">
                                                <label class="form-check-label" >Grn No.</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="grn[date]" type="checkbox">
                                                <label class="form-check-label">Date</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="grn[site]" type="checkbox">
                                                <label class="form-check-label">Site</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="grn[vehicle]" type="checkbox">
                                                <label class="form-check-label">Vehicle</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="grn[supplier]" type="checkbox">
                                                <label class="form-check-label">Supplier</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="grn[book]" type="checkbox">
                                                <label class="form-check-label">Book</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="grn[item]" type="checkbox">
                                                <label class="form-check-label">Item</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="grn[qtyRec]" type="checkbox">
                                                <label class="form-check-label">Quantity Recived</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="grn[unit]" type="checkbox">
                                                <label class="form-check-label">Unit</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="grn[cancelled]" type="checkbox">
                                                <label class="form-check-label">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <h5>BPM View</h5>
                                    <div class="d-flex flex-wrap">
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="bpm[regNo]" type="checkbox">
                                                <label class="form-check-label">Reg. No.</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="bpm[billNo]" type="checkbox">
                                                <label class="form-check-label">Bill No.</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="bpm[date]" type="checkbox">
                                                <label class="form-check-label">Date</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="bpm[supplier]" type="checkbox">
                                                <label class="form-check-label">Supplier</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="bpm[item]" type="checkbox">
                                                <label class="form-check-label">Item</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="bpm[qtyBilled]" type="checkbox">
                                                <label class="form-check-label">Quantity Billed</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="bpm[unit]" type="checkbox">
                                                <label class="form-check-label">Unit</label>
                                            </div>
                                        </div>
                                        <div class="px-2">
                                            <div class="form-check">
                                                <input class="form-check-input" name="bpm[balance]" type="checkbox">
                                                <label class="form-check-label">Balance</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr class="sp">
                            <div class="d-flex justify-content-around flex-wrap sp">
                                <div class="px-2 flex-fill sb" id="byDate">
                                    <div class="form-group">
                                        <label>Date</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="far fa-calendar-alt"></i>
                                            </span>
                                            </div>
                                            <input type="text" name="sfDate" class="form-control float-right" id="reservation">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                </div>
                                <div class="px-2 flex-fill sb" id="byBook">
                                    <div class="form-group">
                                        <label for="book_id">Search By Book</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                        <select class="form-control" name="sfBook">
                                            <option value="">-- Select Book --</option>
                                            <?php
                                                foreach (Books::FetchAll(new Database())['data'] as $v) {
                                            ?>
                                            <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="px-2 flex-fill sb" id="bySupplier">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier's Name</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                        <select class="form-control" name="sfSupplier">
                                            <option value="">-- Select Supplier --</option>
                                            <?php
                                                foreach (Suppliers::FetchAll(new Database())['data'] as $v) {
                                            ?>
                                            <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="px-2 flex-fill sb" id="bySite">
                                    <div class="form-group">
                                        <label>Site Name</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                        <div class="input-group">
                                            <input type="text" list="sites" class="form-control" name="sfSite" placeholder="Enter Site Name" autocomplete="off">
                                            <datalist id="sites">
                                                <option data-value=""></option>
                                                <?php
                                                    foreach (Sites::FetchAll(new Database())['data'] as $v) {
                                                ?>
                                                <option data-value="<?php echo $v['name']; ?>"><?php echo $v['name']; ?></option>
                                                <?php } ?>
                                            </datalist>
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                </div>
                                <div class="px-2 flex-fill sb" id="byItem">
                                    <div class="form-group">
                                        <label for="book_id">Search By Item</label>&nbsp;<small class="text-danger"><em>*</em></small>
                                        <select class="form-control" name="sfItem">
                                            <option value="">-- Select Item --</option>
                                            <?php
                                                foreach (Items::FetchAll(new Database())['data'] as $v) {
                                            ?>
                                            <option value="<?php echo $v['id']; ?>"><?php echo $v['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer sp">
                            <div class="row">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-4">
                                    <button type="submit" name="Search" class="btn btn-primary form-control">Generate</button>
                                </div>
                                <div class="col-sm-4"></div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Report -->
            <?php if(isset($F)){ ?>
            <div class="row">
                <div class="col">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Report</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-y:auto;">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead class="text-center">
                                    <tr>
                                        <th>SI.</th>
                                        <?php  foreach ($F[0] as $key => $value) { ?>
                                        <th><?php echo $key; ?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php
                                        $c=1;
                                        foreach ($F as $v) {
                                    ?>
                                    <tr>
                                        <td><?php echo $c++; ?>.</td>
                                        <?php foreach($v as $val) { ?>
                                        <td><?php echo $val; ?></td>
                                        <?php } ?>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
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
<script src="./assets/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="./assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="./assets/plugins/datatables-buttons/js/buttons.flash.min.js"></script>
<script src="./assets/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="./assets/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="./assets/plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="./assets/plugins/datatables-buttons/js/pdfmake.min.js"></script>
<script src="./assets/plugins/datatables-buttons/js/vfs_fonts.js"></script>
<script src="./assets/plugins/datatables-buttons/js/jszip.min.js"></script>
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
<script>
  $(function () {
    $('#reservation').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
    var table = $("#example1").DataTable({
            dom:    "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [
            'copy',
            'csv',
            {
                extend: 'excel',
                title: function(){
                    return 'Report 6'
                },
                customize: function (xlsx) {
                }
            },
            {
                extend: 'pdf',
                filename: 'Report 6',
                title: function(){
                    return 'Report 6'
                }
            },
            {
                extend: 'print',
                title: function(){
                    return 'Report 6'
                },
                customize: function ( win ) {
                    $(win.document.body).find('h1').css('text-align', 'center').addClass('mb-3');
                }
            }
        ]
    });
  });
</script>
<script>
  $(function () {
      $('.sp').css('display', 'none');
      $('.sb').css('display', 'none');
      $('.sc').on('change', function() {
        if ($('.sc:checked').length > 0) { $('.sp').css('display', 'block'); } else { $('.sp').css('display', 'none'); }
        if ($(this).prop('checked')) {
            if ($(this).data('by') != 'Date') {
                $(`[name="sfDate"]`).val('');
            }
            $(`#by${$(this).data('by')}`).css('display', 'block');
            $(`[name="sf${$(this).data('by')}"]`).prop('required', true);
        } else {
            $(`#by${$(this).data('by')}`).css('display', 'none');
            $(`[name="sf${$(this).data('by')}"]`).prop('required', false);
        }
      })
  })
</script>
</body>
</html>
