<?php
    require_once 'classes/Task.class.php';
    require_once 'classes/TaskDAO.class.php';

    $task_list = (new TaskDAO($db))->find_all();
    $smarty->assign('task_list', $task_list);
    $smarty->assign('page_title', 'Список задач');
    
    $smarty->display("list.tpl");