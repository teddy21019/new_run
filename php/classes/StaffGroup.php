<?php

class StaffGroup
{

    
    private $_db;

    private function __construct(){
        $this->_db = DB::singleton();
    }

    private static $_instance = null;

    public function singleton(){
        if(!isset(self::$_instance)){
            self::$_instance = new StaffGroup();
        }
        return self::$_instance;
    }


    public function getTypes(){
        return $this->_db->select('staff_type')->getResults();
    }

    public function getPositions(){
        return $this->_db->select('position')->getResults();
    }

    public function getPositionById($id){
        return $this->_db->select('position',['id','=',$id])->firstResult();
    }


}
