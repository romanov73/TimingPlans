<?php    
    require_once 'classes/SubjectDAO.class.php';
    require_once 'classes/Subject.class.php';
    require_once 'classes/TeacherDAO.class.php';
    require_once 'classes/Teacher.class.php';

    if (isset($_GET['get_subject_list']) 
            && isset($_GET['teacher_id'])
            && $_GET['teacher_id'] != -1) {   
        $subject_list = array();
        array_push($subject_list, new Subject(-1, 'Выберите'));
        $subject_list = array_merge($subject_list, (new SubjectDAO($db))->find_by_teacher($_GET['teacher_id']));
        echo(json_encode($subject_list));
        exit();
    }    
    
    $teacher_list = array();
    array_push($teacher_list, new Teacher(-1, 'Выберите'));
    $teacher_list = array_merge($teacher_list, (new TeacherDAO($db))->find_all());
    
    
    $smarty->assign('teacher_list', $teacher_list);
    $smarty->assign('page_title', 'Ввод расчасовок');
   
    $smarty->display("new.tpl");