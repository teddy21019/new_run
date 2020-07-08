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
        
        $result = $this->_db->select('notification')->getResults();
        $toReturn = [];

        foreach($result as $key=>$value){
            $toPush = [];
            $toPush['id'] = $value->id;
            $toPush['is_read'] = $value->is_read;

            //  staff
                //get name
                $staff_id = $value->staff;
                $staff_name = Staff::singleton()->getFieldById('name',$staff_id);
                $staff_tel = Staff::singleton()->getFieldById('tel',$staff_id);
            $toPush['staff']=['orig'=>$staff_id, 'show'=>$staff_name, 'tel'=>$staff_tel];

            //position
                //get position
                $pos_id = $value->position;
                $pos_name = StaffGroup::singleton()->getPositionById($pos_id)->name;
            $toPush['position']=['orig'=>$pos_id, 'show'=>$pos_name];

            // time
                $orig_time = $value->time;
                $time_name = explode(' ',$orig_time)[1];
            $toPush['time']=['orig'=>$orig_time, 'show'=>$time_name];
            
            //message
                $message = $this->messageDecode($value->message);
            $toPush['message']=['orig'=>$value->message, 'show'=>$message];
                
        $toReturn[]=$toPush;

        }
        
        //get id 
        

        return $toReturn;


         
    }

    public function pushNotification($param){
        $this->_db->insert('notification',$param);
    }

    public function changeRead($id){

        $result = $this->_db->update('notification',['is_read'=>'1'],['id','=',$id]);

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
         * 
         * Return : 補給：香蕉、水
         */

         $message = '';

         //for each key
         if(isset($obj['SUPPLY'])){
             $supply_list = $obj['SUPPLY'];
             $message.='物資：';
             foreach($supply_list as $id=>$q){
                //get name of $supply's id
                $name = Supply::singleton()->getFieldById('name', $id);
                // $message[] getFieldById
                $message .= $name.' ';
             } 
             $message.='。';
         }

         if(isset($obj['MEDICAL'])){
             $message.='傷患。';
        }

        return $message;
    }
}