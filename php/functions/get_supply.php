<?php
require_once("../core/init.php");


if(Session::exist('user')){
    if(Input::get('action')){
        $result = Supply::singleton()->getAll();
        echo json_encode($result);
    }
}