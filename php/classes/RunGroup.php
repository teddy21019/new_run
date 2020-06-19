<?php

class RunGroup
{

    private $_db;

    private function __construct()
    {
        $this->_db = DB::singleton();
    }

    private static $_instance = null;
    public function singleton()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new RunGroup();
        }

        return self::$_instance;
    }

    public function exist($param)
    {
        //is number
        if (is_numeric($param)) {
            if ($this->_db->select('run_group', ['number', '=', $param])->count()) {
                return true;
            }
        } else {
            //is name
            if ($this->_db->select('run_group', ['name', '=', $param])->count()) {
                return true;
            }
            return false;
        }
    }

    public function search($keyword){
        $sql = " SELECT * FROM `run_group` 
        WHERE 
        `name`LIKE ? 
        or
        `number` LIKE ?
        ORDER BY `name` ASC";
 
        return $this->_db->query($sql, ['%'. $keyword.'%' ,'%'. $keyword.'%']);
     }

    public function add($name, $paramNumber = "")
    {
        //if number used

        $number;
        if (!($this->exist($paramNumber))) {
            $number = $this->generateGroupNumber();
        } else {
            $number = $paramNumber;
        }

        $this->_db->insert('run_group', array(
            'number' => $number,
            'name' => $name
        ));

        return $number;
    }

    public function getAll(){
        return $this->_db->select('run_group')->getResults();

    }

    public function getNum($name)
    {
        return $this->_db->select('run_group', ['name', '=', $name])->firstResult()->number;
    }

    public function getName($number)
    {
        return $this->_db->select('run_group', ['number', '=', $number])->firstResult()->name;
    }

    private function generateGroupNumber()
    {
        //from 1 to 99, if empty, get number
        $allGroup = $this->_db->select('run_group')->getResults();
        $groupNum = [];
        foreach ($allGroup as $group) {
            $groupNum[] = $group->number;
        }

        for ($i = 1; $i < 100; $i++) {
            if (!in_array($i, $groupNum)) {
                return $i;
            } else {
                continue;
            }
        }
        return false;


    }
}