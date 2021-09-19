<?php
    session_start();
    if(!isset($_SESSION['user'])) { header('location: ./login'); }

    require_once('./config/Database.php');
    require_once('./models/Permissions.php');

    $db = new Database;
    $P = Permissions::getUserPermissions($db,$_SESSION['user']['id'])['data'];
    if(!$P['groups']['view']) { header('location: ./'); }

    $r = $db->Backup();

    if($r['success']) {
        // Set Content Type
        header('Content-Type: application/octet-stream');
        // header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: ".filesize($r['filePath'].'/'.$r['fileName'].'.zip'));
        header("Content-disposition: attachment; filename=\"".$r['fileName'].".db\""); 
        readfile($r['filePath'].'/'.$r['fileName'].'.zip');
        unlink($r['filePath'].'/'.$r['fileName'].'.zip');
    } else {
        echo 'Unable to create backup!';
    }