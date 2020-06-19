<?php
require_once("../core/init.php");

if(Session::exist('user') && Session::get('user')=='admin'){
    if(Input::exist()){

        $toSearch = Input::get('text');

        $searchType;
        if($toSearch!=""){
            //搜尋跑者
            if(Input::get('action')=='runner'){
                $searchType = Runner::singleton();

    
    
            //搜尋工作人員
            }elseif(Input::get('action')=='run_group'){
                $searchType = RunGroup::singleton();
            }
            elseif(Input::get('action')=='staff'){
                $searchType = Staff::singleton();   //staff is not singleton, bad design QQ
            }

            $results = $searchType->search($toSearch)->getResults();
            foreach($results as $result){
                unset($result->password);
            }
            echo json_encode($results, JSON_UNESCAPED_UNICODE);
        }

    }
}