<?php
  @session_start();
  if(!isset($_SESSION['user'])) { header('location: ./login'); }

  require_once('./config/Database.php');
  require_once('./models/Permissions.php');

  $P = Permissions::getUserPermissions(new Database(),$_SESSION['user']['id'])['data'];
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="./" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="./profile" class="nav-link">Profile</a>
      </li>
      <?php if($P['grn']['view']) { ?>
      <li class="nav-item d-none d-sm-inline-block dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          GRN
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <?php if($P['grn']['create']) { ?>
          <a class="dropdown-item" href="./grn_create">Create</a>
          <?php } ?>
          <?php if($P['grn']['view']) { ?>
          <a class="dropdown-item" href="./grn_view">View</a>
          <?php } ?>
        </div>
      </li>
      <?php } ?>
      <?php if($P['bpm']['view']) { ?>
      <li class="nav-item d-none d-sm-inline-block dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          BPM
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
          <?php if($P['bpm']['create']) { ?>
          <a class="dropdown-item" href="./bpm_create">Create</a>
          <?php } ?>
          <?php if($P['bpm']['view']) { ?>
          <a class="dropdown-item" href="./bpm_view">View</a>
          <?php } ?>
        </div>
      </li>
      <?php } ?>
    </ul>

    <!-- SEARCH FORM -->
    <!-- <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
            <i class="fas fa-search"></i>
          </button>
        </div>
      </div>
    </form> -->

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item d-none d-sm-inline-block">
        <a href="./logout" class="nav-link text-danger" title="Logout">
            <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>
    </ul>
</nav>