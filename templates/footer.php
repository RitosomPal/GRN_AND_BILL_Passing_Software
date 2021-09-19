<?php
    @session_start();
    if(!isset($_SESSION['user'])) { header('location: ./login'); }
?>
<footer class="main-footer">
  <strong>Copyright &copy; 2020 <a href="/TechniqueOffice/GRN_AND_BILL_Passing_Software">GRN & Billing Software</a>.</strong>
  All rights reserved.
  <div class="float-right d-none d-sm-inline-block">
    <b>Version</b> 1.0.0
  </div>
</footer>