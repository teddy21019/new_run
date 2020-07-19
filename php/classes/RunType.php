<?php

class RunType
{
    /**
     * 組別資訊
     * 關於樂活組、挑戰組、xx組的資訊，都在這邊
     * 
     */

    private $_db,
        $_typeID,
        $_typeName,
        $_loaded,
        $_datas,
        $_error;

    public function __construct($typeID = null)
    {
        $this->_db = DB::singleton();
        $this->_loaded = 0;

        if (is_null($typeID)) {
            return;
        } else {
            if ($this->exist($typeID)) {
                $this->_typeID = $typeID;
                $this->_typeName = $this->_db->firstResult()->name;
                $this->_error = false;
            } else {
                $this->_error = true;
            }
        }
    }

    public function getAll(){
        $t = $this->_db->select('run_type');
        if($t->count()){
            return $t->getResults();
        }
    }

    public function getStarted(){
        return $this->_db->select('run_type', $condition=['started','=',1], $fields=[])->getResults();
    }

    private function exist($id)
    {
        /**
         * Checks whether the id exists
         */
        $t = $this->_db->select('run_type', ['id', '=', $id]);
        if ($t->count()) {
            return true;
        }
    }

    public function setID($param)
    {
        if (is_numeric($param)) {
            //傳入是id
            if ($this->exist($param)) {
                $this->_typeID = $param;
                $this->_typeName = $this->_db->select('run_type', ['id', '=', $param])->firstResult()->name;
                return $param;
            } else {
                return 0;
            }
        } else {
            //傳入是名稱，檢查名稱
            $t = $this->_db->select('run_type', ['name', '=', $param]);
            if ($t->count()) {
                $this->_typeID = $t->firstResult()->id;
                $this->_typeName = $param;
                return $this->_typeID;
            } else {
                return 0;
            }
        }
    }

    public function getName()
    {
        return $this->_typeName;
    }

    public function num($id)
    {
        return $this->_db->select('runner', ['run_type', '=', $id])->count();
    }


     // 0 (get10) 1~10  (get10) 11~20
    public function next($num = 30)
    {

        //if can't load more, back to beginning
        if ($this->_loaded >= $this->num($this->_typeID)) {
            $this->_loaded = 0;
        }


        $this->_datas = $this->getBetween($this->_loaded + 1, $num);

        //
        $this->_loaded += count($this->_datas);
        return $this->_datas;
    }

    public function getBetween($start, $num)
    {
        return $this->_db->selectSeveral(
            $table = 'runner',
            $from = $start,
            $get = $num,
            $condition = ['run_type', "=", $this->_typeID],
            $order = []
        )
            ->getResults();
    }


    public function isStart($id = null)
    {
        if (is_null($id)) {
            $id = $this->_typeID;
        }
        return $this->_db->select('run_type', ['id', '=', $id])->firstResult()->started;
    }

    public function start($id = null)
    {
        if (is_null($id)) {
            $id = $this->_typeID;
        }

        $now  = new DateTime();
        $this->_db
            ->update(
                'run_type',
                $param = array(
                    'start_time' => $now->format("Y-m-d H:i:s") ,
                    'started' => 1
                ),
                $condition = array('id', '=', $id)
            );
    }

    public function end($id = null)
    {
        if (is_null($id)) {
            $id = $this->_typeID;
        }
        $this->_db
            ->update(
                'run_type',
                $param = array(
                    'started' => 0
                ),
                $condition = array('id', '=', $id)
            );
    }


    public function reset($id = null)
    {
        if (is_null($id)) {
            $id = $this->_typeID;
        }
        $this->_db
            ->update(
                'runner',
                $param = array(
                    'end_time' => null,
                    'run_time'=> null,
                    'altered' => '0'
                ),
                $condition = array('run_type', '=', $id)
            )
            ->update(
                'run_type',
                $param = array(
                    'start_time' => null,
                    'started' => '0'
                ),
                $condition = array('id', '=', $id)
            );
    }


    public function getStartTime($id=null){
        if(is_null($id)){
            $id = $this->_typeID;
        }
        if($this->_db->select('run_type', ['id','=',$id])->firstResult()->started == true){
            return $this->_db->firstResult()->start_time;
        }else{
            return false;
        }
        

    }

    /**
     * 重新排序
     * 
     * 根據性別排序
     */
    public function reRank($gender)
    {
        $results = $this->_db->query(
            "SELECT `id`, `run_time` FROM runner WHERE `gender`=? and `run_type`= ? "
            ,[$gender, $this->_typeID])->getResults();        

        //change time string to seconds in able to rank;
        $comparableResult = [];
        foreach($results as $result){
            if(isset($result->run_time)){
                $id = $result->id;
                $timeArr = explode(":",$result->run_time);
                $second = 60*$timeArr[0]+60*$timeArr[1]+$timeArr[2];
                $comparableResult[$id]=$second;        
            }
        }

        //sort by php 
        asort($comparableResult);

        //rank-id array
        $rankIdArr = [];
        $i=1;
        foreach($comparableResult as $key=>$value){
            $rankIdArr[$i++]=$key;
        }
        
        foreach($rankIdArr as $rank=>$runner_id){
            $this->_db->update('runner', $param=['rank'=>$rank], $condition=['id','=',$runner_id]);
        }

    }


    public function getRankData($gender){


        $typeId = $this->_typeID;
        $result = $this->_db->query(
        "SELECT * FROM runner WHERE `run_type`= ${typeId} and `gender`=${gender} and `altered`=1 ORDER BY `rank` ASC"
        )->getResults();

        return $result;
        
        // print_r($result);

    }



}