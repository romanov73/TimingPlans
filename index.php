<?php
    define("ROOT_DIR", "./");
    include("includes/start.inc.php");

    //routing
    if (isset($_GET['list'])) {
        include 'modules/list.php';
    } else {
        include 'modules/edit.php';

    }
