<?php
require_once 'EntityDAO.php';
require_once 'classes/Task.class.php';
require_once 'classes/Assignee.class.php';

class TaskDAO implements EntityDAO {
    private $db;
    
    function __construct($db) {
        $this->db = $db;
    }

    public function find_all() {
        return $this->fetch($this->db->query("SELECT t.*, a.id as assgnee_id, a.name as assignee_name 
                                              FROM public.task t LEFT JOIN assignee a ON a.id = t.assignee_id"));
    }

    public function create($entity) {
        $this->db->begin();
        $sql = "INSERT INTO public.task (title, description, assignee_id) VALUES(%title%, %description%, %assignee_id%)";
        $params = array();
        $params['title'] = $entity->title;
        $params['description'] = $entity->description;
        $params['assignee_id'] = $entity->assignee->id;
        $this->db->query($sql, $params);
        $id = $this->db->insert_id('task');
        $this->db->commit();
        return $id;
    }

    public function delete($id) {
        $this->db->query("DELETE FROM task t WHERE t.id = %id%", $id);
    }

    public function find_by_id($id) {
        return $this->fetch($this->db->query("SELECT * FROM task t WHERE t.id = %id%", $id))[0];
    }
    
    public function update($entity) {
        $this->db->begin();
        $sql = "UPDATE public.task SET title = %title%, description = %description%, assignee_id = %assignee_id% WHERE id = %id%";
        $params = array();
        $params['id'] = $entity->id;
        $params['title'] = $entity->title;
        $params['description'] = $entity->description;
        $params['assignee_id'] = $entity->assignee->id;
        $this->db->query($sql, $params);
        $this->db->commit();
    }
    
    private function fetch($results) {
        $tasks = array();
        while ($row = pg_fetch_assoc($results)) {
            $assignee = new Assignee($row['assignee_id'], $row['assignee_name']);
            array_push($tasks, new Task($row['id'], $row['title'], $row['description'], $assignee));
        }
        return $tasks;
    }
}
