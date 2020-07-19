<?php
require_once("../core/init.php");

if(Input::exist()){
    
    $rt = new RunType();
    if(Input::get('action')=='started'){
        $result_org = $rt->getStarted();

    }else{
        $result_org = $rt->getAll();
    }

    echo json_encode($result_org, JSON_UNESCAPED_UNICODE);
}