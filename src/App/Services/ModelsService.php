<?php

namespace App\Services;

class ModelsService extends BaseService
{

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM models");
    }

    public function get($start,$count,$tags,$orderBy){
        
        if($tags){
            $query = "SELECT DISTINCT id, name, filename, size, createAt FROM models M JOIN models_tagged MT ON(M.id = MT.idmodel) WHERE MT.idterm IN ($tags)";
        }else{
            $query = "SELECT * FROM models";
        }

        if($orderBy &&  in_array(strtoupper($orderBy), ['ASC', 'DESC'])){
             $query .= " ORDER BY createAt $orderBy";
        }
        
        $query .= "  LIMIT $start, $count";
        $models = $this->db->fetchAll($query);
        for($i = 0, $len = count($models); $i < $len; $i++){
            $models[$i]['tags'] = $this->db->fetchAll("SELECT id, text FROM terms T JOIN models_tagged MT ON(T.id = MT.idterm) WHERE idmodel = :model ", array(':model' => $models[$i]['id']));
        }
        return $models;
    }
    
    public function getModelById($id){
        $statement = $this->db->executeQuery('SELECT * FROM models WHERE id = :model', array(':model' => $id));
        return $statement->fetch();
    }

    public function count(){
        return $this->db->executeQuery("SELECT * FROM models")->rowCount();
    }

    public function save($model)
    {
        $this->db->insert("models", $model);
        return $this->db->lastInsertId();
    }
    
    public function delete($id){
       return $this->db->delete('models',array('id' => $id ));
    }

}
