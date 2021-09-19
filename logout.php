<?php
    session_start();

    include_once('./config/Database.php');
    include_once('./models/Users.php');

    Users::Logout(new Database(), $_SESSION['user']['id']);

    session_destroy();

    header('location: ./login');