<?php
    require_once(ROOT_DIR . "config.php");
    require_once(ROOT_DIR . SMARTY_DIR."Smarty.class.php");
    require_once(ROOT_DIR . "classes/common/pgsql.class.php");
   
    if (DEBUG) {
        ini_set('display_errors', 'On');
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
    } else {
        ini_set('display_errors', 'Off');
        error_reporting(0);
    }

    
    $smarty = new Smarty();
    $smarty->setTemplateDir(SMARTY_TEMPLATE_DIR)
       ->setCompileDir(SMARTY_COMPILE_DIR);
    $connect_data = array(
        'host'      => (defined("DB_SYSTEM_HOST") ? DB_SYSTEM_HOST : "")
        , 'user'    => (defined("DB_SYSTEM_USER") ? DB_SYSTEM_USER : "")
        , 'passwd'  => (defined("DB_SYSTEM_PASS") ? DB_SYSTEM_PASS : "")
        , 'db_name' => (defined("DB_SYSTEM_DBNAME") ? DB_SYSTEM_DBNAME : "")
    );
    MyDB::config("system", $connect_data);
    $db = MyDB::get_instance();
