<?php
    define("ROOT_DIR", "./");
    include("includes/start.inc.php");

    //routing
    if (isset($_GET['completed'])) {
        include 'modules/completed.php';
    } else if (isset($_GET['timing_plan_id'])) {
        include 'modules/timing_plan_docx.php';
    } else {
        include 'modules/new.php';
    }
