<?php
require_once("../core/init.php");

if(Input::exist()){
    $id = Session::get('id');
    $positionId = Staff::singleton()->getFieldById('position', $id);
    $positionName = StaffGroup::singleton()->getPositionById($positionId)->name;

    echo(json_encode(['id'=>$positionId, 'name'=>$positionName ]));

}