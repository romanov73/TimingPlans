<?php
require_once 'EntityDAO.php';
require_once 'classes/Subject.class.php';
require_once 'classes/Teacher.class.php';
require_once 'classes/Group.class.php';

class TimingPlanDAO implements EntityDAO {
    private $db;
    
    function __construct($db) {
        $this->db = $db;
    }

    public function find_all() {
        return $this->fetch($this->db->query("select tp.*, g.name as group_name, g.count_subgroups, t.fio, s.name as subject_name, s.validation from public.timing_plan tp
                                                left join \"group\" g on g.id = tp.id
                                                left join teacher t on t.id = tp.teacher_id
                                                left join subject s on s.id = tp.subject_id
                                                left join group_stream gs on gs.id = tp.group_stream_id
                                                order by tp.year, tp.semester, s.name"));
    }

    public function create($entity) {
        $this->db->begin();
        $sql = "INSERT INTO public.stream (subject_id) values(%subject_id%)";
        $this->db->query($sql, $entity->subject->id);
        $stream_id = $this->db->insert_id('stream');
        $sql = "INSERT INTO public.group_stream (group_id, stream_id) values(%group_id%, %stream_id%)";
        $this->db->query($sql, array('group_id' => $entity->group->id, 'stream_id' => $stream_id));
        $group_stream_id = $this->db->insert_id('group_stream');
        
        $sql = "INSERT INTO public.timing_plan(
            group_id, subject_id, teacher_id, semester, year, group_stream_id, 
                wish, a_lec, a_prac, a_lab, pl1, pl2, pp1, pp2, plb1, plb2)
            VALUES (%group_id%, %subject_id%, %teacher_id%, %semester%, %year%, %group_stream_id%, 
                %wish%, %a_lec%, %a_prac%, %a_lab%, %pl1%, %pl2%, %pp1%, %pp2%, %plb1%, %plb2%);
            ";
        $params = array();
        $params['group_id'] = $entity->group->id;
        $params['subject_id'] = $entity->subject->id;
        $params['teacher_id'] = $entity->teacher->id;
        $params['year'] = $entity->year;
        $params['semester'] = $entity->semester;
        $params['group_stream_id'] = $group_stream_id;
        $params['wish'] = $entity->wish;
        $params['a_lec'] = $entity->a_lec;
        $params['a_prac'] = $entity->a_prac;
        $params['a_lab'] = $entity->a_lab;
        $params['pl1'] = $entity->pl1;
        $params['pl2'] = $entity->pl2;
        $params['pp1'] = $entity->pp1;
        $params['pp2'] = $entity->pp2;
        $params['plb1'] = $entity->plb1;
        $params['plb2'] = $entity->plb2;
        $this->db->query($sql, $params);
        $id = $this->db->insert_id('timing_plan');
        
        foreach ($entity->hours as $form => $hours) {
            $form_id = 1;
            if ($form == "labs") {
                $form_id = 3;
            } else if ($form == "prac") {
                $form_id = 2;
            }
            foreach ($hours as $week => $hour) {    
                $this->db->query("insert into hours (week_num, hours_count, form_id, timing_plan_id) values (%week_num%, %hours_count%, %form_id%, %timing_plan_id%)", array("timing_plan_id" => $id, 'week_num' => $week, 'hours_count'=> $hour, 'form_id' =>$form_id));        
            }
        }
        
        $this->db->commit();
        return $id;
    }

    public function delete($id) {
        echo "TODO: implement";
    }

    public function find_by_id($id) {
        return $this->fetch($this->db->query("select tp.*, g.name as group_name, g.count_subgroups, t.id as teacher_id, t.fio, t.position, t.title, s.name as subject_name, s.validation from timing_plan tp
                                                left join \"group\" g on g.id = tp.group_id
                                                left join teacher t on t.id = tp.teacher_id
                                                left join subject s on s.id = tp.subject_id
                                                left join group_stream gs on gs.id = tp.group_stream_id
                                                where tp.id = %id%
                                                order by tp.year, tp.semester, s.name", $id))[0];
    }
    
    private function get_hours($timin_plan_id) {
        return pg_fetch_all($this->db->query("SELECT week_num, hours_count, form_id
                                 FROM hours where timing_plan_id = %id% order by week_num", $timin_plan_id));
    }

    public function update($entity) {
        echo "TODO: implement";
    }
    
    private function fetch($results) {
        $timings = array();
        while ($row = pg_fetch_assoc($results)) {
            array_push($timings, new TimingPlan($row['id'], 
                    new Group($row['group_id'], $row['group_name']),
                    new Subject($row['subject_id'], $row['subject_name'], $row['lect_hours'],$row['lab_hours'],$row['pract_hours'],$row['validation']),
                    new Teacher($row['teacher_id'], $row['fio'], $row['position'], $row['title']),
                    $row['semester'], $row['year'], $row['group_name'],
                    $row['wish'], $row['a_lec'], $row['a_prac'], $row['a_lab'], $row['pl1'],$row['pl2'],$row['pp1'],$row['pp2'],$row['plb1'],$row['plb2'], $this->get_hours($row['id'])));
        }
        return $timings;
    }
}
