<?php
    session_start();

    require_once('./config/DotEnv.php');
    include_once('./config/Database.php');
    include_once('./models/Users.php');
    (new DotEnv(__DIR__ . '/config/.env'))->load();

    Users::Logout(new Database(), $_SESSION['user']['id']);

    session_destroy();

    // $db = new Database;
    // $r = $db->Backup();
    // if($r['success']) {
    //     $fname = explode('_',$r['fileName']);
    //     array_pop($fname);
    //     $fname = implode('_',$fname);
    //     $file = $r['filePath'].'/'.$r['fileName'].'.zip';
    //     $newfile = $_ENV['AUTO_BACKUP_PATH'].'/'.$fname.'.db';
    //     copy($file, $newfile);
    //     unlink($file);
    // }

    header('location: ./login');