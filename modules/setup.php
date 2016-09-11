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
              wish text,
              a_lec character varying(255),
              a_prac character varying(255),
              a_lab character varying(255),
              pl1 character varying(255),
              pl2 character varying(255),
              pp1 character varying(255),
              pp2 character varying(255),
              plb1 character varying(255),
              plb2 character varying(255),
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
            CREATE TABLE form
            (
              id serial NOT NULL,
              name character varying(100),
              CONSTRAINT pk_form PRIMARY KEY (id)
            );
            
            CREATE TABLE hours
            (
              id serial NOT NULL,
              week_num smallint,
              hours_count smallint,
              form_id integer,
              timing_plan_id integer,
              CONSTRAINT pk_hours PRIMARY KEY (id),
              CONSTRAINT fk_form FOREIGN KEY (form_id)
                  REFERENCES form (id) MATCH SIMPLE
                  ON UPDATE CASCADE ON DELETE RESTRICT,
              CONSTRAINT fk_timing_plan FOREIGN KEY (timing_plan_id)
                  REFERENCES timing_plan (id) MATCH SIMPLE
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
            SELECT id, group_id, subject_id, teacher_id, semester, year, group_stream_id, wish, a_lec, a_prac, a_lab, pl1, pl2, pp1, pp2, plb1, plb2 FROM timing_plan;
            SELECT id, week_num, hours_count, form_id, timing_plan_id FROM hours;
            SELECT id, name FROM form;
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
                id, group_id, subject_id, teacher_id, semester, year, group_stream_id, wish, a_lec, a_prac, a_lab, pl1, pl2, pp1, pp2, plb1, plb2)
            VALUES (1, (select id from \"group\" limit 1 offset 1),
                    (select id from subject limit 1 offset 2),
                    (select id from teacher limit 1),
                    5, 2015, (select id from group_stream limit 1), 'не ставить занятия в субботу', '260','260','260', 'пг1 пг2','пг1 пг2','пг1 пг2','пг1 пг2','пг1 пг2','пг1 пг2');
            INSERT INTO timing_plan(
                id, group_id, subject_id, teacher_id, semester, year, group_stream_id, wish, a_lec, a_prac, a_lab, pl1, pl2, pp1, pp2, plb1, plb2)
            VALUES (2, (select id from \"group\" limit 1 offset 2),
                    (select id from subject limit 1 offset 2),
                    (select id from teacher limit 1),
                    5, 2015, (select id from group_stream limit 1 offset 1), 'не ставить занятия в субботу', '260','260','260', 'пг1 пг2','пг1 пг2','пг1 пг2','пг1 пг2','пг1 пг2','пг1 пг2');
            INSERT INTO form(id, name) VALUES (1, 'лекция');
            INSERT INTO form(id, name) VALUES (2, 'практика');
            INSERT INTO form(id, name) VALUES (3, 'лабораторная');
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (1, 2, 1, 1);
            
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (2, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (3, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (4, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (5, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (6, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (7, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (8, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (9, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (10, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (11, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (12, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (13, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (14, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (15, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (16, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (17, 2, 1, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (18, 2, 1, 1);
            
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (1, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (2, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (3, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (4, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (5, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (6, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (7, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (8, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (9, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (10, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (11, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (12, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (13, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (14, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (15, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (16, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (17, 2, 3, 1);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (18, 2, 3, 1);

            
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (1, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (2, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (3, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (4, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (5, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (6, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (7, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (8, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (9, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (10, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (11, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (12, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (13, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (14, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (15, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (16, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (17, 2, 1, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (18, 2, 1, 2);

            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (1, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (2, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (3, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (4, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (5, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (6, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (7, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (8, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (9, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (10, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (11, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (12, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (13, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (14, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (15, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (16, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (17, 2, 3, 2);
            INSERT INTO hours(week_num, hours_count, form_id, timing_plan_id) VALUES (18, 2, 3, 2);
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