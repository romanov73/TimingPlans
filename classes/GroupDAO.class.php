<?php
require_once 'EntityDAO.php';

class GroupDAO implements EntityDAO {
    private $db;
    
    function __construct($db) {
        $this->db = $db;
    }

    public function find_all() {
        return $this->fetch($this->db->query("select * from \"group\" order by name"));
    }

    public function create($entity) {
        echo "TODO: implement";
    }

    public function delete($id) {
        echo "TODO: implement";
    }

    public function find_by_id($id) {
        return $this->fetch($this->db->query("select * from \"group\" where id = %id% order by name", $id))[0];
    }

    public function update($entity) {
        echo "TODO: implement";
    }
    
    private function fetch($results) {
        $groups = array();
        while ($row = pg_fetch_assoc($results)) {
            array_push($groups, new Group($row['id'], $row['name'], $row['count_subgroups']));
        }
        return $groups;
    }
}
