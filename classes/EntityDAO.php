<?php
interface EntityDAO {
    public function create($entity);
    public function update($entity);
    public function delete($id);
    public function find_by_id($id);
    public function find_all();
}
