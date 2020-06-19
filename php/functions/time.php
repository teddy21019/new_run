<?php 
require_once("../core/init.php");

if (Session::exist('user') && Session::get('user') == 'admin') {
    if (Input::exist()) {


        $type = escape(Input::get('type'));
        $rt = new RunType($type);
        if ($startTime = $rt->getStartTime()) {
            echo json_encode(array($startTime, strtotime($startTime)));
        } else {
            echo "";
        }
    }

}

?>