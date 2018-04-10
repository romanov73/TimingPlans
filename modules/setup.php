<?php    
//check smarty writeable dir
    function checkDBConfigured($db, $smarty) {
        $connection_configured = ($db->get_connection()['host'] !== '') 
            && ($db->get_connection()['user_login'] !== '') 
            && ($db->get_connection()['db_name'] !== '') 
            && ($db->get_connection()['user_passwd'] !== '');
        $smarty->assign('db_configured', $connection_configured);        
    }
        
    function checkDBConnectSuccessfull($db, $smarty) {
        $db->query("select 'test str'");
        $smarty->assign('db_connection_exists', isset($db->get_connection()['link']));        
    }


    function clearDB($db) {
        $db->query("drop schema public cascade;
                create schema public;");
    }
    
    function createDB($db) {
        clearDB($db);
        $db_dump_sql = "
            set search_path to 'public';
            CREATE TABLE task
            (
              id serial NOT NULL,
              title character varying(250),
              description character varying(250),
              assignee_id integer,
              CONSTRAINT pk_task PRIMARY KEY (id)
            );
            CREATE TABLE assignee
            (
              id serial NOT NULL,
              name character varying(250),
              CONSTRAINT pk_assignee PRIMARY KEY (id)
            );
            ";

        $db->query($db_dump_sql);
    }
    
    function checkDBStructure($db) {
        $sql_check = "
            SELECT id, title, description, assignee_id FROM task;
            SELECT id, name FROM assignee;
        ";
        $db->query($sql_check);
    }
    
    function generateTestData($db) {
        $sql_insert = "
            BEGIN;
            INSERT INTO assignee(id, name) VALUES (1, 'Иванов Иван');
            INSERT INTO assignee(id, name) VALUES (2, 'Петров Петр');
            
            INSERT INTO task(title, description, assignee_id) VALUES ('Task: Спроектировать БД', '1. Выделить сущности Пр.О. 2. Написать скрипты', 1);
            INSERT INTO task(title, description, assignee_id) VALUES ('Task: Создать классы сущностей', 'По таблицам в БД создать классы в подкаталоге classes и соответствующие DAO классы', 1);
            INSERT INTO task(title, description, assignee_id) VALUES ('Bug: Решить проблему с развертыванием новой системы', 'При установке сделать вызов скрипта setup.php', 2);

            commit;
        ";
        $db->query($sql_insert);
    }
    
    function checkSmartyDirectoryWritable() {
        $newFileName = SMARTY_COMPILE_DIR.'file.txt';
        if ( ! is_writable(dirname($newFileName))) {
            echo dirname($newFileName) . ' must exist and be writable!!!';
        } else {
           // blah blah blah
        }
    }
    
    if ($_GET['clear_db']) {
        clearDB($db);
        ob_clean();
        if (!$db->get_last_error()) {
            echo("&#9745  Успешно очищена");
        } else {
            echo("&#9746 " . $db->get_last_error());
        }
        exit();
    }
    
    if ($_GET['create_db']) {
        createDB($db);
        ob_clean();
        if (!$db->get_last_error()) {
            echo("&#9745  Успешно создана");
        } else {
            echo("&#9746 " . $db->get_last_error());
        }
        exit();
    }
    
    if ($_GET['check_db_structure']) {
        checkDBStructure($db);
        ob_clean();
        if (!$db->get_last_error()) {
            echo("&#9745  Структура соответствует");
        } else {
            echo("&#9746 " . $db->get_last_error());
        }
        exit();
    }
    
    if ($_GET['generate_test_data']) {
        generateTestData($db);
        ob_clean();
        if (!$db->get_last_error()) {
            echo("&#9745  Данные сгенерированы");
        } else {
            echo("&#9746 " . $db->get_last_error());
        }
        exit();
    }

    $smarty->assign('page_title', 'Установка');
    
    checkSmartyDirectoryWritable();
    checkDBConfigured($db, $smarty);
    checkDBConnectSuccessfull($db, $smarty);
    
          
    $smarty->display("setup.tpl");