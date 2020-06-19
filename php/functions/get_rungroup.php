<?php
require_once("../core/init.php");

if(Input::exist()){

    $result_org = RunGroup::singleton()->getAll();
    $result = array();
    foreach ($result_org as $value) {
        $result[$value->number] =  $value->name;
    }

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
}