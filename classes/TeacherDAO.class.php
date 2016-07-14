<?php
require_once 'EntityDAO.php';

class TeacherDAO implements EntityDAO {
    private $db;
    
    function __construct($db) {
        $this->db = $db;
    }

    public function find_all() {
        return $this->fetch($this->db->query("select * from raschas.worker order by lname limit 10"));
    }

    public function create($entity) {
        
    }

    public function delete($id) {
        
    }

    public function find_by_id($id) {
        
    }

    public function update($entity) {
        
    }
    
    private function fetch($results) {
        $teachers = array();
        while ($row = pg_fetch_assoc($results)) {
            array_push($teachers, new Teacher($row['id'], $row['lname'].' '.$row['fname'].' '.$row['sname'], '', ''));
        }
        return $teachers;
    }
}
