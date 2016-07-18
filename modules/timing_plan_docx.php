<?php
require_once 'libs/phpword/PHPWord.php';
require_once 'classes/TimingPlanDAO.class.php';
require_once 'classes/TimingPlan.class.php';

$PHPWord = new PHPWord();
$document = $PHPWord->loadTemplate('modules/template.docx');

$timing = (new TimingPlanDAO($db))->find_by_id($_GET['timing_plan_id']);
print_r($timing->group->name);
$document->setValue('subject_name', $timing->subject->name);
$document->setValue('validation', $timing->subject->validation);
$document->setValue('group_name', $timing->group->name);
$document->setValue('semester', $timing->semester);
$document->setValue('year', $timing->year."/".($timing->year+1));
for ($i=0; $i<19; $i++) {
    $document->setValue('wl'.$i, "2");
    $document->setValue('wp'.$i, "2");
    $document->setValue('wlb'.$i, "2");
    $document->setValue('ws'.$i, "2");
}
$document->setValue('wls', "2");
    $document->setValue('wps', "2");
    $document->setValue('wlbs', "2");
    $document->setValue('wss', "2");


$document->save('/tmp/Solarsystem.docx');

