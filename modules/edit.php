<?php
require_once 'classes/TaskDAO.class.php';
require_once 'classes/Task.class.php';
require_once 'classes/AssigneeDAO.class.php';
require_once 'classes/Assignee.class.php';

if ($_POST['cancel']) {
    header("Location: ?list=1");
    exit;
}

if ($_POST['save']) {
    $assignee = (new AssigneeDAO($db))->find_by_id($_POST['assignee']);
    $task = new Task($_GET['task_id'], $_POST['title'], $_POST['description'], $assignee);
    if (!$_POST['title']) {
        $smarty->assign('error', "Заголовок должен быть не пустым");
    } else if (!$_POST['description']) {
        $smarty->assign('error', "Описание должно быть не пустым");
    } else if (!$_POST['assignee']) {
        $smarty->assign('error', "Выберите исполнителя");
    } else {
        $dao = new TaskDAO($db);
        if ($task->id) {
            $dao->update($task);
        } else {
            $dao->create($task);
        }
        header("Location: ?list=1");
        exit;
    }
}

if ($_POST['delete']) {
    (new TaskDAO($db))->delete($_GET['task_id']);
    header("Location: ?list=1");
    exit;
}

if ($_GET['task_id']) {
    $task = (new TaskDAO($db))->find_by_id($_GET['task_id']);
}

$assignees = (new AssigneeDAO($db))->find_all();

$smarty->assign('task', $task);
$smarty->assign('assignees', $assignees);
$smarty->assign('page_title', 'Редактирование задачи');
$smarty->display("edit.tpl");