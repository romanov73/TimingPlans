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
        return $this->fetch($this->db->query("select tp.*, g.name as group_name, g.count_subgroups, t.fio, s.name as subject_name, s.validation from timing_plan tp
                                                left join \"group\" g on g.id = tp.id
                                                left join teacher t on t.id = tp.teacher_id
                                                left join subject s on s.id = tp.subject_id
                                                left join group_stream gs on gs.id = tp.group_stream_id
                                                order by tp.year, tp.semester, s.name"));
    }

    public function create($entity) {
        echo "TODO: implement";
    }

    public function delete($id) {
        echo "TODO: implement";
    }

    public function find_by_id($id) {
        return $this->fetch($this->db->query("select tp.*, g.name as group_name, g.count_subgroups, t.id as teacher_id, t.fio, t.position, t.title, s.name as subject_name, s.validation from timing_plan tp
                                                left join \"group\" g on g.id = tp.id
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
                    $row['semester'], $row['year'], $row['title'],
                    $row['wish'], $row['a_lec'], $row['a_prac'], $row['a_lab'], $row['pl1'],$row['pl2'],$row['pp1'],$row['pp2'],$row['plb1'],$row['plb2'], $this->get_hours($row['id'])));
        }
        return $timings;
    }
}
