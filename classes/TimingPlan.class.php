<?php

class TimingPlan {
    public $id;
    public $group;
    public $subject;
    public $teacher;
    public $semester;
    public $year;
    public $group_stream;
    public $wish;
    public $a_lec;
    public $a_prac;
    public $a_lab;
    public $pl1;
    public $pl2;
    public $pp1;
    public $pp2;
    public $plb1;
    public $plb2;
    public $hours;
    
    function __construct($id, $group, $subject, $teacher, $semester, $year, $group_stream, $wish, $a_lec, $a_prac, $a_lab, $pl1, $pl2, $pp1, $pp2, $plb1, $plb2, $hours) {
        $this->id = $id;
        $this->group = $group;
        $this->subject = $subject;
        $this->teacher = $teacher;
        $this->semester = $semester;
        $this->year = $year;
        $this->group_stream = $group_stream;
        $this->wish = $wish;
        $this->a_lec = $a_lec;
        $this->a_prac = $a_prac;
        $this->a_lab = $a_lab;
        $this->pl1 = $pl1;
        $this->pl2 = $pl2;
        $this->pp1 = $pp1;
        $this->pp2 = $pp2;
        $this->plb1 = $plb1;
        $this->plb2 = $plb2;
        $this->hours = $hours;
    }

}
