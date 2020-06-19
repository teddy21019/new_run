<?php
class Staff{
    private $_db;
    private function __construct(){
        $this->_db = DB::singleton();
    }

    private static $_instance = null;

    public function singleton(){
        if(!isset(self::$_instance)){
            self::$_instance = new Staff();
        }

        return self::$_instance;
    }

    public function getDataById($id){
        return $this->_db->
        select('staff', ['id','=',$id])->
        firstResult();
    }

    public function getFieldById($field, $id){
        return $this->getDataById($id)->$field;
    }


    public function checkIDExist($id){
        return $this->_db->
        select('staff', ['id','=',$id])->
        firstResult();
    }

    public function search($keyword){
        $sql = " SELECT * FROM `staff` 
        WHERE 
        `name`LIKE ? 
        or
        `tel` LIKE ?
        or 
        `uid` LIKE ?
        or
        `position` IN (SELECT `id` FROM `position` WHERE `name` LIKE ?)
        ORDER BY `name` ASC";
 
        return $this->_db->query($sql, ['%'. $keyword.'%' ,'%'. $keyword.'%' ,'%'. $keyword.'%','%'. $keyword.'%']);
     }

     public function update($id, $newData){
        $this->_db->update('staff',$newData, $condition = ['id','=',$id]);
     }

     public function add($data){
         $this->_db->insert('staff', $data);
     }

     public function remove($id){
         $this->_db->delete('staff', ['id','=',$id]);
     }
}