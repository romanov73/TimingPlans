<?php
require_once 'EntityDAO.php';

class SubjectDAO implements EntityDAO {
    private $db;
    
    function __construct($db) {
        $this->db = $db;
    }

    public function find_all() {
        return $this->fetch($this->db->query("select distinct id, full_name from raschas.discip_kafedra_skill limit 10"));
    }
    
    public function find_by_teacher($teacher_id) {
        return $this->fetch($this->db->query("select distinct id, full_name from raschas.discip_kafedra_skill limit 10"));
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
            array_push($teachers, new Subject($row['id'], $row['full_name'], 16, 16, 16, array("зачет", "курсовой проект")));
        }
        return $teachers;
    }
}
