<?php    
    require_once 'classes/SubjectDAO.class.php';
    require_once 'classes/Subject.class.php';
    require_once 'classes/TeacherDAO.class.php';
    require_once 'classes/Teacher.class.php';
    require_once 'classes/GroupDAO.class.php';
    require_once 'classes/Group.class.php';

    $subject_list = array();
    array_push($subject_list, new Subject(-1, 'Выберите'));
    $subject_list = array_merge($subject_list, (new SubjectDAO($db))->find_all());
    $smarty->assign('subject_list', $subject_list);
    
    $teacher_list = array();
    array_push($teacher_list, new Teacher(-1, 'Выберите'));
    $teacher_list = array_merge($teacher_list, (new TeacherDAO($db))->find_all());
    $smarty->assign('teacher_list', $teacher_list);
    
    $group_list = array();
    array_push($group_list, new Group(-1, 'Выберите'));
    $group_list = array_merge($group_list, (new GroupDAO($db))->find_all());
    $smarty->assign('group_list', $group_list);
    
    $smarty->assign('page_title', 'Ввод расчасовок');
   
    $smarty->display("new.tpl");