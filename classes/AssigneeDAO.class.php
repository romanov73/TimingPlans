<?php
require_once 'EntityDAO.php';
require_once 'classes/Assignee.class.php';

class AssigneeDAO implements EntityDAO {
    private $db;
    
    function __construct($db) {
        $this->db = $db;
    }

    public function find_all() {
        return $this->fetch($this->db->query("SELECT * FROM public.assignee"));
    }

    public function create($entity) {
        echo ("todo!");
    }

    public function delete($id) {
        echo ("todo!");
    }

    public function find_by_id($id) {
        return $this->fetch($this->db->query("SELECT * FROM public.assignee a WHERE a.id = %id%", $id))[0];
    }
    
    public function update($entity) {
        echo ("todo!");
    }
    
    private function fetch($results) {
        $assignees = array();
        while ($row = pg_fetch_assoc($results)) {
            array_push($assignees, new Assignee($row['id'], $row['name']));
        }
        return $assignees;
    }
}
