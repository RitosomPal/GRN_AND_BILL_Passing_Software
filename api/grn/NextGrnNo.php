<?php
    // Set Content Type
    header('Content-Type: application/json');

    // Include Files
    include_once('../../config/Database.php');
    include_once('../../models/GRN.php');

    // Default Variable
    $json = array();

    if (empty($_POST)) {
        $json = array('msg'=>["Invalid Request."], 'success'=>false);
    } elseif (!isset($_POST['userId']) || !isset($_POST['book'])) {
        $json = array('msg'=>["Insert all required data!"], 'success'=>false);
    } else {
        $json = GRN::getNextGrnNo(new Database(),$_POST['userId'],$_POST['book']);
    }
    echo json_encode($json);