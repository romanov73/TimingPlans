<?php

class TimingPlan {
    public $id;
    public $group;
    public $subject;
    public $teacher;
    public $semester;
    public $year;
    public $group_stream;
    
    function __construct($id, $group, $subject, $teacher, $semester, $year, $group_stream) {
        $this->id = $id;
        $this->group = $group;
        $this->subject = $subject;
        $this->teacher = $teacher;
        $this->semester = $semester;
        $this->year = $year;
        $this->group_stream = $group_stream;
    }

}
