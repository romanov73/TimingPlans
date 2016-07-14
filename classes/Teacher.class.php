<?php

class Teacher {
    public $id;
    public $FIO;
    public $position;
    public $title;
    
    public function __construct($id, $FIO = null, $position = null, $title = null) {
        $this->id = $id;
        $this->FIO = $FIO;
        $this->position = $position;
        $this->title = $title;
    }
}
