<?php

class Task {
    public $id;
    public $title;
    public $description;
    public $assignee;

    function __construct($id, $title, $description, $assignee) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->assignee = $assignee;
    }

}
