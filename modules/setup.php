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
            CREATE TABLE teacher
            (
              id serial NOT NULL,
              fio character varying(250),
              \"position\" character varying(100),
              title character varying,
              CONSTRAINT pk_teacher PRIMARY KEY (id)
            );
            
            CREATE TABLE subject
            (
              id serial NOT NULL,
              name character varying(250),
              lect_hours integer,
              pract_hours integer,
              lab_hours integer,
              validation character varying(100),
              CONSTRAINT pk_subject PRIMARY KEY (id)
            );

            CREATE TABLE \"group\"
            (
              id serial NOT NULL,
              name character varying(250),
              count_subgroups integer,
              CONSTRAINT pk_group PRIMARY KEY (id)
            );
            
            CREATE TABLE stream
            (
              id serial NOT NULL,
              subject_id integer,
              CONSTRAINT pk_stream PRIMARY KEY (id),
              CONSTRAINT fk_stream_subject FOREIGN KEY (subject_id)
                  REFERENCES subject (id) MATCH SIMPLE
                  ON UPDATE CASCADE ON DELETE RESTRICT
            );
            
            CREATE TABLE group_stream
            (
              id serial NOT NULL,
              group_id integer,
              stream_id integer,
              CONSTRAINT pk_group_stream PRIMARY KEY (id),
              CONSTRAINT fk_group_stream_group FOREIGN KEY (group_id)
                  REFERENCES \"group\" (id) MATCH SIMPLE
                  ON UPDATE CASCADE ON DELETE RESTRICT,
              CONSTRAINT fk_group_stream_stream FOREIGN KEY (stream_id)
                  REFERENCES stream (id) MATCH SIMPLE
                  ON UPDATE CASCADE ON DELETE RESTRICT
            );
            CREATE TABLE timing_plan
            (
              id serial NOT NULL,
              group_id integer,
              subject_id integer,
              teacher_id integer,
              semester integer,
              year integer,
              group_stream_id integer,
              CONSTRAINT pk_timing_plan PRIMARY KEY (id),
              CONSTRAINT fk_timing_plan_group FOREIGN KEY (group_id)
                  REFERENCES \"group\" (id) MATCH SIMPLE
                  ON UPDATE CASCADE ON DELETE RESTRICT,
              CONSTRAINT fk_timing_plan_group_stream FOREIGN KEY (group_stream_id)
                  REFERENCES group_stream (id) MATCH SIMPLE
                  ON UPDATE CASCADE ON DELETE RESTRICT,
              CONSTRAINT fk_timing_plan_teacher FOREIGN KEY (teacher_id)
                  REFERENCES teacher (id) MATCH SIMPLE
                  ON UPDATE CASCADE ON DELETE RESTRICT
            );
            ";
        $db->query($db_dump_sql);
    }
    
    function checkDBStructure($db) {
        $sql_check = "
            SELECT id, name, count_subgroups FROM \"group\";
            SELECT id, group_id, stream_id FROM group_stream;
            SELECT id, subject_id FROM stream;
            SELECT id, name, lect_hours, pract_hours, lab_hours, validation FROM subject;
            SELECT id, fio, \"position\", title FROM teacher;
            SELECT id, group_id, subject_id, teacher_id, semester, year, group_stream_id FROM timing_plan;
        ";
        $db->query($sql_check);
    }
    
    function generateTestData($db) {
        $sql_insert = "
            BEGIN;
            INSERT INTO teacher(fio, \"position\", title) VALUES ('Иванов Иван Иванович', 'доцент', 'доцент');
            INSERT INTO teacher(fio, \"position\", title) VALUES ('Петров Петр Петрович', 'доцент', 'доцент');
            INSERT INTO teacher(fio, \"position\", title) VALUES ('Сидор Лютый Джафарович', 'ассистент', 'доцент');
            INSERT INTO subject(name, lect_hours, pract_hours, lab_hours, validation) VALUES ('Теор Вер', 16, 32, 32, 'экзамен');
            INSERT INTO subject(name, lect_hours, pract_hours, lab_hours, validation) VALUES ('Выч мат', 16, 32, 32, 'экзамен');
            INSERT INTO subject(name, lect_hours, pract_hours, lab_hours, validation) VALUES ('ООП', 16, 32, 32, 'экзамен, курсовая работа');
            INSERT INTO \"group\"(name, count_subgroups) VALUES ('ПИбд-41', 1);
            INSERT INTO \"group\"(name, count_subgroups) VALUES ('ПИбд-31', 2);
            INSERT INTO \"group\"(name, count_subgroups) VALUES ('ИСЭбд-31', 2);
            INSERT INTO \"group\"(name, count_subgroups) VALUES ('ПИбд-21', 2);
            INSERT INTO stream(subject_id) VALUES ((select id from subject limit 1 offset 2));
            INSERT INTO group_stream(group_id, stream_id) VALUES ((select id from \"group\" limit 1 offset 1), (select id from stream limit 1));
            INSERT INTO group_stream(group_id, stream_id) VALUES ((select id from \"group\" limit 1 offset 2), (select id from stream limit 1));
            INSERT INTO timing_plan(
                group_id, subject_id, teacher_id, semester, year, group_stream_id)
            VALUES ((select id from \"group\" limit 1 offset 1),
                    (select id from subject limit 1 offset 2),
                    (select id from teacher limit 1),
                    5, 2015, (select id from group_stream limit 1));
            INSERT INTO timing_plan(
                group_id, subject_id, teacher_id, semester, year, group_stream_id)
            VALUES ((select id from \"group\" limit 1 offset 2),
                    (select id from subject limit 1 offset 2),
                    (select id from teacher limit 1),
                    5, 2015, (select id from group_stream limit 1 offset 1));
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