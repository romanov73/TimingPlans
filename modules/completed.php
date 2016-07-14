<?php    
    require_once 'classes/SubjectDAO.class.php';
    require_once 'classes/Subject.class.php';
    require_once 'classes/TeacherDAO.class.php';
    require_once 'classes/Teacher.class.php';
    
    $teacher_list = (new TeacherDAO($db))->find_all();
    $smarty->assign('teacher_list', $teacher_list);
    
    if (isset($_GET['get_teacher_list'])) {   
        $subject_DAO = new SubjectDAO($db);
        $subject_list = $subject_DAO->find_all();
        $smarty->assign('subject_list', $subject_list);
    }
    $smarty->assign('page_title', 'Готовые расчасовки');
    
    $smarty->display("completed.tpl");