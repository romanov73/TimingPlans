<?php

class Subject {
    public $id;
    public $name;
    public $lection_hours;
    public $labs_hours;
    public $practic_hours;
    public $validation;
    
    function __construct($id, $name, $lection_hours = null, $labs_hours = null, $practic_hours = null, $validation = null) {
        $this->id = $id;
        $this->name = $name;
        $this->lection_hours = $lection_hours;
        $this->labs_hours = $labs_hours;
        $this->practic_hours = $practic_hours;
        $this->validation = $validation;
    }
}
