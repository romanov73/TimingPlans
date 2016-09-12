<?php
require_once 'libs/phpword/PHPWord.php';
require_once 'classes/TimingPlanDAO.class.php';
require_once 'classes/TimingPlan.class.php';


function send_file($file) {
    ob_clean();
    if (file_exists($file)) {
        if (false !== ($handler = fopen($file, 'r'))) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . OUTPUT_FILE_NAME);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file)); //Remove
            readfile($file);
        }
        exit;
    }
}

$PHPWord = new PHPWord();
$document = $PHPWord->loadTemplate('modules/template.docx');

$timing = (new TimingPlanDAO($db))->find_by_id($_GET['timing_plan_id']);
$document->setValue('subject_name', $timing->subject->name);
$document->setValue('validation', $timing->subject->validation);
$document->setValue('group_name', $timing->group->name);
$document->setValue('semester', $timing->semester);
$document->setValue('year', $timing->year."/".($timing->year+1));

$sum_lec = 0;
$sum_pr = 0;
$sum_lab = 0;
$ws = array();
for ($i=0; $i<count($timing->hours); $i++) {
    if ($timing->hours[$i]['form_id'] == 1) {
        $document->setValue('wl'.$timing->hours[$i]['week_num'], $timing->hours[$i]['hours_count']);    
        $sum_lec += $timing->hours[$i]['hours_count'];
    }
    if ($timing->hours[$i]['form_id'] == 2) {
        $document->setValue('wp'.$timing->hours[$i]['week_num'], $timing->hours[$i]['hours_count']);    
        $sum_pr += $timing->hours[$i]['hours_count'];
    }
    if ($timing->hours[$i]['form_id'] == 3) {
        $document->setValue('wlb'.$timing->hours[$i]['week_num'], $timing->hours[$i]['hours_count']);    
        $sum_lab += $timing->hours[$i]['hours_count'];
    }
    $ws[$timing->hours[$i]['week_num']] += $timing->hours[$i]['hours_count'];
}

for ($i=0; $i<WEEKS_COUNT+1; $i++) {
    $document->setValue("ws$i", $ws[$i]);
}
$document->setValue('wls', $sum_lec);
$document->setValue('stream', $timing->group_stream);
$document->setValue('wps', $sum_pr);
$document->setValue('wlbs', $sum_lab);
$document->setValue('wss', $sum_lec + $sum_lab + $sum_pr);
$document->setValue('wish', $timing->wish);
$document->setValue('al', $timing->a_lec);
$document->setValue('ap', $timing->a_prac);
$document->setValue('alb', $timing->a_lab);
$document->setValue('pl1', $timing->pl1);
$document->setValue('pl2', $timing->pl2);
$document->setValue('pp1', $timing->pp1);
$document->setValue('pp2', $timing->pp2);
$document->setValue('plb1', $timing->plb1);
$document->setValue('plb2', $timing->plb2);
$file_location = sys_get_temp_dir().DIRECTORY_SEPARATOR.md5(date('Y-m-d H:i:s:u')).'.docx'; 
$document->save($file_location);
send_file($file_location);
unlink($file_location);