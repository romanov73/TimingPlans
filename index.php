<?php
    define("ROOT_DIR", "./");
    include("includes/start.inc.php");

    //routing
    if (isset($_GET['list'])) {
        include 'modules/list.php';
    } else if (isset($_GET['task_id'])) {
        include 'modules/edit.php';
    } else {
        include 'modules/list.php';
    }
