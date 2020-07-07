<?php
require_once("../core/init.php");

if (Session::exist('user') && Session::get('user') == 'admin') {
    if (Input::exist()) {

        switch (Input::get('action')){

            case 'PULL':
                try{
                    $result = Notification::singleton()->getNotifications();
                    /**
                     *          want  
                     * 
                     *    ----result-----
                     *  id           int         4  
                     *  time         string      "2020-03-26 12:34:56"
                     *  position     id          3   (name/lonlat get from positions array)
                     *  message      string      {'SUPPLY':{     
                     *                              3:4,
                     *                              5:1
                     *                           }}
                     */
                    echo (json_encode($result));

                }catch(Expectation $e){
                    echo ($e->getMessage);

                }
                

            break;


            //=======================

            case 'READ':
                try{
                    Notification::singleton()->changeRead(Input::get('id'));
                    echo("SUCCESS");
                }catch(Exception $e){
                    echo ($e->getMessage);
                }
                
            break;  


        }


    }else{
        echo("No authority");
        exit();
    }

}