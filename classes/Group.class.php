<?php

class Group {
    public $id;
    public $name;
    public $count_subgroups;

    function __construct($id, $name, $count_subgroups = null) {
        $this->id = $id;
        $this->name = $name;
        $this->count_subgroups = $count_subgroups;
    }

}
