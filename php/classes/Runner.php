<?php
class Runner{
    /**
     * Runner Class
     * 顯示跑者資訊
     * 新增跑者資料
     * 更新跑者內容（改號碼、完賽）
     * 
     * 
     */

    private $_db;
    
    private function __construct()
    {
        $this->_db = DB::singleton();

    }

    private static $_instance = null;
    public function singleton(){
        if(!isset(self::$_instance)){
            self::$_instance = new Runner();
        }
        return self::$_instance;
    }


    public function getDataById($id){
        return $this->_db->select('runner', $condition=['id','=',$id])->firstResult();
    }

    public function getDataByNumber($param){
        $column="";
        if(is_numeric($param)){
            //如果是背號
            $column = 'number';
        }else{
            $column="name";
        }
        return $this->_db->select('runner', $condition=[$column,'=',$param])->firstResult();
    }

    public function altered($number){
        return $this->getDataByNumber($number)->altered;
    }

    public function add($info){
        $newInfo = $this->setInfo($info);   //changes info to database friendly format
        $this->_db->insert('runner',$newInfo);
    }

    public function update($id, $info){
        $newInfo = $this->setInfo($info);
        $this->_db->update('runner',$newInfo, $condition = ['id','=',$id]);
    }

    public function remove($id){
        $this->_db->delete('runner', ['id','=',$id]);
    }

    public function finish($number){
        /**
         * 這個method直接設定登記成績，而不針對各個情境
         * （比賽未開始、已登記、無權限...）進行判定
         * 因為根據不同結果會有不同UI畫面
         * 因此放在架構中其他模組內進行
         */
        
         $run_type = new RunType(
            $this->_db->select('runner', $condition=['number','=',$number])->firstResult()->run_type
         );
         $start_time = new DateTime($run_type->getStartTime());
         $now = new DateTime();

         $run_time = $now->diff($start_time)->format('%H:%I:%S');

        $this->_db->update(
            'runner',
            $param = array(
                'end_time'=>$now->format("Y-m-d H:i:s"),
                'run_time'=>$run_time,
                'altered'=>'1'
            ),
            $condition = array('number','=',$number)
        );
    }



    public function setInfo($info){
        /**
         * Data to send to database
         *              original    ensure
         * run_group    string      id
         * run_type     string      id
         * 
         */
        $toReturn=array();

        if(array_key_exists('name', $info)){
            $toReturn['name']=$info['name'];
            unset($info['name']);
        }

        if(array_key_exists('number', $info)){
            //uniqueness should be validated in the input level
            $toReturn['number']=$info['number'];
            unset($info['number']);

        }

        if(array_key_exists('run_group', $info)){

            //如果沒有填寫跑團，跳過
            if(empty($info['run_group'])){
                //empty means  'run_group'=>""
                $$toReturn['run_group'] = "null";   //should be string since will be passed to sql string
            }else{
                //如果跑團存在，找到他的number並指定
                $rgName = $info['run_group'];
                $rg = RunGroup::singleton();
                $rgNum;  //run group id
                if($rg->exist($rgName)){
                    $rgNum = $rg->getNum($rgName);
                }else{
                    //跑團不存在，則建立一個
                    $rgNum = $rg->add($rgName);
                }
                $toReturn['run_group'] = $rgNum;
                unset($info['run_group']);

            }
        }

        if(array_key_exists('run_type', $info)){
            $rt = new RunType();
            $toReturn['run_type']  =  $rt->setID($info['run_type']);
            unset($info['run_type']);

        }

        if(array_key_exists('altered', $info)){
            if($info['altered']==true || $info['altered']=='1'){
                $toReturn['altered']  = 1;
            }else{
                $toReturn['altered'] = 0;
            }
            unset($info['altered']);

        }
        unset($info['id']);


        return array_merge($info, $toReturn);
    }
   
    public function search($keyword){

       $sql = " SELECT * FROM `runner` 
       WHERE 
       `name`LIKE ? 
       or
       `number` LIKE ?
       or
       `tel` LIKE ?
       or 
       `run_group` in (SELECT `number` FROM `run_group` WHERE `name` like ? ) 
       ORDER BY `run_type` ASC, `number` ASC";

       return $this->_db->query($sql, ['%'. $keyword.'%' ,'%'. $keyword.'%','%'. $keyword.'%' ,'%'. $keyword.'%']);
    }

}