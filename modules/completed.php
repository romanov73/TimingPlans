<?php    
    require_once 'classes/TimingPlanDAO.class.php';
    require_once 'classes/TimingPlan.class.php';

    $timing_list = (new TimingPlanDAO($db))->find_all();
    $smarty->assign('timing_list', $timing_list);
    $smarty->assign('page_title', 'Готовые расчасовки');
    
    $smarty->display("completed.tpl");