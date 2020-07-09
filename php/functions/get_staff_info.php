<?php
require_once("../core/init.php");

if(Session::get('user')=='admin'){
    if(Input::exist()){
        $staff_type = [];
        $position= [];
    
        //get type
        $type_org = StaffGroup::singleton()->getTypes();
        foreach($type_org as $value){
            $staff_type[$value->id] = $value->name;
        }
    
        //get group
        $position_org = StaffGroup::singleton()->getPositions();
        foreach($position_org as $value){
            $position[$value->id] = $value->name;
        }
        echo json_encode(array($staff_type, $position), JSON_UNESCAPED_UNICODE);
    }
}else{
    echo "404";
}
