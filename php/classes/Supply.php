<?php

class Supply{

    private $_db;

    private static $_instance = null;

    private function __construct(){
        $this->_db = DB::singleton();
    }

    public function singleton(){
        if(self::$_instance == null){
            self::$_instance = new Supply();
        }

        return self::$_instance;
    }


    public function getFieldById($field, $id){
        return $this->_db->select('supplies', ['id','=',$id])->firstResult()->$field;
    }

    public function getAll(){
        return $this->_db->select('supplies')->getResults();
    }
}