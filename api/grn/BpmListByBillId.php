<?php
    // Set Content Type
    header('Content-Type: application/json');

    // Include Files
    include_once('../../config/Database.php');
    include_once('../../models/BPM.php');

    // Default Variable
    $json = array();

    if (empty($_POST)) {
        $json = array('msg'=>["Invalid Request."], 'success'=>false);
    } elseif (!isset($_POST['userId']) || !isset($_POST['billId'])) {
        $json = array('msg'=>["Insert all required data!"], 'success'=>false);
    } else {
        $json = BPM::BpmListByBillId(new Database(), $_POST['userId'], $_POST['billId']);;
    }
    echo json_encode($json);