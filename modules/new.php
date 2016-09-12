<?php    
    require_once 'classes/SubjectDAO.class.php';
    require_once 'classes/Subject.class.php';
    require_once 'classes/TeacherDAO.class.php';
    require_once 'classes/Teacher.class.php';
    require_once 'classes/GroupDAO.class.php';
    require_once 'classes/Group.class.php';
    require_once 'classes/TimingPlanDAO.class.php';
    require_once 'classes/TimingPlan.class.php';

    function get_hours() {
        return array();
    }
    
    if ($_POST['save']) {
        $tp = new TimingPlan(null, 
                    (new GroupDao($db))->find_by_id($_POST['group_select']),
                    (new SubjectDao($db))->find_by_id($_POST['subject_select']),
                    (new TeacherDao($db))->find_by_id($_POST['teacher_select']),
                    $_POST['semestr_select'], $_POST['year_select'], null,
                    $_POST['prepod_wish'], 
                    $_POST['lection_audit'], 
                    $_POST['prac_audit'], 
                    $_POST['lab_audit'], 
                    $_POST['teacher_lect1'],
                    $_POST['teacher_lect2'],
                $_POST['teacher_prac1'],
                $_POST['teacher_prac2'],
                $_POST['teacher_lab1'],
                $_POST['teacher_lab1'], get_hours());
        (new TimingPlanDAO($db))->create($tp);
        $smarty->assign('saved', true);
    }
    
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
    $smarty->assign('weeks_count', WEEKS_COUNT);
    
    $smarty->assign('page_title', 'Ввод расчасовок');
   
    $smarty->display("new.tpl");