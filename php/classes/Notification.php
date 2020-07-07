<?php

class Notification{
    private $_db;

    private function __construct(){
        $this->_db = DB::singleton();
    }

    private static $_instance = null;

    public function singleton(){
        if(self::$_instance == null){
            self::$_instance = new Notification();
        }
        return self::$_instance;
    }

    public function getNotifications(){

        /**
         *          want  
         * 
         *    ----result-----
         *  id           int         4  
         *  is_read      bool        0
         *  staff         int         {'id':4, 'name':'陳家威'}
         *  time         string      "2020-03-26 12:34:56"
         *  position     id          {'id':3, 'name':'小台灣'}
         *  message      string      {'':}
         */
        
        $results = $this->_db->select('notification')->getResults();

        $toReturn = [];

        foreach($result as $key=>$value){
            $toPush = [];
            $toPush['id'] = $result['id'];
            $toPush['is_read'] = $result['is_read'];

            //  staff
                //get name
                $staff_id = $result['staff'];
                $staff_name = Runner::singleton()->getFieldById('name',$staff_id);
                $staff_tel = Runner::singleton()->getFieldById('tel',$staff_id);
            $toPush['staff']=['id'=>$staff_id, 'name'=>$staff_name, 'tel'=>$staff_tel];

            //position
                //get position
                $pos_id = $result['position'];
                $pos_name = StaffGroup::singleton()->getPositionById($pos_id);
            $toPush['position']=['id'=>$pos_id, 'name'=>$pos_name];

            // time
                $orig_time = $result['time'];
                $time_name = explode(' ',$orig_time)[1];
            $toPush['time']=['orig'=>$orig_time, 'name'=>$time_name];
            
            //message
                $message = $this->messageDecode($result['message']);
                


        }
        
        //get id 
        

        return $result;


         
    }


    public function changeRead($id){

        $result = $this->_db->update('notification',['is_read'],['id','=',$id]);

    }

    public function messageDecode($json){
        $obj = json_decode($json, true);
        /**
         * {"SUPPLY":{
         *  "3":4,
         *   "1":2,
         *   "2":3
         *   },
         *   "MESSAGE":"謝謝"
        *  }
         */

         $message = [];

         //for each key
         if(isset($obj['SUPPLY'])){
             $supply_list = $obj['SUPPLY'];
             foreach($supply_list as $id=>$q){
                //get name of $supply's id
                $name = Supply::singleton()->getFieldById('name', $id)
                // $message[]getFieldById

             }
             
         }
    }
}