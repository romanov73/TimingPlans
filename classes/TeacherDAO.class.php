<?php
require_once 'EntityDAO.php';

class TeacherDAO implements EntityDAO {
    private $db;
    
    function __construct($db) {
        $this->db = $db;
    }

    public function find_all() {
        return $this->fetch($this->db->query("select * from teacher order by fio"));
    }

    public function create($entity) {
        echo "TODO: implement";
    }

    public function delete($id) {
        echo "TODO: implement";
    }

    public function find_by_id($id) {
        return $this->fetch($this->db->query("select * from teacher where id = %id% order by fio", $id))[0];
    }

    public function update($entity) {
        echo "TODO: implement";
    }
    
    private function fetch($results) {
        $teachers = array();
        while ($row = pg_fetch_assoc($results)) {
            array_push($teachers, new Teacher($row['id'], $row['fio'], $row['position'], $row['title']));
        }
        return $teachers;
    }
}
