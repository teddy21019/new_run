<?php

class Supply{

    private $_db;

    private static $_instance = null;

    private function __construct(){
        $_db = DB::singleton();
    }

    public function singleton(){
        if(self::$_instance == null){
            self::$_instance = new Supply();
        }

        return self::$_instance;
    }


    public function getFieldById($field, $id){
        return $this->_db->select('supply', ['id','=',$id])->firstResult()->$field;
    }
}