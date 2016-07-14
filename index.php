<?php
    define("ROOT_DIR", "./");
    include("includes/start.inc.php");

    //routing
    if (isset($_GET['completed'])) {
        include 'modules/completed.php';
    } else {
        include 'modules/new.php';
    }
