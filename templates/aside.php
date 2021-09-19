<?php
  @session_start();
  if(!isset($_SESSION['user'])) { header('location: ./login'); }

  require_once('./controllers/routes.php');
  require_once('./config/Database.php');
  require_once('./models/Permissions.php');
  require_once('./models/Users.php');

  $P = Permissions::getUserPermissions(new Database(),$_SESSION['user']['id'])['data'];
  $IMG = Users::FetchInfo(new Database(), $_SESSION['user']['id'])['data']['image'];
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="./" class="brand-link">
      <img src="./assets/dist/img/icons8_bill_80px.png" alt="Logo" class="brand-image"
           style="opacity: .8">
      <span class="brand-text font-weight-light">GRN & Billing</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="./assets/images/<?php if(empty($IMG)){echo 'user.png';}else{echo $IMG;} ?>" class="img-circle elevation-1" alt="User Image">
        </div>
        <div class="info">
          <a href="./profile" class="d-block"><?php echo $_SESSION['user']['name']; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item">
            <a href="./" class="nav-link <?php if(Routes::getPageName() == 'index') { echo 'active'; } ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
          <?php if($P['groups']['view'] || $P['users']['view']) { ?>
          <li class="nav-item has-treeview <?php if(in_array(Routes::getPageName(), ['groups', 'users'])) { echo 'menu-open'; } ?>">
            <a href="#" class="nav-link <?php if(in_array(Routes::getPageName(), ['groups', 'users'])) { echo 'active'; } ?>">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>
                Admin
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php if($P['groups']['view']) { ?>
              <li class="nav-item">
                <a href="./groups" class="nav-link <?php if(Routes::getPageName() == 'groups') { echo 'active'; } ?>">
                  <i class="fas fa-users nav-icon"></i>
                  <p>Groups</p>
                </a>
              </li>
              <?php } ?>
              <?php if($P['users']['view']) { ?>
              <li class="nav-item">
                <a href="./users" class="nav-link <?php if(Routes::getPageName() == 'users') { echo 'active'; } ?>">
                  <i class="fas fa-user-friends nav-icon"></i>
                  <p>Users</p>
                </a>
              </li>
              <?php } ?>
            </ul>
          </li>
          <?php } ?>
          <?php if($P['suppliers']['view'] || $P['items']['view'] || $P['units']['view'] || $P['books']['view']) { ?>
          <li class="nav-item has-treeview <?php if(in_array(Routes::getPageName(), ['sites', 'suppliers', 'items', 'units', 'books'])) { echo 'menu-open'; } ?>">
            <a href="#" class="nav-link <?php if(in_array(Routes::getPageName(), ['sites', 'suppliers', 'items', 'units', 'books'])) { echo 'active'; } ?>">
              <i class="nav-icon fas fa-database"></i>
              <p>
                Data
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <?php if($P['suppliers']['view']) { ?>
              <li class="nav-item">
                <a href="./sites" class="nav-link <?php if(Routes::getPageName() == 'sites') { echo 'active'; } ?>">
                  <i class="fas fa-map-marked-alt nav-icon"></i>
                  <p>Sites</p>
                </a>
              </li>
              <?php } ?>
              <?php if($P['suppliers']['view']) { ?>
              <li class="nav-item">
                <a href="./suppliers" class="nav-link <?php if(Routes::getPageName() == 'suppliers') { echo 'active'; } ?>">
                  <i class="fas fa-truck-moving nav-icon"></i>
                  <p>Suppliers</p>
                </a>
              </li>
              <?php } ?>
              <?php if($P['items']['view']) { ?>
              <li class="nav-item">
                <a href="./items" class="nav-link <?php if(Routes::getPageName() == 'items') { echo 'active'; } ?>">
                  <i class="fas fa-boxes nav-icon"></i>
                  <p>Items</p>
                </a>
              </li>
              <?php } ?>
              <?php if($P['units']['view']) { ?>
              <li class="nav-item">
                <a href="./units" class="nav-link <?php if(Routes::getPageName() == 'units') { echo 'active'; } ?>">
                  <i class="fas fa-balance-scale nav-icon"></i>
                  <p>Units</p>
                </a>
              </li>
              <?php } ?>
              <?php if($P['books']['view']) { ?>
              <li class="nav-item">
                <a href="./books" class="nav-link <?php if(Routes::getPageName() == 'books') { echo 'active'; } ?>">
                  <i class="fas fa-book nav-icon"></i>
                  <p>Books</p>
                </a>
              </li>
              <?php } ?>
            </ul>
          </li>
          <?php } ?>

          <li class="nav-header">Tools</li>
          <?php if($P['grn']['view']) { ?>
          <li class="nav-item has-treeview <?php if(in_array(Routes::getPageName(), ['grn_create', 'grn_view'])) { echo 'menu-open'; } ?>">
            <a href="#" class="nav-link <?php if(in_array(Routes::getPageName(), ['grn_create', 'grn_view'])) { echo 'active'; } ?>">
              <i class="nav-icon fas fa-file-invoice"></i>
              <p>
                GRN
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./grn_create" class="nav-link <?php if(Routes::getPageName() == 'grn_create') { echo 'active'; } ?>">
                  <i class="fas fa-plus nav-icon"></i>
                  <p>Create</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./grn_view" class="nav-link <?php if(Routes::getPageName() == 'grn_view') { echo 'active'; } ?>">
                  <i class="fas fa-eye nav-icon"></i>
                  <p>Views</p>
                </a>
              </li>
            </ul>
          </li>
          <?php } ?>
          <?php if($P['bpm']['view']) { ?>
          <li class="nav-item has-treeview <?php if(in_array(Routes::getPageName(), ['bpm_create', 'bpm_view'])) { echo 'menu-open'; } ?>">
            <a href="#" class="nav-link <?php if(in_array(Routes::getPageName(), ['bpm_create', 'bpm_view'])) { echo 'active'; } ?>">
              <i class="nav-icon fas fa-file-invoice-dollar"></i>
              <p>
                BPM
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./bpm_create" class="nav-link <?php if(Routes::getPageName() == 'bpm_create') { echo 'active'; } ?>">
                  <i class="fas fa-plus nav-icon"></i>
                  <p>Create</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./bpm_view" class="nav-link <?php if(Routes::getPageName() == 'bpm_view') { echo 'active'; } ?>">
                  <i class="fas fa-eye nav-icon"></i>
                  <p>Views</p>
                </a>
              </li>
            </ul>
          </li>
          <?php } ?>
          <?php if($P['grn']['report'] || $P['bpm']['report']) { ?>
          <li class="nav-item has-treeview <?php if(in_array(Routes::getPageName(), ['report1', 'report2', 'report3', 'report4', 'report5', 'report6'])) { echo 'menu-open'; } ?>">
            <a href="#" class="nav-link <?php if(in_array(Routes::getPageName(), ['report1', 'report2', 'report3', 'report4', 'report5', 'report6'])) { echo 'active'; } ?>">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>
                Reports
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./report1" class="nav-link <?php if(Routes::getPageName() == 'report1') { echo 'active'; } ?>">
                  <i class="fas fa-file-contract nav-icon"></i>
                  <p>Report 1</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./report2" class="nav-link <?php if(Routes::getPageName() == 'report2') { echo 'active'; } ?>">
                  <i class="fas fa-file-contract nav-icon"></i>
                  <p>Report 2</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./report3" class="nav-link <?php if(Routes::getPageName() == 'report3') { echo 'active'; } ?>">
                  <i class="fas fa-file-contract nav-icon"></i>
                  <p>Report 3</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./report4" class="nav-link <?php if(Routes::getPageName() == 'report4') { echo 'active'; } ?>">
                  <i class="fas fa-file-contract nav-icon"></i>
                  <p>Report 4</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./report5" class="nav-link <?php if(Routes::getPageName() == 'report5') { echo 'active'; } ?>">
                  <i class="fas fa-file-contract nav-icon"></i>
                  <p>Report 5</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./report6" class="nav-link <?php if(Routes::getPageName() == 'report6') { echo 'active'; } ?>">
                  <i class="fas fa-file-contract nav-icon"></i>
                  <p>Report 6</p>
                </a>
              </li>
            </ul>
          </li>
          <?php } ?>

          <li class="nav-header"></li>
          <?php if($P['groups']['view'] || $P['users']['view']) { ?>
          <li class="nav-item has-treeview <?php if(in_array(Routes::getPageName(), ['reset'])) { echo 'menu-open'; } ?>">
            <a href="#" class="nav-link <?php if(in_array(Routes::getPageName(), ['reset'])) { echo 'active'; } ?>">
              <i class="nav-icon fas fa-cog"></i>
              <p>
                System Tools
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="./backup.php" class="nav-link">
                  <i class="fas fa-download nav-icon"></i>
                  <p>Backup</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./import" class="nav-link <?php if(Routes::getPageName() == 'import') { echo 'active'; } ?>">
                  <i class="fas fa-upload nav-icon"></i>
                  <p>Import</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./reset" class="nav-link <?php if(Routes::getPageName() == 'reset') { echo 'active'; } ?>">
                  <i class="fas fa-sync-alt nav-icon"></i>
                  <p>Reset</p>
                </a>
              </li>
            </ul>
          </li>
          <?php }?>
        
          <li class="nav-item">
            <a href="./logout" class="nav-link">
              <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
              <p class="text">Logout</p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>