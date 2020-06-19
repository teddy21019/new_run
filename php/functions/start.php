<?php
require_once("../core/init.php");

//check session
if(Session::exist('user') && Session::get('user')=='admin'){
    //check use post
    if(Input::exist()){
        $type = escape(Input::get('type'));
        $rt = new RunType($type);
        if(Input::get('action')=='start'){

            //check whether already started
            if($rt->isStart()){
                echo "404_STARTED";
            }else{
                $rt->start();
                echo "SUCCESS";
            }
        }

        elseif(Input::get('action')=='stop'){
            $rt->end();
            echo "SUCCESS";
        }

        elseif(Input::get('action')=='reset'){
            $rt->reset();
            echo "SUCCESS";
        }

    }
}

?>